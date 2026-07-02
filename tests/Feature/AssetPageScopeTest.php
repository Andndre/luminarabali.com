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
