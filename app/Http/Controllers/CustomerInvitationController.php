<?php

namespace App\Http\Controllers;

use App\Models\InvitationPage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CustomerInvitationController extends Controller
{
    public function index()
    {
        $invitations = InvitationPage::with('template')
            ->where('owner_id', Auth::id())
            ->latest()
            ->get();

        return view('invitations-customer.index', compact('invitations'));
    }

    public function guests($id)
    {
        $page = InvitationPage::findOrFail($id);
        Gate::authorize('update', $page);

        $responses = $page->rsvpResponses()->latest('submitted_at')->get();

        return view('invitations-customer.guests', compact('page', 'responses'));
    }

    public function toggleRsvpHidden($id, $rsvpId)
    {
        $page = InvitationPage::findOrFail($id);
        Gate::authorize('update', $page);

        $response = $page->rsvpResponses()->findOrFail($rsvpId);
        $response->update(['is_hidden' => ! $response->is_hidden]);

        return response()->json(['success' => true, 'is_hidden' => $response->is_hidden]);
    }
}
