<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Katalog Undangan Digital — Luminara</title>
    <meta name="description" content="Undangan digital buatan Luminara: desain siap pakai yang bisa diubah warna, font, dan susunannya. Lengkap dengan RSVP, galeri, live stream, dan daftar tamu.">
    @vite(['resources/css/app.css'])
</head>
<body class="catalog antialiased">

{{-- 1. Nav ------------------------------------------------------------- --}}
<header class="catalog-nav">
    <div class="catalog-wrap catalog-nav__inner">
        <a href="{{ route('catalog.index') }}" class="catalog-nav__logo">Luminara</a>
        <nav class="catalog-nav__links">
            <a href="#katalog" class="catalog-nav__link">Katalog</a>
            <a href="#cara-pesan" class="catalog-nav__link">Cara Pesan</a>
            <a href="{{ route('login') }}" class="catalog-btn catalog-btn--ghost" style="padding: .5rem 1.25rem">Masuk</a>
        </nav>
    </div>
</header>

<main class="catalog-shell">

    {{-- 2. Hero — komposisi kipas (device gallery) --------------------- --}}
    <section class="catalog-hero">
        <div class="catalog-wrap">
        <div class="catalog-stack catalog-hero__copy">
            <span class="catalog-eyebrow">Undangan Digital</span>
            <h1 class="catalog-display">Undangan yang dibuka, dibaca, lalu diingat.</h1>
            <p class="catalog-hero__lede">
                Yang bergulir di bawah ini undangan sungguhan — bukan gambar contoh. Lihat sendiri
                bagaimana tamu Anda akan membacanya sebelum memutuskan. Warna, huruf, dan urutan
                halamannya masih bisa Anda ubah setelahnya.
            </p>
            <div class="catalog-hero__actions">
                <a href="#katalog" class="catalog-btn catalog-btn--solid">Lihat Katalog</a>
                <a href="{{ route('login') }}" class="catalog-btn catalog-btn--ghost">Pesan Sekarang</a>
            </div>
            <hr class="catalog-hair">
            <div class="catalog-hero__meta">
                <span>Aktif seumur acara</span>
                <span>Dibuka di HP tanpa aplikasi</span>
                <span>Revisi teks tanpa biaya tambahan</span>
            </div>
        </div>
        </div>

        {{--
            Kipas: device tengah (auto-scroll) diapit 2 kartu per sisi yang makin
            ke tepi makin kecil, miring, meredup, dan sengaja ter-crop.
            Pengapit `display:none` di bawah md — IntersectionObserver tidak
            pernah melaporkannya intersecting, jadi iframe-nya tak ter-mount di HP.
        --}}
        @php
            // Posisi kipas kini datang dari `hero_slot` yang diatur admin
            // (lihat CatalogController@index), bukan lagi 5 template teratas.
            $heroLeftOuter = $heroFlankers->firstWhere('hero_slot', 'left-outer');
            $heroLeftInner = $heroFlankers->firstWhere('hero_slot', 'left-inner');
            $heroRightInner = $heroFlankers->firstWhere('hero_slot', 'right-inner');
            $heroRightOuter = $heroFlankers->firstWhere('hero_slot', 'right-outer');
        @endphp
        <div class="catalog-fan">
            <div class="catalog-fan__rail">
                @if ($heroLeftOuter)
                    <div class="catalog-fan__flank catalog-fan__flank--far catalog-fan__flank--l2" aria-hidden="true">
                        <x-catalog.live-frame :src="route('catalog.preview', $heroLeftOuter->slug)" />
                    </div>
                @endif
                @if ($heroLeftInner)
                    <div class="catalog-fan__flank catalog-fan__flank--near catalog-fan__flank--l1" aria-hidden="true">
                        <x-catalog.live-frame :src="route('catalog.preview', $heroLeftInner->slug)" />
                    </div>
                @endif

                <div class="catalog-fan__center">
                    <div class="catalog-device">
                        <span class="catalog-device__pill" aria-hidden="true"></span>
                        @if ($heroCenter)
                            <x-catalog.live-frame
                                :src="route('catalog.preview', $heroCenter->slug)"
                                :autoscroll="true" />
                        @else
                            <div class="catalog-liveframe">
                                <div class="catalog-liveframe__device">
                                    <div class="catalog-liveframe__poster"><span>Preview segera hadir</span></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($heroRightInner)
                    <div class="catalog-fan__flank catalog-fan__flank--near catalog-fan__flank--r1" aria-hidden="true">
                        <x-catalog.live-frame :src="route('catalog.preview', $heroRightInner->slug)" />
                    </div>
                @endif
                @if ($heroRightOuter)
                    <div class="catalog-fan__flank catalog-fan__flank--far catalog-fan__flank--r2" aria-hidden="true">
                        <x-catalog.live-frame :src="route('catalog.preview', $heroRightOuter->slug)" />
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- 3. Pilar nilai — slab wine penuh-lebar ------------------------- --}}
    <section class="catalog-slab">
        <div class="catalog-wrap catalog-stack" style="gap: 2.5rem">
        <div class="catalog-head catalog-reveal">
            <span class="catalog-eyebrow">Yang Anda dapat</span>
            <h2 class="catalog-display">Bukan sekadar halaman berisi tanggal.</h2>
            <p>Empat hal yang paling sering menentukan apakah undangan benar-benar terpakai atau hanya dilihat sekali.</p>
        </div>
        <div class="catalog-pillars">
            <article class="catalog-pillar catalog-reveal">
                <span class="catalog-pillar__num">01</span>
                <h3>Warna &amp; huruf bisa diganti</h3>
                <p>Palet dan jenis huruf tiap desain bisa disesuaikan dengan tema acara Anda — tanpa perlu memesan desain baru dari nol.</p>
            </article>
            <article class="catalog-pillar catalog-reveal">
                <span class="catalog-pillar__num">02</span>
                <h3>RSVP dengan batas waktu</h3>
                <p>Tamu mengonfirmasi kehadiran langsung dari undangan. Anda menentukan sendiri kapan formulirnya ditutup.</p>
            </article>
            <article class="catalog-pillar catalog-reveal">
                <span class="catalog-pillar__num">03</span>
                <h3>Galeri &amp; live stream</h3>
                <p>Sisipkan foto prewedding dan tautan siaran langsung, supaya kerabat yang berhalangan hadir tetap bisa menonton.</p>
            </article>
            <article class="catalog-pillar catalog-reveal">
                <span class="catalog-pillar__num">04</span>
                <h3>Daftar tamu terkelola</h3>
                <p>Kelola nama tamu dan tautan personalnya dari satu tempat, lalu pantau siapa saja yang sudah menjawab.</p>
            </article>
        </div>
        </div>
    </section>

    {{--
        4. Cara pesan — panel kertas yang NAIK MENINDIH slab wine di atasnya.
        Overlap hanya aktif dari 820px (lihat .catalog-overlap); di mobile
        section ini tersusun normal di bawah slab.
        Showcase strip terpisah sengaja dihapus: komposisi kipas di hero sudah
        menampilkan hingga 5 pratinjau hidup, jadi mengulangnya di sini hanya
        menduplikasi konten sekaligus melipatgandakan jumlah iframe.
    --}}
    <section id="cara-pesan" class="catalog-wrap catalog-overlap">
        <div class="catalog-panel catalog-reveal">
            <div class="catalog-head">
                <span class="catalog-eyebrow">Cara pesan</span>
                <h2 class="catalog-display">Dua jalan menuju undangan yang sama.</h2>
                <p>Pilih yang paling nyaman: serahkan pada desainer kami, atau kerjakan sendiri dari layar Anda.</p>
            </div>
            <div class="catalog-paths">
                <div class="catalog-path">
                    <span class="catalog-path__numeral catalog-display" aria-hidden="true">01</span>
                    <div class="catalog-stack catalog-stack--tight">
                        <span class="catalog-eyebrow">Pilihan A</span>
                        <h3 class="catalog-display">Lewat Mitra</h3>
                        <p style="font-size: .92rem; line-height: 1.7; opacity: .8">Cocok bila Anda ingin terima beres dan lebih suka berkomunikasi langsung dengan orangnya.</p>
                    </div>
                    <ol class="catalog-steps">
                        <li>Hubungi mitra atau desainer Luminara terdekat.</li>
                        <li>Kirim data acara, foto, dan desain yang Anda sukai.</li>
                        <li>Pembayaran diselesaikan langsung dengan mitra tersebut.</li>
                        <li>Undangan dibuatkan, Anda tinggal memeriksa dan menyebarkan.</li>
                    </ol>
                </div>
                <div class="catalog-path">
                    <span class="catalog-path__numeral catalog-display" aria-hidden="true">02</span>
                    <div class="catalog-stack catalog-stack--tight">
                        <span class="catalog-eyebrow">Pilihan B</span>
                        <h3 class="catalog-display">Langsung dari sini</h3>
                        <p style="font-size: .92rem; line-height: 1.7; opacity: .8">Cocok bila Anda ingin segera mulai dan senang mengatur sendiri isinya.</p>
                    </div>
                    <ol class="catalog-steps">
                        <li>Pilih desain dari katalog di bawah.</li>
                        <li>Buat akun dan isi data acara Anda.</li>
                        <li>Unggah bukti pembayaran pada halaman pesanan.</li>
                        <li>Undangan aktif setelah pembayaran kami verifikasi.</li>
                    </ol>
                    <a href="{{ route('register') }}" class="catalog-btn catalog-btn--solid" style="align-self: flex-start">Buat Akun</a>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. Katalog ------------------------------------------------------ --}}
    <section id="katalog" class="catalog-wrap catalog-stack" style="gap: 2.5rem">
        <div class="catalog-head catalog-reveal">
            <span class="catalog-eyebrow">Katalog</span>
            <h2 class="catalog-display">Pilih titik awalnya.</h2>
            <p>Semua desain bisa disesuaikan setelah dipilih, jadi mulailah dari yang paling dekat dengan bayangan Anda.</p>
        </div>

        @if ($templates->isNotEmpty())
            <div class="catalog-grid">
                @each('catalog._partials.card', $templates, 'template')
            </div>
        @else
            <div class="catalog-empty catalog-reveal">
                <h3 class="catalog-display" style="font-size: 1.6rem">Belum ada desain</h3>
                <p style="max-width: 26rem; opacity: .8">Belum ada desain yang ditayangkan saat ini — hubungi kami untuk custom sesuai tema acara Anda.</p>
                <a href="{{ route('login') }}" class="catalog-btn catalog-btn--ghost">Hubungi Kami</a>
            </div>
        @endif
    </section>

    {{-- Hairline emas diagonal sebagai pemisah --------------------------- --}}
    <div class="catalog-wrap" aria-hidden="true"><div class="catalog-rule"></div></div>

    {{-- 6. Testimoni — asimetris, satu kutipan memimpin ------------------ --}}
    <section class="catalog-wrap catalog-stack" style="gap: 2.5rem">
        <div class="catalog-head catalog-reveal">
            <span class="catalog-eyebrow">Kata mereka</span>
            <h2 class="catalog-display">Yang paling sering kami dengar.</h2>
        </div>
        <div class="catalog-quotes">
            <figure class="catalog-quote catalog-quote--lead catalog-reveal">
                <blockquote>&ldquo;Yang paling menolong justru RSVP-nya. Kami jadi tahu perkiraan jumlah tamu dua minggu sebelum hari-H, bukan menebak-nebak.&rdquo;</blockquote>
                <figcaption>Dewi &amp; Raka — akad di Ubud, Mei 2026</figcaption>
            </figure>
            <div class="catalog-quotes__aside">
                <figure class="catalog-quote catalog-reveal">
                    <blockquote>&ldquo;Saya sempat ragu karena warnanya kurang cocok dengan tema. Ternyata bisa diganti sendiri dalam beberapa menit.&rdquo;</blockquote>
                    <figcaption>Anindita — mempelai, Denpasar</figcaption>
                </figure>
                <figure class="catalog-quote catalog-reveal">
                    <blockquote>&ldquo;Keluarga di luar kota ikut lewat live stream yang ditempel di undangan. Tidak perlu kirim tautan terpisah ke grup.&rdquo;</blockquote>
                    <figcaption>Bagus Prayoga — panitia keluarga</figcaption>
                </figure>
            </div>
        </div>
        <p style="font-size: .72rem; opacity: .55">Kutipan di atas adalah ilustrasi layanan, ditulis untuk menggambarkan penggunaan umum.</p>
    </section>

    {{-- 7. Footer CTA --------------------------------------------------- --}}
    <section class="catalog-wrap">
        <div class="catalog-cta catalog-reveal">
            <span class="catalog-eyebrow" style="color: #E7C97A">Siap mulai?</span>
            <h2 class="catalog-display">Undangan Anda bisa aktif hari ini juga.</h2>
            <p style="max-width: 34rem; opacity: .85; line-height: 1.7">Pilih satu desain, isi data acara, unggah bukti pembayaran. Kami verifikasi, lalu tautannya siap Anda sebarkan.</p>
            <div class="catalog-hero__actions" style="justify-content: center">
                <a href="{{ route('login') }}" class="catalog-btn catalog-btn--solid">Pesan Sekarang</a>
                <a href="#katalog" class="catalog-btn catalog-btn--ghost" style="border-color: rgba(251,247,241,.4); color: #FBF7F1">Lihat Katalog Lagi</a>
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
