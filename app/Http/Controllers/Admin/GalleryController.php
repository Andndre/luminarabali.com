<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class GalleryController extends Controller
{
    public function index()
    {
        $userAuth = Auth::user()->id;
        $user = User::find($userAuth);
        $query = Gallery::latest();

        if ($user->division !== 'super_admin') {
            $query->where('business_unit', $user->division);
        }

        $galleries = $query->paginate(12);
        return view('admin.galleries.index', compact('galleries'));
    }

    public function create()
    {
        return view('admin.galleries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', // Max 10MB (processed later)
            'title' => 'nullable|string|max:255',
        ]);

        $userAuth = Auth::user()->id;
        $user = User::find($userAuth);

        $businessUnit = ($user->division === 'super_admin')
            ? ($request->business_unit ?? 'photobooth')
            : $user->division;

        // Process Image
        $imageFile = $request->file('image');
        $filename = time().'_'.uniqid().'.webp';
        $path = 'gallery/'.$filename;

        $image = Image::read($imageFile);

        // Resize if width > 1920px, keep aspect ratio
        if ($image->width() > 1920) {
            $image->scale(width: 1920);
        }

        // Encode to WebP with 80% quality
        $encoded = $image->toWebp(quality: 80);

        // Save to storage
        Storage::disk('public')->put($path, (string) $encoded);

        Gallery::create([
            'business_unit' => $businessUnit,
            'image_path' => $path,
            'title' => $request->title,
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('admin.galleries.index')->with('success', 'Foto berhasil ditambahkan & dioptimasi (WebP).');
    }

    public function destroy(Gallery $gallery)
    {
        // Security check
        if (auth()->user()->division !== 'super_admin' && auth()->user()->division !== $gallery->business_unit) {
            abort(403);
        }

        if ($gallery->image_path) {
            Storage::disk('public')->delete($gallery->image_path);
        }

        $gallery->delete();
        return redirect()->route('admin.galleries.index')->with('success', 'Foto dihapus.');
    }

    public function toggleFeatured(Gallery $gallery)
    {
        // Security check
        if (auth()->user()->division !== 'super_admin' && auth()->user()->division !== $gallery->business_unit) {
            abort(403);
        }

        $gallery->is_featured = ! $gallery->is_featured;
        $gallery->save();

        return response()->json([
            'success' => true,
            'is_featured' => $gallery->is_featured,
            'message' => 'Status featured diperbarui.'
        ]);
    }
}
