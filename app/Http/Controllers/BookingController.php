<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BlockedDate;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class BookingController extends Controller
{
    // Public: Gate Page (Main Entry)
    public function landing()
    {
        return view('gate');
    }

    // Public: Photobooth Landing Page
    public function photoboothLanding()
    {
        $heroImages = \App\Models\Gallery::where('business_unit', 'photobooth')
            ->where('is_featured', true)
            ->latest()
            ->pluck('image_path')
            ->map(fn($path) => asset('storage/' . $path))
            ->toArray();

        return view('photobooth', compact('heroImages'));
    }

    // Public: Visual Landing Page
    public function visualLanding()
    {
        // Hero Images (Featured only)
        $heroImages = \App\Models\Gallery::where('business_unit', 'visual')
            ->where('is_featured', true)
            ->latest()
            ->pluck('image_path')
            ->map(fn($path) => asset('storage/' . $path))
            ->toArray();

        // Portfolio Images (All visual images, limit 12 for grid)
        $portfolioImages = \App\Models\Gallery::where('business_unit', 'visual')
            ->latest()
            ->limit(12)
            ->get()
            ->map(function($gallery) {
                return [
                    'path' => asset('storage/' . $gallery->image_path),
                    'title' => $gallery->title ?? 'Visual Work'
                ];
            });

        return view('visual', compact('heroImages', 'portfolioImages'));
    }

    // Public: Photobooth Pricelist
    public function pricelistPhotobooth()
    {
        $packages = \App\Models\Package::with(['prices' => function ($q) {
            $q->orderBy('duration_hours');
        }])->where('is_active', true)
           ->where('business_unit', 'photobooth')
           ->get();

        return view('pricelist_photobooth', compact('packages'));
    }

    // Public: Visual Pricelist
    public function pricelistVisual()
    {
        $packages = \App\Models\Package::with(['prices' => function ($q) {
            $q->orderBy('duration_hours');
        }])->where('is_active', true)
           ->where('business_unit', 'visual')
           ->get();

        return view('pricelist_visual', compact('packages'));
    }

    // Public: Booking Page
    public function create(Request $request)
    {
        $unit = $request->query('unit', 'photobooth'); // Default to photobooth

        $packages = \App\Models\Package::with(['prices' => function ($q) {
            $q->orderBy('duration_hours');
        }])->where('is_active', true)
            ->where('business_unit', $unit)
            ->get();

        return view('booking', compact('packages', 'unit'));
    }

    // Public: Check Availability JSON
    public function availability(Request $request)
    {
        $month = $request->query('month', date('Y-m')); // YYYY-MM
        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start));

        // Get blocked dates
        $blocked = BlockedDate::whereBetween('date', [$start, $end])
            ->get()
            ->map(function ($item) {
                return $item->date->format('Y-m-d');
            })
            ->toArray();

        // Get booking counts per date
        $bookings = Booking::select('event_date', DB::raw('count(*) as total'))
            ->whereBetween('event_date', [$start, $end])
            ->where('status', '!=', Booking::STATUS_DIBATALKAN)
            ->groupBy('event_date')
            ->pluck('total', 'event_date')
            ->toArray();

        $results = [];

        $datesOfInterest = array_unique(array_merge($blocked, array_keys($bookings)));

        foreach ($datesOfInterest as $date) {
            $isBlocked = in_array($date, $blocked);
            $count = $bookings[$date] ?? 0;

            $results[] = [
                'date' => $date,
                'booking_count' => (int)$count,
                'max_booking' => 4,
                'is_blocked' => $isBlocked
            ];
        }

        return response()->json($results);
    }

    // Public: Store Booking
    public function store(Request $request)
    {
        $request->validate([
            'customer_phone' => 'required|string',
            'price_total' => 'required|numeric',
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'event_maps_link' => 'nullable|url',
        ]);

        $date = $request->event_date;

        if (BlockedDate::where('date', $date)->exists()) {
            return back()->withErrors(['event_date' => 'Tanggal ini tidak tersedia (Blocked).']);
        }

        $count = Booking::where('event_date', $date)
            ->where('status', '!=', Booking::STATUS_DIBATALKAN)
            ->count();

        if ($count >= 4) {
            return back()->withErrors(['event_date' => 'Tanggal ini sudah penuh (Max 4 slot).']);
        }

        // Handle File Upload & Status
        $proofPath = null;
        $initialStatus = Booking::STATUS_PENDING;
        $waPaymentMsg = "Saya belum melakukan pembayaran.";

        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

            $amountPaid = $request->dp_amount ?? 0;
            $total = $request->price_total;

            if ($amountPaid >= $total) {
                $initialStatus = Booking::STATUS_LUNAS; // Or whatever status represents Paid
                $statusPay = "LUNAS (Full Payment)";
            } else {
                $initialStatus = Booking::STATUS_DP_DIBAYAR;
                $statusPay = "DP (Down Payment)";
            }

            $waPaymentMsg = "Bukti pembayaran *{$statusPay}* sebesar *Rp " . number_format($amountPaid, 0, ',', '.') . "* sudah diupload ke sistem.";
        }

        $package = \App\Models\Package::where('type', $request->package_type)->first();
        $businessUnit = $package ? $package->business_unit : 'photobooth';

        $booking = Booking::create([
            'business_unit' => $businessUnit,
            'package_name' => $request->package_name,
            'package_type' => $request->package_type,
            'duration_hours' => $request->duration_hours,
            'price_total' => $request->price_total,
            'dp_amount' => $request->dp_amount ?? 0,
            'payment_type' => $proofPath ? 'DP_TRANSFER' : 'NONE',
            'payment_proof' => $proofPath,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'event_location' => $request->event_location,
            'event_maps_link' => $request->event_maps_link,
            'event_type' => $request->event_type ?? '-',
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'notes' => $request->notes,
            'status' => $initialStatus,
        ]);

        // Auto-create Invoice
        try {
            $invNumber = 'INV/' . now()->format('Y/m') . '/' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
            $dpAmount = $booking->dp_amount ?? 0;
            $balanceDue = $booking->price_total - $dpAmount;

            $invoice = Invoice::create([
                'booking_id' => $booking->id,
                'invoice_number' => $invNumber,
                'invoice_date' => now(),
                'customer_name' => $booking->customer_name,
                'customer_phone' => $booking->customer_phone,
                'customer_email' => $booking->customer_email,
                'subtotal' => $booking->price_total,
                'grand_total' => $balanceDue,
                'dp_amount' => $dpAmount,
                'balance_due' => $balanceDue,
                'status' => $balanceDue <= 0 ? 'PAID' : ($dpAmount > 0 ? 'PARTIAL' : 'UNPAID'),
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $booking->package_name . ' (' . $booking->duration_hours . ' Jam)',
                'quantity' => 1,
                'price' => $booking->price_total,
                'total' => $booking->price_total,
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail booking? Or fail?
            // For now silent fail or log is better than crashing user experience, but consistency is key.
            \Illuminate\Support\Facades\Log::error('Failed to create invoice for booking ' . $booking->id . ': ' . $e->getMessage());
        }

        $mapsInfo = $booking->event_maps_link ? "\nMaps: {$booking->event_maps_link}" : "";

        $message = "Halo Admin Luminara,\n\n"
            . "Booking Baru:\n"
            . "Nama: {$booking->customer_name}\n"
            . "WhatsApp: {$booking->customer_phone}\n"
            . "Acara: {$booking->event_type}\n"
            . "Paket: {$booking->package_name}\n"
            . "Tanggal: " . \Carbon\Carbon::parse($booking->event_date)->translatedFormat('d F Y') . "\n"
            . "Jam: " . \Carbon\Carbon::parse($booking->event_time)->format('H:i') . " - " . \Carbon\Carbon::parse($booking->event_time)->addHours($booking->duration_hours)->format('H:i') . " WITA\n"
            . "Durasi: {$booking->duration_hours} jam\n"
            . "Lokasi: {$booking->event_location}"
            . $mapsInfo . "\n\n"
            . $waPaymentMsg;

        $encodedMessage = urlencode($message);
        $adminPhone = '6287788986136';
        $waUrl = "https://wa.me/{$adminPhone}?text={$encodedMessage}";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking saved successfully.',
                'wa_url' => $waUrl
            ]);
        }

        return redirect()->away($waUrl);
    }

    // Admin: Dashboard Overview
    public function dashboard()
    {
        // Monthly Stats
        $startOfMonth = now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = now()->endOfMonth()->format('Y-m-d');
        $userAuth = Auth::user()->id;
        $user = User::find($userAuth);

        $bookingQuery = Booking::query();
        if ($user->division !== 'super_admin') {
            $bookingQuery->where('business_unit', $user->division);
        }

        $totalBookings = (clone $bookingQuery)->whereBetween('event_date', [$startOfMonth, $endOfMonth])
            ->where('status', '!=', Booking::STATUS_DIBATALKAN)
            ->count();

        $revenue = (clone $bookingQuery)->whereBetween('event_date', [$startOfMonth, $endOfMonth])
            ->whereIn('status', [Booking::STATUS_LUNAS, Booking::STATUS_DP_DIBAYAR])
            ->sum('price_total');

        $pendingCount = (clone $bookingQuery)->where('status', Booking::STATUS_PENDING)->count();

        // Upcoming Events (Next 7 Days)
        $upcomingEvents = (clone $bookingQuery)->where('event_date', '>=', now()->format('Y-m-d'))
            ->where('event_date', '<=', now()->addDays(7)->format('Y-m-d'))
            ->where('status', '!=', Booking::STATUS_DIBATALKAN)
            ->orderBy('event_date', 'asc')
            ->orderBy('event_time', 'asc')
            ->get();

        return view('admin.dashboard', compact('totalBookings', 'revenue', 'pendingCount', 'upcomingEvents'));
    }

    // Admin: List Bookings
    public function adminIndex(Request $request)
    {
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');
        $filter = $request->query('filter', 'semua');
        $search = $request->query('search', '');
        $division = $request->query('division', 'semua');

        if (!in_array($sort, ['event_date', 'created_at', 'customer_name', 'price_total'])) {
            $sort = 'created_at';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $query = Booking::query();

        // Division scoping
        $userAuth = Auth::user()->id;
        $user = User::find($userAuth);
        if ($user->division !== 'super_admin') {
            $query->where('business_unit', $user->division);
            $division = $user->division;
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('event_location', 'like', "%{$search}%");
            });
        }

        // Division filter (only for super_admin)
        if ($user->division === 'super_admin' && $division !== 'semua') {
            $query->where('business_unit', $division);
        }

        // Smart date / status filters
        $today = now()->format('Y-m-d');
        switch ($filter) {
            case 'hari_ini':
                $query->where('event_date', $today);
                break;
            case 'besok':
                $query->where('event_date', now()->addDay()->format('Y-m-d'));
                break;
            case 'minggu_ini':
                $query->whereBetween('event_date', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
                break;
            case 'bulan_ini':
                $query->whereBetween('event_date', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')]);
                break;
            case 'pending':
                $query->where('status', Booking::STATUS_PENDING);
                break;
            case 'dp':
                $query->where('status', Booking::STATUS_DP_DIBAYAR);
                break;
            case 'lunas':
                $query->where('status', Booking::STATUS_LUNAS);
                break;
            case 'dibatalkan':
                $query->where('status', Booking::STATUS_DIBATALKAN);
                break;
            case 'belum_lunas':
                $query->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_DP_DIBAYAR]);
                break;
            case 'mendatang':
                $query->where('event_date', '>=', $today)
                      ->where('status', '!=', Booking::STATUS_DIBATALKAN);
                break;
            // 'semua' — no filter
        }

        // Stats for filter badges (scoped to same division rules)
        $statsQuery = Booking::query();
        if ($user->division !== 'super_admin') {
            $statsQuery->where('business_unit', $user->division);
        } elseif ($division !== 'semua') {
            $statsQuery->where('business_unit', $division);
        }

        $stats = [
            'semua'        => (clone $statsQuery)->where('status', '!=', Booking::STATUS_DIBATALKAN)->count(),
            'hari_ini'     => (clone $statsQuery)->where('event_date', $today)->count(),
            'besok'        => (clone $statsQuery)->where('event_date', now()->addDay()->format('Y-m-d'))->count(),
            'mendatang'    => (clone $statsQuery)->where('event_date', '>=', $today)->where('status', '!=', Booking::STATUS_DIBATALKAN)->count(),
            'pending'      => (clone $statsQuery)->where('status', Booking::STATUS_PENDING)->count(),
            'belum_lunas'  => (clone $statsQuery)->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_DP_DIBAYAR])->count(),
            'lunas'        => (clone $statsQuery)->where('status', Booking::STATUS_LUNAS)->count(),
            'dibatalkan'   => (clone $statsQuery)->where('status', Booking::STATUS_DIBATALKAN)->count(),
        ];

        $query->orderBy($sort, $direction);
        $bookings = $query->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings', 'stats', 'filter', 'search', 'division', 'sort', 'direction'));
    }

    // Admin: Create Booking Form
    public function adminCreate()
    {
        $userAuth = Auth::user()->id;
        $user = User::find($userAuth);
        $query = \App\Models\Package::with(['prices' => function ($q) {
            $q->orderBy('duration_hours');
        }])->where('is_active', true);

        if ($user->division !== 'super_admin') {
            $query->where('business_unit', $user->division);
        }

        $packages = $query->get();
        return view('admin.bookings.create', compact('packages'));
    }

    // Admin: Store Manual Booking
    public function adminStore(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'duration_hours' => 'required|integer|min:1',
            'package_type' => 'required|string',
            'price_total' => 'required|numeric',
            'link_drive' => 'nullable|url',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $package = \App\Models\Package::where('type', $request->package_type)->firstOrFail();

        // Determine Business Unit based on Admin
        $userAuth = Auth::user()->id;
        $user = User::find($userAuth);
        $businessUnit = ($user->division === 'super_admin') ? $package->business_unit : $user->division;

        // Handle Thumbnail Upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $imageFile = $request->file('thumbnail');
            $filename = time() . '_' . uniqid() . '.webp';
            $thumbnailPath = 'bookings/thumbnails/' . $filename;

            $image = Image::read($imageFile);
            if ($image->width() > 1920) {
                $image->scale(width: 1920);
            }
            $encoded = $image->toWebp(quality: 80);
            Storage::disk('public')->put($thumbnailPath, (string) $encoded);
        }

        $booking = Booking::create([
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'duration_hours' => $request->duration_hours,
            'package_name' => $package->name,
            'package_type' => $package->type,
            'price_total' => $request->price_total,
            'status' => 'PENDING', // Default to pending, admin can update later
            'business_unit' => $businessUnit,
            'event_location' => $request->event_location ?? '-',
            'event_type' => $request->event_type ?? '-',
            'payment_type' => 'MANUAL_ADMIN',
            'notes' => $request->notes,
            'link_drive' => $request->link_drive,
            'thumbnail' => $thumbnailPath,
        ]);

        // Auto-create Invoice for Admin Booking
        try {
            $invNumber = 'INV/' . now()->format('Y/m') . '/' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
            // Admin created bookings have 0 DP initially usually
            $dpAmount = 0;
            $balanceDue = $request->price_total;

            $invoice = Invoice::create([
                'booking_id' => $booking->id,
                'invoice_number' => $invNumber,
                'invoice_date' => now(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                // 'customer_email' => ... admin form doesn't have email?
                'subtotal' => $request->price_total,
                'grand_total' => $request->price_total,
                'dp_amount' => $dpAmount,
                'balance_due' => $balanceDue,
                'status' => 'UNPAID',
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $package->name . ' (' . $request->duration_hours . ' Jam)',
                'quantity' => 1,
                'price' => $request->price_total,
                'total' => $request->price_total,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create invoice for admin booking ' . $booking->id . ': ' . $e->getMessage());
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Booking manual berhasil dibuat.');
    }

    // Admin: Edit Booking Form
    public function adminEdit($id)
    {
        $booking = Booking::findOrFail($id);
        $packages = \App\Models\Package::where('is_active', true)->get();
        return view('admin.bookings.edit', compact('booking', 'packages'));
    }

    // Admin: Update Booking Data
    public function adminUpdate(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'duration_hours' => 'required|integer|min:1',
            'package_type' => 'required|string',
            'status' => 'required|in:PENDING,DP_DIBAYAR,LUNAS,DIBATALKAN',
            'price_total' => 'required|numeric',
            'link_drive' => 'nullable|url',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        // Find package name based on type
        $package = \App\Models\Package::where('type', $request->package_type)->first();
        $packageName = $package ? $package->name : $booking->package_name;

        // Update business_unit if package changed
        if ($package) {
            $booking->business_unit = $package->business_unit;
        }

        // Handle Thumbnail Upload — store new file first, delete old only after DB update succeeds
        $thumbnailPath = $booking->thumbnail;
        $oldThumbnail = null;
        if ($request->hasFile('thumbnail')) {
            $imageFile = $request->file('thumbnail');
            $filename = time() . '_' . uniqid() . '.webp';
            $thumbnailPath = 'bookings/thumbnails/' . $filename;

            $image = Image::read($imageFile);
            if ($image->width() > 1920) {
                $image->scale(width: 1920);
            }
            $encoded = $image->toWebp(quality: 80);
            Storage::disk('public')->put($thumbnailPath, (string) $encoded);

            if ($booking->thumbnail) {
                $oldThumbnail = $booking->thumbnail;
            }
        }

        $booking->update([
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'duration_hours' => $request->duration_hours,
            'package_type' => $request->package_type,
            'package_name' => $packageName,
            'status' => $request->status,
            'price_total' => $request->price_total,
            'notes' => $request->notes,
            'event_type' => $request->event_type ?? $booking->event_type,
            'link_drive' => $request->link_drive,
            'thumbnail' => $thumbnailPath,
            'business_unit' => $package?->business_unit ?? $booking->business_unit,
        ]);

        // Delete old thumbnail only after DB update succeeded
        if ($oldThumbnail) {
            Storage::disk('public')->delete($oldThumbnail);
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Data booking berhasil diperbarui.');
    }

    // Admin: Delete Booking
    public function adminDestroy($id)
    {
        $booking = Booking::findOrFail($id);
        if ($booking->payment_proof) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($booking->payment_proof);
        }
        if ($booking->thumbnail) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($booking->thumbnail);
        }
        $booking->delete();
        return back()->with('success', 'Booking berhasil dihapus.');
    }

    // Admin: Update Status
    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $request->validate([
            'status' => 'required|in:PENDING,DP_DIBAYAR,LUNAS,DIBATALKAN'
        ]);

        $booking->update(['status' => $request->status]);

        return back()->with('success', 'Status updated.');
    }

    // Admin: Calendar & Block Dates
    public function calendarIndex()
    {
        $blockedDates = BlockedDate::orderBy('date', 'desc')->get();
        return view('admin.calendar.index', compact('blockedDates'));
    }

    public function blockDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date|unique:blocked_dates,date',
            'reason' => 'nullable|string'
        ]);

        BlockedDate::create([
            'date' => $request->date,
            'reason' => $request->reason
        ]);

        return back()->with('success', 'Tanggal berhasil diblokir.');
    }

    public function unblockDate($id)
    {
        BlockedDate::findOrFail($id)->delete();
        return back()->with('success', 'Tanggal kembali dibuka.');
    }
}
