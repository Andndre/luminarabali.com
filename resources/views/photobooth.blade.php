<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <x-seo title="Jasa Photobooth di Bali - Video 360 & Photobooth Print | Luminara"
        description="Luminara menyediakan jasa photobooth di Bali dengan layanan Video 360, Photobooth Print (cetak instan), dan paket event dengan gratis transport area Bali. Cocok untuk wedding, ulang tahun, dan acara kampus."
        keywords="photobooth di bali, jasa photobooth di bali, photobooth, video 360, photobooth print, photobooth free transport"
        og_image="/images/logo.png" />
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@graph": [
                {
                    "@type": "Service",
                    "@id": "https://luminarabali.com/photobooth#service",
                    "name": "Jasa Photobooth di Bali",
                    "url": "https://luminarabali.com/photobooth",
                    "serviceType": "Photobooth",
                    "description": "Layanan Photobooth Print, Video 360, dan paket event dengan gratis transport area Bali.",
                    "provider": {
                        "@type": "LocalBusiness",
                        "@id": "https://luminarabali.com/#business",
                        "name": "Luminara Bali"
                    },
                    "areaServed": {
                        "@type": "AdministrativeArea",
                        "name": "Bali"
                    },
                    "hasOfferCatalog": {
                        "@type": "OfferCatalog",
                        "name": "Paket Photobooth",
                        "itemListElement": [
                            {
                                "@type": "Offer",
                                "itemOffered": {
                                    "@type": "Service",
                                    "name": "Photobooth Print Unlimited"
                                }
                            },
                            {
                                "@type": "Offer",
                                "itemOffered": {
                                    "@type": "Service",
                                    "name": "Video 360 Unlimited"
                                }
                            }
                        ]
                    }
                },
                {
                    "@type": "FAQPage",
                    "@id": "https://luminarabali.com/photobooth#faq",
                    "mainEntity": [
                        {
                            "@type": "Question",
                            "name": "Apakah tersedia layanan Photobooth Print?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Ya, tersedia paket Photobooth Print dengan cetak instan sesuai jenis paket yang dipilih."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Apakah ada layanan Video 360?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Ya, tersedia layanan Video 360 untuk acara wedding, corporate, ulang tahun, dan event kampus."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "Apakah tersedia free transport di Bali?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "Untuk paket tertentu tersedia gratis transport area Bali sesuai ketentuan paket."
                            }
                        }
                    ]
                }
            ]
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        luminara: {
                            gold: '#D4AF37',
                            dark: '#0f0f0f',
                            light: '#f8f9fa',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Cormorant Garamond"', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        /* Fallback background if JS disabled or no images */
        .hero-bg {
            background-color: #1a1a1a;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            transition: background-image 1s ease-in-out;
        }

        @media (max-width: 768px) {
            .hero-bg {
                background-attachment: scroll;
            }
        }

        #navbar {
            transition: all 0.3s ease;
        }

        .nav-transparent {
            background-color: transparent;
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .nav-transparent .nav-item {
            color: white;
        }

        .nav-transparent .nav-item:hover {
            color: #D4AF37;
        }

        .nav-transparent .nav-btn {
            background-color: white;
            color: black;
        }

        .nav-scrolled {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .nav-scrolled .nav-item {
            color: #1a1a1a;
        }

        .nav-scrolled .nav-item:hover {
            color: #D4AF37;
        }

        .nav-scrolled .nav-btn {
            background-color: black;
            color: white;
        }

        /* Mobile specific styles */
        .mobile-menu-open {
            overflow: hidden;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="selection:bg-luminara-gold font-sans text-gray-800 antialiased selection:text-white">

    <!-- Navbar -->
    <nav id="navbar" class="nav-transparent fixed z-50 w-full transition-all duration-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex flex-shrink-0 items-center gap-3">
                    <img id="nav-logo" src="/images/Logo Luminara Visual-WHITE-TPR.png" alt="Luminara Logo"
                        class="h-8 w-auto drop-shadow-md transition-all duration-300 md:h-10">
                    <span
                        class="nav-item font-serif text-xl font-bold tracking-wide transition-colors duration-300 md:text-2xl">Luminara</span>
                </div>

                <div class="hidden items-center space-x-8 md:flex">
                    <a href="#home"
                        class="nav-item text-sm font-medium tracking-wide transition-colors duration-300">BERANDA</a>
                    <a href="#features"
                        class="nav-item text-sm font-medium tracking-wide transition-colors duration-300">KEUNGGULAN</a>
                    <a href="#pricing"
                        class="nav-item text-sm font-medium tracking-wide transition-colors duration-300">HARGA</a>
                    <a href="{{ route('booking.create') }}?unit=photobooth"
                        class="nav-btn transform rounded-full px-6 py-2 text-sm font-bold uppercase tracking-wide shadow-lg transition-all duration-300 hover:-translate-y-0.5">
                        Booking Sekarang
                    </a>
                </div>

                <div class="flex items-center md:hidden">
                    <button id="mobile-menu-btn" class="nav-item p-2 focus:outline-none" aria-label="Toggle menu">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu"
            class="absolute left-0 top-full hidden w-full border-t border-gray-100 bg-white shadow-xl transition-all duration-300 md:hidden">
            <div class="space-y-3 px-4 pb-6 pt-4">
                <a href="#home"
                    class="hover:text-luminara-gold block rounded-lg px-3 py-2 text-base font-medium text-gray-900 transition hover:bg-gray-50">BERANDA</a>
                <a href="#features"
                    class="hover:text-luminara-gold block rounded-lg px-3 py-2 text-base font-medium text-gray-900 transition hover:bg-gray-50">KEUNGGULAN</a>
                <a href="#pricing"
                    class="hover:text-luminara-gold block rounded-lg px-3 py-2 text-base font-medium text-gray-900 transition hover:bg-gray-50">HARGA</a>
                <a href="{{ route('booking.create') }}"
                    class="bg-luminara-gold mt-4 block w-full rounded-full py-3 text-center text-sm font-bold uppercase tracking-wide text-white shadow-md transition hover:bg-yellow-600">
                    Booking Sekarang
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative flex h-screen items-center justify-center overflow-hidden px-4 text-center">
        <!-- Background Layers for Crossfade -->
        <div id="hero-bg-1"
            class="absolute inset-0 z-0 bg-cover bg-fixed bg-center opacity-100 transition-opacity duration-[1500ms]">
        </div>
        <div id="hero-bg-2"
            class="absolute inset-0 z-0 bg-cover bg-fixed bg-center opacity-0 transition-opacity duration-[1500ms]">
        </div>

        <div class="z-10 mx-auto max-w-5xl px-4 text-white opacity-0" style="animation: fadeInUp 1s ease-out forwards;">
            <p class="text-luminara-gold mb-3 font-serif text-lg italic tracking-wider md:mb-4 md:text-xl">Pengalaman
                Event Premium</p>
            <h1
                class="mb-6 font-serif text-4xl font-bold leading-tight tracking-tight drop-shadow-lg sm:text-5xl md:mb-8 md:text-8xl">
                Abadikan Momen <br> <span class="text-white">Berharga</span>
            </h1>
            <p
                class="mx-auto mb-8 max-w-2xl px-2 text-base font-light leading-relaxed text-gray-200 drop-shadow-md sm:text-lg md:mb-12 md:text-xl">
                Layanan Photobooth & 360° Videobooth terbaik di Bali. <br class="hidden md:block">Tangkap setiap
                senyuman, setiap gerakan, setiap momen.
            </p>
            <div class="flex w-full flex-col justify-center gap-4 px-4 sm:w-auto sm:flex-row sm:px-0">
                <a href="{{ route('booking.create') }}?unit=photobooth"
                    class="bg-luminara-gold group w-full rounded-full px-8 py-3 text-base font-semibold text-white shadow-[0_0_20px_rgba(212,175,55,0.4)] transition-all duration-300 hover:bg-white hover:text-black hover:shadow-[0_0_30px_rgba(255,255,255,0.6)] sm:w-auto md:px-10 md:py-4 md:text-lg">
                    Pesan Tanggal
                    <span class="ml-2 inline-block transition-transform group-hover:translate-x-1">&rarr;</span>
                </a>
                <a href="#pricing"
                    class="w-full rounded-full border border-white/30 bg-white/10 px-8 py-3 text-base font-semibold text-white backdrop-blur-sm transition duration-300 hover:bg-white/20 sm:w-auto md:px-10 md:py-4 md:text-lg">
                    Lihat Paket
                </a>
            </div>
        </div>

        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 transform animate-bounce">
            <a href="#features" class="p-2 text-white/70 transition hover:text-white">
                <svg class="h-6 w-6 md:h-8 md:w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path>
                </svg>
            </a>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-white py-16 md:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center md:mb-20">
                <h2 class="mb-4 font-serif text-3xl font-bold md:text-4xl">Standar Luminara</h2>
                <div class="bg-luminara-gold mx-auto h-1 w-24"></div>
                <p class="mx-auto mt-4 max-w-2xl text-sm text-gray-600 md:text-base">Kami menggunakan peralatan
                    profesional untuk memastikan
                    kualitas visual terbaik untuk acara Anda.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:gap-8 lg:grid-cols-4">
                <div
                    class="group rounded-xl border border-transparent bg-gray-50 p-6 text-center transition hover:border-gray-200 hover:shadow-lg">
                    <div
                        class="text-luminara-gold mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-gray-900">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-bold">Canon DSLR Pro</h3>
                    <p class="text-sm text-gray-500">Kualitas foto tajam dengan kamera DSLR profesional, bukan webcam.
                    </p>
                </div>

                <div
                    class="group rounded-xl border border-transparent bg-gray-50 p-6 text-center transition hover:border-gray-200 hover:shadow-lg">
                    <div
                        class="text-luminara-gold mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-gray-900">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-bold">Layar Live View 24"</h3>
                    <p class="text-sm text-gray-500">Monitoring real-time dengan layar besar agar gaya lebih maksimal.
                    </p>
                </div>

                <div
                    class="group rounded-xl border border-transparent bg-gray-50 p-6 text-center transition hover:border-gray-200 hover:shadow-lg">
                    <div
                        class="text-luminara-gold mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-gray-900">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-bold">Pencahayaan Studio</h3>
                    <p class="text-sm text-gray-500">Flash Studio 300 Watt + Light 50 Watt untuk hasil sempurna.</p>
                </div>

                <div
                    class="group rounded-xl border border-transparent bg-gray-50 p-6 text-center transition hover:border-gray-200 hover:shadow-lg">
                    <div
                        class="text-luminara-gold mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-gray-900">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-bold">Gratis QR Code</h3>
                    <p class="text-sm text-gray-500">Unduh softcopy foto & video secara instan via scan QR Code.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="bg-gray-50 py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-16 text-center">
                <h2 class="mb-4 font-serif text-4xl font-bold">Paket Terbaik Kami</h2>
                <div class="bg-luminara-gold mx-auto h-1 w-24"></div>
                <p class="mt-4 text-gray-600">Pilihan paket favorit untuk durasi 2 Jam (Bisa ditambah)</p>
            </div>

            <div class="grid grid-cols-1 items-start gap-8 md:grid-cols-3">

                <div
                    class="flex h-full flex-col overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-xl transition duration-300 hover:-translate-y-1">
                    <div class="p-8">
                        <h3 class="mb-2 text-2xl font-bold text-gray-900">Photobooth Unlimited</h3>
                        <p class="mb-6 text-sm uppercase tracking-wide text-gray-500">Paket Cetak Sepuasnya</p>
                        <div class="mb-6 flex items-baseline">
                            <span class="text-4xl font-extrabold tracking-tight">Rp 2.000k</span>
                            <span class="ml-1 text-xl text-gray-500">/ 2 jam</span>
                        </div>
                        <ul class="mb-8 space-y-4 text-left text-sm text-gray-600">
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-green-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span><b>Cetak Unlimited</b> 4R / Strip</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-green-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Gratis Desain Template Custom
                            </li>
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-green-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Gratis Fun Props / Aksesoris
                            </li>
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-green-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Gratis Transport (Seluruh Bali*)
                            </li>
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-green-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Kru Profesional
                            </li>
                        </ul>
                    </div>
                    <div class="mt-auto bg-gray-50 p-8">
                        <a href="{{ route('booking.create') }}?unit=photobooth&type=pb_unlimited"
                            class="block w-full rounded-xl bg-gray-900 py-3 text-center font-semibold text-white transition hover:bg-gray-800">Pilih
                            Paket Ini</a>
                    </div>
                </div>

                <div
                    class="border-luminara-gold relative z-10 flex h-full transform flex-col overflow-hidden rounded-3xl border-2 bg-white shadow-2xl md:-translate-y-4">
                    <div
                        class="bg-luminara-gold absolute right-0 top-0 rounded-bl-lg px-3 py-1 text-xs font-bold uppercase tracking-wider text-white">
                        Best Value</div>
                    <div class="p-8">
                        <h3 class="mb-2 text-2xl font-bold text-gray-900">Combo Ultimate</h3>
                        <p class="mb-6 text-sm uppercase tracking-wide text-gray-500">Photobooth + 360 Video</p>
                        <div class="mb-6 flex flex-col">
                            <span class="text-lg text-gray-400 line-through">Rp 4.000.000</span>
                            <div class="flex items-baseline">
                                <span class="text-4xl font-extrabold tracking-tight text-gray-900">Rp 3.950k</span>
                                <span class="ml-1 text-xl text-gray-500">/ 2 jam</span>
                            </div>
                        </div>
                        <ul class="mb-8 space-y-4 text-left text-sm text-gray-600">
                            <li class="flex items-start">
                                <svg class="text-luminara-gold mr-3 h-5 w-5 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <b>Cetak Unlimited</b> Foto
                            </li>
                            <li class="flex items-start">
                                <svg class="text-luminara-gold mr-3 h-5 w-5 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <b>Video 360° Unlimited</b>
                            </li>
                            <li class="flex items-start">
                                <svg class="text-luminara-gold mr-3 h-5 w-5 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Berbagi Instan (AirDrop/QR)
                            </li>
                            <li class="flex items-start">
                                <svg class="text-luminara-gold mr-3 h-5 w-5 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Custom Overlay & Musik
                            </li>
                        </ul>
                    </div>
                    <div class="mt-auto bg-gray-50 p-8">
                        <a href="{{ route('booking.create') }}?unit=photobooth&type=combo_unlimited"
                            class="bg-luminara-gold block w-full rounded-xl py-3 text-center font-semibold text-white transition hover:bg-yellow-600">Pilih
                            Paket Ini</a>
                    </div>
                </div>

                <div
                    class="flex h-full flex-col overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-xl transition duration-300 hover:-translate-y-1">
                    <div class="p-8">
                        <h3 class="mb-2 text-2xl font-bold text-gray-900">Videobooth 360°</h3>
                        <p class="mb-6 text-sm uppercase tracking-wide text-gray-500">Paket Video Unlimited</p>
                        <div class="mb-6 flex items-baseline">
                            <span class="text-4xl font-extrabold tracking-tight">Rp 2.000k</span>
                            <span class="ml-1 text-xl text-gray-500">/ 2 jam</span>
                        </div>
                        <ul class="mb-8 space-y-4 text-left text-sm text-gray-600">
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-green-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <b>Video Unlimited</b> Slowmo/Rewind
                            </li>
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-green-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Kapasitas 4-5 Orang (100cm)
                            </li>
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-green-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Gratis Watermark/Overlay Custom
                            </li>
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-green-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Kru & Pencahayaan Standby
                            </li>
                        </ul>
                    </div>
                    <div class="mt-auto bg-gray-50 p-8">
                        <a href="{{ route('booking.create') }}?unit=photobooth&type=videobooth360"
                            class="block w-full rounded-xl bg-gray-900 py-3 text-center font-semibold text-white transition hover:bg-gray-800">Pilih
                            Paket Ini</a>
                    </div>
                </div>

            </div>

            <div class="mt-16 text-center">
                <a href="{{ route('pricelist') }}" class="group inline-flex flex-col items-center">
                    <div
                        class="border-luminara-gold text-luminara-gold group-hover:bg-luminara-gold group-hover:shadow-luminara-gold/30 inline-flex items-center gap-3 rounded-xl border-2 bg-white px-8 py-3.5 text-base font-bold shadow-lg transition-all duration-300 group-hover:-translate-y-1 group-hover:text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        <span>Daftar Harga Lengkap & Detail Per Jam</span>
                        <svg class="h-5 w-5 transition-transform group-hover:translate-x-1" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </div>
                    <p class="group-hover:text-luminara-gold mt-3 text-xs font-medium text-gray-400 transition-colors">
                        Klik untuk melihat detail durasi 2 jam hingga 12 jam
                    </p>
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative overflow-hidden bg-gray-900 py-24 text-center text-white">
        <div
            class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1533174072545-e8d4aa97edf9?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80')] bg-cover bg-center opacity-20">
        </div>
        <div class="relative mx-auto max-w-3xl px-4">
            <h2 class="mb-6 font-serif text-3xl font-bold md:text-5xl">Siap Membuat Acara Anda Berkesan?</h2>
            <p class="mb-8 text-xl text-gray-300">Amankan tanggal Anda sekarang sebelum slot penuh. Maksimal 4 acara
                per hari.</p>
            <a href="{{ route('booking.create') }}"
                class="bg-luminara-gold inline-block transform rounded-full px-10 py-4 text-lg font-bold text-white shadow-lg transition hover:-translate-y-1 hover:bg-yellow-600">
                Cek Ketersediaan Tanggal
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t bg-gray-50 pb-8 pt-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 grid grid-cols-1 gap-12 md:grid-cols-3">
                <div>
                    <div class="mb-4 flex items-center gap-2">
                        <img src="/images/logo.png" alt="Luminara" class="h-8">
                        <span class="font-serif text-xl font-bold">Luminara</span>
                    </div>
                    <p class="text-gray-500">
                        Penyedia layanan Photobooth dan 360 Videobooth profesional di Bali untuk berbagai kebutuhan
                        acara Anda.
                    </p>
                </div>
                <div>
                    <h4 class="mb-4 text-lg font-bold">Kontak</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li>WhatsApp: <a href="https://wa.me/6287788986136"
                                class="hover:text-luminara-gold">0877-8898-6136</a></li>
                        <li>Instagram: <a href="https://instagram.com/luminara_photobooth"
                                class="hover:text-luminara-gold">@luminara_photobooth</a></li>
                        <li>Instagram: <a href="https://instagram.com/luminara_visual"
                                class="hover:text-luminara-gold">@luminara_visual</a></li>
                        <li>Lokasi: Karangasem, Bali</li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-4 text-lg font-bold">Tautan</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('booking.create') }}"
                                class="hover:text-luminara-gold text-gray-500">Formulir Booking</a></li>
                        <li><a href="#pricing" class="hover:text-luminara-gold text-gray-500">Daftar Harga</a></li>
                    </ul>
                </div>
            </div>
            <div class="text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} Luminara Photobooth. Seluruh hak cipta dilindungi.
            </div>
        </div>
    </footer>

    <script>
        const navbar = document.getElementById('navbar');
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const navLogo = document.getElementById('nav-logo');

        function updateNavbar() {
            if (window.scrollY > 50 || !mobileMenu.classList.contains('hidden')) {
                navbar.classList.remove('nav-transparent');
                navbar.classList.add('nav-scrolled');
                navLogo.src = "/images/logo.png";
            } else {
                navbar.classList.add('nav-transparent');
                navbar.classList.remove('nav-scrolled');
                navLogo.src = "/images/Logo Luminara Visual-WHITE-TPR.png";
            }
        }

        window.addEventListener('scroll', updateNavbar);

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                document.body.classList.toggle('mobile-menu-open');
                updateNavbar();
            });
        }

        // Close menu when clicking a link
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                updateNavbar();
            });
        });

        // Hero Slideshow
        const heroImages = @json($heroImages ?? []);
        const bg1 = document.getElementById('hero-bg-1');
        const bg2 = document.getElementById('hero-bg-2');

        if (heroImages.length > 0) {
            let currentIndex = 0;
            let activeBg = bg1;

            const gradient = 'linear-gradient(to bottom, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.4))';

            // Set initial image
            bg1.style.backgroundImage = `${gradient}, url('${heroImages[0]}')`;

            if (heroImages.length > 1) {
                setInterval(() => {
                    currentIndex = (currentIndex + 1) % heroImages.length;
                    const nextImage = heroImages[currentIndex];
                    const nextBg = activeBg === bg1 ? bg2 : bg1;

                    // Preload and switch
                    const img = new Image();
                    img.src = nextImage;
                    img.onload = () => {
                        nextBg.style.backgroundImage = `${gradient}, url('${nextImage}')`;

                        // Fade transition
                        activeBg.classList.add('opacity-0');
                        activeBg.classList.remove('opacity-100');
                        nextBg.classList.add('opacity-100');
                        nextBg.classList.remove('opacity-0');

                        activeBg = nextBg;
                    };
                }, 5000);
            }
        } else {
            bg1.style.backgroundImage =
                `linear-gradient(to bottom, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.4)), url('https://images.unsplash.com/photo-1516035069371-29a1b244cc32?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80')`;
        }
    </script>
</body>

</html>
