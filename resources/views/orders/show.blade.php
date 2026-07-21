@extends('layouts.dashboard')

@section('title', 'Pesanan '.$order->order_number)

@section('content')
    <div>
        <a href="{{ route('orders.index') }}" class="dash-muted">&larr; Pesanan Saya</a>
    </div>

    <div class="dash-card">
        <div class="dash-section-head">
            <div>
                <h1 class="dash-title">{{ $order->template?->name ?? 'Desain' }}</h1>
                <p class="dash-row__meta">{{ $order->order_number }}</p>
            </div>
            <x-dash.status-pill :status="$order->status" :label="$order->statusLabel()" />
        </div>
        <p class="dash-stat__num" style="margin-top: 1rem">{{ $order->priceLabel() }}</p>
    </div>

    @if ($order->status === \App\Models\Order::STATUS_PAID)
        <div class="dash-flash dash-flash--success">Pembayaran terkonfirmasi.</div>

        @if ($order->invitationPage)
            <div class="dash-card">
                <h2 style="font-size: 1.05rem; font-weight: 600">Undangan Anda Siap</h2>
                <p class="dash-muted" style="margin-top: .35rem">Lanjut isi konten, kelola tamu, atau bagikan ke undangan.</p>
                <div style="margin-top: 1rem; display: flex; flex-wrap: wrap; gap: .5rem">
                    <a href="{{ route('invitations.customizer.show', $order->invitationPage->id) }}" class="dash-btn dash-btn--solid">Isi Undangan</a>
                    <a href="{{ route('invitations.guests', $order->invitationPage->id) }}" class="dash-btn dash-btn--ghost">Daftar Tamu</a>
                    <a href="{{ route('invitations.share', $order->invitationPage->id) }}" class="dash-btn dash-btn--ghost">Bagikan</a>
                </div>
            </div>
        @else
            <div class="dash-flash">Undangan Anda sedang disiapkan.</div>
        @endif
    @elseif ($order->status === \App\Models\Order::STATUS_CANCELLED)
        <div class="dash-flash dash-flash--error">Pesanan dibatalkan.</div>
    @else
        <div class="dash-card">
            <h2 style="font-size: 1.05rem; font-weight: 600">Instruksi Pembayaran</h2>
            <p class="dash-muted" style="margin-top: .35rem">
                Transfer sejumlah <strong>{{ $order->priceLabel() }}</strong> ke salah satu rekening berikut, lalu unggah bukti.
            </p>

            <div class="dash-rows" style="margin-top: 1rem">
                @forelse ($bankAccounts as $bank)
                    <div class="dash-row">
                        <div>
                            <div class="dash-row__name">{{ $bank->bank_name }}</div>
                            <div class="dash-row__meta">{{ $bank->account_number }} &middot; a.n. {{ $bank->account_holder }}</div>
                        </div>
                    </div>
                @empty
                    <p class="dash-muted">Rekening tujuan belum tersedia. Hubungi kami.</p>
                @endforelse
            </div>

            @if ($order->payment_proof_path)
                <div style="margin-top: 1.25rem">
                    <p class="dash-muted" style="margin-bottom: .5rem">Bukti terunggah:</p>
                    {{-- Bukti diserve lewat route berotorisasi (disk privat), bukan URL publik. --}}
                    <img src="{{ route('orders.proof.show', $order) }}" alt="Bukti transfer"
                         style="max-height: 16rem; border-radius: .6rem; border: 1px solid var(--dash-hair)">
                </div>
            @endif

            @if ($order->canUploadProof())
                <form method="POST" action="{{ route('orders.proof.upload', $order) }}"
                      enctype="multipart/form-data" style="margin-top: 1.25rem">
                    @csrf
                    <label class="dash-muted" style="display: block; margin-bottom: .4rem">
                        {{ $order->payment_proof_path ? 'Ganti bukti' : 'Unggah bukti transfer' }} (gambar, maks 5MB)
                    </label>
                    <input type="file" name="bukti" accept="image/*" required style="display: block; font-size: .875rem">
                    @error('bukti')
                        <p style="color: #991B1B; font-size: .78rem; margin-top: .35rem">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="dash-btn dash-btn--solid" style="margin-top: .85rem">Kirim Bukti</button>
                </form>
            @endif
        </div>
    @endif
@endsection
