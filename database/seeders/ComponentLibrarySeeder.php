<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ComponentLibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = \App\Models\User::where('division', 'super_admin')->first();
        if (!$superAdmin) return;

        $components = [
            // 1. Premium Cover
            [
                'name' => 'Premium Cover Section',
                'slug' => 'premium-cover-section',
                'category' => 'cover',
                'type' => 'section',
                'description' => 'A full-screen cover section with background image, elegant typography, and a call-to-action button to open the invitation.',
                'code' => '<!-- Premium Cover Section -->
<section class="relative min-h-screen flex flex-col items-center justify-center p-6 text-center text-white" x-show="!isOpen">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=2000&auto=format&fit=crop" class="w-full h-full object-cover object-center" alt="Cover Background">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-[2px]"></div>
    </div>
    
    <!-- Content -->
    <div class="relative z-10 flex flex-col items-center justify-center space-y-6 w-full max-w-lg border border-white/20 p-10 backdrop-blur-sm bg-black/20 rounded-xl" data-reveal="fade">
        <p class="text-sm tracking-[0.3em] uppercase font-light text-gray-200">The Wedding Of</p>
        
        <h1 class="text-6xl md:text-7xl font-[\'Great_Vibes\'] font-normal leading-tight text-white drop-shadow-md">
            <span x-text="groom_name">Romeo</span>
            <span class="block text-4xl text-[#C5A059] my-2">&amp;</span>
            <span x-text="bride_name">Juliet</span>
        </h1>
        
        <div class="w-16 h-px bg-[#C5A059] my-6"></div>
        
        <div class="text-sm font-light text-gray-200 space-y-1">
            <p>Kepada Yth. Bapak/Ibu/Saudara/i</p>
            <p class="text-xl font-medium text-white tracking-wide mt-2" x-text="guest_name">Tamu Spesial</p>
        </div>
        
        <button type="button" @click="$dispatch(\'tab-changed\', \'html\'); openInvitation()" class="mt-8 px-8 py-3 bg-[#C5A059] hover:bg-[#b08d4f] text-white text-sm tracking-widest uppercase rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg border border-white/10">
            Buka Undangan
        </button>
    </div>
</section>',
                'variables' => [],
                'is_public' => true,
                'is_active' => true,
                'created_by' => $superAdmin->id,
            ],
            // 2. Elegant Hero
            [
                'name' => 'Elegant Hero Section',
                'slug' => 'elegant-hero-section',
                'category' => 'hero',
                'type' => 'section',
                'description' => 'A clean and elegant hero section with soft background, couple names, and date.',
                'code' => '<!-- Elegant Hero Section -->
<section class="relative py-24 px-6 bg-white text-center flex flex-col items-center justify-center min-h-[80vh]">
    <div class="max-w-2xl mx-auto space-y-8" data-reveal="up">
        <div class="w-20 h-20 mx-auto rounded-full bg-gray-50 flex items-center justify-center shadow-sm border border-gray-100">
            <svg class="w-8 h-8 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
        </div>
        
        <div class="space-y-4">
            <p class="text-sm tracking-[0.2em] uppercase font-medium text-gray-500">We Are Getting Married</p>
            <h2 class="text-5xl md:text-7xl font-[\'Great_Vibes\'] text-gray-900 leading-tight">
                <span x-text="groom_name">Romeo</span>
                <span class="mx-2 text-3xl text-gray-400 font-sans">&amp;</span>
                <span x-text="bride_name">Juliet</span>
            </h2>
        </div>
        
        <div class="w-24 h-px bg-gray-200 mx-auto"></div>
        
        <div class="text-gray-600 font-light tracking-widest uppercase">
            <span x-text="new Date(event_date).toLocaleDateString(\'id-ID\', { weekday: \'long\', year: \'numeric\', month: \'long\', day: \'numeric\' })">Sabtu, 24 Agustus 2026</span>
        </div>
    </div>
</section>',
                'variables' => [],
                'is_public' => true,
                'is_active' => true,
                'created_by' => $superAdmin->id,
            ],
            // 3. Countdown Timer
            [
                'name' => 'Minimalist Countdown',
                'slug' => 'minimalist-countdown',
                'category' => 'countdown',
                'type' => 'section',
                'description' => 'A clean, 4-column grid displaying days, hours, minutes, and seconds until the event.',
                'code' => '<!-- Countdown Section -->
<section class="py-16 px-4 bg-gray-50 border-y border-gray-100" data-reveal="fade">
    <div class="max-w-4xl mx-auto text-center">
        <h3 class="text-2xl font-serif text-gray-800 mb-8">Menuju Hari Bahagia</h3>
        
        <div class="grid grid-cols-4 gap-4 md:gap-8 max-w-2xl mx-auto" x-data="countdown(event_date)">
            <div class="flex flex-col items-center justify-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <span class="text-3xl md:text-5xl font-light text-gray-900" x-text="days">00</span>
                <span class="text-xs tracking-wider uppercase text-gray-500 mt-2">Hari</span>
            </div>
            
            <div class="flex flex-col items-center justify-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <span class="text-3xl md:text-5xl font-light text-gray-900" x-text="hours">00</span>
                <span class="text-xs tracking-wider uppercase text-gray-500 mt-2">Jam</span>
            </div>
            
            <div class="flex flex-col items-center justify-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <span class="text-3xl md:text-5xl font-light text-gray-900" x-text="minutes">00</span>
                <span class="text-xs tracking-wider uppercase text-gray-500 mt-2">Menit</span>
            </div>
            
            <div class="flex flex-col items-center justify-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <span class="text-3xl md:text-5xl font-light text-gray-900" x-text="seconds">00</span>
                <span class="text-xs tracking-wider uppercase text-gray-500 mt-2">Detik</span>
            </div>
        </div>
    </div>
</section>',
                'variables' => [],
                'is_public' => true,
                'is_active' => true,
                'created_by' => $superAdmin->id,
            ],
            // 4. RSVP Form
            [
                'name' => 'Elegant RSVP Form',
                'slug' => 'elegant-rsvp-form',
                'category' => 'rsvp',
                'type' => 'section',
                'description' => 'A clean RSVP form with AJAX submission support via Alpine.js.',
                'code' => '<!-- RSVP Section -->
<section class="py-24 px-4 bg-white" id="rsvp" data-reveal="up">
    <div class="max-w-xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-serif text-gray-900 mb-4">RSVP & Ucapan</h2>
            <p class="text-gray-500 font-light">Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir.</p>
        </div>

        <div class="bg-gray-50 rounded-2xl shadow-sm p-8 border border-gray-100" 
             x-data="rsvpForm()">
             
            <form @submit.prevent="submitRsvp" x-show="!isSuccess" class="space-y-6">
                <div x-show="errorMessage" class="p-4 bg-red-50 text-red-600 rounded-lg text-sm" x-text="errorMessage" style="display: none;"></div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" x-model="formData.guest_name" required class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm focus:border-[#C5A059] focus:outline-none focus:ring-1 focus:ring-[#C5A059]" placeholder="Contoh: Budi Santoso">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kehadiran</label>
                    <select x-model="formData.status" required class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm focus:border-[#C5A059] focus:outline-none focus:ring-1 focus:ring-[#C5A059]">
                        <option value="Hadir">Ya, Saya Akan Hadir</option>
                        <option value="Tidak Hadir">Maaf, Tidak Bisa Hadir</option>
                        <option value="Masih Ragu">Masih Ragu-ragu</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pesan & Doa (Opsional)</label>
                    <textarea x-model="formData.comments" rows="4" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm focus:border-[#C5A059] focus:outline-none focus:ring-1 focus:ring-[#C5A059]" placeholder="Tuliskan ucapan untuk kedua mempelai..."></textarea>
                </div>

                <button type="submit" :disabled="isSubmitting" class="w-full py-4 bg-gray-900 hover:bg-[#C5A059] text-white text-sm font-bold tracking-widest uppercase rounded-lg transition-colors duration-300 disabled:opacity-50 flex items-center justify-center gap-2">
                    <span x-show="!isSubmitting">Kirim RSVP</span>
                    <svg x-show="isSubmitting" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>
            
            <div x-show="isSuccess" class="text-center py-8" style="display: none;">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-2xl font-serif text-gray-900 mb-2">Terima Kasih!</h3>
                <p class="text-gray-500">Konfirmasi kehadiran Anda telah kami terima.</p>
                <button type="button" @click="isSuccess = false" class="mt-6 text-sm text-[#C5A059] hover:underline">Kirim respons lain</button>
            </div>
        </div>
    </div>
</section>',
                'variables' => [],
                'is_public' => true,
                'is_active' => true,
                'created_by' => $superAdmin->id,
            ]
        ];

        // Clean up old components that use Blade variables
        \App\Models\ComponentLibrary::whereNotIn('slug', collect($components)->pluck('slug'))->delete();
        
        foreach ($components as $component) {
            \App\Models\ComponentLibrary::updateOrCreate(
                ['slug' => $component['slug']],
                $component
            );
        }
    }
}
