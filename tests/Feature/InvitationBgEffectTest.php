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

    public function test_cover_is_exempt_from_treatment_shell(): void
    {
        // Regresi: treatment=image di cover membungkusnya dengan .sec-treat--image,
        // yang rule-nya (.sec-treat--image > :not(.sec-bg){position:relative}) menimpa
        // position:fixed milik .invite-gate → gate berhenti full-viewport & terklip.
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
            'page_id' => $page->id, 'section_type' => 'cover', 'order_index' => 0,
            'is_visible' => true, 'props' => [
                'treatment' => 'image', 'bg_image' => 'invitations/x.webp', 'bg_effect' => 'pinned',
            ],
        ]);

        $html = $this->get('/invitation/'.$page->slug)->getContent();

        $this->assertStringContainsString('class="invite-gate', $html);
        $this->assertStringNotContainsString('sec-treat--image', $html);
        $this->assertStringNotContainsString('sec-treat--pinned', $html);
        $this->assertStringNotContainsString('class="sec-bg', $html);
    }

    public function test_hero_font_family_is_not_html_escaped_in_style_block(): void
    {
        // Regresi: {{ }} meng-escape apostrof jadi &#039; di dalam <style> (raw text,
        // entity tidak di-decode) sehingga deklarasi font-family dibuang browser.
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
            'page_id' => $page->id, 'section_type' => 'hero', 'order_index' => 0,
            'is_visible' => true, 'props' => ['font_family' => 'Playfair Display'],
        ]);

        $html = $this->get('/invitation/'.$page->slug)->getContent();

        $this->assertStringContainsString("font-family: 'Playfair Display', serif;", $html);
        $this->assertStringNotContainsString('&#039;', $html);
    }

    public function test_pinned_section_does_not_use_inline_overflow_hidden(): void
    {
        // Regresi: overflow:hidden inline menjadikan section scroll container terdekat,
        // sehingga sticky milik efek pinned menempel ke elemen yang tak pernah men-scroll.
        $html = $this->get('/invitation/'.$this->pageWithEffect('pinned')->slug)->getContent();

        $this->assertStringContainsString('sec-treat--pinned', $html);
        $this->assertStringNotContainsString('overflow: hidden', $html);
    }

    public function test_image_treatment_without_photo_falls_back_to_surface(): void
    {
        // Tanpa foto, .sec-treat--image hanya menyetel teks terang di atas latar terang.
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
            'is_visible' => true, 'props' => ['content' => 'Halo', 'treatment' => 'image'],
        ]);

        $html = $this->get('/invitation/'.$page->slug)->getContent();

        $this->assertStringNotContainsString('sec-treat--image', $html);
    }

    public function test_none_effect_has_no_effect_attribute(): void
    {
        $html = $this->get('/invitation/'.$this->pageWithEffect('none')->slug)->getContent();
        $this->assertStringNotContainsString('data-effect="none"', $html);
    }
}
