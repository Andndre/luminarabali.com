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

    public function test_webm_and_mp4_within_the_size_limit_are_accepted(): void
    {
        Storage::fake('public');
        $admin = $this->superAdmin();

        $this->upload($admin, UploadedFile::fake()->create('loop.webm', 512, 'video/webm'))->assertOk();
        $this->upload($admin, UploadedFile::fake()->create('loop.mp4', 512, 'video/mp4'))->assertOk();

        $this->assertSame(2, InvitationAsset::where('file_type', 'video')->count());
    }

    public function test_video_in_another_format_is_rejected(): void
    {
        Storage::fake('public');

        // MKV bukan bagian daftar; mime disniff dari isi, jadi ekstensi tak menolong.
        $response = $this->upload($this->superAdmin(), UploadedFile::fake()->create('loop.mkv', 512, 'video/x-matroska'));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
        $this->assertSame(0, InvitationAsset::count());
    }

    /**
     * Nama berkas tidak dipercaya: berkas mp4 yang dinamai .webm tetap diterima (mime-nya
     * sah), tapi harus TERSIMPAN sebagai .mp4 — ekstensi dari mime, bukan dari nama.
     */
    public function test_mislabeled_video_is_stored_with_the_extension_from_its_mime(): void
    {
        Storage::fake('public');

        $this->upload($this->superAdmin(), UploadedFile::fake()->create('fake.webm', 512, 'video/mp4'))->assertOk();

        $this->assertStringEndsWith('.mp4', InvitationAsset::first()->file_path);
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
