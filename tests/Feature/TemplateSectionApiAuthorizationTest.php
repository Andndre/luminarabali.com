<?php

namespace Tests\Feature;

use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateSectionApiAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function template(User $admin): InvitationTemplate
    {
        return InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
    }

    public function test_non_admin_cannot_load_update_delete_or_reorder_template_sections(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $staff = User::factory()->create(['division' => 'photobooth']);
        $template = $this->template($admin);
        $section = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => 'hi'], 'is_visible' => true,
        ]);

        $this->actingAs($staff);

        $this->getJson("/admin/api/templates/{$template->id}/load")->assertForbidden();
        $this->putJson("/admin/api/templates/sections/{$section->id}", ['props' => ['content' => 'hacked']])
            ->assertForbidden();
        $this->deleteJson("/admin/api/templates/sections/{$section->id}")->assertForbidden();
        $this->postJson('/admin/api/templates/sections/reorder', [
            'sections' => [['id' => $section->id, 'order_index' => 0]],
        ])->assertForbidden();

        $this->assertEquals('hi', $section->fresh()->props['content']);
    }

    public function test_admin_updating_a_section_with_invalid_props_is_rejected(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = $this->template($admin);
        $section = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => 'hi', 'align' => 'left'], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $this->putJson("/admin/api/templates/sections/{$section->id}", [
            'props' => ['align' => 'diagonally'],
        ])->assertStatus(422);

        $this->assertEquals('left', $section->fresh()->props['align']);
    }

    public function test_admin_updating_a_section_with_valid_props_succeeds(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = $this->template($admin);
        $section = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => 'hi', 'align' => 'left'], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $this->putJson("/admin/api/templates/sections/{$section->id}", [
            'props' => ['align' => 'center'],
        ])->assertOk();

        $this->assertEquals('center', $section->fresh()->props['align']);
    }
}
