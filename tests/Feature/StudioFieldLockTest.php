<?php

namespace Tests\Feature;

use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioFieldLockTest extends TestCase
{
    use RefreshDatabase;

    private function makeSection(User $admin, string $type = 'couple'): InvitationSection
    {
        $template = InvitationTemplate::create([
            'name' => 'T', 'slug' => 't-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);

        return InvitationSection::create([
            'template_id' => $template->id, 'section_type' => $type, 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);
    }

    public function test_locked_fields_persist_on_the_section_props(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $section = $this->makeSection($admin);
        $this->actingAs($admin);

        $this->putJson("/admin/api/templates/sections/{$section->id}", [
            'locked' => ['groom_label'],
        ])->assertOk();

        $this->assertSame(['groom_label'], $section->fresh()->props['_locked']);
    }

    public function test_locked_survives_a_later_props_save(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $section = $this->makeSection($admin);
        $this->actingAs($admin);

        $this->putJson("/admin/api/templates/sections/{$section->id}", ['locked' => ['groom_label']])->assertOk();
        $this->putJson("/admin/api/templates/sections/{$section->id}", ['props' => ['groom_label' => 'Mempelai Pria']])->assertOk();

        $fresh = $section->fresh();
        $this->assertSame(['groom_label'], $fresh->props['_locked']);
        $this->assertSame('Mempelai Pria', $fresh->props['groom_label']);
    }

    public function test_locking_an_unknown_field_is_rejected(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $section = $this->makeSection($admin);
        $this->actingAs($admin);

        $this->putJson("/admin/api/templates/sections/{$section->id}", [
            'locked' => ['tidak_ada_field_ini'],
        ])->assertStatus(422)->assertJsonValidationErrors('locked.0');
    }

    public function test_only_content_fields_can_be_locked(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $section = $this->makeSection($admin);
        $this->actingAs($admin);

        // 'treatment' ada di grup design — gembok hanya berlaku untuk field konten.
        $this->putJson("/admin/api/templates/sections/{$section->id}", [
            'locked' => ['treatment'],
        ])->assertStatus(422)->assertJsonValidationErrors('locked.0');
    }

    public function test_empty_locked_list_clears_the_key(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $section = $this->makeSection($admin);
        $this->actingAs($admin);

        $this->putJson("/admin/api/templates/sections/{$section->id}", ['locked' => ['groom_label']])->assertOk();
        $this->putJson("/admin/api/templates/sections/{$section->id}", ['locked' => []])->assertOk();

        $this->assertArrayNotHasKey('_locked', $section->fresh()->props);
    }

    public function test_non_super_admin_cannot_lock_fields(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $staff = User::factory()->create(['division' => 'photobooth']);
        $section = $this->makeSection($admin);
        $this->actingAs($staff);

        $this->putJson("/admin/api/templates/sections/{$section->id}", ['locked' => ['groom_label']])
            ->assertForbidden();
    }
}
