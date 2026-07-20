<?php

namespace App\Http\Controllers;

use App\Models\InvitationPage;
use Illuminate\Support\Facades\Auth;

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
}
