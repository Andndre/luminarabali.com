{{--
    Komponen SEO reusable untuk semua halaman guest-facing.
    Penggunaan:
        <x-seo
            title="Judul Halaman"
            description="Deskripsi halaman untuk meta tag dan Google."
            keywords="kata kunci 1, kata kunci 2"
            og_image="/images/logo.png"
            og_type="website"
            canonical="{{ request()->url() }}"
            noindex="false"
        />
--}}
@props([
    'title' => 'Luminara Photobooth & Visual',
    'description' =>
        'Luminara Photobooth & Visual - Premium Wedding, Event Documentation & Photobooth Services in Bali.',
    'keywords' => 'photobooth bali, wedding photography bali, event documentation, 360 video booth',
    'og_image' => '/images/logo.png',
    'og_type' => 'website',
    'canonical' => request()->url(),
    'noindex' => false,
])

<!-- Primary Meta Tags -->
<title>{{ $title }}</title>
<meta name="title" content="{{ $title }}">
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
<link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-192x192.png') }}">
<link rel="icon" type="image/png" sizes="512x512" href="{{ asset('favicon-512x512.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
@if ($noindex)
    <meta name="robots" content="noindex, nofollow">
@else
    <meta name="robots" content="index, follow">
@endif
<meta name="author" content="Luminara Bali">

<!-- Canonical URL -->
@if ($canonical)
    <link rel="canonical" href="{{ $canonical }}">
@endif

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $og_type }}">
<meta property="og:url" content="{{ $canonical ?: request()->url() }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ asset($og_image) }}">
<meta property="og:site_name" content="Luminara Bali">
<meta property="og:locale" content="id_ID">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $canonical ?: request()->url() }}">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ asset($og_image) }}">
