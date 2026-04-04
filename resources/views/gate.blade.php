<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <x-seo
        title="Luminara Group - Premium Wedding & Event Documentation & Photobooth Bali"
        description="Luminara Group menyediakan layanan Photobooth dan Visual Documentation premium untuk pernikahan, graduation, dan acara spesial lainnya di Bali."
        keywords="luminara, photobooth bali, wedding photography bali, event documentation, 360 video booth, graduation photobooth"
        og_image="/images/Logo Luminara Visual-BLACK-TPR.png"
    />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
        .split-bg {
            background: linear-gradient(to bottom, #0f0f0f 50%, #ffffff 50%);
        }
        @media (min-width: 1024px) {
            .split-bg {
                background: linear-gradient(to right, #0f0f0f 50%, #ffffff 50%);
            }
        }
        .card-transition { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="bg-gray-50 overflow-x-hidden relative">

    <!-- Central Logo -->
    <div class="absolute top-8 left-1/2 -translate-x-1/2 z-30 w-32 md:w-40 mix-blend-difference">
        <img src="/images/Logo Luminara Visual-WHITE-TPR.png" alt="Luminara Group" class="w-full h-auto drop-shadow-2xl">
    </div>

    <main class="min-h-screen flex flex-col lg:flex-row split-bg">
        
        <!-- Luminara Visual (Dark Side) -->
        <div class="flex-1 flex flex-col items-center justify-center p-8 lg:p-16 relative group cursor-pointer overflow-hidden" onclick="window.location='{{ route('visual.home') }}'">
            <!-- Hover Overlay -->
            <div class="absolute inset-0 bg-yellow-500 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
            
            <div class="relative z-10 text-center flex flex-col items-center mt-16 lg:mt-0">
                <h2 class="text-white text-4xl lg:text-6xl font-serif mb-4 tracking-tight">Luminara <span class="text-yellow-500">Visual</span></h2>
                <p class="text-gray-400 max-w-sm mb-8 text-sm lg:text-base leading-relaxed">Premium Documentation for Wedding, Graduation, & Private Events. Captured with heart and soul.</p>
                
                <a href="{{ route('visual.home') }}" class="px-8 py-3 border border-white text-white font-bold rounded-full hover:bg-white hover:text-black transition-colors duration-300 uppercase tracking-widest text-xs">Explore Visual</a>
            </div>
            
            <!-- Background Decoration -->
            <div class="absolute -bottom-10 -left-10 text-[15rem] font-bold text-white opacity-5 select-none pointer-events-none font-serif">VISUAL</div>
        </div>

        <!-- Luminara Photobooth (Light Side) -->
        <div class="flex-1 flex flex-col items-center justify-center p-8 lg:p-16 relative group cursor-pointer overflow-hidden bg-white lg:bg-transparent" onclick="window.location='{{ route('photobooth.home') }}'">
             <!-- Hover Overlay -->
             <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>

            <div class="relative z-10 text-center flex flex-col items-center mt-16 lg:mt-0">
                <h2 class="text-black text-4xl lg:text-6xl font-serif mb-4 tracking-tight">Luminara <span class="text-yellow-600">Photobooth</span></h2>
                <p class="text-gray-500 max-w-sm mb-8 text-sm lg:text-base leading-relaxed">Instant Fun, High-Quality Prints, & 360 Video Experience. The ultimate party booster.</p>
                
                <a href="{{ route('photobooth.home') }}" class="px-8 py-3 border border-black text-black font-bold rounded-full hover:bg-black hover:text-white transition-colors duration-300 uppercase tracking-widest text-xs">Explore Photobooth</a>
            </div>

            <!-- Background Decoration -->
            <div class="absolute -top-10 -right-10 text-[12rem] font-bold text-black opacity-5 select-none pointer-events-none font-serif">PHOTO</div>
        </div>

    </main>

    <footer class="fixed bottom-4 w-full text-center z-20 pointer-events-none">
        <p class="text-[10px] uppercase tracking-[0.3em] text-gray-500 font-bold">© {{ date('Y') }} Luminara Group Bali</p>
    </footer>

</body>
</html>
