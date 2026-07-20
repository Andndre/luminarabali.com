<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan {{ $order->order_number }} — Luminara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: system-ui, sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen p-6 md:p-10">
<div class="max-w-2xl mx-auto">
    <a href="{{ route('orders.index') }}" class="text-sm text-gray-600 hover:text-black">&larr; Pesanan Saya</a>

    @if (session('success'))
        <div class="mt-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mt-4 rounded-lg bg-red-100 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="mt-4 bg-white rounded-xl border p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $order->template?->name ?? 'Desain' }}</h1>
                <p class="text-xs text-gray-500 mt-1">{{ $order->order_number }}</p>
            </div>
            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">{{ $order->statusLabel() }}</span>
        </div>
        <p class="mt-4 text-2xl font-bold text-gray-900">{{ $order->priceLabel() }}</p>
    </div>

    @if ($order->status === \App\Models\Order::STATUS_PAID)
        <div class="mt-4 rounded-xl border border-green-200 bg-green-50 p-6 text-green-800">
            Pembayaran terkonfirmasi. Undangan Anda akan disiapkan.
        </div>
    @elseif ($order->status === \App\Models\Order::STATUS_CANCELLED)
        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-6 text-red-800">
            Pesanan dibatalkan.
        </div>
    @else
        <div class="mt-4 bg-white rounded-xl border p-6">
            <h2 class="font-semibold text-gray-900">Instruksi Pembayaran</h2>
            <p class="text-sm text-gray-500 mt-1">Transfer sejumlah <strong>{{ $order->priceLabel() }}</strong> ke salah satu rekening berikut, lalu unggah bukti.</p>

            <div class="mt-4 space-y-3">
                @forelse ($bankAccounts as $bank)
                    <div class="rounded-lg border p-3">
                        <p class="font-medium text-gray-900">{{ $bank->bank_name }}</p>
                        <p class="text-sm text-gray-700">{{ $bank->account_number }} &middot; a.n. {{ $bank->account_holder }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Rekening tujuan belum tersedia. Hubungi kami.</p>
                @endforelse
            </div>

            @if ($order->payment_proof_path)
                <div class="mt-4">
                    <p class="text-sm text-gray-500 mb-2">Bukti terunggah:</p>
                    <img src="{{ route('orders.proof.show', $order) }}" alt="Bukti transfer" class="max-h-64 rounded-lg border">
                </div>
            @endif

            @if ($order->canUploadProof())
                <form method="POST" action="{{ route('orders.proof.upload', $order) }}" enctype="multipart/form-data" class="mt-4">
                    @csrf
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $order->payment_proof_path ? 'Ganti bukti' : 'Unggah bukti transfer' }} (gambar, maks 5MB)</label>
                    <input type="file" name="bukti" accept="image/*" required class="block w-full text-sm">
                    @error('bukti') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    <button type="submit" class="mt-3 rounded-lg bg-black px-4 py-2 text-sm font-bold text-white hover:bg-gray-800">Kirim Bukti</button>
                </form>
            @endif
        </div>
    @endif
</div>
</body>
</html>
