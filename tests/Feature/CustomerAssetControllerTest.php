<?php

namespace Tests\Feature;

use App\Models\InvitationAsset;
use App\Models\InvitationPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerAssetControllerTest extends TestCase
{
    use RefreshDatabase;

    private function pageFor(User $owner): InvitationPage
    {
        $admin = User::factory()->create(['division' => 'super_admin']);

        return InvitationPage::create([
            'title' => 'A & B', 'slug' => 'asset-page-' . uniqid(),
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'owner_id' => $owner->id, 'created_by' => $admin->id,
        ]);
    }

    public function test_non_owner_forbidden_from_data(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $stranger = User::factory()->create(['division' => 'customer']);
        $page = $this->pageFor($owner);

        $this->actingAs($stranger)->getJson("/undangan-saya/{$page->id}/aset/data")->assertForbidden();
    }

    public function test_owner_sees_only_own_page_assets(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $page = $this->pageFor($owner);
        $otherPage = $this->pageFor(User::factory()->create(['division' => 'customer']));

        InvitationAsset::create([
            'page_id' => $page->id, 'asset_name' => 'mine', 'file_path' => 'invitations/mine.webp',
            'file_type' => 'image', 'mime_type' => 'image/webp', 'file_size' => 100,
        ]);
        InvitationAsset::create([
            'page_id' => $otherPage->id, 'asset_name' => 'theirs', 'file_path' => 'invitations/theirs.webp',
            'file_type' => 'image', 'mime_type' => 'image/webp', 'file_size' => 100,
        ]);

        $response = $this->actingAs($owner)->getJson("/undangan-saya/{$page->id}/aset/data");

        $response->assertOk()->assertJsonCount(1);
        $this->assertSame('mine', $response->json('0.asset_name'));
    }

    public function test_owner_can_upload_image(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create(['division' => 'customer']);
        $page = $this->pageFor($owner);

        $response = $this->actingAs($owner)->post("/undangan-saya/{$page->id}/aset", [
            'file' => UploadedFile::fake()->image('foto.jpg', 800, 600),
        ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('invitation_assets', ['page_id' => $page->id, 'file_type' => 'image']);
    }

    public function test_upload_rejects_non_image(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create(['division' => 'customer']);
        $page = $this->pageFor($owner);

        $this->actingAs($owner)
            ->postJson("/undangan-saya/{$page->id}/aset", [
                'file' => UploadedFile::fake()->create('file.svg', 10, 'image/svg+xml'),
            ])
            ->assertStatus(422);
    }
}
