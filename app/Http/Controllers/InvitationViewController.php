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
                ->with(['assets', 'template'])
                ->firstOrFail();
        });

        $content = $page->template->html_content ?? '';

        return view('invitations.public', [
            'page' => $page,
            'content' => $content
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
