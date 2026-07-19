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
        'couple' => 'Mempelai',
        'event_details' => 'Rangkaian Acara',
        'gift' => 'Amplop Digital',
        'quote' => 'Kutipan',
        'love_story' => 'Kisah Kami',
        'live_stream' => 'Live Streaming',
        'closing' => 'Penutup',
        'wishes' => 'Ucapan & Doa',
        'code' => '⚠ Kode (HTML)',
    ];

    // Container UI ditangguhkan (SP2): backend nesting (parent_id, column_index) tetap
    // ada dan section tersimpan lama tetap dirender, tapi tidak bisa ditambah baru.
    private const HIDDEN_SECTION_TYPES = ['section_one_col', 'section_two_col', 'section_three_col'];

    public function studio($id)
    {
        $this->authorizeSuperAdmin();

        $template = InvitationTemplate::findOrFail($id);

        $default = config('invitation.default_theme');
        $themeBase = [
            'colors' => array_merge($default['colors'], $template->theme['colors'] ?? []),
            'fonts' => array_merge($default['fonts'], $template->theme['fonts'] ?? []),
            'scales' => array_merge($default['scales'], $template->theme['scales'] ?? []),
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
        $this->authorizeSuperAdmin();

        $request->validate([
            'template_id' => 'required|exists:invitation_templates,id',
            'global_custom_css' => 'nullable|string',
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
            // tolak < > — mencegah penutupan tag <style> saat di-inline oleh _section-shell
            'custom_css' => 'sometimes|nullable|string|not_regex:/[<>]/',
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
                if (in_array($value, self::HIDDEN_SECTION_TYPES, true) || !is_array(config("invitation_components.{$value}"))) {
                    $fail('Tipe section tidak dikenal.');
                }
            }],
            'parent_id' => [
                'nullable',
                Rule::exists('invitation_sections', 'id')->where(fn ($query) => $query->where('template_id', $templateId)),
            ],
            'props' => 'nullable|array',
        ]);

        $template = InvitationTemplate::findOrFail($templateId);

        $schema = config("invitation_components.{$request->section_type}", []);
        $defaultProps = collect($schema)->pluck('default', 'key')->all();

        // props opsional (dari preset): merge di atas default, tervalidasi skema
        $defaultProps = array_merge($defaultProps, (new \App\Services\SectionPropsValidator())->validate(
            $request->section_type,
            $request->input('props', [])
        ));

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
                if (in_array($value, self::HIDDEN_SECTION_TYPES, true) || !is_array(config("invitation_components.{$value}"))) {
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

        // Section persisted → ambil dari DB untuk atribut shell (custom_css);
        // props tetap dari request (state terbaru di studio, sudah divalidasi).
        $section = $request->filled('section_id')
            ? InvitationSection::find($request->section_id)
            : null;
        if (!$section) {
            $section = new InvitationSection(['section_type' => $request->section_type]);
            if ($request->filled('section_id')) {
                $section->id = $request->section_id;
            }
        }
        $section->section_type = $request->section_type;
        $section->props = $validatedProps;

        $placeholderPage = new InvitationPage([
            'groom_name' => 'Romeo',
            'bride_name' => 'Juliet',
            'event_date' => now()->addMonths(6),
        ]);

        // Render via shell → respons = wrapper [data-section-id] LENGKAP (swap outerHTML).
        $html = view('templates._section-shell', [
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
        $hex = 'regex:/^#([0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/';
        $colorKeys = ['primary', 'accent', 'surface', 'surface_alt', 'text', 'muted', 'ink', 'on_dark'];

        $rules = [
            'colors' => 'required|array',
            'fonts' => 'required|array',
            'fonts.heading' => ['required', \Illuminate\Validation\Rule::in($curatedFontNames)],
            'fonts.body' => ['required', \Illuminate\Validation\Rule::in($curatedFontNames)],
            'scales' => 'required|array',
            'scales.type_base' => 'required|numeric|min:8|max:40',
            'scales.type_ratio' => 'required|numeric|min:1|max:2',
            'scales.radius' => 'required|numeric|min:0|max:64',
            'scales.section_spacing' => 'required|numeric|min:0|max:200',
            'scales.shadow_level' => ['required', \Illuminate\Validation\Rule::in(['none', 'sm', 'md', 'lg'])],
        ];
        foreach ($colorKeys as $k) {
            $rules["colors.{$k}"] = ['required', $hex];
        }

        $request->validate($rules);

        $template = InvitationTemplate::findOrFail($templateId);
        $template->update([
            'theme' => [
                'colors' => $request->colors,
                'fonts' => $request->fonts,
                'scales' => $request->scales,
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

        // Warning tidak memblokir, tapi minta konfirmasi (force) sekali.
        $warnings = $this->lintWarnings($template);
        if (!empty($warnings) && !$request->boolean('force')) {
            return response()->json(['success' => false, 'warnings' => $warnings], 409);
        }

        $template->update(['status' => 'published']);

        return response()->json(['success' => true, 'message' => 'Template published']);
    }

    private function lintWarnings(InvitationTemplate $template): array
    {
        $warnings = [];

        // 1) Terlalu banyak override warna literal (mengabaikan theme buyer).
        $colorOverrides = 0;
        foreach ($template->sections as $section) {
            $schema = config("invitation_components.{$section->section_type}", []);
            foreach ($schema as $field) {
                if (($field['type'] ?? null) === 'color' && array_key_exists($field['key'], $section->props ?? [])
                    && $section->props[$field['key']] !== null) {
                    $colorOverrides++;
                }
            }
        }
        if ($colorOverrides > 5) {
            $warnings[] = "{$colorOverrides} override warna literal — theme buyer tidak akan berpengaruh pada field ini.";
        }

        // 2) CSS/HTML kustom.
        $hasCustom = $template->sections->contains(fn ($s) => trim((string) $s->custom_css) !== '' || $s->section_type === 'code');
        if ($hasCustom) {
            $warnings[] = 'Template memakai CSS/HTML kustom — pastikan tetap rapi di semua perangkat.';
        }

        // 3) Tidak ada RSVP.
        if (!$template->sections->contains(fn ($s) => $s->section_type === 'rsvp')) {
            $warnings[] = 'Template belum memiliki section RSVP.';
        }

        // 4) Kontras WCAG dari 4 token theme.
        $default = config('invitation.default_theme.colors');
        $colors = array_merge($default, $template->theme['colors'] ?? []);
        if ($this->contrastRatio($colors['text'], $colors['surface']) < 4.5) {
            $warnings[] = 'Kontras teks vs latar rendah (< 4.5) — teks bisa sulit dibaca.';
        }
        if ($this->contrastRatio($colors['accent'], $colors['surface']) < 3.0) {
            $warnings[] = 'Kontras aksen vs latar rendah (< 3.0) — elemen aksen bisa kurang terlihat.';
        }

        return $warnings;
    }

    private function contrastRatio(string $hex1, string $hex2): float
    {
        $lum = function (string $hex): float {
            $hex = ltrim($hex, '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            if (strlen($hex) < 6) {
                return 1.0; // hex tak valid → anggap terang, jangan false-alarm
            }
            $channel = function (float $c): float {
                $c /= 255;
                return $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
            };

            return 0.2126 * $channel((float) hexdec(substr($hex, 0, 2)))
                + 0.7152 * $channel((float) hexdec(substr($hex, 2, 2)))
                + 0.0722 * $channel((float) hexdec(substr($hex, 4, 2)));
        };

        $l1 = $lum($hex1);
        $l2 = $lum($hex2);

        return (max($l1, $l2) + 0.05) / (min($l1, $l2) + 0.05);
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
                'coverImage' => $renderer->coverImage($template->sections),
                'studioMode' => true,
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

    private function authorizeSuperAdmin(): void
    {
        $currentUser = \App\Models\User::find(\Illuminate\Support\Facades\Auth::id());

        if (!$currentUser || $currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
    }
}
