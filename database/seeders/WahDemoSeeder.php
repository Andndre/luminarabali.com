<?php

namespace Database\Seeders;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/**
 * Demo "wah" — mengubah page `john-silvia` menjadi undangan lengkap yang
 * memakai (hampir) seluruh tipe section kaya: cover, hero, quote, couple,
 * love_story, event_details, countdown, gallery, gift, wishes, rsvp, closing.
 *
 * Palet: emerald tua + gold di atas ivory, lewat theme token template (bukan
 * warna literal per-section) — kecuali section "gelap" yang sengaja override
 * agar kontras. Bukti nyata jangkauan editor, bukan komponen baru.
 */
class WahDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(OrnamentSeeder::class);

        $page = InvitationPage::where('slug', 'john-silvia')->first();
        if (! $page) {
            $this->command?->error('Page john-silvia tidak ditemukan. Jalankan seeder dasarnya dulu.');
            return;
        }

        $adminId = User::where('division', 'super_admin')->value('id') ?? $page->created_by ?? 1;

        // 1. Template bertema elegan (emerald + gold + ivory) → theme token mengalir ke semua section.
        $template = InvitationTemplate::updateOrCreate(
            ['slug' => 'ivory-emerald-demo'],
            [
                'name' => 'Ivory Emerald (Demo)',
                'description' => 'Demo lengkap — emerald tua, gold, ivory. Serif klasik.',
                'category' => 'classic',
                'status' => 'published',
                'created_by' => $adminId,
                'theme' => [
                    'colors' => [
                        'primary' => '#24433a',
                        'accent' => '#b98a34',
                        'surface' => '#f6f2ea',
                        'text' => '#2c2c2c',
                    ],
                    'fonts' => ['heading' => 'Playfair Display', 'body' => 'Lato'],
                ],
            ]
        );

        $page->template_id = $template->id;
        $page->theme_overrides = null; // buang override buyer lama (pink) → tema template yang berlaku
        $page->save();

        // Warna section "gelap" — override token agar kontras (teks ivory di atas emerald).
        $dark = '#24433a';
        $onDark = '#f6f2ea';
        $gold = '#c9a24b';

        // Foto Unsplash (http; cover memakai asset lokal yang sudah ada).
        $coverImage = $page->sections()->where('section_type', 'cover')->value('props')['bg_image']
            ?? 'invitations/1783028804_6a46dc44d25ae.webp';
        $heroImage = 'https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=2000&auto=format&fit=crop';
        $groomPhoto = 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=800&auto=format&fit=crop';
        $bridePhoto = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=800&auto=format&fit=crop';
        $closingPhoto = 'https://images.unsplash.com/photo-1591604466107-ec97de577aff?q=80&w=1000&auto=format&fit=crop';
        $storyPhotos = [
            'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?q=80&w=600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1518199266791-5375a83190b7?q=80&w=600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1529636798458-92182e662485?q=80&w=600&auto=format&fit=crop',
        ];
        $gallery = [
            'https://images.unsplash.com/photo-1586420669671-701d93b76748?q=80&w=1000&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1587200868091-23e92ff750b4?q=80&w=1000&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1519225421980-715cb0215aed?q=80&w=1000&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?q=80&w=1000&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=1000&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=1000&auto=format&fit=crop',
        ];

        $sections = [
            ['cover', [
                'title' => 'The Wedding Of',
                'bg_image' => $coverImage,
                'button_text' => 'Buka Undangan',
            ]],
            ['hero', [
                // Hero memakai sistem treatment: fotonya bg_image, gelapnya bg_overlay.
                'title' => 'Om Swastiastu',
                'treatment' => 'image',
                'bg_image' => $heroImage,
                'bg_overlay' => 55,
                'alignment' => 'center',
                'padding_top' => 160,
                'padding_bottom' => 160,
            ]],
            ['quote', [
                'content' => 'Dan di antara tanda-tanda kekuasaan-Nya ialah Dia menciptakan untukmu pasangan hidup dari jenismu sendiri, supaya kamu cenderung dan merasa tenteram kepadanya.',
                'attribution' => 'Q.S. Ar-Rum: 21',
                'animation' => 'fade-up',
                // Dua ornamen di slot ATAS: kiri + kanan (aset sama, yang kanan di-flip).
                'ornaments_top' => [
                    ['src' => 'ornaments/sulur-pojok-kiri.svg', 'position' => 'left', 'scale' => 22, 'flip_h' => false, 'flip_v' => false, 'color' => null],
                    ['src' => 'ornaments/sulur-pojok-kiri.svg', 'position' => 'right', 'scale' => 22, 'flip_h' => true, 'flip_v' => false, 'color' => null],
                ],
                'ornaments_bottom' => [
                    ['src' => 'ornaments/flourish-tengah.svg', 'position' => 'center', 'scale' => 55, 'flip_h' => false, 'flip_v' => false, 'color' => null],
                ],
            ]],
            ['couple', [
                'heading' => 'Mempelai',
                'groom_photo' => $groomPhoto,
                'bride_photo' => $bridePhoto,
                'groom_parents' => 'Putra pertama dari Bapak Ahmad Wijaya & Ibu Sri Lestari',
                'bride_parents' => 'Putri kedua dari Bapak Robert Tanaka & Ibu Maria Santoso',
                'groom_instagram' => 'https://instagram.com/john',
                'bride_instagram' => 'https://instagram.com/silvia',
                'background_color' => $dark,
                'text_color' => $onDark,
                'accent_color' => $gold,
                'animation' => 'fade-up',
                'ornaments_top' => [
                    ['src' => 'ornaments/sulur-pojok-kanan.svg', 'position' => 'right', 'scale' => 34, 'flip_h' => false, 'flip_v' => false, 'color' => null],
                ],
            ]],
            ['love_story', [
                'heading' => 'Perjalanan Kami',
                'stories' => [
                    ['year' => '2019', 'title' => 'Pertama Bertemu', 'story' => 'Dipertemukan di sebuah acara kampus, obrolan singkat yang ternyata jadi awal segalanya.', 'photo' => $storyPhotos[0]],
                    ['year' => '2022', 'title' => 'Menjalin Hubungan', 'story' => 'Melewati suka dan duka bersama, saling menguatkan dari kejauhan maupun kedekatan.', 'photo' => $storyPhotos[1]],
                    ['year' => '2025', 'title' => 'Lamaran', 'story' => 'Di bawah langit senja Bali, sebuah janji diucapkan untuk melangkah ke jenjang berikutnya.', 'photo' => $storyPhotos[2]],
                ],
                'animation' => 'fade-up',
            ]],
            ['event_details', [
                'heading' => 'Rangkaian Acara',
                'events' => [
                    ['name' => 'Akad Nikah', 'date_text' => 'Kamis, 23 Juli 2026', 'time_text' => '08.00 – 10.00 WITA', 'venue' => 'The Ubud Village Resort', 'address' => 'Jl. Raya Nyuh Kuning, Ubud, Bali', 'maps_url' => 'https://maps.google.com/?q=Ubud+Bali'],
                    ['name' => 'Resepsi', 'date_text' => 'Kamis, 23 Juli 2026', 'time_text' => '18.00 – 21.00 WITA', 'venue' => 'The Ubud Village Resort', 'address' => 'Jl. Raya Nyuh Kuning, Ubud, Bali', 'maps_url' => 'https://maps.google.com/?q=Ubud+Bali'],
                ],
                'background_color' => $dark,
                'text_color' => $onDark,
                'accent_color' => $gold,
                'animation' => 'fade-up',
            ]],
            ['countdown', [
                'title' => 'Menuju Hari Bahagia',
                'padding_top' => 96,
                'padding_bottom' => 72,
                'animation' => 'fade-up',
                'ornaments_top' => [
                    ['src' => 'ornaments/garland-atas.svg', 'position' => 'full-width', 'scale' => 100, 'flip_h' => false, 'flip_v' => false, 'color' => null],
                ],
            ]],
            ['gallery', [
                'images' => array_map(fn ($url) => ['url' => $url, 'alt' => 'John & Silvia'], $gallery),
                'layout' => 'grid',
                'columns' => 3,
                'gap' => 12,
                'lightbox' => true,
                'animation' => 'fade-in',
            ]],
            ['gift', [
                'heading' => 'Amplop Digital',
                'message' => 'Doa restu Anda adalah hadiah terindah. Namun bila berkenan memberi tanda kasih, dapat melalui:',
                'accounts' => [
                    ['bank' => 'BCA', 'number' => '1234567890', 'holder' => 'John Wijaya'],
                    ['bank' => 'Mandiri', 'number' => '0987654321', 'holder' => 'Silvia Tanaka'],
                ],
                'gift_address' => 'Jl. Melati No. 12, Denpasar, Bali',
                'background_color' => $dark,
                'text_color' => $onDark,
                'accent_color' => $gold,
                'animation' => 'fade-up',
                'ornaments_top' => [
                    ['src' => 'ornaments/sulur-pojok-kiri.svg', 'position' => 'left', 'scale' => 30, 'flip_h' => false, 'flip_v' => false, 'color' => null],
                ],
            ]],
            ['wishes', [
                'heading' => 'Ucapan & Doa',
                'limit' => 50,
                'animation' => 'fade-up',
            ]],
            ['rsvp', [
                'title' => 'Konfirmasi Kehadiran',
                'subtitle' => 'Merupakan kehormatan bagi kami atas doa dan kehadiran Anda',
                'button_text' => 'Kirim Konfirmasi',
                'padding_top' => 80,
                'padding_bottom' => 80,
            ]],
            ['closing', [
                'message' => 'Merupakan suatu kebahagiaan dan kehormatan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu kepada kedua mempelai.',
                'photo' => $closingPhoto,
                'background_color' => $dark,
                'text_color' => $onDark,
                'accent_color' => $gold,
                'animation' => 'fade-up',
                'ornaments_bottom' => [
                    ['src' => 'ornaments/renda-bawah.svg', 'position' => 'full-width', 'scale' => 100, 'flip_h' => false, 'flip_v' => false, 'color' => null],
                ],
            ]],
        ];

        // Tulis dua salinan: master template (sumber instantiate + editable di Studio)
        // dan salinan page john-silvia (yang tampil di /invitation/john-silvia).
        InvitationSection::where('template_id', $template->id)->whereNull('page_id')->delete();
        InvitationSection::where('page_id', $page->id)->delete();
        foreach ($sections as $i => [$type, $props]) {
            InvitationSection::create([
                'page_id' => null,
                'template_id' => $template->id,
                'section_type' => $type,
                'order_index' => $i,
                'props' => $props,
                'is_visible' => true,
            ]);
            InvitationSection::create([
                'page_id' => $page->id,
                'template_id' => null,
                'section_type' => $type,
                'order_index' => $i,
                'props' => $props,
                'is_visible' => true,
            ]);
        }

        // Ucapan contoh agar wishes-wall terisi (idempoten: hapus demo lama dulu).
        $page->rsvpResponses()->where('guest_email', 'like', '%@wahdemo.test')->delete();
        $wishes = [
            ['Budi Santoso', 'Selamat menempuh hidup baru! Semoga menjadi keluarga yang sakinah, mawaddah, warahmah.'],
            ['Siti Rahayu', 'Turut berbahagia atas pernikahan kalian. Semoga langgeng sampai akhir hayat.'],
            ['Andi & Keluarga', 'Barakallahu lakuma. Semoga Allah memberkahi setiap langkah kalian berdua.'],
            ['Maria Gunawan', 'Congratulations John & Silvia! Wishing you a lifetime of love and happiness.'],
        ];
        foreach ($wishes as $j => [$name, $msg]) {
            $page->rsvpResponses()->create([
                'guest_name' => $name,
                'guest_email' => "guest{$j}@wahdemo.test",
                'attendance_status' => 'hadir',
                'number_of_guests' => 2,
                'message' => $msg,
                'is_hidden' => false,
                'submitted_at' => now()->subDays($j),
            ]);
        }

        Cache::forget('invitation:john-silvia');
        $this->command?->info('Demo "wah" john-silvia siap. Buka: /invitation/john-silvia');
    }
}
