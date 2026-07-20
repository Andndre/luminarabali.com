<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>{{ $page->title ?? 'Wedding Invitation' }}</title>
    <meta name="description"
        content="{{ $page->meta_description ?? $page->groom_name . ' & ' . $page->bride_name . ' - Undangan Pernikahan Digital' }}">
    <meta name="robots" content="index, follow">
    <meta name="author" content="{{ $page->groom_name ?? '' }} & {{ $page->bride_name ?? '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:title" content="{{ $page->title ?? 'Wedding Invitation' }}">
    <meta property="og:description"
        content="{{ $page->meta_description ?? $page->groom_name . ' & ' . $page->bride_name . ' - Undangan Pernikahan Digital' }}">
    <meta property="og:image" content="{{ $page->og_image ?? asset('/images/logo.png') }}">
    <meta property="og:site_name" content="Luminara Bali">
    <meta property="og:locale" content="id_ID">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $page->title ?? 'Wedding Invitation' }}">
    <meta name="twitter:description"
        content="{{ $page->meta_description ?? $page->groom_name . ' & ' . $page->bride_name . ' - Undangan Pernikahan Digital' }}">
    <meta name="twitter:image" content="{{ $page->og_image ?? asset('/images/logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/invitation.css', 'resources/js/invitation.js'])

    <style>
        /* Jangan taruh reset universal di sini: blok ini tanpa layer, jadi mengalahkan
           semua utility Tailwind. Preflight sudah melakukannya di @layer base. */

        body {
            /* Fallback, bukan patokan: --font-body datang dari theme template ($themeStyle,
               dirender setelah blok ini) dan harus menang. */
            font-family: var(--font-body, 'Lato'), sans-serif;
            overflow-x: hidden;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #d4af37;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #c9a227;
        }
    </style>

    @if (!empty($page->template->global_custom_css))
        <style>
            {!! $page->template->global_custom_css !!}
        </style>
    @endif

    {!! $themeStyle ?? '' !!}
</head>

<body class="bg-white">
    @php
        $rsvpEnabled = $page->meta_data['rsvp_enabled'] ?? true;
        $page->sections->each(fn ($section) => $section->makeHidden('props'));

        // $content is passed in as a deferred closure (see InvitationViewController)
        // so the section tree renders nested inside this view's own top-level
        // render pass, keeping @push('scripts') content from section partials
        // alive through to @stack('scripts') below.
        $content = is_callable($content ?? null) ? $content() : ($content ?? '');
    @endphp

    <script>
        window.invitationData = @json($page);
        // Default fallbacks for previewing
        window.invitationData.bride_name = window.invitationData.bride_name || 'Juliet';
        window.invitationData.groom_name = window.invitationData.groom_name || 'Romeo';
        window.invitationData.event_date = window.invitationData.event_date || '2026-12-12T08:00:00';
        window.invitationData.guest_name = new URLSearchParams(window.location.search).get('to') || 'Tamu Spesial';
    </script>

    <x-invitation.layout :page="$page" :cover-image="$coverImage ?? null" :skip-cover="!empty($studioMode)" class="bg-gray-50 @container">
        {!! $content ?? '' !!}
    </x-invitation.layout>

    @stack('scripts')

    {{-- Animasi entrance per-section (props animation/animation_delay via _section-shell) --}}
    <style>
        [data-animate] { opacity: 0; }
        [data-animate].anim-in { animation-duration: .8s; animation-timing-function: ease-out; animation-fill-mode: forwards; }
        [data-animate="fade-up"].anim-in { animation-name: anim-fade-up; }
        [data-animate="fade-in"].anim-in { animation-name: anim-fade-in; }
        [data-animate="zoom-in"].anim-in { animation-name: anim-zoom-in; }
        [data-animate="slide-left"].anim-in { animation-name: anim-slide-left; }
        [data-animate="slide-right"].anim-in { animation-name: anim-slide-right; }
        @keyframes anim-fade-up { from { opacity: 0; transform: translateY(32px); } to { opacity: 1; transform: none; } }
        @keyframes anim-fade-in { from { opacity: 0; } to { opacity: 1; } }
        @keyframes anim-zoom-in { from { opacity: 0; transform: scale(.92); } to { opacity: 1; transform: none; } }
        @keyframes anim-slide-left { from { opacity: 0; transform: translateX(48px); } to { opacity: 1; transform: none; } }
        @keyframes anim-slide-right { from { opacity: 0; transform: translateX(-48px); } to { opacity: 1; transform: none; } }
    </style>
    <script>
        (function () {
            const observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    const el = entry.target;
                    el.style.animationDelay = (parseInt(el.dataset.animateDelay, 10) || 0) + 'ms';
                    el.classList.add('anim-in');
                    observer.unobserve(el);
                });
            }, { threshold: 0.15 });
            const scan = function () {
                document.querySelectorAll('[data-animate]:not(.anim-in)').forEach(el => observer.observe(el));
            };
            scan();
            // Fragment swap di Studio menyisipkan node baru — pantau supaya tetap teranimasi.
            new MutationObserver(scan).observe(document.body, { childList: true, subtree: true });
        })();
    </script>

    @if (!empty($studioMode))
        {{-- Mode Studio (iframe editor): klik = pilih section, dblclick = edit teks inline --}}
        <style>
            [data-section-id] > *:hover { outline: 2px dashed rgba(0, 0, 0, .25); outline-offset: -2px; }
            [data-editable][contenteditable="true"] { outline: 2px solid #3b82f6; outline-offset: 2px; }
        </style>
        <script>
            (function () {
                const origin = window.location.origin;

                document.addEventListener('click', function (e) {
                    const el = e.target.closest('[data-section-id]');
                    if (!el) return;
                    window.parent.postMessage({ type: 'studio:select', id: el.dataset.sectionId }, origin);
                });

                document.addEventListener('dblclick', function (e) {
                    const el = e.target.closest('[data-editable]');
                    if (!el) return;
                    el.contentEditable = true;
                    el.focus();
                    el.addEventListener('blur', function () {
                        el.contentEditable = false;
                        const wrapper = el.closest('[data-section-id]');
                        if (!wrapper) return;
                        window.parent.postMessage({
                            type: 'studio:edit',
                            id: wrapper.dataset.sectionId,
                            key: el.dataset.editable,
                            value: el.textContent.trim(),
                        }, origin);
                    }, { once: true });
                    el.addEventListener('keydown', function (ev) {
                        if (ev.key === 'Enter') { ev.preventDefault(); el.blur(); }
                    });
                });
            })();
        </script>
    @endif
</body>

</html>
