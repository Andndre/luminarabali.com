@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
        <h1 class="text-3xl font-bold text-gray-900">Pesanan Undangan</h1>
        <div class="flex flex-wrap gap-2 text-sm">
            <a href="{{ route('admin.orders.index') }}" class="rounded-lg border px-3 py-1 {{ $status ? '' : 'bg-black text-white' }}">Semua</a>
            @foreach (\App\Models\Order::STATUSES as $s)
                <a href="{{ route('admin.orders.index', ['status' => $s]) }}"
                   class="rounded-lg border px-3 py-1 {{ $status === $s ? 'bg-black text-white' : '' }}">{{ ucfirst(str_replace('_', ' ', $s)) }}</a>
            @endforeach
        </div>
    </div>

    <div class="overflow-x-auto rounded-2xl border bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-4 py-3">No. Order</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Desain</th>
                    <th class="px-4 py-3">Harga</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($orders as $order)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $order->order_number }}</td>
                        <td class="px-4 py-3">{{ $order->user?->name }}</td>
                        <td class="px-4 py-3">{{ $order->template?->name }}</td>
                        <td class="px-4 py-3">{{ $order->priceLabel() }}</td>
                        <td class="px-4 py-3">{{ $order->statusLabel() }}</td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:underline">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
