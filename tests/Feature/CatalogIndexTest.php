<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogIndexTest extends TestCase
{
    use RefreshDatabase;

    private function make(string $name, string $slug, string $status, ?int $price): InvitationTemplate
    {
        return InvitationTemplate::create([
            'name' => $name, 'slug' => $slug, 'status' => $status, 'price' => $price,
            'created_by' => User::factory()->create(['division' => 'super_admin'])->id,
        ]);
    }

    public function test_index_ok_and_lists_only_published(): void
    {
        $this->make('Mawar Emas', 'mawar-emas', 'published', 1500000);
        $this->make('Draft Rahasia', 'draft-rahasia', 'draft', 999000);
        $this->make('Arsip Lama', 'arsip-lama', 'archived', 500000);

        $res = $this->get('/undangan');

        $res->assertOk();
        $res->assertSee('Mawar Emas');
        $res->assertSee('Rp1.500.000');
        $res->assertDontSee('Draft Rahasia');
        $res->assertDontSee('Arsip Lama');
    }

    public function test_index_empty_state(): void
    {
        $this->get('/undangan')->assertOk()->assertSee('Belum ada desain');
    }
}
