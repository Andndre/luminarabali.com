<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationComponentHardeningTest extends TestCase
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

    public function test_text_component_escapes_html_in_content_prop(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => '<script>alert(1)</script>'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('<script>alert(1)</script>', false);
        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_map_falls_back_to_address_when_coordinates_empty(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'map', 'order_index' => 0,
            'props' => ['address' => 'Ubud, Bali', 'latitude' => '', 'longitude' => ''], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        // Alamat di-encode sebagai query — bukan q=, yang bikin peta dunia.
        $response->assertSee('maps.google.com/maps?q=Ubud%2C+Bali', false);
        $response->assertDontSee('maps.google.com/maps?q=,', false);
    }

    public function test_map_uses_coordinates_when_present(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'map', 'order_index' => 0,
            'props' => ['address' => 'Ubud', 'latitude' => '-8.5', 'longitude' => '115.2'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('q=-8.5%2C115.2', false);
    }

    public function test_cover_reads_groom_bride_and_date_from_page_not_props(): void
    {
        $page = $this->publishedPage(['groom_name' => 'Romeo', 'bride_name' => 'Juliet']);
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'cover', 'order_index' => 0,
            'props' => ['groom_name' => 'Ignored', 'bride_name' => 'AlsoIgnored'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('Romeo &amp; Juliet', false);
        $response->assertDontSee('Ignored');
    }

    public function test_cover_has_opaque_fallback_background_when_no_image_set(): void
    {
        $page = $this->publishedPage();
        $section = InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'cover', 'order_index' => 0,
            'props' => ['background_image' => null], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee(".cover-visual-{$section->id} {", false);
        $response->assertSee('background-color: #1a1a1a;', false);
    }

    public function test_hero_reads_groom_bride_and_flat_background_props(): void
    {
        $page = $this->publishedPage(['groom_name' => 'Romeo', 'bride_name' => 'Juliet']);
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'hero', 'order_index' => 0,
            'props' => ['background_image' => 'templates/bg.jpg', 'overlay_enabled' => true],
            'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('Romeo');
        $response->assertSee('Juliet');
        $response->assertSee('templates/bg.jpg', false);
    }

    public function test_countdown_reads_target_date_from_page_event_date(): void
    {
        $eventDate = now()->addDays(10);
        $page = $this->publishedPage(['event_date' => $eventDate]);
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'countdown', 'order_index' => 0,
            'props' => ['title' => 'Menuju Hari Bahagia'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee($eventDate->toIso8601String(), false);
    }

    public function test_components_without_explicit_color_consume_theme_tokens(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'countdown', 'order_index' => 0,
            'props' => ['title' => 'Menuju Hari Bahagia'], 'is_visible' => true,
        ]);
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'button', 'order_index' => 1,
            'props' => ['text' => 'RSVP'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('var(--color-accent, #d4af37)', false);
        $response->assertSee('var(--color-text, #212529)', false);
        $response->assertSee('var(--color-surface, #ffffff)', false);
        $response->assertSee('var(--color-primary, #212529)', false);
    }

    public function test_explicit_section_color_prop_wins_over_theme_token(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'countdown', 'order_index' => 0,
            'props' => ['title' => 'Menuju Hari Bahagia', 'accent_color' => '#123456'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('color: #123456;', false);
        $response->assertDontSee('var(--color-accent', false);
    }

    public function test_rsvp_button_style_block_is_actually_rendered(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'rsvp', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('.rsvp-button {', false);
    }

    public function test_rsvp_whatsapp_phone_is_safely_escaped_in_script_context(): void
    {
        $malicious = "'); alert(document.cookie); (`";
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'rsvp', 'order_index' => 0,
            'props' => [
                'whatsapp_enabled' => true,
                'whatsapp_phone' => $malicious,
            ],
            'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        // The raw malicious payload must never appear unescaped inside the script.
        $response->assertDontSee($malicious, false);
        // It must be embedded via Js::from(), i.e. as a JSON-escaped string literal.
        $response->assertSee(\Illuminate\Support\Js::from($malicious)->toHtml(), false);
    }

    public function test_custom_class_prop_is_no_longer_in_the_component_schema(): void
    {
        foreach (['text', 'image', 'button', 'divider', 'spacer'] as $sectionType) {
            $fields = collect(config('invitation_components.' . $sectionType));
            $this->assertFalse(
                $fields->contains(fn ($field) => $field['key'] === 'custom_class'),
                "Expected {$sectionType} schema to no longer declare custom_class"
            );
        }
    }
}
