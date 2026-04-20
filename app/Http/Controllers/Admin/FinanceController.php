<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        // Default to current month and year if not specified
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $period = $request->input('period', 'monthly'); // 'monthly' or 'yearly'

        $query = Invoice::query()->where('status', '!=', 'CANCELLED');

        if ($period === 'monthly') {
            $query->whereYear('invoice_date', $year)
                  ->whereMonth('invoice_date', $month);
        } else {
            $query->whereYear('invoice_date', $year);
        }

        $invoices = $query->latest('invoice_date')->get();

        // Calculations
        $totalRevenue = 0;
        $totalPotential = 0; // Unpaid/Receivable
        $totalTransactions = $invoices->count();
        $paidCount = 0;
        $partialCount = 0;

        foreach ($invoices as $invoice) {
            // Calculate actual money received
            $paidAmount = $invoice->grand_total - $invoice->balance_due;
            
            // If status is PAID, assume full amount is paid (double check against logic)
            // Ideally relying on grand_total - balance_due is safer.
            $totalRevenue += $paidAmount;
            $totalPotential += $invoice->balance_due;

            if ($invoice->status === \App\Models\Invoice::STATUS_LUNAS) {
                $paidCount++;
            } elseif ($invoice->status === \App\Models\Invoice::STATUS_DP_BAYAR) {
                $partialCount++;
            }
        }

        // Chart Data Preparation
        $chartData = $this->prepareChartData($invoices, $period, $year, $month);

        return view('admin.finance.index', compact(
            'invoices', 
            'totalRevenue', 
            'totalPotential', 
            'totalTransactions',
            'paidCount',
            'partialCount',
            'chartData',
            'year',
            'month',
            'period'
        ));
    }

    private function prepareChartData($invoices, $period, $year, $month)
    {
        // Initialize structure
        $data = [];
        $labels = [];
        
        if ($period === 'monthly') {
            // Daily breakdown for the selected month
            $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $labels[] = $i;
                $data[$i] = 0;
            }

            foreach ($invoices as $invoice) {
                $day = (int) $invoice->invoice_date->format('d');
                $paidAmount = $invoice->grand_total - $invoice->balance_due;
                if (isset($data[$day])) {
                    $data[$day] += $paidAmount;
                }
            }
        } else {
            // Monthly breakdown for the selected year
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            foreach ($months as $index => $m) {
                $labels[] = $m;
                $data[$index + 1] = 0;
            }

            foreach ($invoices as $invoice) {
                $m = (int) $invoice->invoice_date->format('m');
                $paidAmount = $invoice->grand_total - $invoice->balance_due;
                if (isset($data[$m])) {
                    $data[$m] += $paidAmount;
                }
            }
        }

        return [
            'labels' => array_values($labels), // Ensure indexed array for JS
            'data' => array_values($data)
        ];
    }
}
