<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationOrnamentMultiTest extends TestCase
{
    use RefreshDatabase;

    private function pageWith(array $props): InvitationPage
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

    public function test_multiple_top_ornaments_all_render(): void
    {
        $page = $this->pageWith(['ornaments_top' => [
            ['src' => 'ornaments/a.webp', 'position' => 'left', 'scale' => 100],
            ['src' => 'ornaments/b.webp', 'position' => 'right', 'scale' => 80],
        ]]);
        $res = $this->get("/invitation/{$page->slug}")->assertOk();
        $res->assertSee('ornaments/a.webp', false);
        $res->assertSee('ornaments/b.webp', false);
    }

    public function test_flip_applies_scale_transform(): void
    {
        $page = $this->pageWith(['ornaments_top' => [
            ['src' => 'ornaments/a.webp', 'position' => 'left', 'scale' => 100, 'flip_h' => true, 'flip_v' => true],
        ]]);
        $res = $this->get("/invitation/{$page->slug}")->assertOk();
        $res->assertSee('scaleX(-1)', false);
        $res->assertSee('scaleY(-1)', false);
    }

    public function test_svg_item_with_color_uses_mask(): void
    {
        $page = $this->pageWith(['ornaments_bottom' => [
            ['src' => 'ornaments/f.svg', 'position' => 'center', 'scale' => 100, 'color' => '#b5654d'],
        ]]);
        $res = $this->get("/invitation/{$page->slug}")->assertOk();
        $res->assertSee('mask-image', false);
        $res->assertSee('background-color:#b5654d', false);
    }

    public function test_legacy_single_ornament_still_renders(): void
    {
        // back-compat: field tunggal lama tanpa list slot
        $page = $this->pageWith(['ornament_top' => 'ornaments/legacy.webp', 'ornament_top_position' => 'corner-tr']);
        $res = $this->get("/invitation/{$page->slug}")->assertOk();
        $res->assertSee('ornaments/legacy.webp', false);
    }
}
