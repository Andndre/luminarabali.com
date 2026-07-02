<?php

namespace Tests\Unit;

use Tests\TestCase;

class InvitationConfigTest extends TestCase
{
    public function test_default_theme_has_required_color_and_font_keys(): void
    {
        $theme = config('invitation.default_theme');

        $this->assertArrayHasKey('primary', $theme['colors']);
        $this->assertArrayHasKey('accent', $theme['colors']);
        $this->assertArrayHasKey('surface', $theme['colors']);
        $this->assertArrayHasKey('text', $theme['colors']);
        $this->assertArrayHasKey('heading', $theme['fonts']);
        $this->assertArrayHasKey('body', $theme['fonts']);
    }

    public function test_default_theme_fonts_are_in_the_curated_list(): void
    {
        $curatedNames = collect(config('invitation.fonts'))->pluck('name')->all();
        $theme = config('invitation.default_theme');

        $this->assertContains($theme['fonts']['heading'], $curatedNames);
        $this->assertContains($theme['fonts']['body'], $curatedNames);
    }

    public function test_every_curated_font_has_a_name_and_a_url(): void
    {
        foreach (config('invitation.fonts') as $font) {
            $this->assertNotEmpty($font['name']);
            $this->assertStringStartsWith('https://fonts.googleapis.com/', $font['url']);
        }
    }
}
