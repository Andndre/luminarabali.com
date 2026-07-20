{{--
    Satu tempat kebenaran warna status pesanan. Label datang dari
    Order::statusLabel() supaya teks Indonesia tak diduplikasi di sini.
--}}
@props(['status', 'label'])

@php
    $variant = match ($status) {
        \App\Models\Order::STATUS_PENDING => 'pending',
        \App\Models\Order::STATUS_AWAITING => 'awaiting',
        \App\Models\Order::STATUS_PAID => 'paid',
        \App\Models\Order::STATUS_CANCELLED => 'cancelled',
        default => 'cancelled',
    };
@endphp

<span class="dash-pill dash-pill--{{ $variant }}">{{ $label }}</span>
