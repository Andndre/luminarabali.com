<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\GoogleCalendarService;
use Illuminate\Console\Command;

class SyncBookingsToGoogleCalendar extends Command
{
    protected $signature = 'calendar:sync {--dry-run : Show what would be synced without making changes}';
    protected $description = 'Sync all existing bookings to Google Calendar';

    public function handle(GoogleCalendarService $calendarService): int
    {
        if (! $calendarService->isEnabled()) {
            $this->error('Google Calendar integration is disabled. Set GOOGLE_CALENDAR_ENABLED=true in .env');
            return self::FAILURE;
        }

        $dryRun = $this->option('dry-run');

        $query = Booking::whereDoesntHave('googleCalendarEvent')
            ->orderBy('event_date', 'asc');

        $bookings = $query->get();
        $total = $bookings->count();

        if ($total === 0) {
            $this->info('Semua booking sudah tersync atau tidak ada booking.');
            return self::SUCCESS;
        }

        $this->info("Menemukan {$total} booking yang perlu di-sync.");

        if ($dryRun) {
            $this->info("Dry run - tidak ada perubahan yang dibuat.");
            $this->table(
                ['ID', 'Customer', 'Event Date', 'Package'],
                $bookings->map(fn ($b) => [
                    $b->id,
                    $b->customer_name,
                    $b->event_date->format('d M Y'),
                    $b->package_name ?? '-',
                ])
            );
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $success = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($bookings as $booking) {
            $result = $calendarService->createEvent($booking);

            if ($result) {
                $success++;
            } elseif ($booking->googleCalendarEvent) {
                $skipped++;
            } else {
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Sync selesai:");
        $this->line("  - Berhasil: {$success}");
        $this->line("  - Skip (sudah ada): {$skipped}");
        $this->line("  - Gagal: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}