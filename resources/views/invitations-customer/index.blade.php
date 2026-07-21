@extends('layouts.dashboard')

@section('title', 'Undangan Saya')

@section('content')
    <div>
        <h1 class="dash-title">Undangan Saya</h1>
        <p class="dash-muted" style="margin-top: .35rem">Undangan yang sudah dibuatkan dari pesanan Anda.</p>
    </div>

    @if ($invitations->isNotEmpty())
        <div class="dash-rows">
            @foreach ($invitations as $page)
                <div class="dash-row">
                    <div class="dash-row__main">
                        <div class="dash-row__name">{{ $page->title }}</div>
                        <div class="dash-row__meta">{{ $page->template?->name ?? 'Desain' }}</div>
                    </div>
                    <div class="dash-row__side" style="flex-wrap: wrap; gap: .4rem">
                        <x-dash.status-pill
                            :status="$page->published_status"
                            :label="$page->published_status === 'published' ? 'Terbit' : 'Draf'" />
                        <a href="{{ route('invitations.customizer.show', $page->id) }}" class="dash-btn dash-btn--solid">Isi Undangan</a>
                        <a href="{{ route('invitations.guests', $page->id) }}" class="dash-btn dash-btn--ghost">Daftar Tamu</a>
                        <a href="{{ route('invitations.share', $page->id) }}" class="dash-btn dash-btn--ghost">Bagikan</a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="dash-empty">
            <p>Anda belum punya undangan.</p>
            <a href="{{ route('catalog.index') }}" class="dash-btn dash-btn--solid">Pilih desain undangan</a>
        </div>
    @endif
@endsection
