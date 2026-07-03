<?php

namespace Tests\Unit;

use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTemplateThemeCastTest extends TestCase
{
    use RefreshDatabase;

    public function test_theme_is_cast_to_array_and_round_trips(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);

        $template = InvitationTemplate::create([
            'name' => 'Rustic Garden',
            'slug' => 'rustic-garden',
            'status' => 'published',
            'created_by' => $admin->id,
            'theme' => [
                'colors' => ['primary' => '#3b2f2f', 'accent' => '#b5654d'],
                'fonts' => ['heading' => 'Playfair Display', 'body' => 'Lora'],
            ],
        ]);

        $fresh = InvitationTemplate::find($template->id);

        $this->assertIsArray($fresh->theme);
        $this->assertSame('#3b2f2f', $fresh->theme['colors']['primary']);
        $this->assertSame('Playfair Display', $fresh->theme['fonts']['heading']);
    }

    public function test_theme_defaults_to_null_when_not_set(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);

        $template = InvitationTemplate::create([
            'name' => 'No Theme', 'slug' => 'no-theme', 'status' => 'published', 'created_by' => $admin->id,
        ]);

        $this->assertNull($template->fresh()->theme);
    }
}
