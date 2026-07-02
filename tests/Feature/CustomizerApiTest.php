<?php

namespace Tests\Feature;

use App\Models\InvitationAsset;
use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CustomizerApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private InvitationPage $page;
    private InvitationSection $section;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['division' => 'super_admin']);
        $this->page = InvitationPage::create([
            'title' => 'A & B', 'slug' => 'customizer-page',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'published', 'created_by' => $this->admin->id,
        ]);
        $this->section = InvitationSection::create([
            'page_id' => $this->page->id, 'section_type' => 'hero', 'order_index' => 0,
            'props' => ['title' => 'Old title', 'alignment' => 'center'], 'is_visible' => true,
        ]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'A & B', 'groom_name' => 'A', 'bride_name' => 'B',
            'event_date' => now()->addMonth()->toDateString(),
        ], $overrides);
    }

    public function test_load_returns_content_fields_and_theme(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson("/admin/api/invitations/{$this->page->id}/customizer");

        $response->assertOk();
        $response->assertJsonPath('page.groom_name', 'A');
        $response->assertJsonStructure(['theme_base' => ['colors', 'fonts'], 'fonts', 'sections']);

        $heroFields = collect($response->json('sections.0.fields'));
        $this->assertTrue($heroFields->contains(fn ($f) => $f['key'] === 'title'));
        // field design tidak ikut
        $this->assertFalse($heroFields->contains(fn ($f) => $f['key'] === 'alignment'));
        // nilai existing terbawa
        $this->assertSame('Old title', $heroFields->firstWhere('key', 'title')['value']);
    }

    public function test_save_updates_core_facts_theme_and_content_props(): void
    {
        Cache::put("invitation:{$this->page->slug}", 'stale', 3600);

        $response = $this->actingAs($this->admin)
            ->putJson("/admin/api/invitations/{$this->page->id}/customizer", $this->validPayload([
                'groom_name' => 'Rama',
                'theme_overrides' => [
                    'colors' => ['primary' => '#123456'],
                    'fonts' => ['heading' => 'Lora'],
                ],
                'sections' => [
                    ['id' => $this->section->id, 'props' => ['title' => 'New title']],
                ],
            ]));

        $response->assertOk();
        $this->page->refresh();
        $this->section->refresh();
        $this->assertSame('Rama', $this->page->groom_name);
        $this->assertSame('#123456', $this->page->theme_overrides['colors']['primary']);
        $this->assertSame('New title', $this->section->props['title']);
        // design prop tidak tertimpa
        $this->assertSame('center', $this->section->props['alignment']);
        $this->assertFalse(Cache::has("invitation:{$this->page->slug}"));
    }

    public function test_save_rejects_invalid_hex_and_uncurated_font(): void
    {
        $this->actingAs($this->admin)
            ->putJson("/admin/api/invitations/{$this->page->id}/customizer", $this->validPayload([
                'theme_overrides' => ['colors' => ['primary' => 'red']],
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['theme_overrides.colors.primary']);

        $this->actingAs($this->admin)
            ->putJson("/admin/api/invitations/{$this->page->id}/customizer", $this->validPayload([
                'theme_overrides' => ['fonts' => ['heading' => 'Comic Sans MS']],
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['theme_overrides.fonts.heading']);
    }

    public function test_save_ignores_design_props_from_payload(): void
    {
        $this->actingAs($this->admin)
            ->putJson("/admin/api/invitations/{$this->page->id}/customizer", $this->validPayload([
                'sections' => [
                    ['id' => $this->section->id, 'props' => ['title' => 'Ok', 'alignment' => 'left']],
                ],
            ]))
            ->assertOk();

        $this->assertSame('center', $this->section->refresh()->props['alignment']);
    }

    public function test_save_rejects_section_of_another_page(): void
    {
        $otherPage = InvitationPage::create([
            'title' => 'X & Y', 'slug' => 'other-page',
            'groom_name' => 'X', 'bride_name' => 'Y', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $this->admin->id,
        ]);
        $foreign = InvitationSection::create([
            'page_id' => $otherPage->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => 'x'], 'is_visible' => true,
        ]);

        $this->actingAs($this->admin)
            ->putJson("/admin/api/invitations/{$this->page->id}/customizer", $this->validPayload([
                'sections' => [['id' => $foreign->id, 'props' => ['content' => 'hacked']]],
            ]))
            ->assertStatus(422);

        $this->assertSame('x', $foreign->refresh()->props['content']);
    }

    public function test_save_rejects_image_not_owned_by_page(): void
    {
        $imageSection = InvitationSection::create([
            'page_id' => $this->page->id, 'section_type' => 'image', 'order_index' => 1,
            'props' => [], 'is_visible' => true,
        ]);
        InvitationAsset::create([
            'page_id' => null, 'asset_name' => 'global', 'file_path' => 'invitations/global.webp',
            'file_type' => 'image', 'mime_type' => 'image/webp', 'file_size' => 100,
        ]);

        $this->actingAs($this->admin)
            ->putJson("/admin/api/invitations/{$this->page->id}/customizer", $this->validPayload([
                'sections' => [['id' => $imageSection->id, 'props' => ['src' => 'invitations/global.webp']]],
            ]))
            ->assertStatus(422);
    }

    public function test_save_accepts_image_owned_by_page(): void
    {
        $imageSection = InvitationSection::create([
            'page_id' => $this->page->id, 'section_type' => 'image', 'order_index' => 1,
            'props' => [], 'is_visible' => true,
        ]);
        InvitationAsset::create([
            'page_id' => $this->page->id, 'asset_name' => 'mine', 'file_path' => 'invitations/mine.webp',
            'file_type' => 'image', 'mime_type' => 'image/webp', 'file_size' => 100,
        ]);

        $this->actingAs($this->admin)
            ->putJson("/admin/api/invitations/{$this->page->id}/customizer", $this->validPayload([
                'sections' => [['id' => $imageSection->id, 'props' => ['src' => 'invitations/mine.webp']]],
            ]))
            ->assertOk();

        $this->assertSame('invitations/mine.webp', $imageSection->refresh()->props['src']);
    }

    public function test_save_rejects_array_value_for_image_prop(): void
    {
        $imageSection = InvitationSection::create([
            'page_id' => $this->page->id, 'section_type' => 'image', 'order_index' => 1,
            'props' => ['src' => 'invitations/original.webp'], 'is_visible' => true,
        ]);
        InvitationAsset::create([
            'page_id' => $this->page->id, 'asset_name' => 'mine', 'file_path' => 'invitations/mine.webp',
            'file_type' => 'image', 'mime_type' => 'image/webp', 'file_size' => 100,
        ]);

        $this->actingAs($this->admin)
            ->putJson("/admin/api/invitations/{$this->page->id}/customizer", $this->validPayload([
                'sections' => [
                    ['id' => $imageSection->id, 'props' => ['src' => ['invitations/mine.webp', 'evil-payload']]],
                ],
            ]))
            ->assertStatus(422);

        $this->assertSame('invitations/original.webp', $imageSection->refresh()->props['src']);
    }

    public function test_save_rejects_image_owned_by_a_different_page(): void
    {
        $otherPage = InvitationPage::create([
            'title' => 'C & D', 'slug' => 'other-page-assets',
            'groom_name' => 'C', 'bride_name' => 'D', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $this->admin->id,
        ]);
        $otherAsset = InvitationAsset::create([
            'page_id' => $otherPage->id, 'asset_name' => 'theirs', 'file_path' => 'invitations/theirs.webp',
            'file_type' => 'image', 'mime_type' => 'image/webp', 'file_size' => 100,
        ]);
        $otherSection = InvitationSection::create([
            'page_id' => $otherPage->id, 'section_type' => 'image', 'order_index' => 0,
            'props' => ['src' => 'invitations/theirs.webp'], 'is_visible' => true,
        ]);

        $imageSection = InvitationSection::create([
            'page_id' => $this->page->id, 'section_type' => 'image', 'order_index' => 1,
            'props' => [], 'is_visible' => true,
        ]);

        $this->actingAs($this->admin)
            ->putJson("/admin/api/invitations/{$this->page->id}/customizer", $this->validPayload([
                'sections' => [['id' => $imageSection->id, 'props' => ['src' => 'invitations/theirs.webp']]],
            ]))
            ->assertStatus(422);

        $this->assertArrayNotHasKey('src', $imageSection->refresh()->props ?? []);
        $this->assertSame('invitations/theirs.webp', $otherAsset->refresh()->file_path);
        $this->assertSame('invitations/theirs.webp', $otherSection->refresh()->props['src']);
    }

    public function test_endpoints_require_super_admin(): void
    {
        $user = User::factory()->create(['division' => 'photobooth']);

        $this->actingAs($user)
            ->getJson("/admin/api/invitations/{$this->page->id}/customizer")
            ->assertForbidden();
        $this->actingAs($user)
            ->putJson("/admin/api/invitations/{$this->page->id}/customizer", $this->validPayload())
            ->assertForbidden();
    }
}
