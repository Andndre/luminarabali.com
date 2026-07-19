<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationOrnamentRecolorTest extends TestCase
{
    use RefreshDatabase;

    private function pageWithOrnament(array $props): InvitationPage
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'T', 'slug' => 't-'.uniqid(), 'status' => 'published', 'created_by' => $admin->id,
        ]);
        $page = InvitationPage::create([
            'template_id' => $template->id, 'title' => 'A & B', 'slug' => 'a-'.uniqid(),
            'groom_name' => 'Romeo', 'bride_name' => 'Juliet', 'event_date' => now()->addMonth(),
            'published_status' => 'published', 'created_by' => $admin->id,
        ]);
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'quote', 'order_index' => 0,
            'props' => $props, 'is_visible' => true,
        ]);
        return $page;
    }

    public function test_svg_ornament_with_color_renders_as_recolorable_mask(): void
    {
        $page = $this->pageWithOrnament([
            'ornament_top' => 'ornaments/flourish.svg', 'ornament_top_color' => '#b5654d',
        ]);
        $res = $this->get("/invitation/{$page->slug}")->assertOk();
        $res->assertSee('mask-image', false);
        $res->assertSee('background-color:#b5654d', false);
        // tak render sebagai <img src ...flourish.svg>
        $res->assertDontSee('<img src="/storage/ornaments/flourish.svg"', false);
    }

    public function test_svg_ornament_without_color_stays_an_image(): void
    {
        $page = $this->pageWithOrnament(['ornament_top' => 'ornaments/flourish.svg']);
        $res = $this->get("/invitation/{$page->slug}")->assertOk();
        $res->assertSee('ornaments/flourish.svg', false);
        $res->assertDontSee('mask-image', false);
    }

    public function test_raster_ornament_with_color_stays_an_image(): void
    {
        // recolor mask hanya utk svg; raster (.webp) tetap <img> walau warna diisi
        $page = $this->pageWithOrnament([
            'ornament_top' => 'ornaments/leaf.webp', 'ornament_top_color' => '#b5654d',
        ]);
        $res = $this->get("/invitation/{$page->slug}")->assertOk();
        $res->assertSee('ornaments/leaf.webp', false);
        $res->assertDontSee('mask-image', false);
    }
}
