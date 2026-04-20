<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Invoice;
use Illuminate\Console\Command;

class SyncInvoiceBookingStatuses extends Command
{
    protected $signature = 'invoices:sync-statuses {--dry-run : Show changes without applying them}';

    protected $description = 'Sync invoice.status from booking.status for existing records with booking_id';

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

            if ($booking->status === 'LUNAS') {
                $newStatus = Invoice::STATUS_LUNAS;
            } elseif ($booking->status === 'DP_BAYAR') {
                $newStatus = Invoice::STATUS_DP_BAYAR;
            } else {
                $newStatus = Invoice::STATUS_PENDING;
            }

            if ($invoice->status === $newStatus) {
                $skipped++;
                continue;
            }

            $table[] = [
                $invoice->id,
                $booking->id,
                $invoice->status,
                $newStatus,
            ];

            if (! $dryRun) {
                $invoice->update(['status' => $newStatus]);
            }

            $synced++;
        }

        if ($dryRun && count($table) > 0) {
            $this->info('DRY RUN — these records would be updated:');
            $this->table(['invoice_id', 'booking_id', 'current_status', 'new_status'], $table);
            $this->info("Found {$synced} records to sync, {$skipped} already in sync.");
        } elseif ($synced > 0) {
            $this->info("Synced {$synced} records. {$skipped} already in sync.");
        } else {
            $this->info("All {$skipped} records already in sync.");
        }

        return self::SUCCESS;
    }
}
