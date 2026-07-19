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

    public function test_map_component_ignores_stale_title_color_prop(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'map', 'order_index' => 0,
            'props' => ['title' => 'Lokasi', 'address' => 'Ubud, Bali', 'title_color' => '#123456'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
        $response->assertSee('map-frame', false);
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
        $response->assertSee('background-color: var(--color-ink, #20302a);', false);
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

    public function test_hero_component_ignores_stale_overlay_and_text_color_props(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'hero', 'order_index' => 0,
            'props' => ['overlay_enabled' => true, 'overlay_color' => '#123456', 'text_color' => '#654321'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
        $response->assertDontSee('#654321', false);
        $response->assertSee('var(--color-ink, #20302a)', false);

        // Warna teks hero pindah ke stylesheet supaya varian bisa menimpanya
        // (hero--split memakai permukaan terang, jadi teksnya bukan on_dark).
        $css = file_get_contents(resource_path('css/invitation.css'));
        $this->assertStringContainsString('color: var(--color-on_dark, #f5f1e8)', $css);
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
        // Warna diatur lewat kelas varian di invitation.css (bukan style inline), jadi yang
        // diverifikasi di level respons HTTP adalah kelas token-driven-nya yang terpasang.
        $response->assertSee('countdown--cards', false);
        $response->assertSee('btn-primary', false);
    }

    public function test_countdown_ignores_stale_accent_color_prop(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'countdown', 'order_index' => 0,
            'props' => ['title' => 'Menuju Hari Bahagia', 'variant' => 'minimal-line', 'accent_color' => '#123456'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
        $response->assertSee('countdown--minimal-line', false);
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
        $response->assertSee('rsvp-button', false);
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

    public function test_rsvp_component_ignores_stale_button_color_prop(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'rsvp', 'order_index' => 0,
            'props' => ['button_color' => '#123456'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
        $response->assertSee('rsvp--elevated', false);
    }

    public function test_button_component_ignores_stale_color_props_and_uses_variant_class(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'button', 'order_index' => 0,
            'props' => ['text' => 'RSVP', 'variant' => 'outline', 'background_color' => '#123456'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
        $response->assertSee('btn-outline', false);
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

    public function test_text_component_ignores_stale_color_prop(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => 'Halo Dunia', 'color' => '#123456'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
        $response->assertSee('Halo Dunia');
    }

    public function test_image_component_ignores_stale_border_color_prop(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'image', 'order_index' => 0,
            'props' => ['src' => '/x.jpg', 'border_width' => 2, 'border_color' => '#123456'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
        $response->assertSee('color-mix(in srgb, var(--color-text', false);
    }

    public function test_divider_component_ignores_stale_color_prop(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'divider', 'order_index' => 0,
            'props' => ['color' => '#123456'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
        $response->assertSee('color-mix(in srgb, var(--color-text', false);
    }

    public function test_cover_component_ignores_stale_button_and_text_color_props(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'cover', 'order_index' => 0,
            'props' => ['button_color' => '#123456', 'text_color' => '#654321'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
        $response->assertDontSee('#654321', false);
        $response->assertSee('background: var(--color-accent, #b5654d);', false);
        $response->assertSee('color: var(--color-on_dark, #f5f1e8);', false);
    }

    public function test_couple_heading_has_no_hardcoded_accent_color(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'couple', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);

        $html = $this->get("/invitation/{$page->slug}")->getContent();

        $this->assertStringContainsString(
            '<h2 class="couple-heading" style="font-family: var(--font-heading, serif); font-size: var(--step-2xl, 32px);"',
            $html
        );
    }

    public function test_event_details_headings_have_no_hardcoded_accent_color(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'event_details', 'order_index' => 0,
            'props' => ['events' => [['name' => 'Akad Nikah']]], 'is_visible' => true,
        ]);

        $html = $this->get("/invitation/{$page->slug}")->getContent();

        $this->assertStringContainsString(
            '<h2 class="events-heading" style="font-family: var(--font-heading, serif); font-size: var(--step-2xl, 32px);">',
            $html
        );
        $this->assertStringContainsString(
            '<h3 style="font-family: var(--font-heading, serif); font-size: var(--step-lg, 20px);">Akad Nikah</h3>',
            $html
        );
    }

    public function test_gallery_grid_uses_token_driven_placeholder_background(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'gallery', 'order_index' => 0,
            'props' => ['layout' => 'grid', 'images' => [['url' => '/x.jpg', 'alt' => '']]], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('var(--color-surface_alt', false);
        $response->assertDontSee('bg-gray-100', false);
    }

    public function test_music_component_ignores_stale_button_color_prop(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'music', 'order_index' => 0,
            'props' => ['button_color' => '#123456'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
        $response->assertSee('background: var(--color-accent, #b5654d);', false);
    }

    public function test_love_story_component_ignores_stale_accent_color_prop(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'love_story', 'order_index' => 0,
            'props' => ['heading' => 'Kisah Kami', 'accent_color' => '#123456'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
    }

    public function test_gift_component_ignores_stale_accent_color_prop(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'gift', 'order_index' => 0,
            'props' => ['heading' => 'Amplop Digital', 'accent_color' => '#123456'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
    }

    public function test_quote_component_ignores_stale_text_color_prop(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'quote', 'order_index' => 0,
            'props' => ['content' => 'Halo', 'text_color' => '#123456'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
        $response->assertSee('color: var(--color-accent, #b5654d);', false);
    }

    public function test_live_stream_component_ignores_stale_accent_color_prop(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'live_stream', 'order_index' => 0,
            'props' => ['heading' => 'Live Streaming', 'accent_color' => '#123456'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
    }

    public function test_closing_component_ignores_stale_accent_color_prop(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'closing', 'order_index' => 0,
            'props' => ['message' => 'Terima kasih', 'accent_color' => '#123456'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
    }

    public function test_wishes_component_ignores_stale_accent_color_prop(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'wishes', 'order_index' => 0,
            'props' => ['heading' => 'Ucapan & Doa', 'accent_color' => '#123456'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('#123456', false);
    }

    public function test_no_component_schema_declares_color_fields_except_hidden_containers(): void
    {
        // section_one/two/three_col tetap punya 2 field warna (background_color, border_color)
        // dari $containerFields — sengaja dibiarkan karena tipe ini disembunyikan dari UI/API
        // (lihat TemplateEditorController::HIDDEN_SECTION_TYPES), bukan diredesain.
        $excludedFromSweep = ['section_one_col', 'section_two_col', 'section_three_col'];

        foreach (config('invitation_components') as $type => $fields) {
            if (in_array($type, $excludedFromSweep, true)) {
                continue;
            }

            // Field warna ber-`hidden => true` (mis. ornament_top_color/ornament_bottom_color)
            // dikecualikan: tak muncul di UI (fieldsFor menyaring `!f.hidden`), sama alasannya
            // dengan pengecualian container di atas.
            $hasColorField = collect($fields)->contains(fn ($field) => ($field['type'] ?? null) === 'color' && empty($field['hidden']));

            $this->assertFalse($hasColorField, "Expected '{$type}' schema to no longer declare a visible 'color' field");
        }
    }

    public function test_countdown_renders_the_requested_variant_class(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'countdown', 'order_index' => 0,
            'props' => ['variant' => 'ring'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('countdown--ring', false);
        $response->assertSee('countdown-ring', false);
    }

    public function test_rsvp_custom_controls_variant_renders_segmented_and_stepper_inputs(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'rsvp', 'order_index' => 0,
            'props' => ['variant' => 'custom-controls'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('rsvp--custom-controls', false);
        // Kontrol kehadiran jadi radio bersegmen, bukan <select>, tapi tetap mengirim
        // name/value yang sama supaya endpoint RSVP tidak perlu berubah.
        $response->assertSee('name="attendance_status" value="hadir"', false);
        $response->assertDontSee('<select name="attendance_status"', false);
        // Jumlah tamu jadi stepper Alpine yang menulis ke input hidden bernama sama.
        $response->assertSee('name="number_of_guests"', false);
        $response->assertSee('x-data="{ n: 1 }"', false);
    }

    public function test_rsvp_underline_variant_uses_borderless_fields(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'rsvp', 'order_index' => 0,
            'props' => ['variant' => 'underline'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('rsvp--underline', false);
        // Field & select tetap ada (markup sama seperti varian lain), hanya gayanya beda lewat CSS.
        $response->assertSee('name="guest_name"', false);
        $response->assertSee('<select name="attendance_status"', false);
    }

    public function test_couple_portrait_overlay_variant_aligns_groom_left_and_bride_right(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'couple', 'order_index' => 0,
            'props' => ['variant' => 'portrait-overlay'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('couple--portrait-overlay', false);
        $response->assertSee('couple-portrait--left', false);
        $response->assertSee('couple-portrait--right', false);
        $response->assertSee('data-reveal', false);
    }

    public function test_couple_portrait_overlay_variant_honors_per_person_text_align_override(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'couple', 'order_index' => 0,
            'props' => ['variant' => 'portrait-overlay', 'groom_text_align' => 'center', 'bride_text_align' => 'center'],
            'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('couple-portrait--center', false);
        $response->assertDontSee('couple-portrait--left', false);
        $response->assertDontSee('couple-portrait--right', false);
    }

    public function test_couple_heading_and_padding_are_customizable(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'couple', 'order_index' => 0,
            'props' => ['variant' => 'portrait-overlay', 'heading' => '', 'padding_top' => 0, 'padding_bottom' => 0],
            'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertDontSee('couple-heading', false);
        $response->assertSee('padding: 0px 20px 0px;', false);
    }

    public function test_no_component_schema_has_a_font_family_field(): void
    {
        foreach (array_keys(config('invitation_components')) as $type) {
            $keys = collect(config("invitation_components.{$type}"))->pluck('key');
            $this->assertFalse(
                $keys->contains('font_family'),
                "{$type} masih punya field font_family — font diatur di level tema (guideline §3.2)."
            );
        }
    }

    public function test_treatment_fields_only_exist_on_section_types(): void
    {
        $basics = ['text', 'image', 'button', 'divider', 'spacer', 'video', 'music', 'code'];
        foreach ($basics as $type) {
            $keys = collect(config("invitation_components.{$type}"))->pluck('key');
            $this->assertFalse($keys->contains('bg_image'), "{$type} (Basic) tidak boleh punya Foto Latar (guideline §9).");
            $this->assertFalse($keys->contains('treatment'), "{$type} (Basic) tidak boleh punya treatment.");
            $this->assertTrue($keys->contains('animation'), "{$type} tetap boleh punya animasi masuk.");
        }

        // hero/quote juga kehilangan font_family di Task 2 — pastikan tidak ikut kehilangan treatment.
        foreach (['couple', 'countdown', 'section_two_col', 'hero', 'quote'] as $type) {
            $keys = collect(config("invitation_components.{$type}"))->pluck('key');
            $this->assertTrue($keys->contains('treatment'), "{$type} (Section) wajib punya treatment.");
            $this->assertTrue($keys->contains('bg_image'));
        }
    }

    public function test_updating_a_basic_section_strips_treatment_props(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
        $section = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'music', 'order_index' => 0,
            'props' => ['src' => 'song.mp3'], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $this->putJson("/admin/api/templates/sections/{$section->id}", [
            'props' => ['bg_image' => 'sneaky.jpg', 'treatment' => 'dark', 'autoplay' => false],
        ])->assertOk();

        $fresh = $section->fresh();
        $this->assertArrayNotHasKey('bg_image', $fresh->props);
        $this->assertArrayNotHasKey('treatment', $fresh->props);
        $this->assertFalse($fresh->props['autoplay']);
    }
}
