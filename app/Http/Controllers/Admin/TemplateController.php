<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function index()
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if (!$currentUser || !$currentUser->canDesignTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        $templates = InvitationTemplate::all();
        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if (!$currentUser || !$currentUser->canDesignTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.templates.create');
    }

    public function store(Request $request)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if (!$currentUser || !$currentUser->canDesignTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:invitation_templates',
            'thumbnail' => 'nullable|image|max:5120', // Max 5MB
            'status' => 'nullable|in:draft,published,archived',
        ]);

        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $thumbnailPath = $file->storeAs('templates/thumbnails', $fileName, 'public');
        }

        InvitationTemplate::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'thumbnail' => $thumbnailPath,
            'description' => $request->description,
            'category' => $request->category,
            'status' => $request->status ?? 'draft',
            'created_by' => $currentUserId,
        ]);

        return redirect()->route('admin.templates.index')->with('success', 'Template berhasil dibuat.');
    }

    public function edit($id)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if (!$currentUser || !$currentUser->canDesignTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        $template = InvitationTemplate::findOrFail($id);
        return view('admin.templates.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if (!$currentUser || !$currentUser->canDesignTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        $template = InvitationTemplate::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:invitation_templates,slug,' . $id,
            'thumbnail' => 'nullable|image|max:5120', // Max 5MB
            'status' => 'nullable|in:draft,published,archived',
        ]);

        // Handle thumbnail upload
        $thumbnailPath = $template->thumbnail; // Keep existing if no new file
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($template->thumbnail && Storage::disk('public')->exists($template->thumbnail)) {
                Storage::disk('public')->delete($template->thumbnail);
            }

            $file = $request->file('thumbnail');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $thumbnailPath = $file->storeAs('templates/thumbnails', $fileName, 'public');
        }

        $template->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'thumbnail' => $thumbnailPath,
            'description' => $request->description,
            'category' => $request->category,
            'status' => $request->status ?? 'draft',
        ]);

        return redirect()->route('admin.templates.index')->with('success', 'Template berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if (!$currentUser || !$currentUser->canDesignTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        $template = InvitationTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('admin.templates.index')->with('success', 'Template berhasil dihapus.');
    }

    public function duplicate($id)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if (!$currentUser || !$currentUser->canDesignTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        $template = InvitationTemplate::with(['sections'])->findOrFail($id);
        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (Copy)';
        $newTemplate->slug = $template->slug . '-copy-' . time();
        $newTemplate->created_by = $currentUserId;
        $newTemplate->save();

        // Duplicate all sections
        foreach ($template->sections as $section) {
            $newTemplate->sections()->create([
                'section_type' => $section->section_type,
                'order_index' => $section->order_index,
                'props' => $section->props,
                'custom_css' => $section->custom_css,
                'is_visible' => $section->is_visible,
            ]);
        }

        return redirect()->route('admin.templates.index')->with('success', 'Template berhasil diduplikasi.');
    }
}
