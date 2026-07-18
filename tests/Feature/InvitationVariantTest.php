<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationVariantTest extends TestCase
{
    use RefreshDatabase;

    private function pageWith(string $type, array $props): InvitationPage
    {
        $user = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'T', 'slug' => 't-'.uniqid(), 'status' => 'published', 'created_by' => $user->id,
        ]);
        $page = InvitationPage::create([
            'title' => 'P', 'slug' => 'p-'.uniqid(), 'published_status' => 'published',
            'template_id' => $template->id, 'created_by' => $user->id,
            'groom_name' => 'Romeo', 'bride_name' => 'Juliet', 'event_date' => now()->addMonth(),
        ]);
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => $type, 'order_index' => 1,
            'is_visible' => true, 'props' => $props,
        ]);
        return $page;
    }

    public function test_couple_variant_switches_markup(): void
    {
        $stacked = $this->get('/invitation/'.$this->pageWith('couple', ['variant' => 'centered-stacked'])->slug)->getContent();
        $alt = $this->get('/invitation/'.$this->pageWith('couple', ['variant' => 'side-alternating'])->slug)->getContent();

        $this->assertStringContainsString('couple--centered-stacked', $stacked);
        $this->assertStringContainsString('couple--side-alternating', $alt);
    }

    public function test_couple_has_no_hardcoded_generic_colors(): void
    {
        $html = $this->get('/invitation/'.$this->pageWith('couple', [])->slug)->getContent();
        // token-driven: tidak ada abu/biru Tailwind mentah pada markup couple
        $this->assertStringNotContainsString('text-gray-700', $html);
        $this->assertStringNotContainsString('bg-blue-600', $html);
    }

    public function test_event_details_variant_switches_markup(): void
    {
        $cards = $this->get('/invitation/'.$this->pageWith('event_details', ['variant' => 'bordered-cards', 'events' => [['name' => 'Akad']]])->slug)->getContent();
        $list = $this->get('/invitation/'.$this->pageWith('event_details', ['variant' => 'divider-list', 'events' => [['name' => 'Akad']]])->slug)->getContent();

        $this->assertStringContainsString('events--bordered-cards', $cards);
        $this->assertStringContainsString('events--divider-list', $list);
    }
}
