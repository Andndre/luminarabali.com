@extends('layouts.dashboard')

@section('title', 'Ringkasan')

@section('content')
    <div>
        <h1 class="dash-title">Halo, {{ auth()->user()->name }}</h1>
        <p class="dash-muted" style="margin-top: .35rem">Ringkasan pesanan undangan Anda.</p>
    </div>

    <div class="dash-stats">
        <div class="dash-card">
            <div class="dash-stat__num">{{ $totalOrders }}</div>
            <div class="dash-stat__label">Total pesanan</div>
        </div>
        <div class="dash-card">
            <div class="dash-stat__num">{{ $pendingCount }}</div>
            <div class="dash-stat__label">Menunggu pembayaran</div>
        </div>
        <div class="dash-card dash-stat--accent">
            <div class="dash-stat__num">{{ $paidCount }}</div>
            <div class="dash-stat__label">Lunas</div>
        </div>
    </div>

    @if ($recentOrders->isNotEmpty())
        <div>
            <div class="dash-section-head" style="margin-bottom: .75rem">
                <h2>Pesanan terbaru</h2>
                <a href="{{ route('orders.index') }}" class="dash-muted">Lihat semua</a>
            </div>
            <div class="dash-rows">
                @foreach ($recentOrders as $order)
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
        </div>
    @else
        <div class="dash-empty">
            <p>Anda belum punya pesanan.</p>
            <a href="{{ route('catalog.index') }}" class="dash-btn dash-btn--solid">Pilih desain undangan</a>
        </div>
    @endif
@endsection
