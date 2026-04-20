@extends('layouts.admin')

@section('content')
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('admin.invoices.index') }}"
            class="rounded-lg border border-gray-200 bg-white p-2 transition hover:bg-gray-50">
            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Edit Invoice {{ $invoice->invoice_number }}</h1>
    </div>

    <form action="{{ route('admin.invoices.update', $invoice->id) }}" method="POST" x-data="invoiceEditor()">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Left Column: Details -->
            <div class="space-y-6 lg:col-span-2">

                <!-- Customer Info -->
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-bold text-gray-900">Informasi Pelanggan</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Nama</label>
                            <input type="text" name="customer_name" x-model="customer.name"
                                class="w-full rounded-lg border px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Telepon</label>
                            <input type="text" name="customer_phone" x-model="customer.phone"
                                class="w-full rounded-lg border px-3 py-2 text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700">Alamat (Opsional)</label>
                            <input type="text" name="customer_address" x-model="customer.address"
                                class="w-full rounded-lg border px-3 py-2 text-sm">
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-bold text-gray-900">Item Tagihan</h3>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex flex-col gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4 md:flex-row">
                                <div class="w-full flex-1">
                                    <label class="text-xs text-gray-500">Deskripsi</label>
                                    <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                                        class="w-full rounded-lg border px-3 py-2 text-sm" placeholder="Nama Item">
                                </div>
                                <div class="w-full md:w-20">
                                    <label class="text-xs text-gray-500">Qty</label>
                                    <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity"
                                        @input="calculateLine(index)" class="w-full rounded-lg border px-3 py-2 text-sm">
                                </div>
                                <div class="w-full md:w-32">
                                    <label class="text-xs text-gray-500">Harga</label>
                                    <input type="number" :name="`items[${index}][price]`" x-model="item.price"
                                        @input="calculateLine(index)" class="w-full rounded-lg border px-3 py-2 text-sm">
                                </div>
                                <div class="w-full md:w-32">
                                    <label class="text-xs text-gray-500">Total</label>
                                    <input type="number" :name="`items[${index}][total]`" x-model="item.total" readonly
                                        class="w-full rounded-lg border bg-gray-100 px-3 py-2 text-sm font-bold">
                                </div>
                                <div
                                    class="flex w-full flex-row items-center justify-end gap-4 pt-2 md:w-auto md:flex-col md:justify-center md:gap-0 md:pt-4">
                                    <label class="flex cursor-pointer items-center gap-1 text-xs">
                                        <input type="checkbox" :name="`items[${index}][is_bonus]`" x-model="item.is_bonus"
                                            @change="calculateLine(index)" class="rounded text-green-600">
                                        <span>Bonus</span>
                                    </label>
                                    <button type="button" @click="removeItem(index)"
                                        class="text-red-500 hover:text-red-700 md:mt-2">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addItem()"
                        class="mt-4 flex items-center gap-1 text-sm font-bold text-blue-600 hover:text-blue-800">
                        + Tambah Item
                    </button>
                </div>

            </div>

            <!-- Right Column: Summary -->
            <div class="sticky top-4 h-fit rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-gray-900">Ringkasan Pembayaran</h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <input type="hidden" name="subtotal" x-model="summary.subtotal">
                        <span class="font-bold" x-text="formatRupiah(summary.subtotal)"></span>
                    </div>

                    <div class="border-t border-dashed pt-3">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-gray-600">Diskon (%)</span>
                            <input type="number" name="discount_percent" x-model="summary.discount_percent"
                                @input="calculateSummary('percent')" class="w-20 rounded border px-2 py-1 text-right">
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Diskon (Rp)</span>
                            <input type="number" name="discount_amount" x-model="summary.discount_amount"
                                @input="calculateSummary('amount')" class="w-32 rounded border px-2 py-1 text-right">
                        </div>
                    </div>

                    <div class="border-t border-dashed pt-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Pajak / Fee (%)</span>
                            <input type="number" name="tax_percent" x-model="summary.tax_percent"
                                @input="calculateSummary()" class="w-20 rounded border px-2 py-1 text-right">
                        </div>
                        <!-- Hidden Tax Amount Calculation -->
                        <input type="hidden" name="tax_amount" x-model="summary.tax_amount">
                    </div>

                    <div class="flex items-center justify-between border-t border-gray-200 pt-3">
                        <span class="text-lg font-bold text-gray-900">Grand Total</span>
                        <input type="hidden" name="grand_total" x-model="summary.grand_total">
                        <span class="text-lg font-bold text-blue-600" x-text="formatRupiah(summary.grand_total)"></span>
                    </div>

                    <div class="mt-4 rounded-lg border border-yellow-100 bg-yellow-50 p-3">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="font-medium text-yellow-800">Uang Muka (DP)</span>
                            <input type="number" name="dp_amount" x-model="summary.dp_amount"
                                @input="calculateSummary()"
                                class="w-32 rounded border border-yellow-300 bg-white px-2 py-1 text-right">
                        </div>
                        <div class="flex items-center justify-between border-t border-yellow-200 pt-2">
                            <span class="font-bold text-yellow-900">Sisa Tagihan</span>
                            <input type="hidden" name="balance_due" x-model="summary.balance_due">
                            <span class="font-bold text-red-600" x-text="formatRupiah(summary.balance_due)"></span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 border-t pt-6">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Tanggal Invoice</label>
                    <input type="date" name="invoice_date" value="{{ $invoice->invoice_date->format('Y-m-d') }}"
                        class="mb-3 w-full rounded-lg border px-3 py-2 text-sm">

                    <label class="mb-1 block text-sm font-medium text-gray-700">Jatuh Tempo</label>
                    <input type="date" name="due_date"
                        value="{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '' }}"
                        class="w-full rounded-lg border px-3 py-2 text-sm">
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="w-full rounded-xl bg-black py-3 font-bold text-white shadow transition hover:bg-gray-800">
                        Simpan & Cetak
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        function invoiceEditor() {
            return {
                customer: {
                    name: '{{ $invoice->customer_name }}',
                    phone: '{{ $invoice->customer_phone }}',
                    address: '{{ $invoice->customer_address }}'
                },
                items: {!! $invoice->items->map(function ($item) {
                        return [
                            'description' => $item->description,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'total' => $item->total,
                            'is_bonus' => $item->is_bonus,
                        ];
                    })->toJson() !!},
                summary: {
                    subtotal: {{ $invoice->subtotal }},
                    discount_percent: {{ $invoice->discount_percent }},
                    discount_amount: {{ $invoice->discount_amount }},
                    tax_percent: {{ $invoice->tax_percent }},
                    tax_amount: {{ $invoice->tax_amount }},
                    grand_total: {{ $invoice->grand_total }},
                    dp_amount: {{ $invoice->dp_amount }},
                    balance_due: {{ $invoice->balance_due }}
                },
                init() {
                    this.calculateSummary();
                },
                addItem() {
                    this.items.push({
                        description: '',
                        quantity: 1,
                        price: 0,
                        total: 0,
                        is_bonus: false
                    });
                },
                removeItem(index) {
                    this.items = this.items.filter((_, i) => i !== index);
                    this.calculateSummary();
                },
                calculateLine(index) {
                    const item = this.items[index];
                    if (item.is_bonus) {
                        item.total = 0;
                    } else {
                        item.total = item.quantity * item.price;
                    }
                    this.calculateSummary();
                },
                calculateSummary(source = 'auto') {
                    // 1. Subtotal
                    this.summary.subtotal = this.items.reduce((sum, item) => sum + parseFloat(item.total), 0);

                    // 2. Discount Logic
                    if (source === 'amount') {
                        // If typing Rp, just use it. Don't calculate %.
                        // We reset % to 0 to indicate fixed amount mode is active
                        this.summary.discount_percent = 0;
                    } else if (source === 'percent') {
                        // If typing %, calculate the amount
                        this.summary.discount_amount = (this.summary.subtotal * this.summary.discount_percent) / 100;
                    } else {
                        // Auto (e.g. item change): Keep the logic consistent based on what's non-zero
                        if (this.summary.discount_percent > 0) {
                            this.summary.discount_amount = (this.summary.subtotal * this.summary.discount_percent) / 100;
                        }
                        // If percent is 0 but amount > 0, assume fixed amount and do nothing (keep amount as is)
                    }

                    const afterDiscount = this.summary.subtotal - this.summary.discount_amount;

                    // 3. Tax
                    this.summary.tax_amount = (afterDiscount * this.summary.tax_percent) / 100;

                    // 4. Grand Total
                    this.summary.grand_total = afterDiscount + this.summary.tax_amount;

                    // 5. Balance Due
                    this.summary.balance_due = this.summary.grand_total - this.summary.dp_amount;
                },
                formatRupiah(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(value);
                }
            }
        }
    </script>
@endsection
