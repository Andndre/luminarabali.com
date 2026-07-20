<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationAsset;
use App\Models\InvitationSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class InvitationAssetController extends Controller
{
    public function indexView()
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if (!$currentUser || !$currentUser->canDesignTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.invitations.assets.index');
    }

    public function index(Request $request)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if (!$currentUser || !$currentUser->canDesignTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        $query = InvitationAsset::query();

        // Filter by file type
        if ($request->has('file_type') && $request->file_type) {
            $query->where('file_type', $request->file_type);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('asset_name', 'like', '%' . $request->search . '%');
        }

        // Filter by page (optional)
        if ($request->has('page_id') && $request->page_id) {
            $query->where('page_id', $request->page_id);
        }

        // Filter by collection (prefix match, e.g. "ornament" matches "ornament/floral")
        if ($request->filled('collection')) {
            $query->where('collection', 'like', $request->input('collection') . '%');
        }

        $assets = $query->latest()->paginate(50);

        return response()->json($assets);
    }

    public function upload(Request $request)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if (!$currentUser || !$currentUser->canDesignTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'file' => 'required|file|max:25600', // Max 25MB (JPG full-res kamera sering >10MB)
            'page_id' => 'nullable|exists:invitation_pages,id',
            'collection' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $mimeType = $file->getMimeType();
        $fileType = $this->getFileType($mimeType);

        // Video dipakai apa adanya — tidak ada transcode di server. Batas format dan
        // ukuran ditegakkan di sini, bukan cuma lewat atribut accept di form yang bisa
        // dilewati siapa pun. Mime disniff dari isi berkas, jadi itu yang menentukan
        // format; nama berkas tidak dipercaya sama sekali.
        $videoExt = null;
        if ($fileType === 'video') {
            $rules = config('invitation.video_upload');
            $maxKb = $rules['max_kb'];

            $videoExt = array_search($mimeType, $rules['formats'], true);
            if ($videoExt === false) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'file' => ['Video harus berformat '.strtoupper(implode('/', array_keys($rules['formats'])))
                        .'. Konversi dulu sebelum mengunggah.'],
                ]);
            }

            if ($file->getSize() > $maxKb * 1024) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'file' => ['Video maksimal '.round($maxKb / 1024).' MB. Perkecil resolusi atau bitrate-nya dulu.'],
                ]);
            }
        }

        // SVG: vektor, GD tidak bisa membacanya — simpan apa adanya tanpa konversi WebP.
        // TIDAK disanitasi. Dirender lewat <img> sehingga script di dalamnya tidak jalan,
        // tapi file-nya tetap bisa dibuka langsung di /storage dan di situ script-nya
        // same-origin. Endpoint ini kini terbuka untuk division 'designer', bukan cuma
        // super admin — yang sudah punya jalur script jauh lebih lurus lewat komponen
        // 'code'. Jadi kalau kepercayaan ke designer dipersempit, keduanya harus ditangani
        // bersama; menyanitasi SVG saja tidak menutup apa pun.
        if ($mimeType === 'image/svg+xml') {
            $fileName = time() . '_' . uniqid() . '.svg';
            $filePath = $file->storeAs('invitations', $fileName, 'public');
            $dimensions = null;
        // Process image - convert to WebP
        } elseif ($fileType === 'image' && str_starts_with($mimeType, 'image/')) {
            $fileName = time() . '_' . uniqid() . '.webp';
            $filePath = 'invitations/' . $fileName;

            // Load and optimize image
            $image = Image::read($file);

            // Downscale: foto kamera full-res (6000px) percuma di undangan yang dibuka di HP.
            // scaleDown hanya mengecilkan — gambar yang sudah kecil dibiarkan apa adanya.
            $image->scaleDown(2000, 2000);

            // Dimensi dicatat setelah downscale supaya cocok dengan file tersimpan
            $dimensions = [
                'width' => $image->width(),
                'height' => $image->height()
            ];

            // Store as WebP with 85% quality using Storage
            $encodedImage = $image->toWebp(85)->toString();
            Storage::disk('public')->put($filePath, $encodedImage);
        } else {
            // For non-images, store as-is. Video: ekstensi diturunkan dari mime (di atas),
            // jadi berkas mp4 yang dinamai .webm tetap tersimpan sebagai .mp4 — tidak
            // pernah salah ekstensi. Audio/dokumen tetap memakai ekstensi aslinya.
            $ext = $videoExt ?: $file->getClientOriginalExtension();
            $fileName = time() . '_' . uniqid() . '.' . $ext;
            $filePath = $file->storeAs('invitations', $fileName, 'public');
            $dimensions = null;
        }

        // Remove extension from filename for display (since we convert to WebP)
        $originalName = $file->getClientOriginalName();
        $assetName = pathinfo($originalName, PATHINFO_FILENAME);

        $asset = InvitationAsset::create([
            'page_id' => $request->page_id ?? null, // Default to null for global library
            'asset_name' => $assetName,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'dimensions' => $dimensions,
            'uploaded_by' => $currentUserId,
            // ponytail: satu designer hari ini — semua upload 'team'; enforcement
            // visibilitas antar-user menunggu role designer (proposal §8).
            'visibility' => 'team',
            'collection' => $request->input('collection'),
        ]);

        return response()->json(['success' => true, 'asset' => $asset]);
    }

    public function destroy($id)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if (!$currentUser || !$currentUser->canDesignTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        $asset = InvitationAsset::findOrFail($id);

        $path = $asset->file_path;

        // JSON columns may store forward slashes escaped (e.g. sqlite stores the raw
        // PHP json_encode() output, which escapes "/" to "\/"; MySQL's native JSON
        // type normalizes this away). Match both forms so the check is portable.
        $escapedPath = str_replace('/', '\/', $path);

        $sectionUsers = InvitationSection::where('props', 'like', "%{$path}%")
            ->orWhere('props', 'like', "%{$escapedPath}%")
            ->with('page:id,title')
            ->get()
            ->map(fn ($s) => [
                'type' => 'section',
                'section_id' => $s->id,
                'section_type' => $s->section_type,
                'page_title' => $s->page?->title,
            ]);

        $usedBy = $sectionUsers->values();

        if ($usedBy->isNotEmpty()) {
            return response()->json([
                'message' => 'Aset masih dipakai dan tidak bisa dihapus.',
                'used_by' => $usedBy,
            ], 409);
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($asset->file_path)) {
            Storage::disk('public')->delete($asset->file_path);
        }

        $asset->delete();

        return response()->json(['success' => true, 'message' => 'Asset deleted']);
    }

    public function update(Request $request, $id)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if (!$currentUser || !$currentUser->canDesignTemplates()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'visibility' => 'sometimes|in:private,team',
            'collection' => 'sometimes|nullable|string|max:255',
        ]);

        $asset = InvitationAsset::findOrFail($id);
        $asset->update($request->only(['asset_name', 'alt_text', 'visibility', 'collection']));

        return response()->json(['success' => true, 'asset' => $asset]);
    }

    private function getFileType($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }
        return 'document';
    }
}
