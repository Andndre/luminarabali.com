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

    public function test_admin_can_add_a_container_section_type(): void
    {
        // Container UI tak lagi disembunyikan (fase 6): kelasnya hidup di
        // config/invitation_component_classes.php, gating "container/basic" kini
        // di client (Mode Lanjutan), bukan penolakan server-side.
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = $this->template($admin);

        $this->actingAs($admin);

        $this->postJson("/admin/api/studio/templates/{$template->id}/sections", [
            'section_type' => 'section_two_col',
        ])->assertStatus(201);

        $this->assertSame(1, $template->sections()->count());
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

        $fresh = $section->fresh();
        $this->assertEquals('center', $fresh->props['align']);
        $this->assertEquals('hi', $fresh->props['content']);
    }

    public function test_clearing_a_text_prop_persists_empty_string_instead_of_reverting_to_default(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = $this->template($admin);
        $section = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'couple', 'order_index' => 0,
            'props' => ['heading' => 'Mempelai'], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $this->putJson("/admin/api/templates/sections/{$section->id}", [
            'props' => ['heading' => ''],
        ])->assertOk();

        $fresh = $section->fresh();
        $this->assertArrayHasKey('heading', $fresh->props);
        $this->assertSame('', $fresh->props['heading']);
    }
}
