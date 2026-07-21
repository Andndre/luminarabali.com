@extends('layouts.dashboard')

@section('title', 'Bagikan — ' . $page->title)

@section('content')
    <div>
        <a href="{{ route('invitations.index') }}" class="dash-muted">&larr; Undangan Saya</a>
    </div>

    <div>
        <h1 class="dash-title">Bagikan Undangan</h1>
        <p class="dash-muted" style="margin-top: .35rem">{{ $page->title }}</p>
    </div>

    @if ($page->published_status !== 'published')
        <div class="dash-flash dash-flash--error">Undangan belum terbit — selesaikan pengisian dulu di Customizer.</div>
    @else
        @php $publicUrl = route('invitation.show', $page->slug); @endphp
        <div class="dash-card">
            <h2 style="font-size: 1.05rem; font-weight: 600">Link Undangan</h2>
            <div class="dash-row" style="margin-top: .75rem">
                <div class="dash-row__main" id="public-link" style="font-family: monospace; word-break: break-all">{{ $publicUrl }}</div>
                <button type="button" class="dash-btn dash-btn--ghost" id="copy-public-link" data-link="{{ $publicUrl }}">Salin</button>
            </div>
        </div>

        <div class="dash-card">
            <h2 style="font-size: 1.05rem; font-weight: 600">Link Personal (VIP) per Tamu</h2>
            <p class="dash-muted" style="margin-top: .35rem">Buat tautan bernama untuk tamu tertentu, lalu bagikan lewat WhatsApp.</p>
            <div style="margin-top: 1rem">
                <label class="dash-muted" style="display: block; margin-bottom: .35rem">Nama Tamu</label>
                <input type="text" id="guest-name" placeholder="Contoh: Bapak Jokowi"
                    style="width: 100%; padding: .6rem .75rem; border: 1px solid var(--dash-hair); border-radius: .5rem">
            </div>
            <div style="margin-top: .75rem">
                <div id="guest-link" class="dash-muted" style="font-family: monospace; word-break: break-all"></div>
            </div>
            <div style="margin-top: .75rem; display: flex; gap: .5rem">
                <button type="button" class="dash-btn dash-btn--ghost" id="copy-guest-link">Salin</button>
                <a href="#" id="wa-link" target="_blank" class="dash-btn dash-btn--solid" style="pointer-events: none; opacity: .5">Kirim via WhatsApp</a>
            </div>
        </div>
    @endif

    <script>
    (() => {
        const publicUrl = {{ Js::from($publicUrl ?? '') }};

        document.getElementById('copy-public-link')?.addEventListener('click', (e) => {
            navigator.clipboard.writeText(e.target.dataset.link);
        });

        const nameInput = document.getElementById('guest-name');
        const guestLinkEl = document.getElementById('guest-link');
        const copyGuestBtn = document.getElementById('copy-guest-link');
        const waLink = document.getElementById('wa-link');

        nameInput?.addEventListener('input', () => {
            const name = nameInput.value.trim();
            if (!name) {
                guestLinkEl.textContent = '';
                waLink.style.pointerEvents = 'none';
                waLink.style.opacity = '.5';
                return;
            }
            const link = `${publicUrl}?to=${encodeURIComponent(name)}`;
            guestLinkEl.textContent = link;
            const message = `Tanpa mengurangi rasa hormat, kami mengundang Bapak/Ibu/Saudara/i ${name} untuk hadir di acara pernikahan kami. Berikut tautan undangan Anda: \n\n${link}`;
            waLink.href = `https://wa.me/?text=${encodeURIComponent(message)}`;
            waLink.style.pointerEvents = 'auto';
            waLink.style.opacity = '1';
        });

        copyGuestBtn?.addEventListener('click', () => {
            if (guestLinkEl.textContent) navigator.clipboard.writeText(guestLinkEl.textContent);
        });
    })();
    </script>
@endsection
