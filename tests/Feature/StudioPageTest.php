<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_the_studio_page(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic Gold', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        $response = $this->get("/admin/templates/{$template->id}/studio");

        $response->assertOk();
        $response->assertSee('Rustic Gold');
        $response->assertSee('studioApp', false);
        $response->assertSee(route('admin.templates.studio.preview', $template->id), false);
    }

    public function test_non_admin_cannot_open_the_studio_page(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $staff = User::factory()->create(['division' => 'photobooth']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);

        $this->actingAs($staff);

        $this->get("/admin/templates/{$template->id}/studio")->assertForbidden();
    }
}
