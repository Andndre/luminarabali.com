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
                    $folderId = $this->extractDriveFolderId($booking->link_drive);
                    $url = $folderId ? route('gallery.drive', $folderId) : $booking->link_drive;

                    return (object) [
                        'title' => $booking->event_type && $booking->event_type !== '-'
                            ? $booking->event_type
                            : $booking->customer_name,
                        'url' => $url,
                        'thumbnail' => $booking->thumbnail ?? null,
                        'type' => 'booking',
                        'event_date' => $booking->event_date,
                    ];
                });

            $todayBookingLinks = $bookings->filter(fn($b) => $b->event_date->toDateString() === $today);
            $olderBookingLinks = $bookings->filter(fn($b) => $b->event_date->toDateString() < $today);
        }

        return view('linktree.show', [
            'division' => $division,
            'adminLinks' => $adminLinks,
            'todayBookingLinks' => $todayBookingLinks,
            'olderBookingLinks' => $olderBookingLinks,
        ]);
    }

    public function driveGallery($folderId)
    {
        return view('gallery.drive', compact('folderId'));
    }

    private function extractDriveFolderId($url)
    {
        if (preg_match('/folders\/([a-zA-Z0-9-_]{25,})/', $url, $matches)) {
            return $matches[1];
        }
        if (preg_match('/id=([a-zA-Z0-9-_]{25,})/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
