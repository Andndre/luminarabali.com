<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioThemeTest extends TestCase
{
    use RefreshDatabase;

    private function template(User $admin): InvitationTemplate
    {
        return InvitationTemplate::create([
            'name' => 'T', 'slug' => 't-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
    }

    public function test_update_theme_persists_all_eight_colors_and_scales(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = $this->template($admin);
        $this->actingAs($admin);

        $payload = [
            'colors' => [
                'primary' => '#111111', 'accent' => '#222222', 'surface' => '#333333',
                'surface_alt' => '#444444', 'text' => '#555555', 'muted' => '#666666',
                'ink' => '#777777', 'on_dark' => '#888888',
            ],
            'fonts' => ['heading' => 'Playfair Display', 'body' => 'Lato'],
            'scales' => ['type_base' => 18, 'type_ratio' => 1.2, 'radius' => 8, 'section_spacing' => 72, 'shadow_level' => 'md'],
        ];

        $this->patchJson("/admin/api/studio/templates/{$template->id}/theme", $payload)->assertOk();

        $theme = $template->fresh()->theme;
        $this->assertSame('#777777', $theme['colors']['ink']);
        $this->assertSame(18, $theme['scales']['type_base']);
        $this->assertSame('md', $theme['scales']['shadow_level']);
    }

    public function test_update_theme_rejects_bad_scale_and_color(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = $this->template($admin);
        $this->actingAs($admin);

        $base = [
            'colors' => [
                'primary' => '#111111', 'accent' => '#222222', 'surface' => '#333333',
                'surface_alt' => '#444444', 'text' => '#555555', 'muted' => '#666666',
                'ink' => '#777777', 'on_dark' => '#888888',
            ],
            'fonts' => ['heading' => 'Playfair Display', 'body' => 'Lato'],
            'scales' => ['type_base' => 16, 'type_ratio' => 1.25, 'radius' => 12, 'section_spacing' => 64, 'shadow_level' => 'sm'],
        ];

        // shadow_level invalid
        $bad = $base; $bad['scales']['shadow_level'] = 'huge';
        $this->patchJson("/admin/api/studio/templates/{$template->id}/theme", $bad)->assertStatus(422);

        // warna invalid
        $bad2 = $base; $bad2['colors']['ink'] = 'notacolor';
        $this->patchJson("/admin/api/studio/templates/{$template->id}/theme", $bad2)->assertStatus(422);
    }
}
