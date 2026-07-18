<?php
namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationBgEffectTest extends TestCase
{
    use RefreshDatabase;

    private function pageWithEffect(string $effect): InvitationPage
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
            'is_visible' => true, 'props' => [
                'content' => 'Halo', 'treatment' => 'image',
                'bg_image' => 'invitations/x.webp', 'bg_effect' => $effect,
            ],
        ]);
        return $page;
    }

    public function test_effect_attribute_rendered_on_bg_layer(): void
    {
        $html = $this->get('/invitation/'.$this->pageWithEffect('scroll-zoom-in')->slug)->getContent();
        $this->assertStringContainsString('data-effect="scroll-zoom-in"', $html);
    }

    public function test_none_effect_has_no_effect_attribute(): void
    {
        $html = $this->get('/invitation/'.$this->pageWithEffect('none')->slug)->getContent();
        $this->assertStringNotContainsString('data-effect="none"', $html);
    }
}
