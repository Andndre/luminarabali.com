<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioRenderSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_a_single_section_fragment_without_persisting_anything(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $this->actingAs($admin);

        $response = $this->postJson('/admin/api/studio/render-section', [
            'section_type' => 'text',
            'props' => ['content' => 'Hello Studio', 'tag' => 'h2'],
        ]);

        $response->assertOk();
        $this->assertStringContainsString('Hello Studio', $response->json('html'));
        $this->assertStringContainsString('<h2', $response->json('html'));
        $this->assertDatabaseCount('invitation_sections', 0);
    }

    public function test_uses_section_id_for_css_scoping_when_provided(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $this->actingAs($admin);

        $response = $this->postJson('/admin/api/studio/render-section', [
            'section_type' => 'text',
            'props' => ['content' => 'Scoped'],
            'section_id' => 999,
        ]);

        $response->assertOk();
        $this->assertStringContainsString('text-block-999', $response->json('html'));
    }

    public function test_rejects_invalid_props(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $this->actingAs($admin);

        $this->postJson('/admin/api/studio/render-section', [
            'section_type' => 'text',
            'props' => ['align' => 'diagonally'],
        ])->assertStatus(422);
    }

    public function test_rejects_an_unknown_section_type(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $this->actingAs($admin);

        $this->postJson('/admin/api/studio/render-section', [
            'section_type' => 'not_a_real_type',
            'props' => [],
        ])->assertStatus(422);
    }

    public function test_non_admin_cannot_use_the_endpoint(): void
    {
        $staff = User::factory()->create(['division' => 'photobooth']);
        $this->actingAs($staff);

        $this->postJson('/admin/api/studio/render-section', [
            'section_type' => 'text',
            'props' => ['content' => 'x'],
        ])->assertForbidden();
    }
}
