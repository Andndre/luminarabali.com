<?php
namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTreatmentTest extends TestCase
{
    use RefreshDatabase;

    private function pageWithSection(array $props): InvitationPage
    {
        $user = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'T', 'slug' => 't-'.uniqid(), 'status' => 'published', 'created_by' => $user->id,
        ]);
        $page = InvitationPage::create([
            'title' => 'P', 'slug' => 'p-'.uniqid(), 'published_status' => 'published',
            'template_id' => $template->id, 'created_by' => $user->id,
            'groom_name' => 'R', 'bride_name' => 'J', 'event_date' => now()->addMonth(),
        ]);
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'quote', 'order_index' => 1,
            'is_visible' => true, 'props' => array_merge(['content' => 'Halo'], $props),
        ]);
        return $page;
    }

    public function test_surface_treatment_adds_no_bg_layer(): void
    {
        $html = $this->get('/invitation/'.$this->pageWithSection(['treatment' => 'surface'])->slug)->getContent();
        $this->assertStringNotContainsString('class="sec-bg', $html);
    }

    public function test_dark_treatment_adds_treatment_class(): void
    {
        $html = $this->get('/invitation/'.$this->pageWithSection(['treatment' => 'dark'])->slug)->getContent();
        $this->assertStringContainsString('sec-treat--dark', $html);
    }

    public function test_image_treatment_renders_bg_layer_and_overlay(): void
    {
        $html = $this->get('/invitation/'.$this->pageWithSection([
            'treatment' => 'image', 'bg_image' => 'invitations/x.webp', 'bg_overlay' => 60,
        ])->slug)->getContent();
        $this->assertStringContainsString('class="sec-bg', $html);
        $this->assertStringContainsString('/storage/invitations/x.webp', $html);
        $this->assertStringContainsString('opacity:0.6', $html); // overlay 60% → number_format 0.60 → "0.6"
    }
}
