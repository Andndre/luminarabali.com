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

    public function test_preview_stub_slug_cannot_collide_with_real_page(): void
    {
        $template = $this->template('published');
        $stub = (new \App\Services\InvitationRenderer())->previewStub($template);

        // Stub slug harus mengandung underscore agar tidak bisa dihasilkan dari Str::slug()
        $this->assertStringContainsString('_', $stub->slug);

        // Str::slug() tidak bisa menghasilkan underscore, jadi slug stub mustahil sama dengan slug page asli
        $this->assertNotEquals(\Illuminate\Support\Str::slug($stub->slug), $stub->slug);
    }
}
