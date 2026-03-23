<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=850, user-scalable=yes">
    <title>Invoice #{{ str_replace('/', '_', $invoice->invoice_number) }}_{{ $invoice->customer_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body class="relative overflow-x-auto bg-gray-100 p-8 font-sans text-gray-900">

    @if (session('success'))
        <div id="toast-success"
            class="no-print fixed left-1/2 top-4 z-50 flex -translate-x-1/2 transform items-center gap-2 rounded-full bg-green-600 px-6 py-3 text-white shadow-xl transition-all duration-500 print:hidden">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast-success');
                if (toast) {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 3000);
        </script>
    @endif

    @if (session('auto_print') || request()->has('print'))
        <script>
            window.onload = function() {
                setTimeout(() => {
                    window.print();
                    // window.close() removed to prevent mobile print errors
                }, 500);
            }
        </script>
    @endif

    <div class="relative mx-auto w-[800px] rounded-lg bg-white p-12 shadow-lg print:w-full print:p-0 print:shadow-none">

        <!-- Header -->
        <div class="mb-8 flex flex-row items-start justify-between border-b pb-8">
            <div>
                @php
                    $unitName =
                        $invoice->booking && $invoice->booking->business_unit == 'visual' ? 'Visual' : 'Photobooth';
                @endphp
                <div class="mb-4 flex items-center gap-3">
                    <img src="/images/Logo Luminara Visual-BLACK-TPR.png" alt="Luminara" class="h-12">
                    <div class="border-l-2 border-gray-300 pl-3">
                        <h2 class="text-xl font-bold leading-tight text-gray-900">Luminara</h2>
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">{{ $unitName }}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500">
                    Jl. Sultan Agung No.9X, Karangasem<br>
                    Bali, Indonesia 80811<br>
                    WhatsApp: 0877-8898-6136
                </p>
            </div>
            <div class="text-right">
                <h1 class="mb-2 text-4xl font-bold text-gray-900">INVOICE</h1>
                <p class="text-lg font-medium text-gray-600">#{{ $invoice->invoice_number }}</p>
                <p class="mt-1 text-sm text-gray-500">Tanggal: {{ $invoice->invoice_date->format('d F Y') }}</p>

                @if ($invoice->balance_due <= 0)
                    <div
                        class="mt-4 inline-block rounded border border-green-200 bg-green-100 px-4 py-1 text-sm font-bold uppercase tracking-wider text-green-700">
                        LUNAS
                    </div>
                @elseif($invoice->dp_amount > 0)
                    <div
                        class="mt-4 inline-block rounded border border-blue-200 bg-blue-100 px-4 py-1 text-sm font-bold uppercase tracking-wider text-blue-700">
                        DP DIBAYAR
                    </div>
                @else
                    <div
                        class="mt-4 inline-block rounded border border-yellow-200 bg-yellow-100 px-4 py-1 text-sm font-bold uppercase tracking-wider text-yellow-700">
                        BELUM LUNAS
                    </div>
                @endif
            </div>
        </div>

        <!-- Bill To -->
        <div class="mb-10">
            <h3 class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-400">Tagihan Kepada:</h3>
            <h2 class="text-xl font-bold text-gray-900">{{ $invoice->customer_name }}</h2>
            <p class="text-gray-600">{{ $invoice->customer_phone }}</p>
            @if ($invoice->customer_address)
                <p class="text-gray-600">{{ $invoice->customer_address }}</p>
            @elseif($invoice->booking && $invoice->booking->event_location)
                <p class="text-gray-600">{{ $invoice->booking->event_location }}</p>
            @endif
        </div>

        <!-- Details -->
        <div class="mb-10">
            <h3 class="mb-4 text-xs font-bold uppercase tracking-wider text-gray-400">Detail Layanan:</h3>
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-600">
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-3 text-right">Harga</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($invoice->items as $item)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-4 font-medium text-gray-900">
                                {{ $item->description }}
                                @if ($item->is_bonus)
                                    <span
                                        class="ml-2 rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-bold text-green-800">BONUS</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">{{ $item->quantity }}</td>
                            <td class="px-4 py-4 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-right font-bold">
                                @if ($item->is_bonus)
                                    FREE
                                @else
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right text-gray-600">Subtotal</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">Rp
                            {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if ($invoice->discount_amount > 0)
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right text-gray-600">Diskon
                                ({{ $invoice->discount_percent + 0 }}%)</td>
                            <td class="px-4 py-2 text-right text-red-600">- Rp
                                {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if ($invoice->tax_amount > 0)
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right text-gray-600">Pajak
                                ({{ $invoice->tax_percent + 0 }}%)</td>
                            <td class="px-4 py-2 text-right text-gray-900">Rp
                                {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr class="border-t border-gray-300">
                        <td colspan="3" class="px-4 py-4 text-right text-lg font-bold text-gray-900">Grand Total</td>
                        <td class="px-4 py-4 text-right text-lg font-bold text-gray-900">Rp
                            {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    @if ($invoice->dp_amount > 0)
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right text-gray-600">Uang Muka (DP)</td>
                            <td class="px-4 py-2 text-right font-bold text-blue-600">Rp
                                {{ number_format($invoice->dp_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-yellow-50">
                            <td colspan="3" class="px-4 py-3 text-right font-bold text-yellow-800">Sisa Tagihan</td>
                            <td class="px-4 py-3 text-right text-lg font-bold text-red-600">Rp
                                {{ number_format($invoice->balance_due, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </tfoot>
            </table>
        </div>

        <!-- Footer / Payment Info -->
        <div class="border-t pt-8 text-sm text-gray-600">
            <h4 class="mb-2 font-bold text-gray-900">Informasi Pembayaran</h4>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p>Bank BRI: <span class="font-mono font-bold text-black">460701039843530</span> (Ida Bagus Yudhi)
                    </p>
                    <p>SeaBank: <span class="font-mono font-bold text-black">901207048574</span></p>
                </div>
                <div class="text-right">
                    @php
                        $unit =
                            $invoice->booking && $invoice->booking->business_unit == 'visual' ? 'Visual' : 'Photobooth';
                    @endphp
                    <p class="italic text-gray-400">Terima kasih telah mempercayai Luminara {{ $unit }} untuk
                        mengabadikan momen berharga Anda.</p>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="no-print absolute right-4 top-4 flex gap-2 print:hidden">
            <a href="{{ route('admin.invoices.edit', $invoice->id) }}"
                class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    </path>
                </svg>
                Edit Invoice
            </a>
            <button onclick="window.print()"
                class="flex items-center gap-2 rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white shadow hover:bg-black">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                    </path>
                </svg>
                Cetak / PDF
            </button>
        </div>

    </div>

</body>

</html>
