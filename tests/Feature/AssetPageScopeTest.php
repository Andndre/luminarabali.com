<?php

namespace Tests\Feature;

use App\Models\InvitationAsset;
use App\Models\InvitationPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AssetPageScopeTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['division' => 'super_admin']);
    }

    private function makePage(User $admin): InvitationPage
    {
        return InvitationPage::create([
            'title' => 'A & B', 'slug' => 'asset-scope-'.uniqid(),
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $admin->id,
        ]);
    }

    public function test_upload_with_page_id_sets_page_id(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $admin = $this->superAdmin();
        $page = $this->makePage($admin);

        $response = $this->actingAs($admin)->postJson('/admin/api/assets/upload', [
            'file' => UploadedFile::fake()->image('photo.jpg', 100, 100),
            'page_id' => $page->id,
        ]);

        $response->assertOk();
        $this->assertSame($page->id, InvitationAsset::first()->page_id);
    }

    public function test_upload_svg_stores_as_is_without_webp_conversion(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $admin = $this->superAdmin();

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10"><circle cx="5" cy="5" r="4"/></svg>';
        $response = $this->actingAs($admin)->postJson('/admin/api/assets/upload', [
            'file' => UploadedFile::fake()->createWithContent('ornament.svg', $svg),
            'collection' => 'ornament',
        ]);

        $response->assertOk();
        $asset = InvitationAsset::first();
        $this->assertSame('image/svg+xml', $asset->mime_type);
        $this->assertSame('ornament', $asset->collection);
        $this->assertStringEndsWith('.svg', $asset->file_path);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($asset->file_path);
    }

    public function test_index_filters_by_page_id(): void
    {
        $admin = $this->superAdmin();
        $page = $this->makePage($admin);
        InvitationAsset::create([
            'page_id' => $page->id, 'asset_name' => 'mine', 'file_path' => 'invitations/mine.webp',
            'file_type' => 'image', 'mime_type' => 'image/webp', 'file_size' => 100,
        ]);
        InvitationAsset::create([
            'page_id' => null, 'asset_name' => 'global', 'file_path' => 'invitations/global.webp',
            'file_type' => 'image', 'mime_type' => 'image/webp', 'file_size' => 100,
        ]);

        $response = $this->actingAs($admin)->getJson("/admin/api/assets?page_id={$page->id}");

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('asset_name');
        $this->assertTrue($names->contains('mine'));
        $this->assertFalse($names->contains('global'));
    }
}
