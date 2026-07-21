<?php

namespace App\Http\Controllers;

use App\Models\InvitationAsset;
use App\Models\InvitationPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class CustomerAssetController extends Controller
{
    public function index($id)
    {
        $page = InvitationPage::findOrFail($id);
        Gate::authorize('update', $page);

        return view('invitations-customer.asset-picker', compact('page'));
    }

    public function data($id)
    {
        $page = InvitationPage::findOrFail($id);
        Gate::authorize('update', $page);

        $assets = InvitationAsset::where('page_id', $page->id)->latest()->get();

        return response()->json($assets);
    }

    public function upload(Request $request, $id)
    {
        $page = InvitationPage::findOrFail($id);
        Gate::authorize('update', $page);

        // Rule bawaan Laravel 'image' TERMASUK svg (jpg,jpeg,png,bmp,gif,svg,webp) — tidak
        // menutup risiko XSS same-origin yang jadi alasan jalur ini dipersempit. 'mimes'
        // eksplisit tanpa svg/gif/bmp yang dipakai, dicek dari isi berkas (bukan nama file).
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,webp,jpg|max:25600', // Max 25MB, JPG full-res kamera sering >10MB
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . uniqid() . '.webp';
        $filePath = 'invitations/' . $fileName;

        $image = Image::read($file);
        // Downscale: foto kamera full-res percuma di undangan yang dibuka di HP.
        $image->scaleDown(2000, 2000);
        $dimensions = ['width' => $image->width(), 'height' => $image->height()];

        Storage::disk('public')->put($filePath, $image->toWebp(85)->toString());

        $asset = InvitationAsset::create([
            'page_id' => $page->id,
            'asset_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'file_path' => $filePath,
            'file_type' => 'image',
            'mime_type' => 'image/webp',
            'file_size' => Storage::disk('public')->size($filePath),
            'dimensions' => $dimensions,
            'uploaded_by' => auth()->id(),
            'visibility' => 'private',
        ]);

        return response()->json(['success' => true, 'asset' => $asset]);
    }
}
