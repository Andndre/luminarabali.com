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
        'couple' => 'Mempelai',
        'event_details' => 'Rangkaian Acara',
        'gift' => 'Amplop Digital',
        'quote' => 'Kutipan',
        'love_story' => 'Kisah Kami',
        'live_stream' => 'Live Streaming',
        'closing' => 'Penutup',
        'wishes' => 'Ucapan & Doa',
        'code' => '⚠ Kode (HTML)',
        'section_one_col' => 'Kolom 1',
        'section_two_col' => 'Kolom 2',
        'section_three_col' => 'Kolom 3',
    ];

    public function studio($id)
    {
        $this->authorizeStudio();

        $template = InvitationTemplate::findOrFail($id);

        $default = config('invitation.default_theme');
        $themeBase = [
            'colors' => array_merge($default['colors'], $template->theme['colors'] ?? []),
            'fonts' => array_merge($default['fonts'], $template->theme['fonts'] ?? []),
            'scales' => array_merge($default['scales'], $template->theme['scales'] ?? []),
            'ornaments' => array_merge($default['ornaments'], $template->theme['ornaments'] ?? []),
        ];

        return view('admin.templates.studio', [
            'template' => $template,
            'themeBase' => $themeBase,
            'fonts' => collect(config('invitation.fonts'))->pluck('name')->values(),
            'sectionTypes' => self::SECTION_TYPE_LABELS,
            'componentClasses' => config('invitation_component_classes'),
            'schema' => config('invitation_components'),
        ]);
    }

    public function load($id)
    {
        $this->authorizeStudio();

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
        $this->authorizeStudio();

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
        $this->authorizeStudio();

        $section = InvitationSection::findOrFail($id);

        // column_index adalah data STRUKTURAL (posisi anak di dalam kolom container),
        // bukan konten/desain — hanya storeSection (saat create) dan reorderSections
        // yang boleh menulisnya, dan keduanya sudah membatasi nilainya sesuai jumlah
        // kolom container. updateSection tidak boleh menyentuhnya sama sekali (termasuk
        // via null eksplisit), supaya nilai yang tersimpan di $section->props tidak
        // pernah berubah lewat endpoint ini dan anak tidak "hilang" ke kolom yang
        // tidak ada di layout.
        $incoming = $request->input('props', []);
        unset($incoming['column_index']);

        // _locked (gembok per-field) juga STRUKTURAL — hanya boleh diubah lewat key
        // request `locked` terpisah di bawah, yang tervalidasi Rule::in(contentKeys).
        // Buang dari props masuk supaya tak bisa di-set/di-clear (via null eksplisit)
        // menembus jalur props generik.
        unset($incoming['_locked']);

        // null eksplisit = "hapus override ini" (kembali ke theme/default partial).
        $nullKeys = array_keys(array_filter($incoming, fn ($v) => $v === null));
        $incoming = array_diff_key($incoming, array_flip($nullKeys));

        $validated = array_merge($section->props ?? [], (new \App\Services\SectionPropsValidator())->validate(
            $section->section_type,
            $incoming
        ));
        foreach ($nullKeys as $key) {
            unset($validated[$key]);
        }

        // Gembok per-field (guideline §10.1b). Disimpan sebagai key reserved `_locked`
        // di props, tapi ditransport lewat key request terpisah supaya tak pernah
        // melewati SectionPropsValidator (yang membuang key non-skema).
        if ($request->has('locked')) {
            $contentKeys = collect(config("invitation_components.{$section->section_type}", []))
                ->filter(fn ($field) => ($field['group'] ?? null) === 'content')
                ->pluck('key')
                ->all();

            $request->validate([
                'locked' => ['array'],
                'locked.*' => ['string', Rule::in($contentKeys)],
            ], [
                'locked.*.in' => 'Hanya field konten yang bisa dikunci.',
            ]);

            $locked = array_values(array_unique($request->input('locked', [])));

            if ($locked === []) {
                unset($validated['_locked']);
            } else {
                $validated['_locked'] = $locked;
            }
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
        $this->authorizeStudio();

        $classes = config('invitation_component_classes');

        $request->validate([
            'section_type' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!is_array(config("invitation_components.{$value}"))) {
                    $fail('Tipe section tidak dikenal.');
                }
            }],
            'parent_id' => [
                'nullable',
                Rule::exists('invitation_sections', 'id')->where(fn ($query) => $query->where('template_id', $templateId)),
                function ($attribute, $value, $fail) use ($request, $classes) {
                    if ($value === null) {
                        return;
                    }
                    if (!in_array($request->section_type, $classes['basic'], true)) {
                        $fail('Hanya blok dasar yang boleh diletakkan di dalam kolom.');

                        return;
                    }
                    $parent = InvitationSection::find($value);
                    if (!$parent || !in_array($parent->section_type, $classes['container'], true)) {
                        $fail('Induk harus berupa Layout section (kolom).');
                    }
                },
            ],
            'column_index' => [
                'nullable', 'integer', 'min:0',
                function ($attribute, $value, $fail) use ($request, $classes) {
                    if ($value === null || $request->parent_id === null) {
                        return;
                    }
                    $parent = InvitationSection::find($request->parent_id);
                    $columns = $classes['container_columns'][$parent->section_type ?? ''] ?? 0;
                    if ($value >= $columns) {
                        $fail('Kolom tidak tersedia pada container ini.');
                    }
                },
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

        if ($request->parent_id !== null) {
            $defaultProps['column_index'] = (int) $request->input('column_index', 0);
        }

        // Urutan dihitung di antara saudara sekandung (top-level ATAU sesama anak),
        // bukan seluruh template — kalau tidak, anak pertama sudah lahir dengan
        // order_index besar dan urutan kolom jadi kacau.
        $siblings = $template->sections()
            ->where(fn ($query) => $request->parent_id === null
                ? $query->whereNull('parent_id')
                : $query->where('parent_id', $request->parent_id));
        $nextOrderIndex = $siblings->count() > 0 ? 1 + (int) $siblings->max('order_index') : 0;

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
        $this->authorizeStudio();

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
        $this->authorizeStudio();

        $curatedFontNames = collect(config('invitation.fonts'))->pluck('name')->all();

        // Font boleh tiga bentuk: nama kurasi (string, bentuk lama), atau objek dengan
        // source google/upload. Pola nama keluarga dan daftar ekstensi diambil dari
        // InvitationRenderer supaya yang divalidasi di sini persis yang aman dirender.
        $fontRule = function (string $attribute, $value, $fail) use ($curatedFontNames) {
            if (is_string($value)) {
                if (! in_array($value, $curatedFontNames, true)) {
                    $fail('Font kurasi tidak dikenal.');
                }

                return;
            }

            if (! is_array($value)) {
                $fail('Format font tidak valid.');

                return;
            }

            $family = $value['family'] ?? null;
            if (! is_string($family) || ! preg_match(\App\Services\InvitationRenderer::FONT_FAMILY_PATTERN, $family)) {
                $fail('Nama font hanya boleh huruf, angka, dan spasi (maksimal 60 karakter).');

                return;
            }

            if (($value['source'] ?? null) === 'google') {
                return;
            }

            if (($value['source'] ?? null) === 'upload') {
                $path = $value['path'] ?? null;
                $ext = is_string($path) ? strtolower(pathinfo($path, PATHINFO_EXTENSION)) : '';
                if (! is_string($path) || ! preg_match('#^[A-Za-z0-9/_.-]+$#', $path)
                    || ! isset(\App\Services\InvitationRenderer::FONT_FILE_FORMATS[$ext])) {
                    $fail('Berkas font harus .woff2, .woff, .ttf, atau .otf dari pustaka aset.');
                }

                return;
            }

            $fail('Sumber font tidak dikenal.');
        };

        $hex = 'regex:/^#([0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/';
        $colorKeys = ['primary', 'accent', 'surface', 'surface_alt', 'text', 'muted', 'ink', 'on_dark'];

        $rules = [
            'colors' => 'required|array',
            'fonts' => 'required|array',
            'fonts.heading' => ['required', $fontRule],
            'fonts.body' => ['required', $fontRule],
            'scales' => 'required|array',
            'scales.type_base' => 'required|numeric|min:8|max:40',
            'scales.type_ratio' => 'required|numeric|min:1|max:2',
            'scales.radius' => 'required|numeric|min:0|max:64',
            'scales.section_spacing' => 'required|numeric|min:0|max:200',
            'scales.shadow_level' => ['required', \Illuminate\Validation\Rule::in(['none', 'sm', 'md', 'lg'])],
            'ornaments' => 'nullable|array',
            // Path aset lokal saja. Nilainya dipakai di url() dalam <style>, jadi kutip,
            // kurung, dan backslash ditolak di sini — bukan di-escape saat render.
            'ornaments.heading_rule' => ['nullable', 'string', 'regex:#^[A-Za-z0-9/_.-]+$#'],
            'ornaments.heading_rule_top' => ['nullable', 'string', 'regex:#^[A-Za-z0-9/_.-]+$#'],
            'ornaments.heading_rule_width' => 'nullable|numeric|min:10|max:100',
            'ornaments.heading_rule_top_width' => 'nullable|numeric|min:10|max:100',
            'ornaments.heading_rule_gap' => 'nullable|numeric|min:0|max:80',
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
                'ornaments' => $request->ornaments ?? [],
            ],
        ]);

        return response()->json(['success' => true, 'theme' => $template->theme]);
    }

    public function deleteSection($id)
    {
        $this->authorizeStudio();

        $section = InvitationSection::findOrFail($id);
        $section->delete();

        return response()->json(['success' => true, 'message' => 'Section deleted']);
    }

    public function reorderSections(Request $request)
    {
        $this->authorizeStudio();

        $classes = config('invitation_component_classes');

        $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:invitation_sections,id',
            'sections.*.order_index' => 'required|integer',
            'sections.*.parent_id' => 'nullable|exists:invitation_sections,id',
            'sections.*.column_index' => 'nullable|integer|min:0',
        ]);

        foreach ($request->sections as $i => $row) {
            if (!array_key_exists('parent_id', $row) || $row['parent_id'] === null) {
                continue;
            }
            $child = InvitationSection::find($row['id']);
            $parent = InvitationSection::find($row['parent_id']);

            if (!$child || !$parent
                || !in_array($child->section_type, $classes['basic'], true)
                || !in_array($parent->section_type, $classes['container'], true)
                || $parent->template_id !== $child->template_id) {
                throw ValidationException::withMessages([
                    "sections.{$i}.parent_id" => ['Induk harus Layout section pada template yang sama, dan anak harus blok dasar.'],
                ]);
            }

            $columns = $classes['container_columns'][$parent->section_type] ?? 0;
            if ((int) ($row['column_index'] ?? 0) >= $columns) {
                throw ValidationException::withMessages([
                    "sections.{$i}.column_index" => ['Kolom tidak tersedia pada container ini.'],
                ]);
            }
        }

        // Transaksi: validasi di atas sudah memastikan payload tidak setengah invalid,
        // tapi hanya transaksi yang menjamin kegagalan runtime di tengah loop (mis. query
        // gagal di baris ke-3) tidak menyisakan sebagian baris ter-update dan sebagian tidak.
        DB::transaction(function () use ($request) {
            foreach ($request->sections as $row) {
                $section = InvitationSection::find($row['id']);
                if (!$section) {
                    continue;
                }

                $attributes = ['order_index' => $row['order_index']];
                $parentId = $row['parent_id'] ?? null;

                if (array_key_exists('parent_id', $row)) {
                    $attributes['parent_id'] = $row['parent_id'];
                }

                if ($parentId !== null) {
                    // Reparenting selalu menulis column_index (default 0 bila key-nya tidak
                    // dikirim) — validasi di atas menganggap key hilang berarti 0, jadi
                    // penulisan harus konsisten, jangan sampai menyisakan column_index lama
                    // yang di luar jumlah kolom container baru.
                    $props = $section->props ?? [];
                    $props['column_index'] = (int) ($row['column_index'] ?? 0);
                    $attributes['props'] = $props;
                } elseif (array_key_exists('parent_id', $row) && $row['parent_id'] === null) {
                    // Naik jadi top-level: column_index lama sudah tidak bermakna, buang.
                    $props = $section->props ?? [];
                    unset($props['column_index']);
                    $attributes['props'] = $props;
                }

                $section->update($attributes);
            }
        });

        return response()->json(['success' => true]);
    }

    public function duplicateSection($id)
    {
        $this->authorizeStudio();

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
        $this->authorizeStudio();

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
                // Field wajib bersifat opt-in via `'required' => true` di skema. Default: opsional.
                // Hanya field yang benar-benar merusak render saat kosong (mis. image.src yang
                // menghasilkan <img src=""> rusak tanpa fallback) yang ditandai wajib. Field teks
                // kosong (heading/label) aman — komponen cukup tidak merender bagian itu.
                if (empty($field['required'])) {
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
        $this->authorizeStudio();

        $template = InvitationTemplate::with(['sections' => function ($query) {
            $query->orderBy('order_index');
        }])->findOrFail($id);

        $renderer = new \App\Services\InvitationRenderer();

        // Stub page (never persisted): invitations.public reads title/names/slug/
        // meta_data off $page, and the rsvp partial reads $page->slug ?? ''.
        $page = $renderer->previewStub($template);

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

    private function authorizeStudio(): void
    {
        $currentUser = \App\Models\User::find(\Illuminate\Support\Facades\Auth::id());

        if (!$currentUser || !$currentUser->canDesignTemplates()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
