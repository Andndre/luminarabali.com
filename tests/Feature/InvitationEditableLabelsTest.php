<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationEditableLabelsTest extends TestCase
{
    use RefreshDatabase;

    private function publishedPage(array $pageAttrs = []): InvitationPage
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'published', 'created_by' => $admin->id,
        ]);

        return InvitationPage::create(array_merge([
            'template_id' => $template->id, 'title' => 'A & B', 'slug' => 'a-and-b-'.uniqid(),
            'groom_name' => 'Romeo', 'bride_name' => 'Juliet', 'event_date' => now()->addMonth(),
            'published_status' => 'published', 'created_by' => $admin->id,
        ], $pageAttrs));
    }

    private function render(InvitationPage $page, string $type, array $props)
    {
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => $type, 'order_index' => 0,
            'props' => $props, 'is_visible' => true,
        ]);

        return $this->get("/invitation/{$page->slug}");
    }

    public function test_couple_eyebrow_labels_default_and_override(): void
    {
        $page = $this->publishedPage();
        $this->render($page, 'couple', ['variant' => 'portrait-overlay'])
            ->assertOk()->assertSee('Mempelai Pria')->assertSee('Mempelai Wanita');

        $page2 = $this->publishedPage();
        $this->render($page2, 'couple', ['variant' => 'portrait-overlay', 'groom_label' => 'The Groom', 'bride_label' => 'The Bride'])
            ->assertOk()->assertSee('The Groom')->assertSee('The Bride')->assertDontSee('Mempelai Pria');
    }

    public function test_event_details_maps_label_default_and_override(): void
    {
        $event = [['name' => 'Akad', 'date_text' => '', 'time_text' => '', 'venue' => '', 'address' => '', 'maps_url' => 'https://maps.google.com/x']];

        $page = $this->publishedPage();
        $this->render($page, 'event_details', ['events' => $event])
            ->assertOk()->assertSee('Lihat Lokasi');

        $page2 = $this->publishedPage();
        $this->render($page2, 'event_details', ['events' => $event, 'maps_label' => 'Open Map'])
            ->assertOk()->assertSee('Open Map')->assertDontSee('Lihat Lokasi');
    }

    public function test_gift_copy_labels_default_and_override(): void
    {
        $acc = [['bank' => 'BCA', 'number' => '123', 'holder' => 'Romeo']];

        $page = $this->publishedPage();
        $this->render($page, 'gift', ['accounts' => $acc])
            ->assertOk()->assertSee('Salin')->assertSee('Tersalin!');

        $page2 = $this->publishedPage();
        $this->render($page2, 'gift', ['accounts' => $acc, 'copy_label' => 'Copy', 'copied_label' => 'Copied!'])
            ->assertOk()->assertSee('Copy')->assertSee('Copied!')->assertDontSee('Tersalin!');
    }

    public function test_rsvp_form_labels_default_and_override(): void
    {
        $page = $this->publishedPage();
        $this->render($page, 'rsvp', [])
            ->assertOk()->assertSee('Nama Lengkap')->assertSee('Konfirmasi Kehadiran')->assertSee('Jumlah Tamu');

        $page2 = $this->publishedPage();
        $this->render($page2, 'rsvp', ['name_label' => 'Full Name', 'guests_label' => 'Guests'])
            ->assertOk()->assertSee('Full Name')->assertSee('Guests')->assertDontSee('Nama Lengkap');
    }
}
