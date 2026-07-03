<?php

namespace Tests\Feature;

use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioCreateSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_blank_section_seeded_with_schema_defaults(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'cover', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->postJson("/admin/api/studio/templates/{$template->id}/sections", [
            'section_type' => 'text',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('section.section_type', 'text');
        $response->assertJsonPath('section.order_index', 1);
        $response->assertJsonPath('section.props.content', 'Tulis teks anda di sini...');

        $this->assertDatabaseHas('invitation_sections', [
            'template_id' => $template->id, 'section_type' => 'text', 'order_index' => 1,
        ]);
    }

    public function test_first_section_on_a_template_with_no_sections_starts_at_order_index_zero(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        $response = $this->postJson("/admin/api/studio/templates/{$template->id}/sections", [
            'section_type' => 'text',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('section.order_index', 0);

        $this->assertDatabaseHas('invitation_sections', [
            'template_id' => $template->id, 'section_type' => 'text', 'order_index' => 0,
        ]);
    }

    public function test_rejects_an_unknown_section_type(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        $this->postJson("/admin/api/studio/templates/{$template->id}/sections", [
            'section_type' => 'not_a_real_type',
        ])->assertStatus(422);
    }

    public function test_non_admin_cannot_create_a_section(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $staff = User::factory()->create(['division' => 'photobooth']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);

        $this->actingAs($staff);

        $this->postJson("/admin/api/studio/templates/{$template->id}/sections", [
            'section_type' => 'text',
        ])->assertForbidden();
    }
}
