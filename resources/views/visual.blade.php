<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <x-seo
        title="Luminara Visual Bali - Premium Wedding, Graduation & Event Photography & Videography"
        description="Luminara Visual Bali hadir dengan layanan dokumentasi pernikahan, graduation, dan acara spesial dengan gaya cinematic dan sinematik yang premium."
        keywords="wedding photography bali, wedding videography bali, graduation photography, event documentation bali, cinematik wedding, dokumentasi pernikahan bali"
        og_image="/images/Logo Luminara Visual-BLACK-TPR.png"
    />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,700;1,400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
        #home {
            transition: background-image 1.5s ease-in-out;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        @media (max-width: 768px) {
            #home {
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
            color: #fde68a; /* amber-200 */
        }

        .nav-scrolled {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .nav-scrolled .nav-item {
            color: #1c1917; /* stone-900 */
        }

        .nav-scrolled .nav-item:hover {
            color: #b45309; /* amber-700 */
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
<body class="bg-stone-50 text-stone-900 selection:bg-amber-200 selection:text-stone-900">

    <!-- Navbar -->
    <nav id="navbar" class="nav-transparent fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img id="nav-logo" src="/images/Logo Luminara Visual-WHITE-TPR.png" alt="Luminara" class="h-8 md:h-10 transition-all duration-300">
                <span class="nav-item font-serif text-xl font-bold tracking-tight transition-colors duration-300">Luminara <span class="italic font-normal">Visual</span></span>
            </a>
            <div class="hidden md:flex items-center gap-8 text-sm font-bold uppercase tracking-widest">
                <a href="#home" class="nav-item transition-colors duration-300">Home</a>
                <a href="#portfolio" class="nav-item transition-colors duration-300">Portfolio</a>
                <a href="#pricing" class="nav-item transition-colors duration-300">Pricelist</a>
                <a href="{{ route('booking.create') }}?unit=visual" class="bg-stone-900 text-white px-6 py-2.5 rounded-full hover:bg-stone-700 transition shadow-lg">Book Visual</a>
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex items-center md:hidden">
                <button id="mobile-menu-btn" class="nav-item focus:outline-none p-2 transition-colors duration-300" aria-label="Toggle menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-stone-50 border-t border-stone-200 shadow-xl transition-all duration-300 absolute w-full left-0 top-full">
             <div class="px-4 pt-4 pb-6 space-y-3">
                <a href="#home" class="block py-2 px-3 text-base font-medium text-stone-900 hover:bg-stone-100 hover:text-amber-700 rounded-lg transition uppercase">HOME</a>
                <a href="#portfolio" class="block py-2 px-3 text-base font-medium text-stone-900 hover:bg-stone-100 hover:text-amber-700 rounded-lg transition uppercase">PORTFOLIO</a>
                <a href="#pricing" class="block py-2 px-3 text-base font-medium text-stone-900 hover:bg-stone-100 hover:text-amber-700 rounded-lg transition uppercase">PRICELIST</a>
                <a href="{{ route('booking.create') }}?unit=visual" class="block w-full mt-4 text-center bg-stone-900 text-white font-bold py-3 rounded-full hover:bg-stone-800 transition shadow-md uppercase text-sm tracking-wide">
                    Book Visual
                </a>
             </div>
        </div>
    </nav>

    <!-- Hero -->
    <header id="home" class="relative pt-40 pb-20 px-4 text-center min-h-screen flex flex-col justify-center items-center text-white overflow-hidden">
        <!-- Background Layers for Crossfade -->
        <div id="hero-bg-1" class="absolute inset-0 z-0 bg-cover bg-center bg-fixed transition-opacity duration-[1500ms] opacity-100"></div>
        <div id="hero-bg-2" class="absolute inset-0 z-0 bg-cover bg-center bg-fixed transition-opacity duration-[1500ms] opacity-0"></div>
        
        <div class="max-w-4xl mx-auto relative z-10 opacity-0" style="animation: fadeInUp 1s ease-out forwards;">
            <span class="text-amber-200 font-bold uppercase tracking-[0.4em] text-xs mb-4 block">Crafting Memories</span>
            <h1 class="text-5xl md:text-7xl font-serif mb-8 leading-tight text-white drop-shadow-lg">Timeless Stories Through <span class="italic font-normal underline decoration-amber-200 underline-offset-8">Cinematic</span> Lenses.</h1>
            <p class="text-stone-200 text-lg md:text-xl max-w-2xl mx-auto mb-12 drop-shadow-md">Luminara Visual mengkhususkan diri dalam dokumentasi pernikahan, kelulusan, dan acara pribadi yang elegan di seluruh Bali.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#portfolio" class="px-10 py-4 bg-white text-stone-900 font-bold rounded-full hover:shadow-xl hover:-translate-y-1 transition duration-300">View Gallery</a>
                <a href="{{ route('booking.create') }}?unit=visual" class="px-10 py-4 border border-white text-white font-bold rounded-full hover:bg-white hover:text-stone-900 transition duration-300">Start Booking</a>
            </div>
        </div>

        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 transform animate-bounce">
            <a href="#portfolio" class="text-white/70 transition hover:text-white p-2">
                <svg class="h-6 w-6 md:h-8 md:w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path>
                </svg>
            </a>
        </div>
    </header>

            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-white py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12 md:mb-20 text-center">
                <h2 class="mb-4 font-serif text-3xl md:text-4xl font-bold text-stone-900">The Visual Experience</h2>
                <div class="bg-amber-700 mx-auto h-1 w-24"></div>
                <p class="mx-auto mt-4 max-w-2xl text-stone-500 text-sm md:text-base">Kami menggabungkan estetika sinematik dengan momen autentik untuk hasil yang tak lekang oleh waktu.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                <!-- Feature 1 -->
                <div class="group rounded-xl border border-transparent bg-stone-50 p-6 text-center transition hover:border-stone-200 hover:shadow-lg">
                    <div class="text-amber-700 mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-stone-200 group-hover:bg-stone-900 group-hover:text-white transition-colors">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-bold text-stone-900">Cinematic Storytelling</h3>
                    <p class="text-sm text-stone-500">Gaya pengambilan gambar yang bercerita, menangkap emosi dan atmosfer secara natural.</p>
                </div>

                <!-- Feature 2 -->
                <div class="group rounded-xl border border-transparent bg-stone-50 p-6 text-center transition hover:border-stone-200 hover:shadow-lg">
                    <div class="text-amber-700 mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-stone-200 group-hover:bg-stone-900 group-hover:text-white transition-colors">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-bold text-stone-900">High-End Gear</h3>
                    <p class="text-sm text-stone-500">Menggunakan kamera Sony Alpha Series & Lensa G-Master untuk ketajaman maksimal.</p>
                </div>

                <!-- Feature 3 -->
                <div class="group rounded-xl border border-transparent bg-stone-50 p-6 text-center transition hover:border-stone-200 hover:shadow-lg">
                    <div class="text-amber-700 mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-stone-200 group-hover:bg-stone-900 group-hover:text-white transition-colors">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-bold text-stone-900">Professional Editing</h3>
                    <p class="text-sm text-stone-500">Color grading estetik (Warm/Moody) yang konsisten dan memanjakan mata.</p>
                </div>

                <!-- Feature 4 -->
                <div class="group rounded-xl border border-transparent bg-stone-50 p-6 text-center transition hover:border-stone-200 hover:shadow-lg">
                    <div class="text-amber-700 mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-stone-200 group-hover:bg-stone-900 group-hover:text-white transition-colors">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-bold text-stone-900">Fast Delivery</h3>
                    <p class="text-sm text-stone-500">Preview foto H+1 dan hasil edit final dikirim via Google Drive dengan cepat.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Section (Masonry) -->
    <section id="portfolio" class="py-20 bg-stone-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="text-amber-700 font-bold uppercase tracking-[0.2em] text-xs mb-3 block">Portfolio</span>
                <h2 class="font-serif text-4xl text-stone-900">Selected Works</h2>
            </div>

            <!-- Masonry Grid or Empty State -->
            @if(isset($portfolioImages) && count($portfolioImages) > 0)
                <div class="columns-1 md:columns-2 lg:columns-3 gap-6 space-y-6">
                    @foreach($portfolioImages as $img)
                        <div class="break-inside-avoid relative group rounded-xl overflow-hidden cursor-zoom-in">
                            <img src="{{ $img['path'] }}" class="w-full h-auto object-cover transform transition duration-700 group-hover:scale-105" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-6">
                                <span class="text-white font-serif italic text-lg">{{ $img['title'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State UI -->
                <div class="py-20 px-6 text-center bg-stone-50 rounded-3xl border-2 border-dashed border-stone-200 max-w-3xl mx-auto">
                    <div class="text-stone-300 mb-6">
                        <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-serif text-stone-800 mb-3">Our Portfolio is Growing</h3>
                    <p class="text-stone-500 mb-8 max-w-md mx-auto leading-relaxed">
                        Kami sedang memilih karya-karya terbaik untuk ditampilkan di sini. Untuk saat ini, Anda dapat melihat karya terbaru kami melalui media sosial.
                    </p>
                    <a href="https://instagram.com/luminara_visual" target="_blank" class="inline-flex items-center gap-2 text-stone-900 font-bold hover:text-amber-700 transition group">
                        <span class="border-b-2 border-amber-200 group-hover:border-amber-700 pb-0.5">Follow @luminara_visual</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
            @endif
        </div>
    </section>
        </div>
    </section>

    <!-- Pricing Preview -->
    <section id="pricing" class="py-24 bg-stone-50 border-t border-stone-200">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-16">
                <span class="text-amber-700 font-bold uppercase tracking-[0.2em] text-xs mb-3 block">Investment</span>
                <h2 class="font-serif text-4xl text-stone-900">Document Your Journey</h2>
                <p class="mt-4 text-stone-500">Pilih layanan yang sesuai dengan kebutuhan momen spesial Anda.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12 items-start max-w-4xl mx-auto">
                
                <!-- Card 1: Wedding/Event -->
                <div class="flex h-full flex-col overflow-hidden rounded-3xl border border-stone-100 bg-white shadow-xl transition duration-300 hover:-translate-y-1">
                    <div class="p-8">
                        <h3 class="mb-2 text-2xl font-serif font-bold text-stone-900">Wedding & Event</h3>
                        <p class="mb-6 text-sm uppercase tracking-wide text-stone-500">Dokumentasi Cinematic</p>
                        <div class="mb-6 flex items-baseline">
                            <span class="text-4xl font-extrabold tracking-tight text-stone-900">1.300k</span>
                            <span class="ml-1 text-xl text-stone-500">/ 8 jam</span>
                        </div>
                        <ul class="mb-8 space-y-4 text-left text-sm text-stone-600">
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span><b>Professional</b> Photographer</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Unlimited Shoot</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Edited Files via Google Drive</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Cinematic Video Highlight (Add-on)</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-auto bg-stone-50 p-8">
                        <a href="{{ route('booking.create') }}?unit=visual&package_type=visual_basic"
                            class="block w-full rounded-xl bg-stone-900 py-3 text-center font-semibold text-white transition hover:bg-stone-800 shadow-lg">
                            Pilih Paket Ini
                        </a>
                    </div>
                </div>

                <!-- Card 2: Graduation -->
                <div class="flex h-full flex-col overflow-hidden rounded-3xl border border-stone-100 bg-white shadow-xl transition duration-300 hover:-translate-y-1">
                    <div class="p-8">
                        <h3 class="mb-2 text-2xl font-serif font-bold text-stone-900">Graduation Session</h3>
                        <p class="mb-6 text-sm uppercase tracking-wide text-stone-500">Abadikan Momen Wisuda</p>
                        <div class="mb-6 flex items-baseline">
                            <span class="text-4xl font-extrabold tracking-tight text-stone-900">250k</span>
                            <span class="ml-1 text-xl text-stone-500">/ sesi</span>
                        </div>
                        <ul class="mb-8 space-y-4 text-left text-sm text-stone-600">
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Sesi Foto <b>30 Menit</b></span>
                            </li>
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Maksimal 5 Orang</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>30+ Edited Files</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Studio / Outdoor Campus</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-auto bg-stone-50 p-8">
                        <a href="{{ route('booking.create') }}?unit=visual&package_type=grad_1"
                            class="block w-full rounded-xl border-2 border-stone-900 bg-transparent py-3 text-center font-bold text-stone-900 transition hover:bg-stone-900 hover:text-white">
                            Pilih Paket Ini
                        </a>
                    </div>
                </div>

            </div>

            <div class="text-center">
                <a href="{{ route('pricelist.visual') }}" class="inline-flex items-center gap-2 text-stone-900 font-bold uppercase tracking-widest text-sm hover:text-amber-700 transition group">
                    View Full Pricelist
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
    </section>

    <footer class="py-12 border-t border-stone-200 text-center">
        <p class="text-stone-400 text-sm">© {{ date('Y') }} Luminara Visual Documentation</p>
    </footer>

    <script>
        // Hero Slideshow
        const heroImages = @json($heroImages ?? []);
        const bg1 = document.getElementById('hero-bg-1');
        const bg2 = document.getElementById('hero-bg-2');
        
        // Default background
        const defaultBg = "linear-gradient(to bottom, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.4)), url('https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&q=80&w=1920')";

        if (heroImages.length > 0) {
            let currentIndex = 0;
            let activeBg = bg1;
            const gradient = 'linear-gradient(to bottom, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.4))';

            bg1.style.backgroundImage = `${gradient}, url('${heroImages[0]}')`;

            if (heroImages.length > 1) {
                setInterval(() => {
                    currentIndex = (currentIndex + 1) % heroImages.length;
                    const nextImage = heroImages[currentIndex];
                    const nextBg = activeBg === bg1 ? bg2 : bg1;

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
            bg1.style.backgroundImage = defaultBg;
        }

        // Navbar Scroll Logic
        const navbar = document.getElementById('navbar');
        const navLogo = document.getElementById('nav-logo');
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        function updateNavbar() {
            // Check if mobile menu is open, if so keep navbar solid/white
            const isMenuOpen = mobileMenu && !mobileMenu.classList.contains('hidden');

            if (window.scrollY > 50 || isMenuOpen) {
                navbar.classList.remove('nav-transparent');
                navbar.classList.add('nav-scrolled');
                navLogo.src = "/images/Logo Luminara Visual-BLACK-TPR.png";
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

        // Initial check
        updateNavbar();
    </script>
