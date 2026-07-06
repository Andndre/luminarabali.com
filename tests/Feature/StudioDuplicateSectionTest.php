<?php

namespace Tests\Feature;

use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioDuplicateSectionTest extends TestCase
{
    use RefreshDatabase;

    private function template(User $admin): InvitationTemplate
    {
        return InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
    }

    public function test_duplicate_copies_props_and_inserts_directly_after_the_original(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = $this->template($admin);
        $first = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => 'Halo', 'align' => 'center'], 'is_visible' => true,
        ]);
        $second = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'rsvp', 'order_index' => 1,
            'props' => [], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->postJson("/admin/api/studio/sections/{$first->id}/duplicate");

        $response->assertCreated();
        $response->assertJsonPath('section.section_type', 'text');
        $response->assertJsonPath('section.props.content', 'Halo');
        $response->assertJsonPath('section.order_index', 1);
        // The section that used to sit at index 1 must have shifted to 2.
        $this->assertEquals(2, $second->fresh()->order_index);
    }

    public function test_duplicate_copies_direct_children(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = $this->template($admin);
        $column = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'section_two_col', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);
        InvitationSection::create([
            'template_id' => $template->id, 'parent_id' => $column->id, 'section_type' => 'text',
            'order_index' => 0, 'props' => ['content' => 'Anak'], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->postJson("/admin/api/studio/sections/{$column->id}/duplicate");

        $response->assertCreated();
        $copyId = $response->json('section.id');
        $this->assertDatabaseHas('invitation_sections', [
            'parent_id' => $copyId, 'section_type' => 'text',
        ]);
        // 1 column + 1 child, duplicated = 4 rows total.
        $this->assertEquals(4, InvitationSection::count());
    }

    public function test_page_owned_sections_are_rejected(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = $this->template($admin);
        $page = \App\Models\InvitationPage::create([
            'template_id' => $template->id, 'title' => 'X', 'slug' => 'x-'.uniqid(),
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now(), 'created_by' => $admin->id,
        ]);
        $section = InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $this->postJson("/admin/api/studio/sections/{$section->id}/duplicate")->assertStatus(422);
    }

    public function test_non_admin_cannot_duplicate(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $staff = User::factory()->create(['division' => 'photobooth']);
        $template = $this->template($admin);
        $section = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);

        $this->actingAs($staff);

        $this->postJson("/admin/api/studio/sections/{$section->id}/duplicate")->assertForbidden();
    }
}
