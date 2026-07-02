<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationPage;
use App\Services\InvitationRenderer;
use Illuminate\Support\Facades\Auth;

class InvitationCustomizerController extends Controller
{
    protected function authorizeSuperAdmin(): void
    {
        $currentUser = \App\Models\User::find(Auth::id());

        if (! $currentUser || $currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function preview($id)
    {
        $this->authorizeSuperAdmin();

        $page = InvitationPage::with(['assets', 'template', 'sections' => fn ($q) => $q->orderBy('order_index')])
            ->findOrFail($id);

        $usesSections = $page->sections->isNotEmpty();

        if (! $usesSections && empty($page->template?->html_content)) {
            return response()->view('invitations.not-ready', ['page' => $page]);
        }

        $renderer = new InvitationRenderer();

        if ($usesSections) {
            // Deferred closure: harus dievaluasi di dalam render pass
            // invitations.public agar @push/@stack section tidak hilang
            // (lihat komentar panjang di InvitationViewController::show).
            $content = fn () => $renderer->render($page);
            $themeStyle = $renderer->themeStyle($page);
        } else {
            $content = fn () => $page->template->html_content ?? '';
            $themeStyle = '';
        }

        return view('invitations.public', [
            'page' => $page,
            'content' => $content,
            'themeStyle' => $themeStyle,
            'usesSections' => $usesSections,
        ]);
    }
}
