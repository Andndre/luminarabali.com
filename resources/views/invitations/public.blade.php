<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>{{ $page->title ?? 'Wedding Invitation' }}</title>
    <meta name="description" content="{{ $page->meta_description ?? ($page->groom_name . ' & ' . $page->bride_name . ' - Undangan Pernikahan Digital') }}">
    <meta name="robots" content="index, follow">
    <meta name="author" content="{{ $page->groom_name ?? '' }} & {{ $page->bride_name ?? '' }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:title" content="{{ $page->title ?? 'Wedding Invitation' }}">
    <meta property="og:description" content="{{ $page->meta_description ?? ($page->groom_name . ' & ' . $page->bride_name . ' - Undangan Pernikahan Digital') }}">
    <meta property="og:image" content="{{ $page->og_image ?? asset('/images/Logo Luminara Visual-BLACK-TPR.png') }}">
    <meta property="og:site_name" content="Luminara Group Bali">
    <meta property="og:locale" content="id_ID">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $page->title ?? 'Wedding Invitation' }}">
    <meta name="twitter:description" content="{{ $page->meta_description ?? ($page->groom_name . ' & ' . $page->bride_name . ' - Undangan Pernikahan Digital') }}">
    <meta name="twitter:image" content="{{ $page->og_image ?? asset('/images/Logo Luminara Visual-BLACK-TPR.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

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

    @if (!empty(data_get($page->meta_data, 'global_custom_css')))
        <style>
            {!! data_get($page->meta_data, 'global_custom_css') !!}
        </style>
    @endif

    @stack('rsvp_styles')
</head>

<body class="bg-white">
    <!-- Invitation Content -->
    <main>
        {!! $content ?? '' !!}
    </main>

    <!-- Footer -->
    @if ($page->template)
        <footer class="py-8 text-center text-sm text-gray-600">
            <p>Created with love using Luminara Photobooth</p>
            <p class="mt-1">
                <a href="https://luminaraphotobooth.com" class="text-yellow-600 hover:text-yellow-700">Create your
                    invitation</a>
            </p>
        </footer>
    @endif

    <!-- SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>

</html>
