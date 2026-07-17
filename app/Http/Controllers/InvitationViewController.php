<?php

namespace App\Http\Controllers;

use App\Models\InvitationPage;
use App\Models\InvitationRsvpResponse;
use App\Services\InvitationRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class InvitationViewController extends Controller
{
    public function show($slug)
    {
        $page = Cache::remember("invitation:{$slug}", 3600, function () use ($slug) {
            return InvitationPage::where('slug', $slug)
                ->where('published_status', 'published')
                ->with(['assets', 'template', 'sections' => fn ($query) => $query->orderBy('order_index')])
                ->firstOrFail();
        });

        if ($page->sections->isEmpty()) {
            return response()->view('invitations.not-ready', ['page' => $page]);
        }

        $renderer = new InvitationRenderer();

        return view('invitations.public', [
            'page' => $page,
            // Deferred closure: must be evaluated nested inside invitations.public's
            // own render pass (while `{!! $content !!}` is being evaluated), not as a
            // standalone top-level render here — Blade's Factory flushes @push/@stack
            // state whenever a top-level render() call completes, so rendering eagerly
            // here would silently drop every @push('scripts') block from section partials.
            'content' => fn () => $renderer->render($page),
            'themeStyle' => $renderer->themeStyle($page),
        ]);
    }

    public function rsvp(Request $request, $slug)
    {
        $page = InvitationPage::where('slug', $slug)->firstOrFail();

        $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'nullable|string|max:50',
            'guest_email' => 'nullable|email|max:255',
            'attendance_status' => 'required|in:hadir,tidak_hadir,ragu',
            'number_of_guests' => 'required|integer|min:1',
            'message' => 'nullable|string|max:1000'
        ]);

        $rsvp = $page->rsvpResponses()->create([
            'guest_name' => $request->guest_name,
            'guest_phone' => $request->guest_phone,
            'guest_email' => $request->guest_email,
            'attendance_status' => $request->attendance_status,
            'number_of_guests' => $request->number_of_guests,
            'message' => $request->message,
            'submitted_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'RSVP submitted!']);
    }
}
