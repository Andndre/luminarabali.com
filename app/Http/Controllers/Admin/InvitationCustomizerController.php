<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Services\InvitationRenderer;
use App\Services\SectionPropsValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class InvitationCustomizerController extends Controller
{
    /** Tipe field content yang boleh diedit buyer di MVP (spec §8: teks + foto). */
    private const BUYER_FIELD_TYPES = ['text', 'image'];

    protected function authorizeSuperAdmin(): void
    {
        $currentUser = \App\Models\User::find(Auth::id());

        if (! $currentUser || $currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function load($id)
    {
        $this->authorizeSuperAdmin();

        $page = InvitationPage::with(['sections' => fn ($q) => $q->orderBy('order_index')])
            ->findOrFail($id);

        $default = config('invitation.default_theme');
        $templateTheme = is_array($page->template?->theme) ? $page->template->theme : [];

        $themeBase = [
            'colors' => array_merge($default['colors'], $templateTheme['colors'] ?? []),
            'fonts' => array_merge($default['fonts'], $templateTheme['fonts'] ?? []),
        ];

        $sections = $page->sections
            ->whereNull('parent_id')
            ->values()
            ->map(function ($section) {
                $fields = collect(config("invitation_components.{$section->section_type}", []))
                    ->filter(fn ($f) => ($f['group'] ?? null) === 'content'
                        && in_array($f['type'], self::BUYER_FIELD_TYPES, true))
                    ->map(fn ($f) => [
                        'key' => $f['key'],
                        'type' => $f['type'],
                        'label' => $f['label'],
                        'value' => $section->props[$f['key']] ?? ($f['default'] ?? null),
                    ])
                    ->values();

                return [
                    'id' => $section->id,
                    'section_type' => $section->section_type,
                    'fields' => $fields,
                ];
            })
            ->filter(fn ($s) => count($s['fields']) > 0)
            ->values();

        return response()->json([
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'groom_name' => $page->groom_name,
                'bride_name' => $page->bride_name,
                'event_date' => optional($page->event_date)->toDateString(),
            ],
            'theme_base' => $themeBase,
            'theme_overrides' => $page->theme_overrides ?? ['colors' => [], 'fonts' => []],
            'fonts' => collect(config('invitation.fonts'))->pluck('name')->values(),
            'sections' => $sections,
        ]);
    }

    public function save(Request $request, $id)
    {
        $this->authorizeSuperAdmin();

        $page = InvitationPage::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'theme_overrides' => 'nullable|array',
            'sections' => 'nullable|array',
            'sections.*.id' => 'required|integer',
            'sections.*.props' => 'required|array',
        ]);

        $overrides = $this->validateThemeOverrides($request->input('theme_overrides'));

        // Validasi semua section SEBELUM menulis apa pun.
        $sectionWrites = [];
        foreach ($request->input('sections', []) as $index => $payload) {
            $section = InvitationSection::where('page_id', $page->id)->find($payload['id']);
            if (! $section) {
                throw ValidationException::withMessages([
                    "sections.{$index}.id" => ['Section tidak ditemukan pada undangan ini.'],
                ]);
            }

            $schema = collect(config("invitation_components.{$section->section_type}", []));
            $allowedKeys = $schema
                ->filter(fn ($f) => ($f['group'] ?? null) === 'content'
                    && in_array($f['type'], self::BUYER_FIELD_TYPES, true))
                ->pluck('key')->all();

            $incoming = array_intersect_key($payload['props'], array_flip($allowedKeys));
            $validated = app(SectionPropsValidator::class)
                ->validate($section->section_type, $incoming, 'content');

            // Props image harus menunjuk aset milik page ini.
            $imageKeys = $schema->where('type', 'image')->pluck('key')->all();
            foreach ($imageKeys as $key) {
                $value = $validated[$key] ?? null;
                if ($value !== null && $value !== ''
                    && ! $page->assets()->where('file_path', $value)->exists()) {
                    throw ValidationException::withMessages([
                        "sections.{$index}.props.{$key}" => ['Foto harus berasal dari galeri undangan ini.'],
                    ]);
                }
            }

            $sectionWrites[] = [$section, $validated];
        }

        $page->update([
            'title' => $request->title,
            'groom_name' => $request->groom_name,
            'bride_name' => $request->bride_name,
            'event_date' => $request->event_date,
            'theme_overrides' => $overrides,
        ]);

        foreach ($sectionWrites as [$section, $validated]) {
            $section->update(['props' => array_merge($section->props ?? [], $validated)]);
        }

        Cache::forget("invitation:{$page->slug}");

        return response()->json(['success' => true]);
    }

    private function validateThemeOverrides(?array $overrides): ?array
    {
        if ($overrides === null) {
            return null;
        }

        $errors = [];
        $curated = collect(config('invitation.fonts'))->pluck('name')->all();
        $clean = ['colors' => [], 'fonts' => []];

        foreach ((array) ($overrides['colors'] ?? []) as $key => $value) {
            if (! is_string($value) || ! preg_match('/^#[0-9a-fA-F]{3,8}$/', $value)) {
                $errors["theme_overrides.colors.{$key}"] = ["Warna {$key} harus hex valid."];
                continue;
            }
            $clean['colors'][$key] = $value;
        }

        foreach ((array) ($overrides['fonts'] ?? []) as $key => $value) {
            if (! in_array($value, $curated, true)) {
                $errors["theme_overrides.fonts.{$key}"] = ["Font {$key} harus dari daftar kurasi."];
                continue;
            }
            $clean['fonts'][$key] = $value;
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return $clean;
    }

    public function preview($id)
    {
        $this->authorizeSuperAdmin();

        $page = InvitationPage::with(['assets', 'template', 'sections' => fn ($q) => $q->orderBy('order_index')])
            ->findOrFail($id);

        $usesSections = $page->sections->isNotEmpty();

        if (! $usesSections && empty($page->template?->html_content)) {
            return response()->view('invitations.not-ready', ['page' => $page]);
        }

        $renderer = new InvitationRenderer();

        if ($usesSections) {
            // Deferred closure: harus dievaluasi di dalam render pass
            // invitations.public agar @push/@stack section tidak hilang
            // (lihat komentar panjang di InvitationViewController::show).
            $content = fn () => $renderer->render($page);
            $themeStyle = $renderer->themeStyle($page);
        } else {
            $content = fn () => $page->template->html_content ?? '';
            $themeStyle = '';
        }

        return view('invitations.public', [
            'page' => $page,
            'content' => $content,
            'themeStyle' => $themeStyle,
            'usesSections' => $usesSections,
        ]);
    }
}
