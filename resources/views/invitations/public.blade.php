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

    @vite(['resources/css/app.css'])

    <!-- Tailwind CSS (CDN for dynamic DB templates) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Lato', sans-serif;
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

    @stack('rsvp_styles')
</head>

<body class="bg-white">
    @php
        $music = $page->meta_data['bg_music'] ?? '';
        $rsvpEnabled = $page->meta_data['rsvp_enabled'] ?? true;
    @endphp

    <script>
        window.invitationData = @json($page);
        // Default fallbacks for previewing
        window.invitationData.bride_name = window.invitationData.bride_name || 'Juliet';
        window.invitationData.groom_name = window.invitationData.groom_name || 'Romeo';
        window.invitationData.event_date = window.invitationData.event_date || '2026-12-12T08:00:00';
    </script>

    <x-invitation.layout class="bg-gray-50 @container" x-data="window.invitationData">
        <x-invitation.audio :src="$music" />

        @if (!empty($page->template->cover_content))
            {!! $page->template->cover_content !!}
        @else
            <x-invitation.cover :groom="$page->groom_name ?? 'Romeo'" :bride="$page->bride_name ?? 'Juliet'" :guest="request()->query('to', 'Tamu Spesial')"
                image="{{ $page->og_image ?? 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=2000&auto=format&fit=crop' }}" />
        @endif

        <div x-show="isOpen" style="display: none;" class="w-full">
            {!! $content ?? '' !!}
        </div>
    </x-invitation.layout>

    <!-- SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>

</html>
