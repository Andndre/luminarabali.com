<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $templates = \App\Models\InvitationTemplate::where('is_active', true)->get();
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
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'template_id' => 'nullable|exists:invitation_templates,id',
        ]);

        // Generate unique slug
        $slug = Str::slug($request->groom_name . '-' . $request->bride_name);
        $originalSlug = $slug;
        $counter = 1;

        while (InvitationPage::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $invitation = InvitationPage::create([
            'template_id' => $request->template_id,
            'title' => $request->title,
            'slug' => $slug,
            'groom_name' => $request->groom_name,
            'bride_name' => $request->bride_name,
            'event_date' => $request->event_date,
            'published_status' => 'draft',
            'created_by' => $currentUserId,
        ]);

        // Duplicate sections from template as a snapshot starter
        if ($request->template_id) {
            $template = \App\Models\InvitationTemplate::find($request->template_id);
            if ($template && $template->sections()->count() > 0) {
                DB::transaction(function () use ($template, $invitation) {
                    $idMapping = [];
                    $templateSections = $template->sections()
                        ->orderBy('order_index')
                        ->get();

                    foreach ($templateSections as $section) {
                        $newSection = $invitation->sections()->create([
                            'parent_id' => $section->parent_id
                                ? ($idMapping[(int) $section->parent_id] ?? null)
                                : null,
                            'section_type' => $section->section_type,
                            'order_index' => $section->order_index,
                            'props' => $section->props,
                            'custom_css' => $section->custom_css,
                            'is_visible' => $section->is_visible,
                        ]);

                        $idMapping[$section->id] = $newSection->id;
                    }
                });
            }
        }

        return redirect()->route('admin.invitations.editor', $invitation->id)
            ->with('success', 'Undangan berhasil dibuat. Silakan edit dengan visual editor.');
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
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'event_date' => 'required|date',
        ]);

        $invitation->update([
            'title' => $request->title,
            'groom_name' => $request->groom_name,
            'bride_name' => $request->bride_name,
            'event_date' => $request->event_date,
        ]);

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
