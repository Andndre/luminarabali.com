<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioThemeUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_a_templates_theme(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        $response = $this->patchJson("/admin/api/studio/templates/{$template->id}/theme", [
            'colors' => [
                'primary' => '#4a5d43', 'accent' => '#93a686', 'surface' => '#f6f8f3',
                'surface_alt' => '#e9ede4', 'text' => '#2c332a', 'muted' => '#6b7565',
                'ink' => '#1c221a', 'on_dark' => '#f5f7f2',
            ],
            'fonts' => ['heading' => 'Lora', 'body' => 'Open Sans'],
            'scales' => ['type_base' => 16, 'type_ratio' => 1.25, 'radius' => 12, 'section_spacing' => 64, 'shadow_level' => 'sm'],
        ]);

        $response->assertOk();
        $this->assertEquals('#4a5d43', $template->fresh()->theme['colors']['primary']);
        $this->assertEquals('Lora', $template->fresh()->theme['fonts']['heading']);
    }

    public function test_rejects_a_non_curated_font(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        $this->patchJson("/admin/api/studio/templates/{$template->id}/theme", [
            'colors' => ['primary' => '#4a5d43', 'accent' => '#93a686', 'surface' => '#f6f8f3', 'text' => '#2c332a'],
            'fonts' => ['heading' => 'Comic Sans MS', 'body' => 'Open Sans'],
        ])->assertStatus(422);
    }

    public function test_accepts_google_and_uploaded_fonts(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        $this->patchJson("/admin/api/studio/templates/{$template->id}/theme", [
            'colors' => [
                'primary' => '#4a5d43', 'accent' => '#93a686', 'surface' => '#f6f8f3',
                'surface_alt' => '#e9ede4', 'text' => '#2c332a', 'muted' => '#6b7565',
                'ink' => '#1c221a', 'on_dark' => '#f5f7f2',
            ],
            'fonts' => [
                'heading' => ['source' => 'google', 'family' => 'Cormorant Garamond'],
                'body' => ['source' => 'upload', 'family' => 'Gentium Plus', 'path' => 'invitations/gentium.woff2'],
            ],
            'scales' => ['type_base' => 16, 'type_ratio' => 1.25, 'radius' => 12, 'section_spacing' => 64, 'shadow_level' => 'sm'],
        ])->assertOk();

        $this->assertEquals('Cormorant Garamond', $template->fresh()->theme['fonts']['heading']['family']);
    }

    public function test_rejects_font_family_and_file_that_are_unsafe_to_render(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        $base = ['colors' => ['primary' => '#4a5d43', 'accent' => '#93a686', 'surface' => '#f6f8f3', 'text' => '#2c332a']];

        // Nama keluarga yang bisa keluar dari aturan CSS.
        $this->patchJson("/admin/api/studio/templates/{$template->id}/theme", $base + [
            'fonts' => ['heading' => ['source' => 'google', 'family' => "X'; } body { display:none } .y{a:'b"], 'body' => 'Open Sans'],
        ])->assertStatus(422);

        // Berkas di luar daftar format font.
        $this->patchJson("/admin/api/studio/templates/{$template->id}/theme", $base + [
            'fonts' => ['heading' => ['source' => 'upload', 'family' => 'Sneaky', 'path' => 'invitations/x.svg'], 'body' => 'Open Sans'],
        ])->assertStatus(422);

        // Sumber yang tidak dikenal.
        $this->patchJson("/admin/api/studio/templates/{$template->id}/theme", $base + [
            'fonts' => ['heading' => ['source' => 'remote', 'family' => 'Whatever'], 'body' => 'Open Sans'],
        ])->assertStatus(422);
    }

    public function test_rejects_a_malformed_hex_color(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        $this->patchJson("/admin/api/studio/templates/{$template->id}/theme", [
            'colors' => ['primary' => 'not-a-color', 'accent' => '#93a686', 'surface' => '#f6f8f3', 'text' => '#2c332a'],
            'fonts' => ['heading' => 'Lora', 'body' => 'Open Sans'],
        ])->assertStatus(422);
    }

    public function test_non_admin_cannot_update_theme(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $staff = User::factory()->create(['division' => 'photobooth']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);

        $this->actingAs($staff);

        $this->patchJson("/admin/api/studio/templates/{$template->id}/theme", [
            'colors' => ['primary' => '#4a5d43'], 'fonts' => ['heading' => 'Lora'],
        ])->assertForbidden();
    }
}
