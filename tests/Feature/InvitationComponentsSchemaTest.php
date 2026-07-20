<?php

namespace Tests\Feature;

use Tests\TestCase;

class InvitationComponentsSchemaTest extends TestCase
{
    public function test_every_field_has_a_valid_group(): void
    {
        foreach (config('invitation_components') as $type => $fields) {
            foreach ($fields as $field) {
                $this->assertContains(
                    $field['group'] ?? null,
                    ['content', 'design', 'advanced'],
                    "{$type}.{$field['key']} punya group tidak dikenal."
                );
            }
        }
    }

    public function test_color_field_tokens_reference_real_theme_tokens(): void
    {
        // Whitelist = kunci warna kanonik di default_theme (satu sumber kebenaran).
        $canonical = array_keys(config('invitation.default_theme.colors'));

        foreach (config('invitation_components') as $type => $fields) {
            foreach ($fields as $field) {
                if (($field['type'] ?? null) === 'color' && isset($field['token'])) {
                    $this->assertContains(
                        $field['token'],
                        $canonical,
                        "{$type}.{$field['key']} token tidak valid."
                    );
                }
            }
        }
    }

    public function test_element_id_and_custom_css_live_in_the_advanced_group(): void
    {
        foreach (config('invitation_components') as $type => $fields) {
            foreach ($fields as $field) {
                if (in_array($field['key'], ['element_id', 'custom_css'], true)) {
                    $this->assertEquals('advanced', $field['group'], "{$type}.{$field['key']}");
                }
            }
        }
    }

    public function test_token_wired_color_fields_are_declared(): void
    {
        // Peta hasil audit partial (var(--color-X) yang benar-benar dikonsumsi).
        $expected = [
            'section_one_col' => ['background_color' => 'surface'],
            'section_two_col' => ['background_color' => 'surface'],
            'section_three_col' => ['background_color' => 'surface'],
        ];

        foreach ($expected as $type => $tokens) {
            $fields = collect(config("invitation_components.{$type}"));
            foreach ($tokens as $key => $token) {
                $this->assertEquals(
                    $token,
                    $fields->firstWhere('key', $key)['token'] ?? null,
                    "{$type}.{$key} harus punya token '{$token}'."
                );
            }
        }
    }

    public function test_variant_scoped_fields_reference_a_real_variant_option(): void
    {
        foreach (config('invitation_components') as $type => $fields) {
            $variantField = collect($fields)->firstWhere('key', 'variant');
            $validVariants = $variantField['options'] ?? [];

            foreach ($fields as $field) {
                if (!isset($field['variant'])) {
                    continue;
                }
                $this->assertIsArray($field['variant'], "{$type}.{$field['key']} atribut variant harus array.");
                $this->assertNotEmpty($validVariants, "{$type}.{$field['key']} punya atribut variant tapi {$type} tak punya field 'variant'.");
                foreach ($field['variant'] as $v) {
                    $this->assertContains($v, $validVariants,
                        "{$type}.{$field['key']} merujuk varian '{$v}' yang tak ada di options field variant {$type}.");
                }
            }
        }
    }

    public function test_couple_align_fields_are_scoped_to_portrait_overlay_without_label_hack(): void
    {
        $fields = collect(config('invitation_components.couple'));
        foreach (['groom_text_align', 'bride_text_align'] as $key) {
            $field = $fields->firstWhere('key', $key);
            $this->assertNotNull($field, "couple.{$key} hilang.");
            $this->assertSame(['portrait-overlay'], $field['variant'] ?? null,
                "couple.{$key} harus di-scope ke portrait-overlay lewat atribut variant.");
            $this->assertStringNotContainsStringIgnoringCase('varian', $field['label'],
                "couple.{$key} label tak boleh lagi pakai hack '(varian …)'.");
        }
    }

    public function test_layout_variant_fields_use_the_variant_field_type(): void
    {
        $layoutTypes = ['cover', 'hero', 'couple', 'countdown', 'gallery', 'rsvp', 'event_details'];
        foreach ($layoutTypes as $type) {
            $field = collect(config("invitation_components.{$type}"))->firstWhere('key', 'variant');
            $this->assertNotNull($field, "{$type} tak punya field variant.");
            $this->assertSame('variant', $field['type'] ?? null,
                "{$type}.variant harus type 'variant' (selektor layout), bukan '{$field['type']}'.");
            $this->assertNotEmpty($field['options'] ?? [], "{$type}.variant wajib punya options.");
        }
    }

