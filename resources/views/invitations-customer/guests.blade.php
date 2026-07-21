@extends('layouts.dashboard')

@section('title', 'Daftar Tamu — ' . $page->title)

@section('content')
    <div>
        <a href="{{ route('invitations.index') }}" class="dash-muted">&larr; Undangan Saya</a>
    </div>

    <div>
        <h1 class="dash-title">Daftar Tamu</h1>
        <p class="dash-muted" style="margin-top: .35rem">{{ $page->title }} &middot; {{ $responses->count() }} respons</p>
    </div>

    @if ($responses->isEmpty())
        <div class="dash-empty">
            <p>Belum ada tamu yang mengisi RSVP.</p>
        </div>
    @else
        <div class="dash-rows" id="guest-rows">
            @foreach ($responses as $response)
                <div class="dash-row" data-rsvp-id="{{ $response->id }}" style="align-items: flex-start">
                    <div class="dash-row__main">
                        <div class="dash-row__name">{{ $response->guest_name }}</div>
                        <div class="dash-row__meta">
                            {{ $response->attendance_status === 'hadir' ? 'Hadir' : 'Tidak hadir' }}
                            &middot; {{ $response->number_of_guests }} orang
                        </div>
                        @if ($response->message)
                            <p class="dash-muted" style="margin-top: .35rem">{{ $response->message }}</p>
                        @endif
                    </div>
                    <div class="dash-row__side">
                        <button type="button" class="dash-btn dash-btn--ghost toggle-hidden-btn"
                            data-url="{{ route('invitations.guests.toggle-hidden', [$page->id, $response->id]) }}">
                            {{ $response->is_hidden ? 'Tampilkan' : 'Sembunyikan' }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <script>
    (() => {
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        document.querySelectorAll('.toggle-hidden-btn').forEach((btn) => {
            btn.addEventListener('click', async () => {
                btn.disabled = true;
                try {
                    const res = await fetch(btn.dataset.url, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': csrf } });
                    const data = await res.json();
                    btn.textContent = data.is_hidden ? 'Tampilkan' : 'Sembunyikan';
                } finally {
                    btn.disabled = false;
                }
            });
        });
    })();
    </script>
@endsection
