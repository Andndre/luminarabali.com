<?php
// tests/Feature/TemplateHeroSlotTest.php
namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateHeroSlotTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['division' => 'super_admin']);
    }

    private function make(string $slug, string $status, ?string $slot): InvitationTemplate
    {
        return InvitationTemplate::create([
            'name' => 'T '.$slug, 'slug' => $slug, 'status' => $status,
            'hero_slot' => $slot, 'created_by' => $this->admin()->id,
        ]);
    }

    public function test_admin_can_store_hero_slot(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.templates.store'), [
                'name' => 'Pusat', 'slug' => 'pusat', 'status' => 'published',
                'hero_slot' => 'center',
            ])->assertRedirect();

        $this->assertDatabaseHas('invitation_templates', ['slug' => 'pusat', 'hero_slot' => 'center']);
    }

    public function test_blank_hero_slot_saved_as_null(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.templates.store'), [
                'name' => 'Tanpa Slot', 'slug' => 'tanpa-slot', 'status' => 'published',
                'hero_slot' => '',
            ])->assertRedirect();

        $this->assertDatabaseHas('invitation_templates', ['slug' => 'tanpa-slot', 'hero_slot' => null]);
    }

    public function test_invalid_hero_slot_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.templates.store'), [
                'name' => 'Ngawur', 'slug' => 'ngawur', 'status' => 'published',
                'hero_slot' => 'tengah-banget',
            ])->assertSessionHasErrors('hero_slot');
    }

    public function test_index_uses_hero_slot_for_center_and_flankers(): void
    {
        $this->make('kiri-luar', 'published', 'left-outer');
        $this->make('pusat', 'published', 'center');
        $this->make('kanan-dalam', 'published', 'right-inner');
        $this->make('biasa', 'published', null);

        $res = $this->get('/undangan');

        $res->assertOk();
        $res->assertViewHas('heroCenter', fn ($t) => $t !== null && $t->slug === 'pusat');
        $res->assertViewHas('heroFlankers', fn ($f) => $f->pluck('slug')->all() === ['kiri-luar', 'kanan-dalam']);
    }

    public function test_unpublished_template_never_enters_hero(): void
    {
        $this->make('draft-pusat', 'draft', 'center');

        $this->get('/undangan')->assertOk()->assertViewHas('heroCenter', null);
    }

    public function test_index_falls_back_when_no_hero_slot_set(): void
    {
        $this->make('satu-satunya', 'published', null);

        $this->get('/undangan')->assertOk()
            ->assertViewHas('heroCenter', fn ($t) => $t !== null && $t->slug === 'satu-satunya');
    }
}
