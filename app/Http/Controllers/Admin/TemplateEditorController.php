<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationPage;
use App\Models\InvitationTemplate;
use App\Models\InvitationSection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TemplateEditorController extends Controller
{
    private const SECTION_TYPE_LABELS = [
        'cover' => 'Cover',
        'hero' => 'Hero',
        'text' => 'Teks',
        'image' => 'Gambar',
        'button' => 'Tombol',
        'divider' => 'Pembatas',
        'spacer' => 'Spasi',
        'countdown' => 'Hitung Mundur',
        'gallery' => 'Galeri',
        'map' => 'Peta',
        'music' => 'Musik',
        'rsvp' => 'RSVP',
        'video' => 'Video',
        'section_one_col' => 'Kontainer 1 Kolom',
        'section_two_col' => 'Kontainer 2 Kolom',
        'section_three_col' => 'Kontainer 3 Kolom',
    ];

    public function editor($id)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $template = InvitationTemplate::with(['sections', 'creator'])->findOrFail($id);
        return view('admin.templates.editor-native', compact('template'));
    }

    public function studio($id)
    {
        $this->authorizeSuperAdmin();

        $template = InvitationTemplate::findOrFail($id);

        $default = config('invitation.default_theme');
        $themeBase = [
            'colors' => array_merge($default['colors'], $template->theme['colors'] ?? []),
            'fonts' => array_merge($default['fonts'], $template->theme['fonts'] ?? []),
        ];

        return view('admin.templates.studio', [
            'template' => $template,
            'themeBase' => $themeBase,
            'fonts' => collect(config('invitation.fonts'))->pluck('name')->values(),
            'sectionTypes' => self::SECTION_TYPE_LABELS,
            'schema' => config('invitation_components'),
        ]);
    }

    public function load($id)
    {
        $this->authorizeSuperAdmin();

        $template = InvitationTemplate::with(['sections' => function ($query) {
            $query->orderBy('order_index');
        }])->findOrFail($id);

        return response()->json([
            'template' => $template,
            'sections' => $template->sections->map(fn($section) => $this->transformSection($section))->values()->toArray(),
        ]);
    }

    public function saveSection(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:invitation_templates,id',
            'global_custom_css' => 'nullable|string',
            'html_content' => 'nullable|string',
            'cover_content' => 'nullable|string',
            'meta_data' => 'nullable|string',
        ]);

        $templateId = (int) $request->template_id;
        $template = InvitationTemplate::findOrFail($templateId);

        $metaData = null;
        if ($request->meta_data) {
            $metaData = json_decode($request->meta_data, true);
        }

        $template->update([
            'global_custom_css' => $request->global_custom_css,
            'html_content' => $request->html_content,
            'cover_content' => $request->cover_content,
            'meta_data' => $metaData,
        ]);

        InvitationPage::where('template_id', $templateId)
            ->pluck('slug')
            ->each(fn ($slug) => Cache::forget("invitation:{$slug}"));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Berhasil disimpan!']);
        }

        return redirect()->back()->with('success', 'Berhasil disimpan!');
    }

    public function updateSection(Request $request, $id)
    {
        $this->authorizeSuperAdmin();

        $section = InvitationSection::findOrFail($id);

        // null eksplisit = "hapus override ini" (kembali ke theme/default partial).
        $incoming = $request->input('props', []);
        $nullKeys = array_keys(array_filter($incoming, fn ($v) => $v === null));
        $incoming = array_diff_key($incoming, array_flip($nullKeys));

        $validated = array_merge($section->props ?? [], (new \App\Services\SectionPropsValidator())->validate(
            $section->section_type,
            $incoming
        ));
        foreach ($nullKeys as $key) {
            unset($validated[$key]);
        }

        $validatedFields = $request->validate([
            'custom_css' => 'sometimes|nullable|string',
            'is_visible' => 'sometimes|boolean',
        ]);
        $section->update(array_merge(
            $validatedFields,
            ['props' => $validated]
        ));

        return response()->json(['success' => true, 'section' => $section]);
    }

    public function storeSection(Request $request, $templateId)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'section_type' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!is_array(config("invitation_components.{$value}"))) {
                    $fail('Tipe section tidak dikenal.');
                }
            }],
            'parent_id' => [
                'nullable',
                Rule::exists('invitation_sections', 'id')->where(fn ($query) => $query->where('template_id', $templateId)),
            ],
        ]);

        $template = InvitationTemplate::findOrFail($templateId);

        $schema = config("invitation_components.{$request->section_type}", []);
        $defaultProps = collect($schema)->pluck('default', 'key')->all();

        $nextOrderIndex = $template->sections()->count() > 0
            ? 1 + (int) $template->sections()->max('order_index')
            : 0;

        $section = $template->sections()->create([
            'parent_id' => $request->parent_id,
            'section_type' => $request->section_type,
            'order_index' => $nextOrderIndex,
            'props' => $defaultProps,
            'is_visible' => true,
        ]);

        return response()->json(['success' => true, 'section' => $section], 201);
    }

    public function renderSection(Request $request)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'section_type' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!is_array(config("invitation_components.{$value}"))) {
                    $fail('Tipe section tidak dikenal.');
                }
            }],
            'props' => 'nullable|array',
            'section_id' => 'nullable',
        ]);

        $validatedProps = (new \App\Services\SectionPropsValidator())->validate(
            $request->section_type,
            $request->input('props', [])
        );

        $section = new InvitationSection([
            'section_type' => $request->section_type,
            'props' => $validatedProps,
        ]);
        if ($request->filled('section_id')) {
            $section->id = $request->section_id;
        }

        $placeholderPage = new InvitationPage([
            'groom_name' => 'Romeo',
            'bride_name' => 'Juliet',
            'event_date' => now()->addMonths(6),
        ]);

        $viewPath = "templates.components.{$request->section_type}";
        $html = view($viewPath, [
            'props' => $validatedProps,
            'section' => $section,
            'page' => $placeholderPage,
            'elements' => [],
        ])->render();

        return response()->json(['html' => $html]);
    }

    public function updateTheme(Request $request, $templateId)
    {
        $this->authorizeSuperAdmin();

        $curatedFontNames = collect(config('invitation.fonts'))->pluck('name')->all();

        $request->validate([
            'colors' => 'required|array',
            'colors.primary' => ['required', 'regex:/^#([0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/'],
            'colors.accent' => ['required', 'regex:/^#([0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/'],
            'colors.surface' => ['required', 'regex:/^#([0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/'],
            'colors.text' => ['required', 'regex:/^#([0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/'],
            'fonts' => 'required|array',
            'fonts.heading' => ['required', \Illuminate\Validation\Rule::in($curatedFontNames)],
            'fonts.body' => ['required', \Illuminate\Validation\Rule::in($curatedFontNames)],
        ]);

        $template = InvitationTemplate::findOrFail($templateId);
        $template->update([
            'theme' => [
                'colors' => $request->colors,
                'fonts' => $request->fonts,
            ],
        ]);

        return response()->json(['success' => true, 'theme' => $template->theme]);
    }

    public function deleteSection($id)
    {
        $this->authorizeSuperAdmin();

        $section = InvitationSection::findOrFail($id);
        $section->delete();

        return response()->json(['success' => true, 'message' => 'Section deleted']);
    }

    public function reorderSections(Request $request)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:invitation_sections,id',
            'sections.*.order_index' => 'required|integer',
        ]);

        foreach ($request->sections as $sectionData) {
            $section = InvitationSection::find($sectionData['id']);
            if ($section) {
                $section->update(['order_index' => $sectionData['order_index']]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function duplicateSection($id)
    {
        $this->authorizeSuperAdmin();

        $section = InvitationSection::findOrFail($id);

        if (!$section->template_id) {
            return response()->json([
                'success' => false, 'message' => 'Hanya section template yang bisa diduplikat dari Studio.',
            ], 422);
        }

        $copy = null;

        DB::transaction(function () use ($section, &$copy) {
            InvitationSection::where('template_id', $section->template_id)
                ->where(fn ($q) => $section->parent_id === null
                    ? $q->whereNull('parent_id')
                    : $q->where('parent_id', $section->parent_id))
                ->where('order_index', '>', $section->order_index)
                ->increment('order_index');

            $copy = $section->replicate();
            $copy->order_index = $section->order_index + 1;
            $copy->save();

            // ponytail: depth-1 child copy — the section tree never nests deeper today;
            // make this recursive if a partial ever renders grandchildren.
            foreach ($section->children()->orderBy('order_index')->get() as $child) {
                $childCopy = $child->replicate();
                $childCopy->parent_id = $copy->id;
                $childCopy->save();
            }
        });

        return response()->json(['success' => true, 'section' => $copy], 201);
    }

    public function publish(Request $request, $id)
    {
        $this->authorizeSuperAdmin();

        $template = InvitationTemplate::with(['sections' => function ($query) {
            $query->where('is_visible', true);
        }])->findOrFail($id);

        $errors = $this->lintTemplate($template);

        if (!empty($errors)) {
            return response()->json(['success' => false, 'errors' => $errors], 422);
        }

        $template->update(['status' => 'published']);

        return response()->json(['success' => true, 'message' => 'Template published']);
    }

    private function lintTemplate(InvitationTemplate $template): array
    {
        $errors = [];

        $hasCover = $template->sections->contains(fn ($section) => $section->section_type === 'cover');
        if (!$hasCover) {
            $errors[] = 'Template harus memiliki section cover.';
        }

        foreach ($template->sections as $section) {
            $schema = config("invitation_components.{$section->section_type}", []);
            foreach ($schema as $field) {
                if (($field['group'] ?? null) !== 'content') {
                    continue;
                }
                if ($section->section_type === 'cover' && $field['key'] === 'background_image') {
                    // Cover's background_image is optional by design: cover.blade.php always
                    // renders an opaque #1a1a1a fallback background unconditionally (see Task 6
                    // brief scope note), so an empty cover background image must not block
                    // publishing. This exemption is intentionally narrow: hero.blade.php has no
                    // equivalent fallback (an unset background_image just renders `url('')`), and
                    // other image-type content fields (e.g. image.src) have no fallback either, so
                    // they are NOT exempted here.
                    continue;
                }
                $effective = $section->props[$field['key']] ?? $field['default'] ?? null;
                if ($effective === null || $effective === '') {
                    $errors[] = "Section \"{$section->section_type}\" (#{$section->id}): field \"{$field['label']}\" wajib diisi.";
                }
            }
        }

        return $errors;
    }

    public function preview($id)
    {
        $template = InvitationTemplate::with(['sections' => function ($query) {
            $query->orderBy('order_index');
        }])->findOrFail($id);

        return view('admin.templates.preview', compact('template'));
    }

    public function studioPreview($id)
    {
        $this->authorizeSuperAdmin();

        $template = InvitationTemplate::with(['sections' => function ($query) {
            $query->orderBy('order_index');
        }])->findOrFail($id);

        $renderer = new \App\Services\InvitationRenderer();

        // Stub page (never persisted): invitations.public reads title/names/slug/
        // meta_data off $page, and the rsvp partial reads $page->slug ?? ''.
        $page = new InvitationPage([
            'title' => 'Studio Preview: '.$template->name,
            'slug' => 'studio-preview',
            'groom_name' => 'Romeo',
            'bride_name' => 'Juliet',
            'event_date' => now()->addMonths(6),
            'meta_data' => [],
        ]);
        $page->setRelation('template', $template);
        $page->setRelation('sections', $template->sections);

        return response()
            ->view('invitations.public', [
                'page' => $page,
                // Deferred closure: must render nested inside invitations.public's own
                // render pass so section partials' @push('scripts') survive to @stack.
                'content' => fn () => $renderer->renderTemplate($template),
                'themeStyle' => $renderer->templateThemeStyle($template),
                'usesSections' => true,
            ])
            ->header('Cache-Control', 'no-store, private');
    }

    private function transformSection(InvitationSection $section): array
    {
        return [
            'id' => (string) $section->id,
            'parent_id' => $section->parent_id ? (string) $section->parent_id : null,
            'section_type' => $section->section_type,
            'order_index' => $section->order_index,
            'props' => $section->props ?? [],
            'custom_css' => $section->custom_css,
            'is_visible' => (bool) $section->is_visible,
        ];
    }

    private function resolveParentId(?string $parentId, array $tempIdMapping, array $persistedIds): ?int
    {
        if (empty($parentId)) {
            return null;
        }

        if (str_starts_with($parentId, 'temp-')) {
            if (!isset($tempIdMapping[$parentId])) {
                throw ValidationException::withMessages([
                    'sections' => ['Invalid section structure: parent must be created before child.'],
                ]);
            }

            return (int) $tempIdMapping[$parentId];
        }

        if (!in_array($parentId, $persistedIds, true)) {
            throw ValidationException::withMessages([
                'sections' => ['Invalid parent_id reference found in sections payload.'],
            ]);
        }

        return (int) $parentId;
    }

    private function authorizeSuperAdmin(): void
    {
        $currentUser = \App\Models\User::find(\Illuminate\Support\Facades\Auth::id());

        if (!$currentUser || $currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    private function allowedSectionTypes(): array
    {
        return [
            'cover',
            'section_one_col',
            'section_two_col',
            'section_three_col',
            'hero',
            'text',
            'image',
            'button',
            'divider',
            'spacer',
            'countdown',
            'gallery',
            'map',
            'music',
            'rsvp',
            'video',
        ];
    }
}
