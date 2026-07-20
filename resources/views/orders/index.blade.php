@extends('layouts.dashboard')

@section('title', 'Pesanan Saya')

@section('content')
    <div class="dash-section-head">
        <div>
            <h1 class="dash-title">Pesanan Saya</h1>
            <p class="dash-muted" style="margin-top: .35rem">Semua pesanan undangan Anda.</p>
        </div>
        <a href="{{ route('catalog.index') }}" class="dash-btn dash-btn--ghost">Jelajah katalog</a>
    </div>

    @if ($orders->isNotEmpty())
        <div class="dash-rows">
            @foreach ($orders as $order)
                <a href="{{ route('orders.show', $order) }}" class="dash-row">
                    <div>
                        <div class="dash-row__name">{{ $order->template?->name ?? 'Desain' }}</div>
                        <div class="dash-row__meta">{{ $order->order_number }}</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem">
                        <x-dash.status-pill :status="$order->status" :label="$order->statusLabel()" />
                        <span class="dash-row__price">{{ $order->priceLabel() }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="dash-empty">
            <p>Belum ada pesanan.</p>
            <a href="{{ route('catalog.index') }}" class="dash-btn dash-btn--solid">Pilih desain undangan</a>
        </div>
    @endif
@endsection
