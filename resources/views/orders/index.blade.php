<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan Saya — Luminara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: system-ui, sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen p-6 md:p-10">
<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pesanan Saya</h1>
        <a href="{{ route('catalog.index') }}" class="text-sm text-gray-600 hover:text-black">Jelajah katalog</a>
    </div>

    @forelse ($orders as $order)
        <a href="{{ route('orders.show', $order) }}"
           class="block bg-white rounded-xl border p-4 mb-3 hover:border-gray-400 transition">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-gray-900">{{ $order->template?->name ?? 'Desain' }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $order->order_number }}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-gray-900">{{ $order->priceLabel() }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $order->statusLabel() }}</p>
                </div>
            </div>
        </a>
    @empty
        <div class="bg-white rounded-xl border p-8 text-center text-gray-500">
            Belum ada pesanan. <a href="{{ route('catalog.index') }}" class="text-black underline">Pilih desain</a>.
        </div>
    @endforelse
</div>
</body>
</html>
