<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionPropsValidationEndpointTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['division' => 'super_admin']);
    }

    public function test_bulk_save_rejects_invalid_props_for_a_section(): void
    {
        $admin = $this->superAdmin();
        $page = InvitationPage::create([
            'title' => 'A & B', 'slug' => 'a-and-b-'.uniqid(),
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->postJson('/admin/api/sections', [
            'page_id' => $page->id,
            'global_custom_css' => '',
            'sections' => [
                [
                    'id' => 'temp-1', 'parent_id' => null, 'section_type' => 'section_one_col',
                    'order_index' => 0, 'props' => ['background_color' => 'not-a-hex-color'],
                ],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['props.background_color']);
    }

    public function test_bulk_save_strips_unknown_props_keys(): void
    {
        $admin = $this->superAdmin();
        $page = InvitationPage::create([
            'title' => 'A & B', 'slug' => 'a-and-b-'.uniqid(),
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->post('/admin/api/sections', [
            'page_id' => $page->id,
            'global_custom_css' => '',
            'sections' => [
                [
                    'id' => 'temp-1', 'parent_id' => null, 'section_type' => 'text',
                    'order_index' => 0, 'props' => ['content' => 'Hello', 'not_real' => 'x'],
                ],
            ],
        ])->assertRedirect();

        $section = InvitationSection::where('page_id', $page->id)->first();
        $this->assertArrayHasKey('content', $section->props);
        $this->assertArrayNotHasKey('not_real', $section->props);
    }

    public function test_single_section_update_rejects_invalid_props(): void
    {
        $admin = $this->superAdmin();
        $page = InvitationPage::create([
            'title' => 'A & B', 'slug' => 'a-and-b-'.uniqid(),
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $admin->id,
        ]);
        $section = InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'gallery', 'order_index' => 0,
            'props' => ['columns' => 3], 'is_visible' => true,
        ]);

        $response = $this->actingAs($admin)->putJson("/admin/api/sections/{$section->id}", [
            'props' => ['columns' => 'not-a-number'],
        ]);

        $response->assertStatus(422);
    }

    public function test_explicit_null_removes_a_prop_key_and_other_keys_survive(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
        $section = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => 'Halo', 'font_size' => 20], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $this->putJson("/admin/api/templates/sections/{$section->id}", [
            'props' => ['font_size' => null],
        ])->assertOk()
          ->assertJsonMissingPath('section.props.font_size');

        $fresh = $section->fresh();
        $this->assertArrayNotHasKey('font_size', $fresh->props);
        $this->assertEquals('Halo', $fresh->props['content']);
    }
}
