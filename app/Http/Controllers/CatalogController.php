<?php

namespace App\Http\Controllers;

use App\Models\InvitationTemplate;
use App\Services\InvitationRenderer;

class CatalogController extends Controller
{
    public function index()
    {
        $templates = InvitationTemplate::where('status', 'published')
            ->orderByDesc('id')
            ->get();

        // Komposisi kipas di hero: 1 device tengah + hingga 4 pengapit
        // (2 per sisi), posisinya diatur admin lewat `hero_slot`.
        // Live-frame hanya dipakai di sini; grid katalog tetap thumbnail statis.
        $fan = InvitationTemplate::heroFan();
        $heroCenter = $fan->firstWhere('hero_slot', 'center')
            // Belum ada yang ditandai admin: pakai template published terbaru
            // supaya hero tak pernah kosong.
            ?? $templates->first();
        $heroFlankers = $fan->reject(fn ($t) => $t->hero_slot === 'center')->values();

        return view('catalog.index', compact('templates', 'heroCenter', 'heroFlankers'));
    }

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
