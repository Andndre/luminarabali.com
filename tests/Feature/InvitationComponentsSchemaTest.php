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
        foreach (config('invitation_components') as $type => $fields) {
            foreach ($fields as $field) {
                if (($field['type'] ?? null) === 'color' && isset($field['token'])) {
                    $this->assertContains(
                        $field['token'],
                        ['primary', 'accent', 'surface', 'text'],
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
            'cover' => ['button_color' => 'accent', 'text_color' => 'surface'],
            'hero' => ['text_color' => 'surface'],
            'text' => ['color' => 'text'],
            'button' => ['background_color' => 'accent', 'text_color' => 'surface'],
            'countdown' => ['background_color' => 'surface', 'text_color' => 'text', 'accent_color' => 'accent'],
            'gallery' => ['background_color' => 'surface'],
            'rsvp' => ['button_color' => 'accent', 'background_color' => 'surface'],
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
}
