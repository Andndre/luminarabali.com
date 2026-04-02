<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LinkController extends Controller
{
    public function index()
    {
        $links = Link::orderBy('order', 'asc')->paginate(10);
        return view('admin.links.index', compact('links'));
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
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('links', 'public');
        }

        Link::create([
            'title' => $validated['title'],
            'url' => $validated['url'],
            'thumbnail' => $thumbnailPath,
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.links.index')
            ->with('success', 'Link berhasil ditambahkan.');
    }

    public function edit(Link $link)
    {
        return view('admin.links.edit', compact('link'));
    }

    public function update(Request $request, Link $link)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'title' => $validated['title'],
            'url' => $validated['url'],
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('thumbnail')) {
            if ($link->thumbnail) {
                Storage::disk('public')->delete($link->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('links', 'public');
        }

        $link->update($data);

        return redirect()
            ->route('admin.links.index')
            ->with('success', 'Link berhasil diperbarui.');
    }

    public function destroy(Link $link)
    {
        if ($link->thumbnail) {
            Storage::disk('public')->delete($link->thumbnail);
        }
        $link->delete();

        return redirect()
            ->route('admin.links.index')
            ->with('success', 'Link berhasil dihapus.');
    }
}
