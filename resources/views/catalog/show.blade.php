<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $template->name }} | Undangan Digital Luminara</title>
    <meta name="description" content="{{ Str::limit($template->description ?: 'Desain undangan digital '.$template->name.' dari Luminara. Lihat pratinjau langsung sebelum memesan.', 155) }}">
    @vite(['resources/css/app.css'])
</head>
<body class="catalog antialiased">

<header class="catalog-nav">
    <div class="catalog-wrap catalog-nav__inner">
        <a href="{{ route('catalog.index') }}" class="catalog-nav__logo">Luminara</a>
        <nav class="catalog-nav__links">
            <a href="{{ route('catalog.index') }}#katalog" class="catalog-nav__link">Katalog</a>
            <a href="{{ route('catalog.index') }}#cara-pesan" class="catalog-nav__link">Cara Pesan</a>
            <a href="{{ route('login') }}" class="catalog-btn catalog-btn--ghost" style="padding: .5rem 1.25rem">Masuk</a>
        </nav>
    </div>
</header>

<main class="catalog-shell">

    <section class="catalog-wrap catalog-detail">
        {{--
            Kolom kiri: device yang sama dengan hero landing, tapi preview-nya
            bergulir penuh supaya calon pembeli melihat seluruh isi desain,
            bukan hanya sampul seperti di kartu katalog.
        --}}
        <div class="catalog-detail__stage">
            <div class="catalog-device">
                <span class="catalog-device__pill" aria-hidden="true"></span>
                <x-catalog.live-frame
                    :src="route('catalog.preview', $template->slug)"
                    :poster="$template->thumbnail ? asset('storage/'.$template->thumbnail) : null"
                    :autoscroll="true" />
            </div>
            <a href="{{ route('catalog.preview', $template->slug) }}"
               target="_blank" rel="noopener"
               class="catalog-btn catalog-btn--ghost">Buka preview penuh</a>
        </div>

        <div class="catalog-stack catalog-detail__info">
            <nav class="catalog-crumb" aria-label="Breadcrumb">
                <a href="{{ route('catalog.index') }}">Katalog</a>
                <span aria-hidden="true">/</span>
                <span>{{ $template->name }}</span>
            </nav>

            @if ($template->category)
                <span class="catalog-eyebrow">{{ $template->category }}</span>
            @endif
            <h1 class="catalog-display catalog-detail__title">{{ $template->name }}</h1>
            <p class="catalog-detail__price">{{ $template->priceLabel() }}</p>

            @if ($template->description)
                <p class="catalog-detail__desc">{{ $template->description }}</p>
            @endif

            <hr class="catalog-hair">
            <ul class="catalog-detail__specs">
                <li>Warna dan huruf bisa disesuaikan dengan tema acara</li>
                <li>RSVP dengan batas waktu yang Anda tentukan</li>
                <li>Galeri foto, peta lokasi, dan tautan live stream</li>
                <li>Aktif seumur acara, dibuka di HP tanpa aplikasi</li>
            </ul>

            <div class="catalog-hero__actions">
                @if ($template->price === null)
                    <a href="{{ route('login') }}" class="catalog-btn catalog-btn--solid">Hubungi kami</a>
                @elseif (auth()->check())
                    <form method="POST" action="{{ route('orders.store', $template->slug) }}">
                        @csrf
                        <button type="submit" class="catalog-btn catalog-btn--solid">Pesan desain ini</button>
                    </form>
                @else
                    {{-- Tamu: login dulu, lalu tombol jadi form POST saat kembali ke halaman ini. --}}
                    <a href="{{ route('login') }}" class="catalog-btn catalog-btn--solid">Masuk untuk memesan</a>
                @endif
                <a href="{{ route('catalog.index') }}#katalog" class="catalog-btn catalog-btn--ghost">Lihat desain lain</a>
            </div>
        </div>
    </section>

    <footer class="catalog-wrap catalog-footer">
        <span>&copy; {{ date('Y') }} Luminara</span>
        <span>Undangan digital &middot; Bali</span>
    </footer>
</main>

@include('catalog._partials.scripts')
</body>
</html>