    public function test_button_variant_uses_the_schematic_picker(): void
    {
        $field = collect(config('invitation_components.button'))->firstWhere('key', 'variant');
        $this->assertSame('variant', $field['type'] ?? null,
            'button.variant memakai picker skematik — bentuk tombol perlu terlihat sebelum dipilih.');

        // Peta skematik di Studio dipakai bersama SEMUA komponen, jadi nama varian tombol
        // tidak boleh menabrak nama yang sudah dipakai komponen lain.
        $taken = [];
        foreach (config('invitation_components') as $type => $fields) {
            if ($type === 'button') {
                continue;
            }
            foreach ($fields as $f) {
                if (($f['type'] ?? null) === 'variant') {
                    $taken = array_merge($taken, $f['options']);
                }
            }
        }
        foreach ($field['options'] as $opt) {
            $this->assertNotContains($opt, $taken,
                "Varian tombol '{$opt}' menabrak nama varian komponen lain di map skematik.");
        }
    }

    public function test_legacy_button_variant_names_still_render(): void
    {
        $blade = file_get_contents(resource_path('views/templates/components/button.blade.php'));
        foreach (['primary', 'secondary', 'ghost'] as $legacy) {
            $this->assertStringContainsString("'{$legacy}' =>", $blade,
                "Nama varian lama '{$legacy}' masih tersimpan di baris section — harus tetap dipetakan.");
        }
    }

