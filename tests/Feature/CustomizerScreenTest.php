<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomizerScreenTest extends TestCase
{
    use RefreshDatabase;

    private function makePage(User $admin): InvitationPage
    {
        return InvitationPage::create([
            'title' => 'A & B', 'slug' => 'screen-page',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $admin->id,
        ]);
    }

    public function test_customizer_screen_loads_for_super_admin(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $page = $this->makePage($admin);

        $response = $this->actingAs($admin)->get("/admin/invitations/{$page->id}/customizer");

        $response->assertOk();
        $response->assertSee('customizerApp', false);
        $response->assertSee("/admin/invitations/{$page->id}/preview", false);
    }

    public function test_customizer_screen_requires_super_admin(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $user = User::factory()->create(['division' => 'photobooth']);
        $page = $this->makePage($admin);

        $this->actingAs($user)->get("/admin/invitations/{$page->id}/customizer")->assertForbidden();
    }

    public function test_old_editor_route_redirects_to_customizer(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $page = $this->makePage($admin);

        $this->actingAs($admin)
            ->get("/admin/invitations/{$page->id}/editor")
            ->assertRedirect("/admin/invitations/{$page->id}/customizer");
    }
}
