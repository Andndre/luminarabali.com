<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function index()
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $invitations = InvitationPage::with('template')->latest()->get();
        return view('admin.invitations.index', compact('invitations'));
    }

    public function create()
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $templates = \App\Models\InvitationTemplate::where('status', 'published')->get();
        return view('admin.invitations.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:invitation_pages,slug',
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'template_id' => 'nullable|exists:invitation_templates,id',
        ]);

        $attributes = [
            'title' => $request->title,
            'slug' => Str::slug($request->slug),
            'groom_name' => $request->groom_name,
            'bride_name' => $request->bride_name,
            'event_date' => $request->event_date,
            'published_status' => 'draft',
            'created_by' => $currentUserId,
        ];

        if ($request->template_id) {
            $template = \App\Models\InvitationTemplate::findOrFail($request->template_id);
            $invitation = app(\App\Services\TemplateInstantiator::class)
                ->instantiate($template, $attributes);
        } else {
            $invitation = InvitationPage::create($attributes);
        }

        return redirect()->route('admin.invitations.edit', $invitation->id)
            ->with('success', 'Undangan berhasil dibuat.');
    }

    public function edit($id)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $invitation = InvitationPage::findOrFail($id);
        return view('admin.invitations.edit', compact('invitation'));
    }

    public function update(Request $request, $id)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $invitation = InvitationPage::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:invitation_pages,slug,' . $id,
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'meta_data' => 'nullable|string',
            'published_status' => 'required|in:draft,published,archived',
        ]);

        $metaData = null;
        if ($request->meta_data) {
            $metaData = json_decode($request->meta_data, true);
        }

        Cache::forget("invitation:{$invitation->slug}");

        $invitation->update([
            'title' => $request->title,
            'slug' => Str::slug($request->slug),
            'groom_name' => $request->groom_name,
            'bride_name' => $request->bride_name,
            'event_date' => $request->event_date,
            'meta_data' => $metaData,
            'published_status' => $request->published_status,
        ]);

        Cache::forget("invitation:{$invitation->slug}");

        return redirect()->route('admin.invitations.index')->with('success', 'Undangan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $invitation = InvitationPage::findOrFail($id);
        $invitation->delete();

        return redirect()->route('admin.invitations.index')->with('success', 'Undangan berhasil dihapus.');
    }
}
