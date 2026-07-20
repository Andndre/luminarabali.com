<?php

namespace Tests\Feature;

use App\Models\InvitationAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Tidak ada transcode di server, jadi berkas yang diunggah itulah yang diunduh tamu.
 * Batas format dan ukuran harus ditegakkan di server: atribut accept dan petunjuk di
 * form cuma kenyamanan, dan siapa pun bisa memanggil endpoint-nya langsung.
 */
class VideoUploadLimitTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['division' => 'super_admin']);
    }

    private function upload(User $admin, UploadedFile $file)
    {
        return $this->actingAs($admin)->postJson('/admin/api/assets/upload', ['file' => $file]);
    }

    public function test_webm_within_the_size_limit_is_accepted(): void
    {
        Storage::fake('public');

        $response = $this->upload($this->superAdmin(), UploadedFile::fake()->create('loop.webm', 512, 'video/webm'));

        $response->assertOk();
        $this->assertSame('video', InvitationAsset::first()->file_type);
    }

    public function test_video_in_another_format_is_rejected(): void
    {
        Storage::fake('public');

        $response = $this->upload($this->superAdmin(), UploadedFile::fake()->create('loop.mp4', 512, 'video/mp4'));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
        $this->assertSame(0, InvitationAsset::count());
    }

    /** Nama berkas bisa dikarang, jadi mime harus ikut dicek — bukan ekstensinya saja. */
    public function test_wrong_mime_behind_a_webm_extension_is_rejected(): void
    {
        Storage::fake('public');

        $response = $this->upload($this->superAdmin(), UploadedFile::fake()->create('fake.webm', 512, 'video/mp4'));

        $response->assertStatus(422);
        $this->assertSame(0, InvitationAsset::count());
    }

    public function test_video_over_the_size_limit_is_rejected(): void
    {
        Storage::fake('public');
        $maxKb = config('invitation.video_upload.max_kb');

        $response = $this->upload($this->superAdmin(), UploadedFile::fake()->create('big.webm', $maxKb + 64, 'video/webm'));

        $response->assertStatus(422);
        $this->assertSame(0, InvitationAsset::count());
    }

    /** Batas video tidak boleh ikut mengganjal unggahan foto. */
    public function test_image_upload_is_unaffected(): void
    {
        Storage::fake('public');

        $this->upload($this->superAdmin(), UploadedFile::fake()->image('photo.jpg', 100, 100))->assertOk();
    }
}
