<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogPreviewTest extends TestCase
{
    use RefreshDatabase;

    private function template(string $status): InvitationTemplate
    {
        return InvitationTemplate::create([
            'name' => 'Preview '.$status, 'slug' => 'prev-'.$status,
            'status' => $status,
            'created_by' => User::factory()->create(['division' => 'super_admin'])->id,
        ]);
    }

    public function test_published_preview_renders_public(): void
    {
        $t = $this->template('published');

        $res = $this->get("/undangan/{$t->slug}/preview");

        $res->assertOk();
        $res->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_draft_preview_404(): void
    {
        $t = $this->template('draft');
        $this->get("/undangan/{$t->slug}/preview")->assertNotFound();
    }

    public function test_missing_preview_404(): void
    {
        $this->get('/undangan/tidak-ada/preview')->assertNotFound();
    }
}
