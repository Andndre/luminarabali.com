<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        $userAuth = Auth::user()->id;
        $user = User::find($userAuth);
        $query = Link::query();

        $divisionFilter = $request->get('division');
        if ($divisionFilter) {
            $query->where('business_unit', $divisionFilter);
        } elseif ($user->division !== 'super_admin') {
            $query->where('business_unit', $user->division);
        }

        $query->orderBy('order', 'asc');

        // Disable pagination when filtered by division so all links are available for drag & drop
        if ($divisionFilter) {
            $links = $query->get();
            $paginated = null;
        } else {
            $links = $paginated = $query->paginate(10);
        }

        return view('admin.links.index', compact('links', 'divisionFilter', 'paginated'));
    }

    public function create()
    {
        return view('admin.links.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'icon' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'business_unit' => 'nullable|in:photobooth,visual',
        ]);

        if ($validated['icon'] === '') {
            $validated['icon'] = null;
        }

        $userAuth = Auth::user()->id;
        $user = User::find($userAuth);

        $businessUnit = ($user->division === 'super_admin')
            ? ($request->business_unit ?? 'photobooth')
            : $user->division;

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('links', 'public');
        }

        $maxOrder = Link::where('business_unit', $businessUnit)->max('order') ?? -1;

        Link::create([
            'title' => $validated['title'],
            'url' => $validated['url'],
            'thumbnail' => $thumbnailPath,
            'icon' => $validated['icon'] ?? null,
            'order' => $maxOrder + 1,
            'is_active' => $request->boolean('is_active'),
            'business_unit' => $businessUnit,
        ]);

        return redirect()
            ->route('admin.links.index')
            ->with('success', 'Link berhasil ditambahkan.');
    }

    public function edit(Link $link)
    {
        $userAuth = Auth::user()->id;
        $user = User::find($userAuth);
        if ($user->division !== 'super_admin' && $user->division !== $link->business_unit) {
            abort(403);
        }

        return view('admin.links.edit', compact('link'));
    }

    public function update(Request $request, Link $link)
    {
        $userAuth = Auth::user()->id;
        $user = User::find($userAuth);

        if ($user->division !== 'super_admin' && $user->division !== $link->business_unit) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'icon' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'business_unit' => 'nullable|in:photobooth,visual',
        ]);

        if ($validated['icon'] === '') {
            $validated['icon'] = null;
        }

        $data = [
            'title' => $validated['title'],
            'url' => $validated['url'],
            'icon' => $validated['icon'],
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('thumbnail')) {
            if ($link->thumbnail) {
                Storage::disk('public')->delete($link->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('links', 'public');
        }

        if ($user->division === 'super_admin' && $request->has('business_unit') && $request->business_unit !== $link->business_unit) {
            $data['business_unit'] = $request->business_unit;
            $maxOrder = Link::where('business_unit', $request->business_unit)->max('order') ?? -1;
            $data['order'] = $maxOrder + 1;
        }

        $link->update($data);

        return redirect()
            ->route('admin.links.index')
            ->with('success', 'Link berhasil diperbarui.');
    }

    public function destroy(Link $link)
    {
        $userAuth = Auth::user()->id;
        $user = User::find($userAuth);

        if ($user->division !== 'super_admin' && $user->division !== $link->business_unit) {
            abort(403);
        }

        if ($link->thumbnail) {
            Storage::disk('public')->delete($link->thumbnail);
        }
        $link->delete();

        return redirect()
            ->route('admin.links.index')
            ->with('success', 'Link berhasil dihapus.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:links,id',
        ]);

        $user = User::find(Auth::user()->id);
        $division = $user->division;

        foreach ($request->order as $index => $linkId) {
            $link = Link::find($linkId);

            if ($division !== 'super_admin' && $link->business_unit !== $division) {
                continue;
            }

            $link->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