    public function test_every_layout_variant_option_has_a_schematic_entry_in_studio(): void
    {
        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));
        $this->assertStringContainsString('variantSchematic', $blade, 'Helper variantSchematic belum ada.');

        // Picker varian = skematik SVG saja (tak ada jalur thumbnail hasil capture).
        $inspector = file_get_contents(resource_path('views/admin/templates/studio/_field.blade.php'));
        $this->assertStringContainsString('variantSchematic(opt)', $inspector, 'Picker varian tak memakai skematik.');
        $this->assertStringNotContainsString('variant_thumbnails', $inspector, 'Picker varian masih punya jalur thumbnail.');

        foreach (config('invitation_components') as $type => $fields) {
            foreach ($fields as $field) {
                if (($field['type'] ?? null) !== 'variant') {
                    continue;
                }
                foreach ($field['options'] as $opt) {
                    $this->assertStringContainsString("'{$opt}':", $blade,
                        "Varian '{$opt}' ({$type}) tak punya entri di map variantSchematic.");
                }
            }
        }
    }

    public function test_image_and_video_expose_per_corner_radius_behind_a_toggle(): void
    {
        foreach (['image', 'video'] as $type) {
            $fields = collect(config("invitation_components.{$type}"))->keyBy('key');

            $this->assertSame('boolean', $fields['radius_per_corner']['type'] ?? null,
                "{$type} tak punya centang radius per pojok.");

            // Tanpa show_if, satu radius dan empat pojok tampil bersamaan di inspector dan
            // tidak ada yang tahu mana yang sebenarnya berlaku.
            $this->assertSame(['radius_per_corner', false], $fields['border_radius']['show_if'] ?? null);
            foreach (['radius_tl', 'radius_tr', 'radius_br', 'radius_bl'] as $corner) {
                $this->assertSame(['radius_per_corner', true], $fields[$corner]['show_if'] ?? null,
                    "{$type}.{$corner} harus tersembunyi selama centangnya mati.");
            }
        }

        $this->assertStringContainsString('showField(field)',
            file_get_contents(resource_path('views/admin/templates/studio/_field.blade.php')),
            'Inspector tidak menghormati show_if.');
    }

    /**
     * Field latar tidak berpengaruh apa-apa saat treatment bukan image, dan tiap jenis
     * media hanya memakai field miliknya sendiri. Tanpa gerbang ini inspector memajang
     * "Foto Latar" di samping "Foto Slideshow" dan tak ada yang tahu mana yang berlaku.
     */
    public function test_background_media_fields_are_gated_by_treatment_and_media_type(): void
    {
        $fields = collect(config('invitation_components.hero'))->keyBy('key');
        $bgOn = ['treatment', 'image'];

        $this->assertSame($bgOn, $fields['bg_media_type']['show_if'] ?? null);
        $this->assertSame($bgOn, $fields['bg_overlay']['show_if'] ?? null);

        // Tiap jenis media punya kolomnya sendiri — termasuk poster video, yang perannya
        // beda dari foto latar dan karena itu tidak menumpang bg_image.
        $this->assertSame([$bgOn, ['bg_media_type', 'image']], $fields['bg_image']['show_if'] ?? null);
        $this->assertSame([$bgOn, ['bg_media_type', 'slideshow']], $fields['bg_images']['show_if'] ?? null);
        $this->assertSame([$bgOn, ['bg_media_type', 'video']], $fields['bg_poster']['show_if'] ?? null);
        // Satu kolom video untuk dua sumber: unggahan dan tautan YouTube dibedakan dari
        // isinya saat render, bukan lewat saklar yang harus diingat pemakai.
        $this->assertSame([$bgOn, ['bg_media_type', 'video']], $fields['bg_video']['show_if'] ?? null);
        $this->assertArrayNotHasKey('bg_video_source', $fields->all());
        $this->assertArrayNotHasKey('bg_video_url', $fields->all());

        // Efek berlaku untuk ketiga jenis media, jadi gerbangnya cuma treatment.
        foreach (['bg_effect', 'bg_effect_strength'] as $key) {
            $this->assertSame($bgOn, $fields[$key]['show_if'] ?? null);
        }

        // Gerbang majemuk dan daftar nilai butuh dukungan di showField, bukan cuma di config.
        $studio = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));
        $this->assertStringContainsString('Array.isArray(field.show_if[0])', $studio);
        $this->assertStringContainsString('want.includes(cur)', $studio);
        // Prop yang belum tersimpan harus dibaca sebagai default skemanya.
        $this->assertStringContainsString('schemaDefault(key)', $studio);
    }

    public function test_code_editor_escapes_before_highlighting(): void
    {
        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));

        $this->assertStringContainsString('highlightHtml(src)', $blade, 'Helper highlightHtml belum ada.');

        // Hasil highlightHtml masuk x-html. Kalau escape-nya hilang, HTML yang sedang
        // diketik super admin dieksekusi di halaman Studio, bukan sekadar diwarnai.
        $body = substr($blade, strpos($blade, 'highlightHtml(src)'));
        $body = substr($body, 0, strpos($body, 'codeWarnings('));
        foreach (["replace(/&/g, '&amp;')", "replace(/</g, '&lt;')", "replace(/>/g, '&gt;')"] as $needle) {
            $this->assertStringContainsString($needle, $body, "highlightHtml tidak meng-escape: {$needle}");
        }
        $this->assertLessThan(
            strpos($body, '<span class="tok-'),
            strpos($body, "replace(/</g, '&lt;')"),
            'Escape harus terjadi sebelum span pewarnaan disisipkan.'
        );
    }

    public function test_public_page_has_no_unlayered_universal_reset(): void
    {
        // Komentar dibuang dulu: komentar peringatan di file itu mengutip reset yang
        // dilarang, jadi penjaganya akan menangkap dirinya sendiri.
        $blade = preg_replace('#/\*.*?\*/|\{\{--.*?--\}\}#s', '', file_get_contents(
            resource_path('views/invitations/public.blade.php')
        ));

        // CSS tanpa layer mengalahkan semua @layer, jadi `* { padding: 0 }` di sini
        // mematikan setiap utility padding/margin Tailwind tanpa error apa pun.
        $this->assertDoesNotMatchRegularExpression(
            '/\*\s*\{[^}]*\b(padding|margin)\s*:/s',
            $blade,
            'Reset universal padding/margin di <style> halaman undangan mematikan semua utility Tailwind.'
        );
    }

    public function test_bulk_design_fields_are_grouped_into_collapsible_panels(): void
    {
        // Field yang di-merge ke banyak komponen sekaligus adalah sumber daftar panjang
        // di tab Desain (guideline §10). Semuanya wajib punya `panel` supaya masuk
        // accordion, bukan menumpuk datar di atas.
        // bg_image sengaja TIDAK di sini: fotonya pindah ke tab Konten (isi yang sering
        // diganti), jadi ia bukan lagi sumber daftar panjang di tab Desain.
        $mustHavePanel = [
            'treatment', 'bg_overlay', 'bg_effect', 'bg_effect_strength',
            'ornaments_top', 'ornaments_bottom',
            'animation', 'animation_delay',
            'padding_top', 'padding_bottom', 'margin_top', 'margin_bottom',
            'radius_per_corner', 'radius_tl',
        ];

        foreach (config('invitation_components') as $type => $fields) {
            foreach ($fields as $field) {
                if (in_array($field['key'], $mustHavePanel, true)) {
                    $this->assertNotEmpty(
                        $field['panel'] ?? null,
                        "{$type}.{$field['key']} tidak punya 'panel' — akan muncul datar di tab Desain."
                    );
                }
            }
        }
    }

    public function test_inspector_renders_panels_through_the_shared_field_partial(): void
    {
        $inspector = file_get_contents(resource_path('views/admin/templates/studio/_inspector.blade.php'));

        // Dua loop (tanpa panel + per panel) harus memakai partial yang sama; markup
        // field ~400 baris tak boleh digandakan.
        $this->assertSame(2, substr_count($inspector, "@include('admin.templates.studio._field')"));
        $this->assertStringContainsString('<details', $inspector, 'Panel colaps belum memakai <details>.');
        $this->assertStringContainsString('panelsFor(', $inspector);

        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));
        $this->assertStringContainsString('panelLabel(', $blade, 'Helper panelLabel belum ada.');
    }

    public function test_studio_inserts_new_sections_next_to_the_selected_one(): void
    {
        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));

        // Menambah section dulu selalu menempel di ujung daftar; di template belasan
        // section itu berarti tambah lalu seret belasan baris ke atas.
        $this->assertStringContainsString('insertAfterIndex()', $blade, 'Helper posisi sisip belum ada.');
        $this->assertStringContainsString('this.sections.splice(at + 1, 0, created)', $blade,
            'Section baru tidak disisipkan setelah yang terpilih.');
        $this->assertStringContainsString('saveOrder()', $blade, 'Urutan hasil sisip tidak dipersist.');

        // Memilih section di panel kiri harus membawa kanvas ke sana — kecuali
        // pilihannya justru datang dari klik di kanvas.
        $this->assertStringContainsString('scrollPreviewTo(', $blade, 'Preview tidak digulirkan ke section terpilih.');

        // _section-shell membungkus section polos dengan display:contents. Elemen itu
        // tak punya kotak, jadi scrollIntoView() di atasnya diam saja.
        $this->assertStringContainsString('getClientRects().length', $blade,
            'Pembungkus display:contents tidak ditangani — section polos tak akan tergulir.');
        $this->assertStringContainsString('this.scrollOnSelect = false', $blade,
            'Klik dari kanvas harus menekan auto-scroll, kalau tidak tampilannya menyentak.');
    }

    public function test_studio_offers_to_restore_a_deleted_section(): void
    {
        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));

        $this->assertStringContainsString('offerRestore(', $blade, 'Tawaran urungkan belum ada.');
        $this->assertStringContainsString('restoreSection(', $blade);

        // Menghapus container ikut menghapus anaknya — pemulihan harus membawa
        // mereka kembali, bukan hanya kotak kosongnya.
        $this->assertStringContainsString('for (const kid of backup.children)', $blade,
            'Blok anak tidak ikut dipulihkan.');

        // custom_css, is_visible, dan _locked tidak lewat endpoint create.
        $this->assertStringContainsString('patch.custom_css', $blade);
        $this->assertStringContainsString('patch.locked = locked', $blade,
            'Gembok per-field hilang saat restore — _locked di-strip dari jalur props.');

        // Blok anak harus kembali ke kolom asalnya. Tanpa induk+kolom di backup, ia
        // dipulihkan sebagai section top-level di ujung daftar.
        $this->assertStringContainsString('backup.parentId', $blade, 'Induk tidak direkam saat hapus.');
        $this->assertStringContainsString('saveColumnOrder(', $blade,
            'Posisi blok anak di dalam kolom tidak dipulihkan.');
    }

    public function test_studio_drag_crosses_between_top_level_and_columns(): void
    {
        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));

        // Satu grup Sortable = daftar atas dan kolom saling menerima.
        $this->assertSame(2, substr_count($blade, "name: 'studio'"),
            'Kedua daftar harus berada di grup Sortable yang sama.');

        // Server menolak container/feature sebagai anak; tolak juga saat menyeret supaya
        // penolakannya terasa sebelum dilepas, bukan sesudahnya.
        $this->assertStringContainsString("=== 'basic'", $blade, 'Aturan siapa boleh masuk kolom belum ada.');

        // Satu drag mengubah kedua sisi, jadi urutannya dikirim sekali jalan.
        $this->assertStringContainsString('persistStructure()', $blade);
        $this->assertStringNotContainsString('this.persistColumnOrder()', $blade,
            'Masih ada jalur persist terpisah — separuh struktur akan tertinggal.');
    }

    public function test_studio_moves_selection_with_arrow_keys(): void
    {
        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));

        $this->assertStringContainsString('moveSelection(', $blade);
        $this->assertStringContainsString('navigableIds()', $blade);

        // Panah punya arti sendiri di kontrol form; jangan dibajak di sana.
        $this->assertStringContainsString("['INPUT', 'TEXTAREA', 'SELECT'].includes(t.tagName)", $blade,
            'Navigasi panah harus mundur saat fokus di kontrol form.');
        $this->assertStringContainsString('scrollRowIntoView(', $blade,
            'Baris terpilih harus ikut terlihat di panel kiri.');
    }

    public function test_studio_chrome_colors_come_only_from_the_ui_palette(): void
    {
        $layout = file_get_contents(resource_path('views/layouts/studio.blade.php'));

        $views = [
            'views/admin/templates/studio.blade.php',
            'views/admin/templates/studio/_inspector.blade.php',
            'views/admin/templates/studio/_field.blade.php',
        ];

        foreach ($views as $view) {
            $blade = file_get_contents(resource_path($view));

            // Skala abu Tailwind yang dipatok akan tampil sebagai bercak terang di panel
            // gelap. Warna chrome harus lewat --ui-*, supaya menyetel ulang skema cukup
            // satu blok di layouts/studio.blade.php.
            $this->assertDoesNotMatchRegularExpression(
                '/\b(?:bg|text|border|ring|divide)-(?:white|gray-\d+)\b/',
                $blade,
                "{$view} memakai warna abu/putih yang dipatok, bukan token --ui-*."
            );

            // Tiap var yang dipakai harus benar-benar ada di palet — typo di nama var
            // gagal diam-diam jadi transparan.
            preg_match_all('/--ui-[a-z0-9-]+/', $blade, $m);
            foreach (array_unique($m[0]) as $var) {
                $this->assertStringContainsString("{$var}:", $layout, "{$var} dipakai {$view} tapi tak ada di palet.");
            }
        }
    }

    public function test_public_page_body_font_follows_the_theme(): void
    {
        $blade = file_get_contents(resource_path('views/invitations/public.blade.php'));

        // $themeStyle mengeluarkan --font-body dari fonts.body template. Kalau body
        // mematok nama font langsung, seluruh teks isi mengabaikan pilihan tema.
        $this->assertMatchesRegularExpression(
            '/body\s*\{[^}]*font-family:\s*var\(--font-body/s',
            $blade,
            'body harus memakai var(--font-body, …), bukan nama font tetap.'
        );
    }

    public function test_color_fields_use_the_in_app_picker_not_native_os_dialog(): void
    {
        $studio = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));
        $field = file_get_contents(resource_path('views/admin/templates/studio/_field.blade.php'));
        $picker = file_get_contents(resource_path('views/admin/templates/studio/_color-picker.blade.php'));

        // Picker in-app punya method HSV & pemicunya. Tak boleh ada lagi <input type="color">
        // (dialog OS) di Studio; Panel Tema & inspector memicu picker yang sama.
        $this->assertStringContainsString('openColorPicker', $studio, 'Method openColorPicker belum ada.');
        $this->assertStringContainsString('cpHsv2rgb', $studio, 'Math HSV picker belum ada.');
        $this->assertStringContainsString('cpick-sv', $picker, 'Area saturasi/nilai picker belum ada.');
        $this->assertStringContainsString('openColorPicker', $studio, 'Panel Tema belum memicu picker.');
        $this->assertStringContainsString('openColorPicker', $field, 'Inspector belum memicu picker.');
        $this->assertStringNotContainsString('type="color"', $studio, 'Panel Tema masih pakai dialog warna OS.');
        $this->assertStringNotContainsString('type="color"', $field, 'Inspector masih pakai dialog warna OS.');
    }

    public function test_number_fields_use_a_custom_stepper_not_native_spinner(): void
    {
        $studio = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));
        $field = file_get_contents(resource_path('views/admin/templates/studio/_field.blade.php'));
        $stepper = file_get_contents(resource_path('views/admin/templates/studio/_stepper.blade.php'));

        $this->assertStringContainsString('nudge($event', $stepper, 'Tombol stepper belum wired ke nudge().');
        $this->assertStringContainsString('nudge(e, dir)', $studio, 'Method nudge() belum ada.');
        // Panel Tema scales & inspector number memakai partial stepper.
        $this->assertStringContainsString('_stepper', $studio, 'Panel Tema belum pakai stepper.');
        $this->assertStringContainsString('_stepper', $field, 'Inspector number belum pakai stepper.');
    }

    public function test_theme_panel_has_scales_editor(): void
    {
        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));
        $this->assertStringContainsString('setScale', $blade, 'Method setScale belum ada.');
        foreach (['type_base', 'type_ratio', 'radius', 'section_spacing', 'shadow_level'] as $k) {
            $this->assertStringContainsString($k, $blade, "Scale '{$k}' tak ada di Panel Tema.");
        }
    }

    public function test_ornament_field_supports_uploading_to_collection(): void
    {
        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));
        $this->assertStringContainsString('uploadOrnament', $blade, 'Method uploadOrnament belum ada.');
        $this->assertStringContainsString("collection', 'ornament'", $blade, 'Upload ornamen tak set collection=ornament.');
    }

    public function test_studio_has_ornament_picker_modal_and_svg_color_control(): void
    {
        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));
        $inspector = file_get_contents(resource_path('views/admin/templates/studio/_field.blade.php'));
        $this->assertStringContainsString('openOrnamentPicker', $blade, 'Method openOrnamentPicker belum ada.');
        $this->assertStringContainsString('ornamentPicker', $blade, 'State modal ornamen belum ada.');
        $this->assertStringContainsString('isSvgPath', $blade, 'Helper isSvgPath belum ada.');
        $this->assertStringContainsString('openOrnamentPicker', $inspector, 'Tombol pilih ornamen belum wired ke modal.');
        $this->assertStringContainsString("setOrnItem(field, i, 'color'", $inspector, 'Kontrol warna svg per item belum ada.');
    }

    public function test_studio_has_ornament_list_ui_with_flip(): void
    {
        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));
        $inspector = file_get_contents(resource_path('views/admin/templates/studio/_field.blade.php'));
        $this->assertStringContainsString('openOrnamentPickerItem', $blade, 'Picker per-item belum ada.');
        $this->assertStringContainsString('addOrnItem', $blade, 'Helper addOrnItem belum ada.');
        $this->assertStringContainsString("field.type === 'ornament_list'", $inspector, 'Blok ornament_list belum ada.');
        $this->assertStringContainsString('flip_h', $inspector, 'Toggle flip belum ada di inspector.');
    }

    public function test_studio_gates_advanced_tooling_behind_a_toggle(): void
    {
        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));
        $this->assertStringContainsString('toggleAdvanced', $blade, 'Toggle Mode Lanjutan belum ada.');
        $this->assertStringContainsString("localStorage.getItem('luminara.studio.advanced')", $blade,
            'Mode Lanjutan tak dipersist.');
        $this->assertStringContainsString('curatedTypes', $blade, 'Palet Kurasi belum dipisah.');
        $this->assertStringContainsString('advancedTypes', $blade, 'Palet Lanjutan belum dipisah.');
    }

    public function test_studio_has_a_preview_as_customer_mode(): void
    {
        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));
        $this->assertStringContainsString('toggleAsCustomer', $blade, 'Toggle Preview-as-Customer belum ada.');
        // String ini cuma muncul di dalam cabang customer getter availableTabs (array
        // satu-tab yang dikembalikan), bukan di toggleAsCustomer() — jadi kalau cabang
        // gating-nya dihapus dari availableTabs, assertion ini ikut gagal.
        $this->assertStringContainsString("[{ id: 'content', label: 'Konten' }]", $blade,
            'availableTabs belum dikunci ke tab Konten saat mode customer.');
    }
}
