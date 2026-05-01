<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Services\GoogleCalendarService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SyncBookingToGoogleCalendar implements ShouldQueue
{
    public function __construct(
        protected GoogleCalendarService $calendarService
    ) {
    }

    public function handle(BookingCreated $event): void
    {
        $result = $this->calendarService->createEvent($event->booking);

        if ($result) {
            Log::info('GoogleCalendar: Event berhasil dibuat', [
                'booking_id' => $event->booking->id,
                'google_event_id' => $result->google_event_id,
            ]);
        } else {
            Log::warning('GoogleCalendar: Event gagal dibuat', [
                'booking_id' => $event->booking->id,
            ]);
        }
    }
}
