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
                    <div class="dash-row__side">
                        <x-dash.status-pill
                            :status="$page->published_status"
                            :label="$page->published_status === 'published' ? 'Terbit' : 'Draf'" />
                        @if ($page->published_status === 'published')
                            <a href="{{ route('invitation.show', $page->slug) }}" class="dash-btn dash-btn--ghost">Buka</a>
                        @endif
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
