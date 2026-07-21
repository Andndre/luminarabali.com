<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerInvitationListTest extends TestCase
{
    use RefreshDatabase;

    private function customer(): User
    {
        return User::factory()->create(['division' => 'customer']);
    }

    private function pageFor(User $owner, string $status = 'draft', string $title = 'Undangan Saya'): InvitationPage
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'T', 'slug' => 't'.uniqid(), 'status' => 'published',
            'price' => 500000, 'created_by' => $admin->id,
        ]);

        return InvitationPage::create([
            'template_id' => $template->id, 'title' => $title,
            'slug' => 'slug-'.uniqid(), 'groom_name' => 'A', 'bride_name' => 'B',
            'event_date' => now()->addMonths(6), 'published_status' => $status,
            'owner_id' => $owner->id, 'created_by' => $admin->id,
        ]);
    }

    public function test_guest_redirected_to_login(): void
    {
        $this->get('/undangan-saya')->assertRedirect(route('login'));
    }

    public function test_customer_sees_own_invitation(): void
    {
        $me = $this->customer();
        $page = $this->pageFor($me, 'draft', 'Pernikahan Sari & Budi');

        $this->actingAs($me)->get('/undangan-saya')
            ->assertOk()
            ->assertSee('Pernikahan Sari & Budi');
    }

    public function test_customer_does_not_see_others_invitation(): void
    {
        $me = $this->customer();
        $other = $this->pageFor($this->customer(), 'draft', 'Punya Orang Lain');

        $this->actingAs($me)->get('/undangan-saya')
            ->assertOk()
            ->assertDontSee('Punya Orang Lain');
    }

    public function test_draft_invitation_has_no_public_link_rendered(): void
    {
        $me = $this->customer();
        $page = $this->pageFor($me, 'draft');

        $this->actingAs($me)->get('/undangan-saya')
            ->assertOk()
            ->assertDontSee("/invitation/{$page->slug}", false);
    }

    public function test_published_invitation_shows_action_buttons(): void
    {
        $me = $this->customer();
        $page = $this->pageFor($me, 'published');

        $this->actingAs($me)->get('/undangan-saya')
            ->assertOk()
            ->assertSee(route('invitations.customizer.show', $page->id), false)
            ->assertSee(route('invitations.guests', $page->id), false)
            ->assertSee(route('invitations.share', $page->id), false);
    }

    public function test_empty_state_points_to_catalog(): void
    {
        $me = $this->customer();

        $this->actingAs($me)->get('/undangan-saya')
            ->assertOk()
            ->assertSee(route('catalog.index'), false);
    }

    public function test_draft_invitation_still_404_on_public_view(): void
    {
        $me = $this->customer();
        $page = $this->pageFor($me, 'draft');

        $this->get("/invitation/{$page->slug}")->assertNotFound();
    }
}
