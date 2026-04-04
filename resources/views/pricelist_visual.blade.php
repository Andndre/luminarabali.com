<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <x-seo
        title="Pricelist Wedding & Event Photography Bali 2026 - Luminara Visual"
        description="Paket lengkap dokumentasi pernikahan dan acara di Bali. Photography, videography, dan paket kombinasi. Harga transparan dan sesuai budget."
        keywords="harga wedding photography bali, pricelist wedding bali, paket dokumentasi pernikahan, photography videography bali"
        og_image="/images/Logo Luminara Visual-BLACK-TPR.png"
    />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,700;1,400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
        #navbar { transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-stone-50 text-stone-900 selection:bg-amber-200 selection:text-stone-900">

    <!-- Navbar -->
    <nav id="navbar" class="bg-white/90 backdrop-blur-md fixed z-50 w-full border-b border-stone-100 shadow-sm">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">
                <a href="{{ route('visual.home') }}" class="flex flex-shrink-0 items-center gap-3">
                    <img src="/images/Logo Luminara Visual-BLACK-TPR.png" alt="Luminara Logo" class="h-8 w-auto">
                    <span class="font-serif text-xl font-bold tracking-wide">Luminara <span class="italic font-normal text-stone-500 text-base">Visual</span></span>
                </a>

                <div class="hidden items-center space-x-8 md:flex">
                    <a href="{{ route('visual.home') }}" class="text-sm font-medium tracking-wide text-stone-600 hover:text-amber-700 transition uppercase">KEMBALI KE BERANDA</a>
                    <a href="{{ route('booking.create') }}?unit=visual"
                        class="transform rounded-full bg-stone-900 px-6 py-2 text-sm font-bold text-white uppercase tracking-wide shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:bg-stone-800">
                        Booking Sekarang
                    </a>
                </div>
                 <!-- Mobile Menu Button -->
                 <div class="flex items-center md:hidden">
                    <a href="{{ route('booking.create') }}?unit=visual" class="text-xs font-bold uppercase bg-stone-900 text-white px-3 py-2 rounded-full mr-3">Booking</a>
                    <a href="{{ route('visual.home') }}" class="p-2 text-stone-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-32 pb-20 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-amber-700 font-bold uppercase tracking-[0.2em] text-xs mb-3 block">Investment</span>
                <h1 class="font-serif text-4xl md:text-5xl mb-6 text-stone-900">Documentation Packages</h1>
                <p class="text-stone-500 max-w-2xl mx-auto text-lg font-light">Koleksi paket dokumentasi yang dirancang untuk mengabadikan momen spesial Anda secara estetis dan abadi.</p>
            </div>

            <!-- Graduation Section -->
            <div class="mb-20">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-px bg-stone-300 flex-1"></div>
                    <h2 class="font-serif text-2xl text-stone-800 italic">Graduation Sessions</h2>
                    <div class="h-px bg-stone-300 flex-1"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @php
                        $gradPackages = $packages->filter(fn($p) => str_starts_with($p->type, 'grad'));
                    @endphp

                    @forelse($gradPackages as $pkg)
                    <div class="bg-white p-8 rounded-2xl shadow-[0_5px_20px_rgba(0,0,0,0.03)] border border-stone-100 hover:-translate-y-1 transition duration-300 flex flex-col">
                        <div class="mb-6 text-center">
                            <h3 class="font-serif text-2xl mb-2">{{ $pkg->name }}</h3>
                            <div class="text-amber-700 font-bold text-lg tracking-widest">
                                Rp {{ number_format($pkg->base_price / 1000, 0) }}k
                            </div>
                        </div>
                        <div class="flex-grow space-y-4 text-stone-600 text-sm mb-8 text-center">
                            <p>{{ $pkg->description }}</p>
                            @foreach($pkg->prices as $price)
                                @if($price->description)
                                    <p class="font-medium text-stone-800">{{ $price->description }}</p>
                                @endif
                            @endforeach
                        </div>
                        <a href="{{ route('booking.create') }}?unit=visual&type={{ $pkg->type }}" class="block w-full py-3 border border-stone-300 text-stone-600 font-bold text-center rounded-full hover:bg-stone-900 hover:text-white hover:border-stone-900 transition text-sm uppercase tracking-wider">
                            Select Package
                        </a>
                    </div>
                    @empty
                    <div class="col-span-3 text-center text-stone-400 italic">Belum ada paket graduation tersedia.</div>
                    @endforelse
                </div>
            </div>

            <!-- Event Section -->
            <div class="mb-20">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-px bg-stone-300 flex-1"></div>
                    <h2 class="font-serif text-2xl text-stone-800 italic">Wedding & Event Documentation</h2>
                    <div class="h-px bg-stone-300 flex-1"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    @php
                        $visualPackages = $packages->filter(fn($p) => str_starts_with($p->type, 'visual'))->sortBy('base_price');
                    @endphp

                    @forelse($visualPackages as $pkg)
                    <div class="bg-stone-900 text-stone-100 p-8 rounded-2xl shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col relative overflow-hidden group">
                        @if($pkg->type == 'visual_premium')
                            <div class="absolute top-0 right-0 bg-amber-600 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg uppercase tracking-widest z-20">Most Complete</div>
                        @endif

                        <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                        </div>
                        
                        <div class="mb-6 relative z-10">
                            <h3 class="font-serif text-2xl mb-2">{{ str_replace('Visual: ', '', $pkg->name) }}</h3>
                            <div class="text-amber-200 font-bold text-2xl tracking-widest">
                                Rp {{ number_format($pkg->base_price / 1000, 0) }}k
                            </div>
                        </div>
                        
                        <div class="flex-grow relative z-10 mb-8">
                             <div class="space-y-2 text-stone-300 text-xs leading-relaxed">
                                {!! nl2br(e($pkg->description)) !!}
                             </div>
                        </div>

                        <a href="{{ route('booking.create') }}?unit=visual&type={{ $pkg->type }}" class="block w-full py-3 bg-amber-700 text-white font-bold text-center rounded-xl hover:bg-amber-600 transition text-xs uppercase tracking-wider relative z-10 shadow-lg shadow-amber-900/50">
                            Book This Package
                        </a>
                    </div>
                    @empty
                    <div class="col-span-3 text-center text-stone-400 italic">Belum ada paket event tersedia.</div>
                    @endforelse
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-stone-100 p-8 rounded-2xl text-center max-w-3xl mx-auto">
                <p class="text-stone-600 text-sm mb-4">Untuk kebutuhan custom, extra hour, atau wedding full-day, silakan hubungi kami langsung.</p>
                <a href="https://wa.me/6287788986136" class="inline-flex items-center gap-2 text-stone-900 font-bold hover:text-amber-700 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    Chat Admin for Custom Request
                </a>
            </div>

        </div>
    </main>

    <footer class="py-12 border-t border-stone-200 text-center bg-white">
        <p class="text-stone-400 text-sm">© {{ date('Y') }} Luminara Visual Documentation</p>
    </footer>

</body>
</html>