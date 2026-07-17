<?php

namespace Tests\Feature;

use App\Models\InvitationAsset;
use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssetReferenceSafeDeleteTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['division' => 'super_admin']);
    }

    private function makeAsset(): InvitationAsset
    {
        return InvitationAsset::create([
            'asset_name' => 'photo', 'file_path' => 'invitations/ref-test-photo.webp',
            'file_type' => 'image', 'mime_type' => 'image/webp', 'file_size' => 100,
        ]);
    }

    public function test_asset_referenced_by_section_props_is_rejected_with_409(): void
    {
        $admin = $this->superAdmin();
        $asset = $this->makeAsset();
        $page = InvitationPage::create([
            'title' => 'A & B', 'slug' => 'ref-check-page',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $admin->id,
        ]);
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'image', 'order_index' => 0,
            'props' => ['src' => $asset->file_path], 'is_visible' => true,
        ]);

        $response = $this->actingAs($admin)->deleteJson("/admin/api/assets/{$asset->id}");

        $response->assertStatus(409);
        $response->assertJsonStructure(['message', 'used_by']);
        $this->assertDatabaseHas('invitation_assets', ['id' => $asset->id]);
    }

    public function test_orphan_asset_is_deleted_along_with_file(): void
    {
        Storage::fake('public');
        $admin = $this->superAdmin();
        $asset = $this->makeAsset();
        Storage::disk('public')->put($asset->file_path, 'fake-bytes');

        $response = $this->actingAs($admin)->deleteJson("/admin/api/assets/{$asset->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('invitation_assets', ['id' => $asset->id]);
        Storage::disk('public')->assertMissing($asset->file_path);
    }
}
