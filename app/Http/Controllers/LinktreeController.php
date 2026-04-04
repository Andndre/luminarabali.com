<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Link;
use Illuminate\Http\Request;

class LinktreeController extends Controller
{
    public function show(string $division)
    {
        $businessUnit = match ($division) {
            'LuminaraPhotobooth' => 'photobooth',
            'LuminaraVisual' => 'visual',
            default => abort(404),
        };

        $adminLinks = Link::where('is_active', true)
            ->where('business_unit', $businessUnit)
            ->orderBy('order')
            ->get();

        $todayBookingLinks = collect();
        $olderBookingLinks = collect();

        if ($businessUnit === 'photobooth') {
            $today = now()->toDateString();
            $yesterday = now()->subDay()->toDateString();

            $bookings = Booking::where('business_unit', 'photobooth')
                ->whereNotNull('link_drive')
                ->where('link_drive', '!=', '')
                ->orderBy('event_date', 'desc')
                ->get()
                ->map(function ($booking) {
                    return (object) [
                        'title' => $booking->customer_name,
                        'url' => $booking->link_drive,
                        'thumbnail' => $booking->thumbnail ?? null,
                        'type' => 'booking',
                        'event_date' => $booking->event_date,
                    ];
                });

            $todayBookingLinks = $bookings->filter(fn($b) => $b->event_date === $today);
            $olderBookingLinks = $bookings->filter(fn($b) => $b->event_date < $today);
        }

        return view('linktree.show', [
            'division' => $division,
            'adminLinks' => $adminLinks,
            'todayBookingLinks' => $todayBookingLinks,
            'olderBookingLinks' => $olderBookingLinks,
        ]);
    }
}
