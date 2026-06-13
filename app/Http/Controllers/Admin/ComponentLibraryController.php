<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComponentLibraryController extends Controller
{
    public function index()
    {
        // Currently restrict to super_admin as per implementation plan
        $currentUserId = \Illuminate\Support\Facades\Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $components = \App\Models\ComponentLibrary::latest()->get();
        return view('admin.component-library.index', compact('components'));
    }

    public function create()
    {
        return view('admin.component-library.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:component_library,slug',
            'category' => 'required|string|max:100',
            'type' => 'required|in:component,section',
        ]);

        $component = \App\Models\ComponentLibrary::create([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->slug),
            'category' => $request->category,
            'description' => $request->description,
            'code' => $request->code,
            'variables' => $request->variables ? json_decode($request->variables, true) : [],
            'type' => $request->type,
            'is_public' => $request->has('is_public'),
            'is_active' => $request->has('is_active'),
            'created_by' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'component' => $component]);
        }

        return redirect()->route('admin.component-library.index')->with('success', 'Component created successfully!');
    }

    public function edit($id)
    {
        $component = \App\Models\ComponentLibrary::findOrFail($id);
        return view('admin.component-library.form', compact('component'));
    }

    public function update(Request $request, $id)
    {
        $component = \App\Models\ComponentLibrary::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:component_library,slug,' . $component->id,
            'category' => 'required|string|max:100',
            'type' => 'required|in:component,section',
        ]);

        $component->update([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->slug),
            'category' => $request->category,
            'description' => $request->description,
            'code' => $request->code,
            'variables' => $request->variables ? json_decode($request->variables, true) : [],
            'type' => $request->type,
            'is_public' => $request->has('is_public'),
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'component' => $component]);
        }

        return redirect()->route('admin.component-library.index')->with('success', 'Component updated successfully!');
    }

    public function destroy($id)
    {
        $component = \App\Models\ComponentLibrary::findOrFail($id);
        
        if ($component->thumbnail) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('storage/', '', $component->thumbnail));
        }
        
        $component->delete();

        return redirect()->route('admin.component-library.index')->with('success', 'Component deleted successfully!');
    }

    // API endpoints for the Template Editor sidebar
    public function apiIndex()
    {
        $components = \App\Models\ComponentLibrary::where('is_active', true)
            ->where(function ($query) {
                $query->where('is_public', true)
                      ->orWhere('created_by', \Illuminate\Support\Facades\Auth::id());
            })
            ->latest()
            ->get();
            
        return response()->json($components);
    }

    public function apiShow($id)
    {
        $component = \App\Models\ComponentLibrary::findOrFail($id);
        return response()->json($component);
    }

    public function uploadThumbnail(Request $request, $id)
    {
        $component = \App\Models\ComponentLibrary::findOrFail($id);
        
        $request->validate([
            'thumbnail' => 'required|image|max:2048', // max 2MB
        ]);

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($component->thumbnail) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('storage/', '', $component->thumbnail));
            }

            $path = $request->file('thumbnail')->store('component-thumbnails', 'public');
            $component->update(['thumbnail' => 'storage/' . $path]);

            return response()->json([
                'success' => true, 
                'thumbnail_url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['success' => false], 400);
    }
}
