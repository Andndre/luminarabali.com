<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InvitationTemplate;
use App\Models\InvitationPage;
use App\Models\InvitationAsset;

class FullInvitationTemplateSeeder extends Seeder
{
    public function run()
    {
        // Bersihkan data lama jika ada
        InvitationPage::where('slug', 'romeo-juliet')->delete();
        InvitationTemplate::where('slug', 'rustic-elegance-demo')->delete();

        // 1. Buat Template dengan Meta Data (Background Music, RSVP)
        $template = InvitationTemplate::create([
            'name' => 'Rustic Elegance (Demo)',
            'slug' => 'rustic-elegance-demo',
            'description' => 'A full custom template showcasing the new architecture.',
            'category' => 'rustic',
            'is_active' => true,
            'created_by' => 1,
            'meta_data' => [
                'bg_music' => 'https://www.soundhelix.com/architectureplay/SoundHelix-Song-1.mp3', // Backup URL
                'rsvp_enabled' => true,
                'fonts' => ['Cormorant Garamond', 'Montserrat']
            ],
            'global_custom_css' => <<<'CSS'
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Noto+Serif+Balinese&display=swap');

body {
    font-family: 'Montserrat', sans-serif;
    -webkit-font-smoothing: antialiased;
}

.font-serif {
    font-family: 'Playfair Display', serif;
}

.font-balinese {
    font-family: 'Noto Serif Balinese', serif;
}

@keyframes zoomSlow {
    0% { transform: scale(1); }
    100% { transform: scale(1.15); }
}
.animate-zoom-slow {
    animation: zoomSlow 20s ease-in-out infinite alternate;
}
CSS,
            'cover_content' => <<<'BLADE'
<!-- Cover Page - Balinese Style -->
<div id="invitation-cover" 
     x-show="!isOpen" 
     x-transition.opacity.duration.1500ms
     class="fixed inset-0 z-[100] flex items-center justify-center bg-[#2C1E16] overflow-hidden font-sans">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1625759190925-a67568fd123b?q=80&w=687&auto=format&fit=crop" 
             alt="Cover" class="w-full h-full object-cover opacity-60 scale-105 animate-zoom-slow">
        <!-- Balinese gradient overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#2C1E16] via-[#2C1E16]/50 to-[#2C1E16]/20"></div>
    </div>
    
    <!-- Ornaments Top/Bottom (Simulated with CSS/SVG) -->
    <div class="absolute top-0 left-0 w-full h-32 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
    
    <div class="relative z-10 text-center px-6 w-full max-w-lg border-x border-[#C5A059]/30 py-12 backdrop-blur-sm bg-[#2C1E16]/30">
        <!-- Top Ornament -->
        <div class="flex justify-center mb-6">
            <svg class="w-8 h-8 text-[#C5A059]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15 8H9L12 2Z M12 22L9 16H15L12 22Z M2 12L8 9V15L2 12Z M22 12L16 15V9L22 12Z M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/></svg>
        </div>
        
        <p class="font-sans text-[10px] tracking-[0.4em] text-[#C5A059] uppercase mb-6">Pewiwahan / The Wedding Of</p>
        
        <h1 class="font-serif text-5xl md:text-7xl text-white mb-4 leading-tight drop-shadow-lg">
            {{ $page->groom_name ?? 'Wayan' }}<br>
            <span class="text-3xl italic text-[#C5A059] my-2 block">&amp;</span>
            {{ $page->bride_name ?? 'Ni Luh' }}
        </h1>
        
        <div class="mt-12 mb-12">
            <p class="text-[10px] text-white/50 uppercase tracking-[0.3em] mb-3">Om Swastiastu</p>
            <p class="text-xs text-white/70 uppercase tracking-widest mb-2">Kepada Yth. Bapak/Ibu/Saudara/i</p>
            <p class="text-xl font-serif text-[#C5A059] border-b border-[#C5A059]/50 inline-block pb-1 px-4">{{ request()->query('to', 'Tamu Spesial') }}</p>
        </div>

        <button @click="openInvitation()"  class="group relative inline-flex items-center justify-center px-8 py-3 border border-[#C5A059] bg-[#C5A059]/10 text-white font-medium tracking-[0.2em] text-[10px] uppercase hover:bg-[#C5A059] hover:text-[#2C1E16] transition-all duration-700">
            <span class="flex items-center gap-3">
                Buka Undangan
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </span>
        </button>
        
        <!-- Bottom Ornament -->
        <div class="flex justify-center mt-10">
            <svg class="w-6 h-6 text-[#C5A059]/50" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15 8H9L12 2Z M12 22L9 16H15L12 22Z"/></svg>
        </div>
    </div>
</div>
BLADE,
            'blade_content' => <<<'BLADE'
<!-- Hero -->
<section class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden bg-[#F9F6F0]">
    <div class="absolute inset-0 z-0">
        <!-- Balinese Background Architecture/Nature -->
        <img src="https://images.unsplash.com/photo-1537953773345-d172ccf13cf1?q=80&w=2000&auto=format&fit=crop" class="w-full h-full object-cover opacity-10 grayscale">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-[#F9F6F0]"></div>
    </div>
    
    <div class="relative z-10 text-center px-6 pt-20">
        <!-- Balinese motif SVG -->
        <svg class="w-12 h-12 mx-auto text-[#C5A059] mb-6" data-reveal="up" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15 8H9L12 2Z M12 22L9 16H15L12 22Z M2 12L8 9V15L2 12Z M22 12L16 15V9L22 12Z M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/></svg>
        
        <p class="font-sans text-[10px] tracking-[0.4em] uppercase text-[#2C1E16]/60 mb-6" data-reveal="up">Om Swastiastu</p>
        <h2 class="font-serif text-6xl md:text-8xl text-[#2C1E16] leading-tight mb-8" data-reveal="up" style="transition-delay: 200ms;">
            {{ $page->groom_name ?? 'Wayan' }}<br>
            <span class="italic text-[#C5A059] text-5xl md:text-6xl my-2 block">&amp;</span>
            {{ $page->bride_name ?? 'Ni Luh' }}
        </h2>
        <p class="font-sans text-[#2C1E16]/80 tracking-[0.3em] uppercase text-xs" data-reveal="up" style="transition-delay: 400ms;">
            {{ optional($page->event_date)->format('d . m . Y') ?? '12 . 12 . 2026' }}
        </p>
    </div>
</section>

<!-- Quote (Balinese / Hindu Sloka) -->
<section class="py-32 bg-[#2C1E16] text-center px-6 relative overflow-hidden">
    <div class="absolute inset-0 opacity-5 bg-[url('https://www.transparenttextures.com/patterns/diamond-upholstery.png')]"></div>
    <div class="max-w-4xl mx-auto relative z-10" data-reveal="up">
        <p class="font-sans text-[10px] uppercase tracking-[0.3em] text-[#C5A059] mb-8">Sloka Rg Veda X.85.42</p>
        <p class="font-serif text-2xl md:text-4xl text-white leading-loose mb-10 italic">
            "Ihaiva stam ma vi yaustam, visvam ayur vyasnutam,<br>kridantau puttrair naptrbhih, modamanau sve grhe."
        </p>
        <p class="font-sans text-white/60 tracking-[0.1em] text-sm leading-relaxed max-w-2xl mx-auto">
            "Wahai pasangan suami-istri, semoga kalian tetap bersatu dan tidak pernah terpisahkan. Semoga kalian mencapai umur penuh, bersukaria bersama putra-putri dan cucu-cucu kalian, senantiasa bergembira di rumah kalian sendiri."
        </p>
    </div>
</section>

<!-- Couple Info -->
<section class="py-32 bg-[#F9F6F0] relative">
    <!-- Corner Ornaments -->
    <div class="absolute top-0 left-0 w-32 h-32 opacity-20 bg-[url('data:image/svg+xml;utf8,<svg viewBox=\%220 0 100 100\%22 xmlns=\%22http://www.w3.org/2000/svg\%22><path d=\%22M0,0 L100,0 C100,55.23 55.23,100 0,100 L0,0 Z\%22 fill=\%22%23C5A059\%22/></svg>')]"></div>
    <div class="absolute bottom-0 right-0 w-32 h-32 opacity-20 bg-[url('data:image/svg+xml;utf8,<svg viewBox=\%220 0 100 100\%22 xmlns=\%22http://www.w3.org/2000/svg\%22><path d=\%22M100,100 L0,100 C0,44.77 44.77,0 100,0 L100,100 Z\%22 fill=\%22%23C5A059\%22/></svg>')]"></div>

    <div class="max-w-6xl mx-auto px-6 relative z-10">
        <div class="text-center mb-24" data-reveal="up">
            <h3 class="font-serif text-5xl text-[#2C1E16]">Sang Pengantin</h3>
            <div class="w-24 h-1 bg-gradient-to-r from-transparent via-[#C5A059] to-transparent mx-auto mt-8"></div>
        </div>
        
        <div class="flex flex-col md:flex-row items-center justify-center gap-16 md:gap-24">
            <!-- Groom -->
            <div class="text-center md:w-1/3" data-reveal="left" style="transition-delay: 100ms;">
                <div class="w-64 h-80 mx-auto mb-8 overflow-hidden rounded-tl-[80px] rounded-br-[80px] border-4 border-[#C5A059]/20 shadow-xl shadow-[#2C1E16]/10 p-2 bg-white">
                    <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover rounded-tl-[70px] rounded-br-[70px] hover:scale-105 transition-transform duration-1000 grayscale sepia-[.3]">
                </div>
                <h4 class="font-serif text-3xl text-[#2C1E16] mb-3">{{ $page->groom_name ?? 'I Wayan Romeo' }}</h4>
                <p class="text-[#2C1E16]/60 text-sm mb-6 leading-relaxed">Putra dari<br>Bpk. I Made Montague & Ibu Ni Nyoman Lady</p>
                <a href="#" class="text-[#C5A059] hover:text-[#2C1E16] text-[10px] uppercase tracking-[0.2em] transition border border-[#C5A059] px-4 py-2 rounded-full">@romeo_ig</a>
            </div>
            
            <div class="text-7xl font-serif italic text-[#C5A059] opacity-30" data-reveal="zoom">&amp;</div>
            
            <!-- Bride -->
            <div class="text-center md:w-1/3" data-reveal="right" style="transition-delay: 300ms;">
                <div class="w-64 h-80 mx-auto mb-8 overflow-hidden rounded-tr-[80px] rounded-bl-[80px] border-4 border-[#C5A059]/20 shadow-xl shadow-[#2C1E16]/10 p-2 bg-white">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover rounded-tr-[70px] rounded-bl-[70px] hover:scale-105 transition-transform duration-1000 grayscale sepia-[.3]">
                </div>
                <h4 class="font-serif text-3xl text-[#2C1E16] mb-3">{{ $page->bride_name ?? 'Ni Luh Juliet' }}</h4>
                <p class="text-[#2C1E16]/60 text-sm mb-6 leading-relaxed">Putri dari<br>Bpk. I Ketut Capulet & Ibu Ni Putu Lady</p>
                <a href="#" class="text-[#C5A059] hover:text-[#2C1E16] text-[10px] uppercase tracking-[0.2em] transition border border-[#C5A059] px-4 py-2 rounded-full">@juliet_ig</a>
            </div>
        </div>
    </div>
</section>

<!-- Image Break / Balinese Ritual -->
<section class="h-[70vh] bg-fixed bg-center bg-cover relative" data-reveal="zoom" style="background-image: url('https://plus.unsplash.com/premium_photo-1682097623645-4fd444d9cecb?q=80&w=2000&auto=format&fit=crop');">
    <div class="absolute inset-0 bg-[#2C1E16]/40"></div>
    <div class="absolute inset-0 flex items-center justify-center">
        <h2 class="text-white font-balinese text-[8rem] md:text-[12rem] text-center opacity-90 tracking-wider drop-shadow-md">ᬒᬁ</h2>
    </div>
</section>

<!-- Countdown (Alpine) -->
<section class="py-24 bg-white relative overflow-hidden border-b border-[#F9F6F0]" 
         x-data="countdown('08:00 12-12-2026')">
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10" data-reveal="up">
        <h3 class="font-sans text-[10px] tracking-[0.4em] uppercase text-[#C5A059] mb-16 border-b border-[#C5A059]/30 inline-block pb-2">Menuju Hari Bahagia</h3>
        <div class="flex flex-wrap justify-center gap-6 md:gap-16">
            <div class="w-24 bg-[#F9F6F0] p-4 rounded-t-full rounded-b-lg border border-[#C5A059]/20 shadow-sm hover:-translate-y-1 transition-transform">
                <div class="font-serif text-5xl text-[#2C1E16] mb-2" x-text="days">00</div>
                <div class="text-[9px] uppercase tracking-widest text-[#C5A059]">Hari</div>
            </div>
            <div class="w-24 bg-[#F9F6F0] p-4 rounded-t-full rounded-b-lg border border-[#C5A059]/20 shadow-sm hover:-translate-y-1 transition-transform">
                <div class="font-serif text-5xl text-[#2C1E16] mb-2" x-text="hours">00</div>
                <div class="text-[9px] uppercase tracking-widest text-[#C5A059]">Jam</div>
            </div>
            <div class="w-24 bg-[#F9F6F0] p-4 rounded-t-full rounded-b-lg border border-[#C5A059]/20 shadow-sm hover:-translate-y-1 transition-transform">
                <div class="font-serif text-5xl text-[#2C1E16] mb-2" x-text="minutes">00</div>
                <div class="text-[9px] uppercase tracking-widest text-[#C5A059]">Menit</div>
            </div>
            <div class="w-24 bg-[#F9F6F0] p-4 rounded-t-full rounded-b-lg border border-[#C5A059]/20 shadow-sm hover:-translate-y-1 transition-transform">
                <div class="font-serif text-5xl text-[#C5A059] mb-2" x-text="seconds">00</div>
                <div class="text-[9px] uppercase tracking-widest text-[#2C1E16]">Detik</div>
            </div>
        </div>
    </div>
</section>

<!-- Event Details -->
<section class="py-32 bg-[#F9F6F0]">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-20" data-reveal="up">
            <h3 class="font-serif text-5xl text-[#2C1E16] mb-6">Rangkaian Acara</h3>
            <div class="w-24 h-1 bg-gradient-to-r from-transparent via-[#C5A059] to-transparent mx-auto"></div>
        </div>
        
        <div class="grid md:grid-cols-2 gap-12 max-w-4xl mx-auto relative">
            <!-- Central Line for Desktop -->
            <div class="hidden md:block absolute left-1/2 top-0 bottom-0 w-px bg-gradient-to-b from-transparent via-[#C5A059]/50 to-transparent transform -translate-x-1/2"></div>
            
            <!-- Pawiwahan -->
            <div class="bg-white p-10 text-center shadow-lg shadow-[#2C1E16]/5 rounded-sm border-x border-[#C5A059]/20 relative z-10" data-reveal="right">
                <div class="w-16 h-16 mx-auto bg-[#F9F6F0] rounded-full flex items-center justify-center mb-8 border border-[#C5A059]/30">
                    <svg class="w-8 h-8 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
                </div>
                <h4 class="font-serif text-3xl text-[#2C1E16] mb-4">Pawiwahan</h4>
                <p class="font-sans font-medium text-[#C5A059] mb-2 text-sm">{{ optional($page->event_date)->format('l, d F Y') ?? 'Minggu, 12 Desember 2026' }}</p>
                <p class="text-[#2C1E16]/60 mb-8 font-light text-sm">08:00 WITA - Selesai</p>
                <div class="mb-10">
                    <p class="font-bold text-[#2C1E16] mb-1">Griya Santrian</p>
                    <p class="text-xs text-[#2C1E16]/60 font-light">Jl. Danau Tamblingan No. 47, Sanur, Bali</p>
                </div>
                <a href="#" target="_blank" class="inline-block px-8 py-3 border border-[#2C1E16] text-[#2C1E16] text-[10px] uppercase tracking-[0.2em] hover:bg-[#2C1E16] hover:text-white transition">Lihat Peta</a>
            </div>
            
            <!-- Resepsi -->
            <div class="bg-white p-10 text-center shadow-lg shadow-[#2C1E16]/5 rounded-sm border-x border-[#C5A059]/20 relative z-10" data-reveal="left" style="transition-delay: 200ms;">
                <div class="w-16 h-16 mx-auto bg-[#F9F6F0] rounded-full flex items-center justify-center mb-8 border border-[#C5A059]/30">
                    <svg class="w-8 h-8 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"></path></svg>
                </div>
                <h4 class="font-serif text-3xl text-[#2C1E16] mb-4">Resepsi</h4>
                <p class="font-sans font-medium text-[#C5A059] mb-2 text-sm">{{ optional($page->event_date)->format('l, d F Y') ?? 'Minggu, 12 Desember 2026' }}</p>
                <p class="text-[#2C1E16]/60 mb-8 font-light text-sm">11:00 WITA - 14:00 WITA</p>
                <div class="mb-10">
                    <p class="font-bold text-[#2C1E16] mb-1">Griya Santrian</p>
                    <p class="text-xs text-[#2C1E16]/60 font-light">Jl. Danau Tamblingan No. 47, Sanur, Bali</p>
                </div>
                <a href="#" target="_blank" class="inline-block px-8 py-3 border border-[#2C1E16] text-[#2C1E16] text-[10px] uppercase tracking-[0.2em] hover:bg-[#2C1E16] hover:text-white transition">Lihat Peta</a>
            </div>
        </div>
    </div>
</section>

<!-- Gallery -->
<section class="py-32 bg-white border-t border-[#F9F6F0]">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-20" data-reveal="up">
            <h3 class="font-serif text-5xl text-[#2C1E16] mb-6">Galeri Kasih</h3>
            <div class="w-24 h-1 bg-gradient-to-r from-transparent via-[#C5A059] to-transparent mx-auto"></div>
        </div>
        
        <!-- Masonry Grid with some spacing -->
        <div class="columns-1 sm:columns-2 lg:columns-3 gap-6 space-y-6">
            <div class="break-inside-avoid" data-reveal="up">
                <img x-lightbox src="https://images.unsplash.com/photo-1586420669671-701d93b76748?q=80&w=1200&auto=format&fit=crop" class="w-full rounded-sm shadow-sm hover:opacity-90 transition grayscale hover:grayscale-0 sepia-[.2] hover:scale-[1.02] duration-500">
            </div>
            <div class="break-inside-avoid" data-reveal="up" style="transition-delay: 100ms;">
                <img x-lightbox src="https://images.unsplash.com/photo-1587200868091-23e92ff750b4?q=80&w=1200&auto=format&fit=crop" class="w-full rounded-sm shadow-sm hover:opacity-90 transition grayscale hover:grayscale-0 sepia-[.2] hover:scale-[1.02] duration-500">
            </div>
            <div class="break-inside-avoid" data-reveal="up" style="transition-delay: 200ms;">
                <img x-lightbox src="https://plus.unsplash.com/premium_photo-1661443432542-d934aba7c922?q=80&w=1200&auto=format&fit=crop" class="w-full rounded-sm shadow-sm hover:opacity-90 transition grayscale hover:grayscale-0 sepia-[.2] hover:scale-[1.02] duration-500">
            </div>
            <div class="break-inside-avoid" data-reveal="up">
                <img x-lightbox src="https://images.unsplash.com/photo-1671517477698-fafce6ec9c02?q=80&w=1200&auto=format&fit=crop" class="w-full rounded-sm shadow-sm hover:opacity-90 transition grayscale hover:grayscale-0 sepia-[.2] hover:scale-[1.02] duration-500">
            </div>
            <div class="break-inside-avoid" data-reveal="up" style="transition-delay: 100ms;">
                <img x-lightbox src="https://images.unsplash.com/photo-1672251486261-71681105b331?q=80&w=1200&auto=format&fit=crop" class="w-full rounded-sm shadow-sm hover:opacity-90 transition grayscale hover:grayscale-0 sepia-[.2] hover:scale-[1.02] duration-500">
            </div>
            <div class="break-inside-avoid" data-reveal="up" style="transition-delay: 200ms;">
                <img x-lightbox src="https://images.unsplash.com/photo-1625759190925-a67568fd123b?q=80&w=1200&auto=format&fit=crop" class="w-full rounded-sm shadow-sm hover:opacity-90 transition grayscale hover:grayscale-0 sepia-[.2] hover:scale-[1.02] duration-500">
            </div>
        </div>
    </div>
</section>

<!-- RSVP -->
<section class="py-32 bg-[#2C1E16] text-white relative overflow-hidden">
    <!-- Subtle Pattern -->
    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/black-scales.png')]"></div>
    <div class="max-w-3xl mx-auto px-6 text-center relative z-10" data-reveal="up">
        <h3 class="font-serif text-4xl md:text-5xl mb-6 text-[#C5A059]">Buku Tamu & RSVP</h3>
        <p class="text-white/60 mb-16 font-light tracking-wide text-sm">Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu.</p>
        
        <form class="bg-[#2C1E16] p-8 md:p-14 rounded-sm border border-[#C5A059]/30 text-left shadow-2xl relative">
            <!-- Corner Accents -->
            <div class="absolute top-0 left-0 w-4 h-4 border-t border-l border-[#C5A059] -translate-x-1 -translate-y-1"></div>
            <div class="absolute top-0 right-0 w-4 h-4 border-t border-r border-[#C5A059] translate-x-1 -translate-y-1"></div>
            <div class="absolute bottom-0 left-0 w-4 h-4 border-b border-l border-[#C5A059] -translate-x-1 translate-y-1"></div>
            <div class="absolute bottom-0 right-0 w-4 h-4 border-b border-r border-[#C5A059] translate-x-1 translate-y-1"></div>

            <div class="mb-8">
                <label class="block text-[10px] uppercase tracking-widest text-[#C5A059] mb-3">Nama Lengkap</label>
                <input type="text" class="w-full bg-transparent border-b border-white/10 focus:border-[#C5A059] py-3 outline-none text-white text-sm transition placeholder-white/20" placeholder="Tulis nama anda...">
            </div>
            <div class="mb-8">
                <label class="block text-[10px] uppercase tracking-widest text-[#C5A059] mb-3">Kehadiran</label>
                <select class="w-full bg-transparent border-b border-white/10 focus:border-[#C5A059] py-3 outline-none text-white text-sm transition [&>option]:text-[#2C1E16] appearance-none cursor-pointer">
                    <option value="">Pilih status kehadiran...</option>
                    <option value="hadir">Ya, saya akan hadir</option>
                    <option value="tidak">Maaf, saya tidak bisa hadir</option>
                </select>
            </div>
            <div class="mb-10">
                <label class="block text-[10px] uppercase tracking-widest text-[#C5A059] mb-3">Ucapan & Doa</label>
                <textarea rows="3" class="w-full bg-transparent border-b border-white/10 focus:border-[#C5A059] py-3 outline-none text-white text-sm transition placeholder-white/20" placeholder="Berikan ucapan terbaik anda..."></textarea>
            </div>
            <button type="button" class="w-full py-4 bg-[#C5A059] text-[#2C1E16] font-medium uppercase tracking-[0.2em] text-[10px] hover:bg-[#a6864a] transition shadow-lg shadow-[#C5A059]/10">Kirim Konfirmasi</button>
        </form>
    </div>
</section>

<!-- Footer -->
<footer class="py-16 bg-[#1a110d] text-center text-white/30 text-[10px] tracking-[0.3em] uppercase">
    <p class="mb-4 text-[#C5A059]">Om Shanti Shanti Shanti Om</p>
    <p>Created with ♥ by Luminara Visual</p>
</footer>
BLADE
        ]);

        // Tetapkan URL musik yang benar jika tersedia dalam Asset
        $lagu = InvitationAsset::where('asset_name', 'Lagu Pernikahan Kita - Tiara Andini')->first();
        if ($lagu) {
            $md = $template->meta_data;
            $md['bg_music'] = asset('storage/'.$lagu->file_path);
            $template->meta_data = $md;
            $template->save();
        }

        // 2. Buat Dummy Invitation Page untuk menguji Template ini
        $page = InvitationPage::create([
            'template_id' => $template->id,
            'title' => 'Pernikahan Romeo & Juliet',
            'slug' => 'romeo-juliet',
            'groom_name' => 'Romeo Montague',
            'bride_name' => 'Juliet Capulet',
            'event_date' => '2026-12-12 08:00:00',
            'published_status' => 'published',
            'created_by' => 1,
            'meta_data' => []
        ]);

        $this->command->info('Template dan Page "Romeo & Juliet" berhasil disimulasikan! Buka: /invitation/romeo-juliet');
    }
}

