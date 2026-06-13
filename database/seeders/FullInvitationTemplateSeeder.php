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
            'global_custom_css' => '
                @import url("https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500&family=Montserrat:wght@200;300;400;500&display=swap");
                
                body {
                    font-family: "Montserrat", sans-serif;
                    background-color: #FAF8F5;
                    color: #2C3E35;
                }
                
                h1, h2, h3, h4, h5, h6, .font-serif {
                    font-family: "Cormorant Garamond", serif;
                }

                .bg-forest { background-color: #1B2B24; }
                .text-forest { color: #1B2B24; }
                .bg-gold { background-color: #C5A059; }
                .text-gold { color: #C5A059; }
                .border-gold { border-color: #C5A059; }

                /* Animations */
                @keyframes fadeUp {
                    from { opacity: 0; transform: translateY(40px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                @keyframes slowZoom {
                    from { transform: scale(1); }
                    to { transform: scale(1.1); }
                }
                @keyframes float {
                    0% { transform: translateY(0px); }
                    50% { transform: translateY(-10px); }
                    100% { transform: translateY(0px); }
                }

                .animate-fade-up {
                    animation: fadeUp 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                }
                .animate-zoom {
                    animation: slowZoom 20s linear infinite alternate;
                }
                .animate-float {
                    animation: float 4s ease-in-out infinite;
                }
                
                .reveal-on-scroll {
                    opacity: 0;
                    transform: translateY(30px);
                    transition: all 1.2s cubic-bezier(0.16, 1, 0.3, 1);
                }
                .reveal-on-scroll.is-visible {
                    opacity: 1;
                    transform: translateY(0);
                }

                /* Sembunyikan scrollbar saat cover terbuka */
                .no-scroll { overflow: hidden; }
                
                .glass-panel {
                    background: rgba(255, 255, 255, 0.1);
                    backdrop-filter: blur(10px);
                    border: 1px solid rgba(255, 255, 255, 0.2);
                }
            ',
            'cover_content' => '
<!-- Default Cover Page -->
<div id="invitation-cover" 
     x-show="!isOpen" 
     x-transition.opacity.duration.1000ms
     class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900 transition-transform duration-1000 ease-in-out">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=2000&auto=format&fit=crop" 
             alt="Cover Image" class="w-full h-full object-cover opacity-60">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>
    </div>
    
    <!-- Content -->
    <div class="relative z-10 text-center px-6 max-w-lg mx-auto transform transition-all duration-1000 translate-y-0">
        <p class="invitation-accent text-sm tracking-[0.3em] uppercase mb-6 text-gray-300">The Wedding Of</p>
        
        <h1 class="invitation-title text-6xl md:text-8xl font-serif mb-8 text-white">
            {{ $page->groom_name ?? \'Romeo\' }}<br>
            <span class="invitation-accent text-4xl italic">&</span><br>
            {{ $page->bride_name ?? \'Juliet\' }}
        </h1>
        
        <div class="mt-12 mb-12">
            <p class="text-sm text-gray-400 uppercase tracking-widest mb-2">Kepada Yth.</p>
            <p class="text-xl font-serif text-white">{{ request()->query(\'to\', \'Tamu Spesial\') }}</p>
        </div>
        
        <button @click="openInvitation()" 
                class="invitation-button group relative px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 text-white tracking-[0.2em] text-xs uppercase transition-all duration-500 overflow-hidden">
            <span class="relative z-10 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path></svg>
                Buka Undangan
            </span>
        </button>
    </div>
</div>
            ',
            'blade_content' => '
<!-- Hero Section -->
<section class="invitation-hero relative min-h-screen flex flex-col items-center justify-center text-center p-8 overflow-hidden bg-white">
    <div class="absolute inset-0 bg-cover bg-center opacity-30 fixed" style="background-image: url(\'https://images.unsplash.com/photo-1520854221256-17451cc331bf?q=80&w=2000&auto=format&fit=crop\')"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white"></div>
    
    <div class="relative z-10 max-w-2xl mx-auto pt-32">
        <p class="invitation-accent uppercase tracking-[0.3em] text-sm mb-6 reveal-on-scroll">We Are Getting Married</p>
        <h2 class="invitation-title text-6xl md:text-8xl font-serif text-gray-900 mb-8 reveal-on-scroll" style="transition-delay: 200ms;">
            {{ $page->groom_name ?? \'Romeo\' }} <br> <span class="invitation-accent text-4xl italic">&</span> <br> {{ $page->bride_name ?? \'Juliet\' }}
        </h2>
        <div class="invitation-line w-px h-32 mx-auto mt-12 animate-float reveal-on-scroll bg-gray-400" style="transition-delay: 400ms;"></div>
    </div>
</section>

<!-- Quote Section -->
<section class="invitation-quote py-24 px-6 bg-gray-50">
    <div class="max-w-4xl mx-auto text-center reveal-on-scroll">
        <svg class="invitation-accent w-12 h-12 mx-auto mb-8 opacity-50 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
        <p class="invitation-title font-serif text-3xl md:text-5xl leading-tight mb-8 text-gray-900">
            "Two souls with but a single thought,<br>two hearts that beat as one."
        </p>
        <p class="invitation-accent uppercase tracking-widest text-sm text-gray-500">— John Keats</p>
    </div>
</section>

<!-- Image Break -->
<section class="invitation-image-break h-[60vh] md:h-[80vh] bg-fixed bg-center bg-cover reveal-on-scroll" style="background-image: url(\'https://images.unsplash.com/photo-1606800052052-a08af7148866?q=80&w=2000&auto=format&fit=crop\');">
</section>

<!-- Details Section -->
<section class="invitation-details py-32 relative bg-white">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-20 reveal-on-scroll">
            <p class="invitation-accent uppercase tracking-[0.2em] text-sm mb-4 text-gray-500">Join Us to Celebrate</p>
            <h2 class="invitation-title text-5xl md:text-7xl font-serif text-gray-900">The Wedding Details</h2>
        </div>
        
        <div class="grid md:grid-cols-2 gap-12 md:gap-20">
            <!-- Akad/Ceremony Card -->
            <div class="invitation-card group bg-gray-50 p-12 text-center shadow-[0_20px_50px_rgba(0,0,0,0.05)] reveal-on-scroll hover:-translate-y-2 transition-transform duration-500 border-t-4 border-transparent hover:border-gray-300">
                <div class="invitation-icon-box w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-8 bg-white">
                    <svg class="invitation-accent w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
                </div>
                <h3 class="invitation-title text-4xl font-serif mb-2 text-gray-900">Akad Nikah</h3>
                <div class="invitation-line w-12 h-px mx-auto mb-8 bg-gray-300"></div>
                <p class="invitation-date text-gray-500 font-medium tracking-widest uppercase text-sm mb-2">{{ optional($page->event_date)->format(\'l, d F Y\') ?? \'Minggu, 12 Desember 2026\' }}</p>
                <p class="text-gray-400 mb-8 font-light">08.00 WIB - Selesai</p>
                <h4 class="invitation-title font-serif text-2xl mb-2 text-gray-800">Masjid Agung Luminara</h4>
                <p class="text-sm text-gray-500 mb-8 font-light">Jl. Cinta Damai No. 1, Bali, Indonesia</p>
                <a href="#" class="invitation-button inline-block px-8 py-3 border border-gray-900 uppercase tracking-wider text-xs transition-colors duration-300 text-gray-900 hover:bg-gray-900 hover:text-white">View Map</a>
            </div>

            <!-- Resepsi/Reception Card -->
            <div class="invitation-card group bg-gray-50 p-12 text-center shadow-[0_20px_50px_rgba(0,0,0,0.05)] reveal-on-scroll hover:-translate-y-2 transition-transform duration-500 border-t-4 border-transparent hover:border-gray-300" style="transition-delay: 200ms;">
                <div class="invitation-icon-box w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-8 bg-white">
                    <svg class="invitation-accent w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"></path></svg>
                </div>
                <h3 class="invitation-title text-4xl font-serif mb-2 text-gray-900">Resepsi</h3>
                <div class="invitation-line w-12 h-px mx-auto mb-8 bg-gray-300"></div>
                <p class="invitation-date text-gray-500 font-medium tracking-widest uppercase text-sm mb-2">{{ optional($page->event_date)->format(\'l, d F Y\') ?? \'Minggu, 12 Desember 2026\' }}</p>
                <p class="text-gray-400 mb-8 font-light">11.00 WIB - 14.00 WIB</p>
                <h4 class="invitation-title font-serif text-2xl mb-2 text-gray-800">Luminara Grand Ballroom</h4>
                <p class="text-sm text-gray-500 mb-8 font-light">Jl. Cinta Damai No. 1, Bali, Indonesia</p>
                <a href="#" class="invitation-button inline-block px-8 py-3 border border-gray-900 uppercase tracking-wider text-xs transition-colors duration-300 text-gray-900 hover:bg-gray-900 hover:text-white">View Map</a>
            </div>
        </div>
    </div>
</section>

<!-- Our Gallery Section -->
<section class="invitation-gallery py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16 reveal-on-scroll">
            <h2 class="invitation-title text-5xl md:text-7xl font-serif text-gray-900">Moments in Time</h2>
            <p class="invitation-accent uppercase tracking-[0.2em] text-sm mt-6 text-gray-500">A glimpse of our journey</p>
        </div>
        
        <!-- Simple Masonry Gallery -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="grid gap-4">
                <img class="h-auto max-w-full rounded-lg reveal-on-scroll hover:opacity-90 transition" src="https://images.unsplash.com/photo-1583939000240-690e16fa01e6?q=80&w=800&auto=format&fit=crop" alt="">
                <img class="h-auto max-w-full rounded-lg reveal-on-scroll hover:opacity-90 transition" src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?q=80&w=800&auto=format&fit=crop" alt="">
            </div>
            <div class="grid gap-4">
                <img class="h-auto max-w-full rounded-lg reveal-on-scroll hover:opacity-90 transition" style="transition-delay:100ms;" src="https://images.unsplash.com/photo-1606490204369-1736b4859a58?q=80&w=800&auto=format&fit=crop" alt="">
                <img class="h-auto max-w-full rounded-lg reveal-on-scroll hover:opacity-90 transition" style="transition-delay:100ms;" src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=800&auto=format&fit=crop" alt="">
            </div>
            <div class="grid gap-4">
                <img class="h-auto max-w-full rounded-lg reveal-on-scroll hover:opacity-90 transition" style="transition-delay:200ms;" src="https://images.unsplash.com/photo-1621801306184-fbdd90048e58?q=80&w=800&auto=format&fit=crop" alt="">
                <img class="h-auto max-w-full rounded-lg reveal-on-scroll hover:opacity-90 transition" style="transition-delay:200ms;" src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?q=80&w=800&auto=format&fit=crop" alt="">
            </div>
        </div>
    </div>
</section>
'
        ]);

        // Tetapkan URL musik yang benar jika tersedia dalam Asset
        $lagu = InvitationAsset::where('asset_name', 'Lagu Pernikahan Kita - Tiara Andini')->first();
        if ($lagu) {
            $md = $template->meta_data;
            $md['bg_music'] = asset('storage/' . $lagu->file_path);
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

