<?php

namespace App\Http\Controllers;

use App\Models\InvitationTemplate;
use App\Services\InvitationRenderer;

class CatalogController extends Controller
{
    public function preview(string $slug)
    {
        $template = InvitationTemplate::with(['sections' => fn ($q) => $q->orderBy('order_index')])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $renderer = new InvitationRenderer();
        $page = $renderer->previewStub($template);

        return response()
            ->view('invitations.public', [
                'page' => $page,
                'content' => fn () => $renderer->renderTemplate($template),
                'themeStyle' => $renderer->templateThemeStyle($template),
                'coverImage' => $renderer->coverImage($template->sections),
                'studioMode' => true, // skip gate → langsung konten untuk iframe
            ])
            ->header('Cache-Control', 'no-store, private');
    }
}
