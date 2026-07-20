<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplatePriceTest extends TestCase
{
    use RefreshDatabase;

    private function designer(): User
    {
        return User::factory()->create(['division' => 'super_admin']);
    }

    public function test_price_label_formats_rupiah(): void
    {
        $t = InvitationTemplate::create([
            'name' => 'Mawar', 'slug' => 'mawar', 'status' => 'draft',
            'price' => 1500000, 'created_by' => $this->designer()->id,
        ]);

        $this->assertSame('Rp1.500.000', $t->priceLabel());
    }

    public function test_price_label_null_shows_contact(): void
    {
        $t = InvitationTemplate::create([
            'name' => 'Melati', 'slug' => 'melati', 'status' => 'draft',
            'price' => null, 'created_by' => $this->designer()->id,
        ]);

        $this->assertSame('Hubungi kami', $t->priceLabel());
    }

    public function test_admin_can_store_price(): void
    {
        $this->actingAs($this->designer())
            ->post(route('admin.templates.store'), [
                'name' => 'Kamboja', 'slug' => 'kamboja', 'status' => 'draft',
                'price' => 250000,
            ])->assertRedirect();

        $this->assertDatabaseHas('invitation_templates', [
            'slug' => 'kamboja', 'price' => 250000,
        ]);
    }

    public function test_admin_can_update_price(): void
    {
        $t = InvitationTemplate::create([
            'name' => 'Anggrek', 'slug' => 'anggrek', 'status' => 'draft',
            'created_by' => $this->designer()->id,
        ]);

        $this->actingAs($this->designer())
            ->put(route('admin.templates.update', $t->id), [
                'name' => 'Anggrek', 'slug' => 'anggrek', 'status' => 'draft',
                'price' => 300000,
            ])->assertRedirect();

        $this->assertDatabaseHas('invitation_templates', [
            'id' => $t->id, 'price' => 300000,
        ]);
    }

    public function test_admin_can_store_blank_price_as_null(): void
    {
        $this->actingAs($this->designer())
            ->post(route('admin.templates.store'), [
                'name' => 'Tanpa Harga', 'slug' => 'tanpa-harga', 'status' => 'draft',
                'price' => '',
            ])->assertRedirect();

        $this->assertDatabaseHas('invitation_templates', [
            'slug' => 'tanpa-harga', 'price' => null,
        ]);
    }

    public function test_admin_can_clear_price_on_update(): void
    {
        $t = InvitationTemplate::create([
            'name' => 'Bunga', 'slug' => 'bunga', 'status' => 'draft',
            'price' => 500000, 'created_by' => $this->designer()->id,
        ]);

        $this->actingAs($this->designer())
            ->put(route('admin.templates.update', $t->id), [
                'name' => 'Bunga', 'slug' => 'bunga', 'status' => 'draft',
                'price' => '',
            ])->assertRedirect();

        $this->assertDatabaseHas('invitation_templates', [
            'id' => $t->id, 'price' => null,
        ]);
    }
}
