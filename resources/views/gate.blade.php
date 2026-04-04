<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <x-seo title="Luminara Group - Premium Wedding & Event Documentation & Photobooth Bali"
        description="Luminara Group menyediakan layanan Photobooth dan Visual Documentation premium untuk pernikahan, graduation, dan acara spesial lainnya di Bali."
        keywords="luminara, photobooth bali, wedding photography bali, event documentation, 360 video booth, graduation photobooth"
        og_image="/images/logo.png" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        .split-bg {
            background: linear-gradient(to bottom, #0f0f0f 50%, #ffffff 50%);
        }

        @media (min-width: 1024px) {
            .split-bg {
                background: linear-gradient(to right, #0f0f0f 50%, #ffffff 50%);
            }
        }

        .card-transition {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>

<body class="relative overflow-x-hidden bg-gray-50">

    <!-- Central Logo -->
    <div class="absolute left-1/2 top-8 z-30 w-32 -translate-x-1/2 mix-blend-difference md:w-40">
        <img src="/images/Logo Luminara Visual-WHITE-TPR.png" alt="Luminara Group"
            class="h-auto w-full drop-shadow-2xl">
    </div>

    <main class="split-bg flex min-h-screen flex-col lg:flex-row">

        <!-- Luminara Visual (Dark Side) -->
        <div class="group relative flex flex-1 cursor-pointer flex-col items-center justify-center overflow-hidden p-8 lg:p-16"
            onclick="window.location='{{ route('visual.home') }}'">
            <!-- Hover Overlay -->
            <div
                class="absolute inset-0 bg-yellow-500 opacity-0 transition-opacity duration-500 group-hover:opacity-10">
            </div>

            <div class="relative z-10 mt-16 flex flex-col items-center text-center lg:mt-0">
                <h2 class="mb-4 font-serif text-4xl tracking-tight text-white lg:text-6xl">Luminara <span
                        class="text-yellow-500">Visual</span></h2>
                <p class="mb-8 max-w-sm text-sm leading-relaxed text-gray-400 lg:text-base">Premium Documentation for
                    Wedding, Graduation, & Private Events. Captured with heart and soul.</p>

                <a href="{{ route('visual.home') }}"
                    class="rounded-full border border-white px-8 py-3 text-xs font-bold uppercase tracking-widest text-white transition-colors duration-300 hover:bg-white hover:text-black">Explore
                    Visual</a>
            </div>

            <!-- Background Decoration -->
            <div
                class="pointer-events-none absolute -bottom-10 -left-10 select-none font-serif text-[15rem] font-bold text-white opacity-5">
                VISUAL</div>
        </div>

        <!-- Luminara Photobooth (Light Side) -->
        <div class="group relative flex flex-1 cursor-pointer flex-col items-center justify-center overflow-hidden bg-white p-8 lg:bg-transparent lg:p-16"
            onclick="window.location='{{ route('photobooth.home') }}'">
            <!-- Hover Overlay -->
            <div class="absolute inset-0 bg-black opacity-0 transition-opacity duration-500 group-hover:opacity-5">
            </div>

            <div class="relative z-10 mt-16 flex flex-col items-center text-center lg:mt-0">
                <h2 class="mb-4 font-serif text-4xl tracking-tight text-black lg:text-6xl">Luminara <span
                        class="text-yellow-600">Photobooth</span></h2>
                <p class="mb-8 max-w-sm text-sm leading-relaxed text-gray-500 lg:text-base">Instant Fun, High-Quality
                    Prints, & 360 Video Experience. The ultimate party booster.</p>

                <a href="{{ route('photobooth.home') }}"
                    class="rounded-full border border-black px-8 py-3 text-xs font-bold uppercase tracking-widest text-black transition-colors duration-300 hover:bg-black hover:text-white">Explore
                    Photobooth</a>
            </div>

            <!-- Background Decoration -->
            <div
                class="pointer-events-none absolute -right-10 -top-10 select-none font-serif text-[12rem] font-bold text-black opacity-5">
                PHOTO</div>
        </div>

    </main>

    <footer class="pointer-events-none fixed bottom-4 z-20 w-full text-center">
        <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-gray-500">© {{ date('Y') }} Luminara Group
            Bali</p>
    </footer>

</body>

</html>
