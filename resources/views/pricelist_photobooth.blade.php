<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <x-seo
        title="Daftar Harga Photobooth Bali 2026 - Luminara Photobooth"
        description="Lihat daftar harga photobooth dan 360 video booth untuk berbagai paket acara di Bali. Harga transparan, kualitas premium, booking mudah."
        keywords="harga photobooth bali, harga 360 video booth, sewa photobooth bali, paket photobooth pernikahan, daftar harga photobooth"
        og_image="/images/Logo Luminara Visual-BLACK-TPR.png"
    />
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
</head>

<body class="selection:bg-luminara-gold font-sans text-gray-800 antialiased selection:text-white bg-slate-50">

    <!-- Navbar -->
    <nav id="navbar" class="bg-white/90 backdrop-blur-md fixed z-50 w-full border-b border-gray-100 shadow-sm">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">
                <a href="{{ route('home') }}" class="flex flex-shrink-0 items-center gap-3">
                    <img src="/images/Logo Luminara Visual-BLACK-TPR.png" alt="Luminara Logo" class="h-8 w-auto">
                    <span class="font-serif text-xl font-bold tracking-wide">Luminara</span>
                </a>

                <div class="hidden items-center space-x-8 md:flex">
                    <a href="{{ route('home') }}" class="text-sm font-medium tracking-wide text-gray-600 hover:text-luminara-gold transition">KEMBALI KE BERANDA</a>
                    <a href="{{ route('booking.create') }}"
                        class="transform rounded-full bg-black px-6 py-2 text-sm font-bold text-white uppercase tracking-wide shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:bg-gray-800">
                        Booking Sekarang
                    </a>
                </div>
                 <!-- Mobile Menu Button -->
                 <div class="flex items-center md:hidden">
                    <a href="{{ route('booking.create') }}" class="text-xs font-bold uppercase bg-black text-white px-3 py-2 rounded-full mr-3">Booking</a>
                    <a href="{{ route('home') }}" class="p-2 text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-24 pb-12 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
        
        <div class="text-center mb-12">
            <h1 class="font-serif text-4xl md:text-5xl font-bold mb-4">Daftar Harga Lengkap</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Transparan dan fleksibel. Pilih paket yang sesuai dengan kebutuhan acara Anda.</p>
        </div>

        @php
            $pbFile = $packages->firstWhere('type', 'pb_file');
            $pbLimited = $packages->firstWhere('type', 'pb_limited');
            $pbUnlimited = $packages->firstWhere('type', 'pb_unlimited');
            $video360 = $packages->firstWhere('type', 'videobooth360');
            $comboUnlimited = $packages->firstWhere('type', 'combo_unlimited');
            $comboFile = $packages->firstWhere('type', 'combo_file');

            // Collect all unique durations from PB packages to build the table rows
            $durations = collect([]);
            if($pbFile) $durations = $durations->merge($pbFile->prices->pluck('duration_hours'));
            if($pbLimited) $durations = $durations->merge($pbLimited->prices->pluck('duration_hours'));
            if($pbUnlimited) $durations = $durations->merge($pbUnlimited->prices->pluck('duration_hours'));
            $durations = $durations->unique()->sort();
        @endphp

        <!-- 1. PHOTOBOOTH SECTION -->
        <section class="mb-16 bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gray-900 px-6 py-8 text-white text-center">
                <h2 class="font-serif text-2xl md:text-3xl font-bold text-luminara-gold mb-2">📸 Photo Booth Packages</h2>
                <p class="text-gray-400 text-xs md:text-sm">Include: Kamera DSLR Canon, Layar 24", Lighting Studio, Fun Props, & Softfile QR.</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="p-4 font-bold text-gray-900 sticky left-0 bg-gray-50 text-sm md:text-base">Durasi</th>
                            <th class="p-4 font-bold text-gray-600 text-center w-1/4 text-sm md:text-base">
                                <span class="block text-base md:text-lg text-gray-900">QR Only</span>
                                <span class="text-[10px] md:text-xs font-normal">(File Only, No Print)</span>
                            </th>
                            <th class="p-4 font-bold text-gray-600 text-center w-1/4 text-sm md:text-base">
                                <span class="block text-base md:text-lg text-gray-900">Limited Print</span>
                                <span class="text-[10px] md:text-xs font-normal">(Kuota Cetak Terbatas)</span>
                            </th>
                            <th class="p-4 font-bold text-luminara-gold text-center w-1/4 bg-yellow-50/50 text-sm md:text-base">
                                <span class="block text-base md:text-lg">✨ Unlimited</span>
                                <span class="text-[10px] md:text-xs font-normal text-gray-600">(Cetak Sepuasnya)</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($durations as $hour)
                            @php
                                $priceFile = $pbFile ? $pbFile->prices->firstWhere('duration_hours', $hour) : null;
                                $priceLimited = $pbLimited ? $pbLimited->prices->firstWhere('duration_hours', $hour) : null;
                                $priceUnlimited = $pbUnlimited ? $pbUnlimited->prices->firstWhere('duration_hours', $hour) : null;
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-bold sticky left-0 bg-white text-sm md:text-base whitespace-nowrap">{{ $hour }} Jam</td>
                                
                                <td class="p-4 text-center text-sm md:text-base whitespace-nowrap">
                                    @if($priceFile)
                                        Rp {{ number_format($priceFile->price/1000, 0) }}k
                                        @if($priceFile->description) <span class="block text-[10px] text-gray-500">({{ $priceFile->description }})</span> @endif
                                    @else - @endif
                                </td>
                                
                                <td class="p-4 text-center text-sm md:text-base whitespace-nowrap">
                                    @if($priceLimited)
                                        Rp {{ number_format($priceLimited->price/1000, 0) }}k
                                        @if($priceLimited->description) <span class="block text-[10px] text-gray-500">({{ $priceLimited->description }})</span> @endif
                                    @else - @endif
                                </td>
                                
                                <td class="p-4 text-center font-bold text-luminara-gold bg-yellow-50/30 text-sm md:text-base whitespace-nowrap">
                                    @if($priceUnlimited)
                                        Rp {{ number_format($priceUnlimited->price/1000, 0) }}k
                                        @if($priceUnlimited->description) <span class="block text-[10px] text-gray-500">({{ $priceUnlimited->description }})</span> @endif
                                    @else - @endif
                                </td>
                            </tr>
                        @endforeach
                         <tr class="bg-gray-50 transition border-t-2 border-gray-200">
                            <td class="p-4 font-bold sticky left-0 bg-gray-50 text-sm md:text-base">Extra</td>
                            <td class="p-4 text-center text-gray-500 text-sm md:text-base">300k/jam</td>
                            <td class="p-4 text-center text-gray-500 text-sm md:text-base">300k/jam <span class="block text-[10px]">(no print)</span></td>
                            <td class="p-4 text-center font-bold text-luminara-gold bg-yellow-50/30 text-sm md:text-base">700k/jam</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 px-6 py-4 text-xs text-gray-500 border-t border-gray-100">
                * Note: Harga Limited Print bervariasi untuk durasi 6-12 jam. Hubungi admin untuk detail lengkap 12 jam.
            </div>
        </section>

        <!-- 2. VIDEO 360 & COMBO SECTION -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
            
            <!-- Video 360 -->
            <section class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden flex flex-col">
                <div class="bg-gray-900 px-6 py-6 text-white text-center">
                    <h2 class="font-serif text-2xl font-bold text-luminara-gold">🔄 Video Booth 360</h2>
                    <p class="text-gray-400 text-xs mt-1">Unlimited Video, Slowmo/Rewind, Custom Overlay</p>
                </div>
                <div class="p-6 flex-grow">
                    @if($video360)
                        <ul class="space-y-4">
                            @foreach($video360->prices as $price)
                                <li class="flex justify-between items-center border-b border-dashed border-gray-200 pb-2">
                                    <span>{{ $price->duration_hours }} Jam</span> 
                                    <span class="font-bold text-xl">Rp {{ number_format($price->price/1000, 0) }}k</span>
                                </li>
                            @endforeach
                            <li class="flex justify-between items-center text-gray-500 pt-2"><span>Overtime Charge</span> <span>+600k / jam</span></li>
                        </ul>
                    @else
                        <p class="text-center text-gray-500">Paket tidak tersedia.</p>
                    @endif
                </div>
            </section>

            <!-- Combo Packages -->
            <section class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl shadow-xl overflow-hidden flex flex-col text-white relative">
                 <div class="absolute top-0 right-0 bg-luminara-gold text-xs font-bold px-3 py-1 rounded-bl-lg text-black">BEST VALUE</div>
                <div class="px-6 py-6 text-center border-b border-gray-700">
                    <h2 class="font-serif text-2xl font-bold text-white">⚡ Paket COMBO</h2>
                    <p class="text-gray-400 text-xs mt-1">Photobooth + Video 360 (Hemat hingga 500rb)</p>
                </div>
                <div class="p-6 flex-grow">
                     @if($comboUnlimited)
                     <div class="mb-6">
                        <h3 class="text-luminara-gold font-bold text-sm uppercase tracking-wider mb-3">Combo Unlimited Print</h3>
                        <ul class="space-y-3 text-sm">
                            @foreach($comboUnlimited->prices as $price)
                                <li class="flex justify-between">
                                    <span>{{ $price->duration_hours }} Jam</span> 
                                    <span class="font-bold">Rp {{ number_format($price->price/1000, 0) }}k</span>
                                </li>
                            @endforeach
                        </ul>
                     </div>
                     @endif

                     @if($comboFile)
                     <div>
                        <h3 class="text-gray-400 font-bold text-sm uppercase tracking-wider mb-3">Combo File Only (No Print)</h3>
                        <ul class="space-y-3 text-sm text-gray-300 border-t border-gray-700 pt-3">
                             @foreach($comboFile->prices as $price)
                                <li class="flex justify-between">
                                    <span>{{ $price->duration_hours }} Jam</span> 
                                    <span class="font-bold">Rp {{ number_format($price->price/1000, 0) }}k</span>
                                </li>
                            @endforeach
                        </ul>
                     </div>
                     @endif
                </div>
            </section>
        </div>

        <!-- 3. HOW TO ORDER -->
        <section class="bg-luminara-gold/10 rounded-3xl p-8 border border-luminara-gold/30">
            <h2 class="font-serif text-3xl font-bold text-center mb-8">Cara Pemesanan (How To Order)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <ol class="list-decimal list-inside space-y-3 text-gray-700">
                    <li><span class="font-bold">Hubungi Kami</span> via WhatsApp/Telepon.</li>
                    <li><span class="font-bold">Konsultasi</span> tanggal, lokasi, dan paket acara.</li>
                    <li><span class="font-bold">Deal Harga</span> & Paket sesuai kebutuhan.</li>
                    <li><span class="font-bold">Isi Formulir Booking</span> yang kami berikan.</li>
                </ol>
                <ol class="list-decimal list-inside space-y-3 text-gray-700" start="5">
                    <li><span class="font-bold">DP Rp 500.000</span> & kirim bukti transfer.</li>
                    <li><span class="font-bold">Terima Invoice</span> & Tanggal terkunci.</li>
                    <li><span class="font-bold">Pelunasan</span> di awal atau setelah acara selesai.</li>
                </ol>
            </div>
            <div class="mt-8 text-center">
                 <a href="https://wa.me/6287788986136" target="_blank"
                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-full transition shadow-lg hover:shadow-green-600/30">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    Chat WhatsApp
                </a>
            </div>
        </section>

    </main>

    <!-- Floating CTA Mobile -->
    <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 p-4 md:hidden z-50 shadow-[0_-5px_10px_rgba(0,0,0,0.05)]">
        <a href="{{ route('booking.create') }}" class="block w-full bg-luminara-gold text-white text-center font-bold py-3 rounded-xl uppercase tracking-wide">
            Booking Tanggal
        </a>
    </div>

    <footer class="bg-gray-900 text-white py-8 text-center text-sm md:pb-8 pb-24">
        <p>&copy; {{ date('Y') }} Luminara Photobooth. All rights reserved.</p>
    </footer>

</body>
</html>
