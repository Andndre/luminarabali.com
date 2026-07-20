@extends('layouts.admin')

@section('content')
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-600 hover:text-black">&larr; Semua pesanan</a>

    <div class="mt-4 grid gap-6 md:grid-cols-2">
        <div class="rounded-2xl border bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-bold text-gray-900">{{ $order->order_number }}</h1>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Customer</dt><dd>{{ $order->user?->name }} ({{ $order->user?->email }})</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Desain</dt><dd>{{ $order->template?->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Harga</dt><dd>{{ $order->priceLabel() }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Status</dt><dd>{{ $order->statusLabel() }}</dd></div>
                @if ($order->confirmedBy)
                    <div class="flex justify-between"><dt class="text-gray-500">Dikonfirmasi oleh</dt><dd>{{ $order->confirmedBy->name }}</dd></div>
                @endif
            </dl>

            @if (! in_array($order->status, [\App\Models\Order::STATUS_PAID, \App\Models\Order::STATUS_CANCELLED], true))
                <div class="mt-6 flex gap-2">
                    <form method="POST" action="{{ route('admin.orders.confirm', $order) }}">
                        @csrf
                        <button class="rounded-lg bg-green-600 px-4 py-2 text-sm font-bold text-white hover:bg-green-700">Konfirmasi Lunas</button>
                    </form>
                    <form method="POST" action="{{ route('admin.orders.cancel', $order) }}">
                        @csrf
                        <button class="rounded-lg border border-red-300 px-4 py-2 text-sm font-bold text-red-600 hover:bg-red-50">Batalkan</button>
                    </form>
                </div>
            @endif
        </div>

        <div class="rounded-2xl border bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-gray-900">Bukti Transfer</h2>
            @if ($order->payment_proof_path)
                <img src="{{ route('orders.proof.show', $order) }}" alt="Bukti transfer" class="mt-3 max-h-96 rounded-lg border">
            @else
                <p class="mt-3 text-sm text-gray-500">Belum ada bukti diunggah.</p>
            @endif
        </div>
    </div>
@endsection
