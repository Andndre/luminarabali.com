@extends('layouts.admin')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Keuangan</h1>
            <p class="text-gray-500">Laporan pendapatan dan riwayat transaksi.</p>
        </div>
        
        <!-- Filter Form -->
        <form action="{{ route('admin.finance.index') }}" method="GET" class="flex flex-wrap gap-2 bg-white p-2 rounded-lg shadow-sm border border-gray-100">
            <select name="period" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-md focus:ring-yellow-500 focus:border-yellow-500 block p-2">
                <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Tahunan</option>
            </select>

            @if($period == 'monthly')
                <select name="month" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-md focus:ring-yellow-500 focus:border-yellow-500 block p-2">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            @endif

            <select name="year" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-md focus:ring-yellow-500 focus:border-yellow-500 block p-2">
                @foreach(range(now()->year, now()->year - 5) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-100 text-green-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Pendapatan (Cash In)</p>
                    <h3 class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                    <p class="text-xs text-green-600 font-medium flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        Dari {{ $paidCount + $partialCount }} transaksi aktif
                    </p>
                </div>
            </div>
        </div>

        <!-- Receivables -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-yellow-100 text-yellow-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Potensi / Piutang</p>
                    <h3 class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalPotential, 0, ',', '.') }}</h3>
                    <p class="text-xs text-yellow-600 font-medium flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                        Belum dibayarkan
                    </p>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 text-blue-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Status Pembayaran</p>
                    <div class="flex gap-3 mt-1">
                        <div class="text-center">
                            <span class="block text-lg font-bold text-green-600">{{ $paidCount }}</span>
                            <span class="text-[10px] text-gray-400 uppercase">Lunas</span>
                        </div>
                        <div class="w-px bg-gray-200"></div>
                        <div class="text-center">
                            <span class="block text-lg font-bold text-blue-600">{{ $partialCount }}</span>
                            <span class="text-[10px] text-gray-400 uppercase">Partial</span>
                        </div>
                        <div class="w-px bg-gray-200"></div>
                        <div class="text-center">
                            <span class="block text-lg font-bold text-gray-600">{{ $totalTransactions - $paidCount - $partialCount }}</span>
                            <span class="text-[10px] text-gray-400 uppercase">Unpaid</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Table Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Grafik Pendapatan ({{ $period == 'monthly' ? \Carbon\Carbon::create()->month($month)->translatedFormat('F') : 'Tahun' }} {{ $year }})</h3>
            <div id="revenueChart" class="w-full h-80"></div>
        </div>

        <!-- Mini Insights or Top Packages (Placeholder for now) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Distribusi Status</h3>
            <div id="statusChart" class="w-full h-64 flex items-center justify-center"></div>
        </div>
    </div>

    <!-- Transaction History Table -->
    <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Riwayat Transaksi</h3>
        </div>
        
        <!-- Desktop View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-4">Invoice</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Pelanggan</th>
                        <th class="px-6 py-4 text-right">Total Tagihan</th>
                        <th class="px-6 py-4 text-right">Sudah Bayar</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="text-blue-600 hover:underline">
                                {{ $invoice->invoice_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $invoice->invoice_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ Str::limit($invoice->customer_name, 20) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 text-right">
                            Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-green-600 font-bold text-right">
                            Rp {{ number_format($invoice->grand_total - $invoice->balance_due, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-bold 
                                {{ $invoice->status == 'LUNAS' ? 'bg-green-100 text-green-800' : 
                                  ($invoice->status == 'DP_BAYAR' ? 'bg-blue-100 text-blue-800' : 
                                  'bg-gray-100 text-gray-800') }}">
                                {{ $invoice->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada data transaksi untuk periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile View -->
        <div class="block md:hidden divide-y divide-gray-100">
            @forelse($invoices as $invoice)
            <div class="p-4 space-y-3">
                <div class="flex justify-between items-start">
                    <div>
                        <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="text-sm font-bold text-blue-600 hover:underline block mb-1">
                            {{ $invoice->invoice_number }}
                        </a>
                        <div class="text-xs text-gray-500">{{ $invoice->invoice_date->format('d/m/Y') }}</div>
                    </div>
                    <div>
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold 
                            {{ $invoice->status == 'LUNAS' ? 'bg-green-100 text-green-800' : 
                              ($invoice->status == 'DP_BAYAR' ? 'bg-blue-100 text-blue-800' : 
                              'bg-gray-100 text-gray-800') }}">
                            {{ $invoice->status }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-bold text-gray-900">{{ $invoice->customer_name }}</h3>
                </div>

                <div class="flex justify-between items-end pt-2 border-t border-gray-50">
                    <div>
                        <div class="text-[10px] text-gray-500">Total Tagihan</div>
                        <div class="text-sm font-bold text-gray-900">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] text-gray-500">Sudah Bayar</div>
                        <div class="text-sm font-bold text-green-600">Rp {{ number_format($invoice->grand_total - $invoice->balance_due, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">Tidak ada data transaksi.</div>
            @endforelse
        </div>
    </div>

    <!-- ApexCharts Script -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Revenue Chart
            const options = {
                series: [{
                    name: 'Pendapatan',
                    data: @json($chartData['data'])
                }],
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: { show: false },
                    fontFamily: 'Plus Jakarta Sans, sans-serif'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '40%',
                    }
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: {
                    categories: @json($chartData['labels']),
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return "Rp " + new Intl.NumberFormat('id-ID', { notation: "compact" }).format(value);
                        }
                    }
                },
                fill: { opacity: 1, colors: ['#EAB308'] }, // Yellow-500
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return "Rp " + new Intl.NumberFormat('id-ID').format(val)
                        }
                    }
                },
                colors: ['#EAB308']
            };

            const chart = new ApexCharts(document.querySelector("#revenueChart"), options);
            chart.render();

            // Status Pie Chart
            const statusOptions = {
                series: [{{ $paidCount }}, {{ $partialCount }}, {{ $totalTransactions - $paidCount - $partialCount }}],
                chart: {
                    type: 'donut',
                    height: 250,
                    fontFamily: 'Plus Jakarta Sans, sans-serif'
                },
                labels: ['Lunas', 'Partial/DP', 'Unpaid'],
                colors: ['#22c55e', '#3b82f6', '#9ca3af'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%'
                        }
                    }
                },
                dataLabels: { enabled: false },
                legend: { position: 'bottom' }
            };

            const statusChart = new ApexCharts(document.querySelector("#statusChart"), statusOptions);
            statusChart.render();
        });
    </script>
@endsection
