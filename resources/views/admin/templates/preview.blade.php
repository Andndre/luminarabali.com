<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: {{ $template->name }} - Luminara</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css'])

    <!-- Tailwind CSS CDN -->
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
            background-color: white;
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

    @if (!empty($template->global_custom_css))
        <style>
            {!! $template->global_custom_css !!}
        </style>
    @endif
</head>

<body class="bg-white">
    <!-- Preview Banner -->
    <div class="sticky top-0 z-50 bg-yellow-500 px-4 py-2 text-center text-sm font-medium text-black">
        Preview Mode - Template: {{ $template->name }}
    </div>

    @php
        $page = new \stdClass();
        $page->groom_name = 'Romeo';
        $page->bride_name = 'Juliet';
        $page->event_date = now()->addDays(30);
        $page->meta_data = $template->meta_data ?? [];
        $page->template = $template; // Pass the template relationship
        $page->slug = 'demo';

        $music = $page->meta_data['bg_music'] ?? '';
        $rsvpEnabled = $page->meta_data['rsvp_enabled'] ?? true;
    @endphp

    <x-invitation.layout class="bg-gray-50" :skip-cover="request()->query('skip_cover') == 1">
        <x-invitation.audio :src="$music" />

        @if (!empty($template->cover_content))
            {!! \Illuminate\Support\Facades\Blade::render($template->cover_content, ['page' => $page]) !!}
        @else
            <x-invitation.cover :groom="$page->groom_name ?? 'Romeo'" :bride="$page->bride_name ?? 'Juliet'" :guest="request()->query('to', 'Tamu Spesial')"
                image="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=2000&auto=format&fit=crop" />
        @endif

        <div x-show="isOpen" style="display: none;" class="w-full">
            {!! \Illuminate\Support\Facades\Blade::render($template->blade_content ?? '', ['page' => $page]) !!}
        </div>
    </x-invitation.layout>
</body>

</html>
