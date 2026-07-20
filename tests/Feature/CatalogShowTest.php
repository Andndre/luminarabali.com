<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogShowTest extends TestCase
{
    use RefreshDatabase;

    private function make(string $slug, string $status, ?int $price = 750000): InvitationTemplate
    {
        return InvitationTemplate::create([
            'name' => 'Detail '.$slug,
            'slug' => $slug,
            'status' => $status,
            'price' => $price,
            'description' => 'Deskripsi '.$slug,
            'created_by' => User::factory()->create(['division' => 'super_admin'])->id,
        ]);
    }

    public function test_published_show_ok(): void
    {
        $t = $this->make('kirana', 'published');

        $res = $this->get("/undangan/{$t->slug}");

        $res->assertOk();
        $res->assertSee('Detail kirana');
        $res->assertSee('Rp750.000');
        $res->assertSee('Deskripsi kirana');
    }

    public function test_show_tanpa_harga_pakai_label_hubungi_kami(): void
    {
        $t = $this->make('tanpa-harga', 'published', null);

        $this->get("/undangan/{$t->slug}")->assertSee('Hubungi kami');
    }

    public function test_draft_show_404(): void
    {
        $t = $this->make('rahasia', 'draft');

        $this->get("/undangan/{$t->slug}")->assertNotFound();
    }

    public function test_archived_show_404(): void
    {
        $t = $this->make('arsip', 'archived');

        $this->get("/undangan/{$t->slug}")->assertNotFound();
    }

    public function test_missing_show_404(): void
    {
        $this->get('/undangan/entah-apa')->assertNotFound();
    }

    /** Route `/undangan/{slug}` tak boleh menelan `/undangan/{slug}/preview`. */
    public function test_preview_tetap_render_undangan_bukan_halaman_detail(): void
    {
        $t = $this->make('urutan-rute', 'published');

        $this->get("/undangan/{$t->slug}/preview")
            ->assertOk()
            ->assertDontSee('Pesan desain ini');
    }
}
