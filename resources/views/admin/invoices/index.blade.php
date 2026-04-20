@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
        <h1 class="text-3xl font-bold text-gray-900">Daftar Invoice</h1>
        <a href="{{ route('admin.invoices.create') }}"
            class="flex items-center gap-2 rounded-lg bg-black px-4 py-2 font-bold text-white transition hover:bg-gray-800">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Invoice Baru
        </a>
    </div>

    <div class="mb-4 rounded-2xl border bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row">
            <div class="flex flex-1 flex-wrap gap-1.5">
                @php
                    $filters = [
                        'semua' => 'Semua',
                        'hari_ini' => 'Hari Ini',
                        'bulan_ini' => 'Bulan Ini',
                        'unpaid' => 'Belum Lunas',
                        'partial' => 'Partial',
                        'lunas' => 'Lunas',
                    ];
                @endphp
                @foreach ($filters as $key => $label)
                    <a href="{{ route('admin.invoices.index', array_merge(request()->except('filter'), ['filter' => $key])) }}"
                        class="{{ $filter === $key
                            ? 'border-black bg-black text-white'
                            : 'border-gray-200 bg-gray-50 text-gray-600 hover:bg-gray-100' }} whitespace-nowrap rounded-full border px-3 py-1.5 text-xs font-semibold transition-all">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.invoices.index') }}" class="relative">
                @foreach (request()->except('search') as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari no. invoice, nama, WA..."
                    class="w-60 rounded-lg border border-gray-200 py-2 pl-9 pr-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black">
                <svg class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </form>
        </div>
    </div>

    @if ($filter !== 'semua' || $search)
        <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Menampilkan:
            @if ($filter !== 'semua')
                <span
                    class="rounded-full bg-black px-2 py-0.5 text-xs font-semibold text-white">{{ $filters[$filter] ?? $filter }}</span>
            @endif
            @if ($search)
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700">Pencarian:
                    "{{ $search }}"</span>
            @endif
            <a href="{{ route('admin.invoices.index') }}" class="ml-1 text-gray-400 transition hover:text-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <!-- Desktop Table -->
        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-left">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="whitespace-nowrap px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">
                            <a href="{{ route('admin.invoices.index', array_merge(request()->except('sort', 'direction'), ['sort' => 'created_at', 'direction' => $sort === 'created_at' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                class="flex items-center gap-1 hover:text-gray-700">
                                No. Invoice
                                @if ($sort === 'created_at')
                                    <span class="text-gray-700">{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="whitespace-nowrap px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">
                            <a href="{{ route('admin.invoices.index', array_merge(request()->except('sort', 'direction'), ['sort' => 'customer_name', 'direction' => $sort === 'customer_name' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                class="flex items-center gap-1 hover:text-gray-700">
                                Pelanggan
                                @if ($sort === 'customer_name')
                                    <span class="text-gray-700">{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="whitespace-nowrap px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">
                            <a href="{{ route('admin.invoices.index', array_merge(request()->except('sort', 'direction'), ['sort' => 'invoice_date', 'direction' => $sort === 'invoice_date' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                class="flex items-center gap-1 hover:text-gray-700">
                                Tanggal
                                @if ($sort === 'invoice_date')
                                    <span class="text-gray-700">{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </a>
                        </th>
                        <th
                            class="whitespace-nowrap px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-gray-500">
                            <a href="{{ route('admin.invoices.index', array_merge(request()->except('sort', 'direction'), ['sort' => 'grand_total', 'direction' => $sort === 'grand_total' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                class="inline-flex items-center gap-1 hover:text-gray-700">
                                Total
                                @if ($sort === 'grand_total')
                                    <span class="text-gray-700">{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="whitespace-nowrap px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">
                            Status</th>
                        <th
                            class="whitespace-nowrap px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $invoice)
                        @php
                            $normalizedGrandTotal =
                                (float) $invoice->subtotal -
                                (float) $invoice->discount_amount +
                                (float) $invoice->tax_amount;
                            $normalizedBalanceDue = $normalizedGrandTotal - (float) $invoice->dp_amount;
                        @endphp
                        <tr class="transition hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4 font-mono text-sm font-bold text-blue-600">
                                {{ $invoice->invoice_number }}
                                @if ($invoice->booking)
                                    <div class="mt-1 text-[10px] uppercase text-gray-400">
                                        {{ $invoice->booking->business_unit }}</div>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $invoice->customer_name }}</div>
                                <div class="text-xs text-gray-500">{{ $invoice->customer_phone }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                {{ $invoice->invoice_date->format('d M Y') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right font-bold text-gray-900">
                                Rp {{ number_format($normalizedGrandTotal, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($normalizedBalanceDue <= 0)
                                    <span
                                        class="rounded bg-green-100 px-2 py-1 text-xs font-bold text-green-700">LUNAS</span>
                                @elseif($invoice->dp_amount > 0)
                                    <span
                                        class="rounded bg-yellow-100 px-2 py-1 text-xs font-bold text-yellow-700">PARTIAL</span>
                                @else
                                    <span
                                        class="rounded bg-gray-100 px-2 py-1 text-xs font-bold text-gray-600">UNPAID</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    @if ($normalizedBalanceDue > 0)
                                        <form action="{{ route('admin.invoices.markAsPaid', $invoice->id) }}" method="POST"
                                            class="mark-paid-form">
                                            @csrf
                                            <button type="button" onclick="confirmMarkPaid(this)"
                                                class="rounded-lg p-2 text-green-600 transition hover:bg-green-50"
                                                title="Tandai Lunas">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.invoices.edit', $invoice->id) }}"
                                        class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50" title="Edit">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.invoices.print', $invoice->id) }}?print=1" target="_blank"
                                        class="rounded-lg p-2 text-gray-600 transition hover:bg-gray-100"
                                        title="Cetak / Download">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                            </path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.invoices.destroy', $invoice->id) }}" method="POST"
                                        class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)"
                                            class="rounded-lg p-2 text-red-600 transition hover:bg-red-50" title="Hapus">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Belum ada invoice yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block divide-y divide-gray-100 md:hidden">
            <div class="flex items-center justify-between gap-2 bg-gray-50 px-4 py-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-gray-500">Urutkan:</span>
                    <select onchange="window.location.href=this.value"
                        class="rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-black">
                        <option
                            value="{{ route('admin.invoices.index', array_merge(request()->except('sort', 'direction'), ['sort' => 'invoice_date', 'direction' => $sort === 'invoice_date' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                            {{ $sort === 'invoice_date' ? 'selected' : '' }}>
                            Tanggal Invoice {{ $sort === 'invoice_date' ? ($direction === 'asc' ? '↑' : '↓') : '' }}
                        </option>
                        <option
                            value="{{ route('admin.invoices.index', array_merge(request()->except('sort', 'direction'), ['sort' => 'created_at', 'direction' => $sort === 'created_at' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                            {{ $sort === 'created_at' ? 'selected' : '' }}>
                            Tanggal Buat {{ $sort === 'created_at' ? ($direction === 'asc' ? '↑' : '↓') : '' }}
                        </option>
                        <option
                            value="{{ route('admin.invoices.index', array_merge(request()->except('sort', 'direction'), ['sort' => 'grand_total', 'direction' => $sort === 'grand_total' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                            {{ $sort === 'grand_total' ? 'selected' : '' }}>
                            Total {{ $sort === 'grand_total' ? ($direction === 'asc' ? '↑' : '↓') : '' }}
                        </option>
                    </select>
                </div>
                <a href="{{ route('admin.invoices.index', array_merge(request()->except('direction'), ['direction' => $direction === 'asc' ? 'desc' : 'asc'])) }}"
                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition hover:bg-gray-100"
                    title="{{ $direction === 'asc' ? 'Urutkan Terbalik' : 'Urutkan Normal' }}">
                    @if ($direction === 'asc')
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                        </svg>
                    @else
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                        </svg>
                    @endif
                </a>
            </div>
            @forelse($invoices as $invoice)
                @php
                    $normalizedGrandTotal =
                        (float) $invoice->subtotal - (float) $invoice->discount_amount + (float) $invoice->tax_amount;
                    $normalizedBalanceDue = $normalizedGrandTotal - (float) $invoice->dp_amount;
                @endphp
                <div class="space-y-3 p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="mb-1 font-mono text-xs font-bold text-blue-600">
                                {{ $invoice->invoice_number }}
                                @if ($invoice->booking)
                                    <span
                                        class="ml-1 font-sans uppercase text-gray-400">({{ $invoice->booking->business_unit }})</span>
                                @endif
                            </div>
                            <h3 class="font-bold text-gray-900">{{ $invoice->customer_name }}</h3>
                            <div class="text-xs text-gray-500">{{ $invoice->customer_phone }}</div>
                        </div>
                        <div class="text-right">
                            <div class="mb-1 text-xs text-gray-500">{{ $invoice->invoice_date->format('d/m/y') }}</div>
                            @if ($normalizedBalanceDue <= 0)
                                <span
                                    class="inline-block rounded bg-green-100 px-2 py-1 text-[10px] font-bold text-green-700">LUNAS</span>
                            @elseif($invoice->dp_amount > 0)
                                <span
                                    class="inline-block rounded bg-yellow-100 px-2 py-1 text-[10px] font-bold text-yellow-700">PARTIAL</span>
                            @else
                                <span
                                    class="inline-block rounded bg-gray-100 px-2 py-1 text-[10px] font-bold text-gray-600">UNPAID</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-gray-50 pt-2">
                        <div class="font-bold text-gray-900">
                            Rp {{ number_format($normalizedGrandTotal, 0, ',', '.') }}
                        </div>
                        <div class="flex gap-2">
                            @if ($normalizedBalanceDue > 0)
                                <form action="{{ route('admin.invoices.markAsPaid', $invoice->id) }}" method="POST"
                                    class="mark-paid-form">
                                    @csrf
                                    <button type="button" onclick="confirmMarkPaid(this)"
                                        class="rounded bg-green-50 p-1.5 text-green-600 hover:bg-green-100"
                                        title="Tandai Lunas">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.invoices.edit', $invoice->id) }}"
                                class="rounded bg-blue-50 p-1.5 text-blue-600 hover:bg-blue-100">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </a>
                            <a href="{{ route('admin.invoices.print', $invoice->id) }}?print=1" target="_blank"
                                class="rounded bg-gray-100 p-1.5 text-gray-600 hover:bg-gray-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                    </path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.invoices.destroy', $invoice->id) }}" method="POST"
                                class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete(this)"
                                    class="rounded bg-red-50 p-1.5 text-red-600 hover:bg-red-100">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">Belum ada invoice.</div>
            @endforelse
        </div>

        <div class="border-t border-gray-100 px-6 py-4">
            {{ $invoices->links() }}
        </div>
    </div>

    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Invoice yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            })
        }

        function confirmMarkPaid(button) {
            Swal.fire({
                title: 'Tandai Lunas?',
                text: "Invoice akan ditandai lunas. Jumlah DP akan diset ke total invoice.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Lunas!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            })
        }
    </script>
@endsection
