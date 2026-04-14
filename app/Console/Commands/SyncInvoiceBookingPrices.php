<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Invoice;
use Illuminate\Console\Command;

class SyncInvoiceBookingPrices extends Command
{
    protected $signature = 'invoices:sync-booking-prices {--dry-run : Show changes without applying them}';

    protected $description = 'Sync invoices.grand_total to bookings.price_total for existing records';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $invoices = Invoice::with('booking')
            ->whereNotNull('booking_id')
            ->whereHas('booking')
            ->get();

        $synced = 0;
        $skipped = 0;

        $table = [];

        foreach ($invoices as $invoice) {
            $booking = $invoice->booking;

            if ((string) $booking->price_total === (string) $invoice->grand_total) {
                $skipped++;
                continue;
            }

            $table[] = [
                $invoice->id,
                $booking->id,
                number_format($booking->price_total, 2),
                number_format($invoice->grand_total, 2),
            ];

            if (! $dryRun) {
                $booking->update(['price_total' => $invoice->grand_total]);
            }

            $synced++;
        }

        if ($dryRun && count($table) > 0) {
            $this->info('DRY RUN — these records would be updated:');
            $this->table(['invoice_id', 'booking_id', 'current_price_total', 'invoice_grand_total'], $table);
            $this->info("Found {$synced} records to sync, {$skipped} already in sync.");
        } elseif ($synced > 0) {
            $this->info("Synced {$synced} records. {$skipped} already in sync.");
        } else {
            $this->info("All {$skipped} records already in sync.");
        }

        return self::SUCCESS;
    }
}
