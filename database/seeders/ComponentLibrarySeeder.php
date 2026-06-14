<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            [
                'name' => 'Classic Hero Section',
                'slug' => 'classic-hero-section',
                'category' => 'hero',
                'type' => 'section',
                'description' => 'A beautiful hero section with background image, title, and couple names.',
                'code' => '<!-- Hero Section -->
<section class="relative min-h-screen flex items-center justify-center pt-20 pb-16 px-4">
    <!-- Background -->
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url(\'{{ $bg_image }}\');">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-[2px]"></div>
    </div>
    
    <!-- Content -->
    <div class="relative z-10 text-center max-w-4xl mx-auto space-y-8 animate-fade-in-up">
        <div class="inline-block px-4 py-1.5 rounded-full border border-white/30 bg-white/10 backdrop-blur-md text-white text-sm tracking-widest uppercase mb-4" x-text="eyebrow_text">
            The Wedding Of
        </div>
        
        <h1 class="text-5xl md:text-7xl lg:text-8xl font-serif text-white leading-tight drop-shadow-lg">
            <span class="block italic font-light" x-text="bride_name">Sarah</span>
            <span class="block text-4xl md:text-6xl my-2 text-yellow-400">&amp;</span>
            <span class="block italic font-light" x-text="groom_name">Michael</span>
        </h1>
        
        <p class="text-lg md:text-xl text-gray-200 mt-6 tracking-wide font-light" x-text="date_text">
            Saturday, 24 August 2026
        </p>
    </div>
</section>',
                'variables' => [
                    ['key' => 'bg_image', 'label' => 'Background Image URL', 'type' => 'image', 'default' => 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=2069&auto=format&fit=crop'],
                    ['key' => 'eyebrow_text', 'label' => 'Top Small Text', 'type' => 'text', 'default' => 'The Wedding Of'],
                    ['key' => 'bride_name', 'label' => 'Bride Name', 'type' => 'text', 'default' => 'Sarah'],
                    ['key' => 'groom_name', 'label' => 'Groom Name', 'type' => 'text', 'default' => 'Michael'],
                    ['key' => 'date_text', 'label' => 'Wedding Date Text', 'type' => 'text', 'default' => 'Saturday, 24 August 2026'],
                ],
                'is_public' => true,
                'is_active' => true,
                'created_by' => $superAdmin->id,
            ],
            [
                'name' => 'Elegant Event Details',
                'slug' => 'elegant-event-details',
                'category' => 'event',
                'type' => 'section',
                'description' => 'Two column event details for Akad and Resepsi.',
                'code' => '<!-- Event Details Section -->
<section class="py-24 px-4 bg-white">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-3xl md:text-5xl font-serif text-gray-900 mb-4">{{ $section_title }}</h2>
            <div class="w-24 h-1 bg-yellow-500 mx-auto"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <div class="bg-gray-50 rounded-2xl p-8 md:p-12 text-center border border-gray-100 hover:shadow-xl transition duration-300 group" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm group-hover:scale-110 transition">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-2xl font-serif text-gray-900 mb-2" x-text="event1_title">Akad Nikah</h3>
                <p class="text-gray-600 mb-6"><span x-text="event1_date">Sabtu, 24 Agustus 2026</span><br><span x-text="event1_time">08:00 - 10:00 WITA</span></p>
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="font-bold text-gray-900 mb-1" x-text="event1_location_name">Masjid Agung</h4>
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed" x-text="event1_address">Jl. Sudirman No. 1, Denpasar, Bali</p>
                    <a :href="event1_map_url" target="_blank" class="inline-block px-6 py-3 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition">
                        Google Maps
                    </a>
                </div>
            </div>
                        Google Maps
                    </a>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl p-8 md:p-12 text-center border border-gray-100 hover:shadow-xl transition duration-300 group" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm group-hover:scale-110 transition">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"></path><circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle></svg>
                </div>
                <h3 class="text-2xl font-serif text-gray-900 mb-2" x-text="event2_title">Resepsi Pernikahan</h3>
                <p class="text-gray-600 mb-6"><span x-text="event2_date">Sabtu, 24 Agustus 2026</span><br><span x-text="event2_time">11:00 - 14:00 WITA</span></p>
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="font-bold text-gray-900 mb-1" x-text="event2_location_name">Grand Ballroom Hotel</h4>
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed" x-text="event2_address">Jl. Gatot Subroto No. 99, Denpasar, Bali</p>
                    <a :href="event2_map_url" target="_blank" class="inline-block px-6 py-3 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition">
                        Google Maps
                    </a>
                </div>
            </div>
                        Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>',
                'variables' => [
                    ['key' => 'section_title', 'label' => 'Section Title', 'type' => 'text', 'default' => 'Rangkaian Acara'],
                    ['key' => 'event1_title', 'label' => 'Event 1 Title', 'type' => 'text', 'default' => 'Akad Nikah'],
                    ['key' => 'event1_date', 'label' => 'Event 1 Date', 'type' => 'text', 'default' => 'Sabtu, 24 Agustus 2026'],
                    ['key' => 'event1_time', 'label' => 'Event 1 Time', 'type' => 'text', 'default' => '08:00 - 10:00 WITA'],
                    ['key' => 'event1_location_name', 'label' => 'Event 1 Location Name', 'type' => 'text', 'default' => 'Masjid Agung'],
                    ['key' => 'event1_address', 'label' => 'Event 1 Address', 'type' => 'textarea', 'default' => 'Jl. Sudirman No. 1, Denpasar, Bali'],
                    ['key' => 'event1_map_url', 'label' => 'Event 1 Maps URL', 'type' => 'text', 'default' => 'https://maps.google.com'],
                    ['key' => 'event2_title', 'label' => 'Event 2 Title', 'type' => 'text', 'default' => 'Resepsi Pernikahan'],
                    ['key' => 'event2_date', 'label' => 'Event 2 Date', 'type' => 'text', 'default' => 'Sabtu, 24 Agustus 2026'],
                    ['key' => 'event2_time', 'label' => 'Event 2 Time', 'type' => 'text', 'default' => '11:00 - 14:00 WITA'],
                    ['key' => 'event2_location_name', 'label' => 'Event 2 Location Name', 'type' => 'text', 'default' => 'Grand Ballroom Hotel'],
                    ['key' => 'event2_address', 'label' => 'Event 2 Address', 'type' => 'textarea', 'default' => 'Jl. Gatot Subroto No. 99, Denpasar, Bali'],
                    ['key' => 'event2_map_url', 'label' => 'Event 2 Maps URL', 'type' => 'text', 'default' => 'https://maps.google.com'],
                ],
                'is_public' => true,
                'is_active' => true,
                'created_by' => $superAdmin->id,
            ],
            [
                'name' => 'RSVP & Greetings Form',
                'slug' => 'rsvp-greetings-form',
                'category' => 'rsvp',
                'type' => 'section',
                'description' => 'Form for guests to confirm attendance and send wishes.',
                'code' => '<!-- RSVP Section -->
@if($meta_data[\'rsvp_enabled\'] ?? true)
<section class="py-24 px-4 bg-gray-50" id="rsvp">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl md:text-5xl font-serif text-gray-900 mb-4" x-text="section_title">RSVP & Ucapan</h2>
            <p class="text-gray-600" x-text="section_subtitle">Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir di acara pernikahan kami.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 md:p-10 border border-gray-100" data-aos="fade-up" data-aos-delay="100">
            <form action="{{ route(\'invitation.rsvp\', $page->slug) }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="guest_name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 py-3 px-4">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kehadiran</label>
                        <select name="status" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 py-3 px-4">
                            <option value="Hadir">Hadir</option>
                            <option value="Tidak Hadir">Maaf, Tidak Bisa Hadir</option>
                            <option value="Masih Ragu">Masih Ragu</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pesan & Doa (Opsional)</label>
                    <textarea name="comments" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 py-3 px-4" placeholder="Tuliskan ucapan dan doa untuk kedua mempelai..."></textarea>
                </div>

                <button type="submit" class="w-full py-4 bg-gray-900 text-white font-bold rounded-lg hover:bg-yellow-600 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Kirim Konfirmasi
                </button>
            </form>
        </div>
    </div>
</section>
@endif',
                'variables' => [
                    ['key' => 'section_title', 'label' => 'Section Title', 'type' => 'text', 'default' => 'RSVP & Ucapan'],
                    ['key' => 'section_subtitle', 'label' => 'Section Subtitle', 'type' => 'text', 'default' => 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir di acara pernikahan kami.'],
                ],
                'is_public' => true,
                'is_active' => true,
                'created_by' => $superAdmin->id,
            ]
        ];

        foreach ($components as $component) {
            \App\Models\ComponentLibrary::updateOrCreate(
                ['slug' => $component['slug']],
                $component
            );
        }
    }
}
