<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioInspectorTest extends TestCase
{
    use RefreshDatabase;

    private function studioResponse()
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
        $this->actingAs($admin);

        return $this->get("/admin/templates/{$template->id}/studio");
    }

    public function test_studio_page_renders_the_schema_driven_inspector(): void
    {
        $this->studioResponse()
            ->assertOk()
            ->assertSee('inspectorTab', false)     // state tab aktif
            ->assertSee('fieldsFor', false)        // loop field dari skema
            ->assertSee('Lanjutan', false)         // tab advanced
            ->assertSee('swapSection', false)      // pipeline fragment swap
            ->assertSee('fieldErrors', false);     // error inline per field
    }

    public function test_placeholder_copy_is_gone(): void
    {
        $this->studioResponse()
            ->assertDontSee('Form properti section hadir di Fase A2c', false);
    }

    public function test_inspector_has_the_token_chip_color_control(): void
    {
        $this->studioResponse()
            ->assertOk()
            ->assertSee('hasOverride', false)   // mode Theme vs Custom
            ->assertSee('override', false)      // badge override
            ->assertSee('resetProp', false);    // tombol reset ke theme
    }

    public function test_inspector_has_media_upload_controls(): void
    {
        $this->studioResponse()
            ->assertOk()
            ->assertSee('uploadToProp', false)
            ->assertSee('assets/upload', false)
            ->assertSee('appendListItem', false); // image_list (gallery)
    }
}
