<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\GoogleCalendarEvent;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected ?Client $client = null;
    protected ?Calendar $calendar = null;
    protected bool $enabled;

    public function __construct()
    {
        $this->enabled = config('google_calendar.enabled', false);

        if (!$this->enabled) {
            return;
        }

        try {
            $this->client = new Client();
            $this->client->setAuthConfig(base_path(config('google_calendar.service_account_json')));
            $this->client->addScope(Calendar::CALENDAR_EVENTS);
            $this->calendar = new Calendar($this->client);
        } catch (\Throwable $e) {
            Log::error('GoogleCalendarService: Gagal inisialisasi client', [
                'error' => $e->getMessage(),
            ]);
            $this->enabled = false;
        }
    }

    public function createEvent(Booking $booking): ?GoogleCalendarEvent
    {
        if (!$this->enabled || !$this->calendar) {
            Log::info('GoogleCalendarService: Integration disabled atau client tidak tersedia');
            return null;
        }

        if ($booking->googleCalendarEvent) {
            Log::info('GoogleCalendarService: Event sudah ada untuk booking', [
                'booking_id' => $booking->id,
            ]);
            return $booking->googleCalendarEvent;
        }

        $eventDate = Carbon::parse($booking->event_date->format('Y-m-d') . ' ' . $booking->event_time);
        $eventEnd = $eventDate->copy()->addHours((int) $booking->duration_hours);

        $summary = sprintf(
            '%s - %s | %s',
            strtoupper($booking->package_type ?? 'BOOKING'),
            $booking->customer_name,
            $booking->event_type
        );

        $location = $booking->event_location ?? null;
        $description = $this->buildDescription($booking);

        $googleEvent = new Event([
            'summary' => $summary,
            'location' => $location,
            'description' => $description,
            'start' => [
                'dateTime' => $eventDate->toRfc3339String(),
                'timeZone' => config('app.timezone', 'Asia/Makassar'),
            ],
            'end' => [
                'dateTime' => $eventEnd->toRfc3339String(),
                'timeZone' => config('app.timezone', 'Asia/Makassar'),
            ],
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'popup', 'minutes' => 60],
                    ['method' => 'popup', 'minutes' => 1440],
                ],
            ],
        ]);

        try {
            $createdEvent = $this->calendar->events->insert(
                config('google_calendar.calendar_id'),
                $googleEvent
            );

            return GoogleCalendarEvent::create([
                'booking_id' => $booking->id,
                'google_event_id' => $createdEvent->getId(),
                'calendar_id' => config('google_calendar.calendar_id'),
                'event_summary' => $summary,
                'event_start' => $eventDate->toDateTimeString(),
                'event_end' => $eventEnd->toDateTimeString(),
                'html_link' => $createdEvent->getHtmlLink(),
                'status' => $createdEvent->getStatus() ?? 'confirmed',
            ]);
        } catch (\Throwable $e) {
            Log::error('GoogleCalendarService: Gagal insert event', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected function buildDescription(Booking $booking): string
    {
        $parts = [];

        if ($booking->customer_phone) {
            $parts[] = "Phone: {$booking->customer_phone}";
        }

        if ($booking->customer_email) {
            $parts[] = "Email: {$booking->customer_email}";
        }

        if ($booking->package_name) {
            $parts[] = "Package: {$booking->package_name}";
        }

        if ($booking->duration_hours) {
            $parts[] = "Duration: {$booking->duration_hours} jam";
        }

        if ($booking->price_total) {
            $paymentInfo = $booking->status === 'LUNAS'
                ? 'LUNAS'
                : "DP {$booking->dp_amount} / {$booking->price_total}";
            $parts[] = "Payment: {$paymentInfo}";
        }

        if ($booking->notes) {
            $parts[] = "Notes: {$booking->notes}";
        }

        $parts[] = "Booking ID: {$booking->id}";

        return implode("\n", $parts);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}