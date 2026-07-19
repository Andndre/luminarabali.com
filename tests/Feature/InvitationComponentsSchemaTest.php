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

    public function test_button_variant_stays_a_plain_select_not_a_layout_variant(): void
    {
        $field = collect(config('invitation_components.button'))->firstWhere('key', 'variant');
        $this->assertSame('select', $field['type'] ?? null,
            'button.variant = gaya tombol, harus tetap type select (bukan picker layout).');
    }

    public function test_every_layout_variant_option_has_a_schematic_entry_in_studio(): void
    {
        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));
        $this->assertStringContainsString('variantSchematic', $blade, 'Helper variantSchematic belum ada.');

        // Picker varian = skematik SVG saja (tak ada jalur thumbnail hasil capture).
        $inspector = file_get_contents(resource_path('views/admin/templates/studio/_inspector.blade.php'));
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

    public function test_theme_panel_uses_custom_hex_color_control(): void
    {
        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));
        $this->assertStringContainsString('normalizeHex', $blade, 'Helper normalizeHex belum ada.');
        // Panel Tema warna harus punya input teks hex (maxlength 9), bukan hanya native color.
        $this->assertStringContainsString('theme-hex-input', $blade, 'Kontrol hex kustom Panel Tema belum ada.');
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
        $inspector = file_get_contents(resource_path('views/admin/templates/studio/_inspector.blade.php'));
        $this->assertStringContainsString('openOrnamentPicker', $blade, 'Method openOrnamentPicker belum ada.');
        $this->assertStringContainsString('ornamentPicker', $blade, 'State modal ornamen belum ada.');
        $this->assertStringContainsString('isSvgPath', $blade, 'Helper isSvgPath belum ada.');
        $this->assertStringContainsString('openOrnamentPicker', $inspector, 'Tombol pilih ornamen belum wired ke modal.');
        $this->assertStringContainsString("setOrnItem(field, i, 'color'", $inspector, 'Kontrol warna svg per item belum ada.');
    }

    public function test_studio_has_ornament_list_ui_with_flip(): void
    {
        $blade = file_get_contents(resource_path('views/admin/templates/studio.blade.php'));
        $inspector = file_get_contents(resource_path('views/admin/templates/studio/_inspector.blade.php'));
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
