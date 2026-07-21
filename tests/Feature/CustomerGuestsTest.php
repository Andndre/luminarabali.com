<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationRsvpResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerGuestsTest extends TestCase
{
    use RefreshDatabase;

    private function pageFor(User $owner): InvitationPage
    {
        $admin = User::factory()->create(['division' => 'super_admin']);

        return InvitationPage::create([
            'title' => 'A & B', 'slug' => 'guests-page-'.uniqid(),
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'published', 'owner_id' => $owner->id, 'created_by' => $admin->id,
        ]);
    }

    public function test_owner_sees_guest_list(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $page = $this->pageFor($owner);
        InvitationRsvpResponse::create([
            'page_id' => $page->id, 'guest_name' => 'Budi Santoso',
            'attendance_status' => 'hadir', 'number_of_guests' => 2,
            'message' => 'Selamat ya!', 'submitted_at' => now(), 'is_hidden' => false,
        ]);

        $this->actingAs($owner)->get("/undangan-saya/{$page->id}/tamu")
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('Selamat ya!');
    }

    public function test_non_owner_forbidden(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $stranger = User::factory()->create(['division' => 'customer']);
        $page = $this->pageFor($owner);

        $this->actingAs($stranger)->get("/undangan-saya/{$page->id}/tamu")->assertForbidden();
    }

    public function test_owner_can_toggle_hidden(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $page = $this->pageFor($owner);
        $response = InvitationRsvpResponse::create([
            'page_id' => $page->id, 'guest_name' => 'Budi', 'attendance_status' => 'hadir',
            'number_of_guests' => 1, 'submitted_at' => now(), 'is_hidden' => false,
        ]);

        $this->actingAs($owner)
            ->patchJson("/undangan-saya/{$page->id}/tamu/{$response->id}/toggle-hidden")
            ->assertOk()
            ->assertJsonPath('is_hidden', true);

        $this->assertTrue($response->refresh()->is_hidden);
    }

    public function test_cannot_toggle_rsvp_belonging_to_another_page(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $page = $this->pageFor($owner);
        $otherPage = $this->pageFor(User::factory()->create(['division' => 'customer']));
        $foreignResponse = InvitationRsvpResponse::create([
            'page_id' => $otherPage->id, 'guest_name' => 'Orang Lain', 'attendance_status' => 'hadir',
            'number_of_guests' => 1, 'submitted_at' => now(), 'is_hidden' => false,
        ]);

        $this->actingAs($owner)
            ->patchJson("/undangan-saya/{$page->id}/tamu/{$foreignResponse->id}/toggle-hidden")
            ->assertNotFound();
    }
}
