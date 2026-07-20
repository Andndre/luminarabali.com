<?php

namespace Database\Seeders;

use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class FlagshipTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('division', 'super_admin')->value('id') ?? 1;

        // Template A — "Classic Elegant": cover foto+pinned, mempelai berjejer tengah,
        // acara dalam kartu berbingkai di atas latar kontras (bukan hijau gelap).
        $this->seedTemplate($adminId, [
            'name' => 'Terracotta Dawn',
            'slug' => 'terracotta-dawn',
            'description' => 'Flagship v2 — warm terracotta, serif klasik. Classic Elegant.',
            'category' => 'classic',
            'theme' => [
                'colors' => ['primary' => '#8a4b32', 'accent' => '#d97b4f', 'surface' => '#fdf6ef', 'text' => '#33261f'],
                'fonts' => ['heading' => 'Playfair Display', 'body' => 'Lato'],
            ],
        ], [
            ['section_type' => 'cover', 'props' => [
                'title' => 'The Wedding Of', 'button_text' => 'Buka Undangan',
                // Cover menggambar visual sendiri (gate + sticky reveal), tapi memakai field
                // media bersama: bg_image untuk foto, bg_media_type untuk slideshow/video.
                'bg_image' => 'https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=2000&auto=format&fit=crop',
            ]],
            ['section_type' => 'hero', 'props' => [
                // Hero memakai sistem treatment: fotonya bg_image, gelapnya bg_overlay.
                'title' => 'The Wedding Of', 'treatment' => 'image', 'bg_overlay' => 45,
                'alignment' => 'center', 'padding_top' => 140, 'padding_bottom' => 140,
            ]],
            ['section_type' => 'couple', 'props' => [
                'variant' => 'centered-stacked',
                'heading' => 'Mempelai',
                'groom_parents' => 'Putra dari Bapak Ahmad Wijaya & Ibu Sri Lestari',
                'bride_parents' => 'Putri dari Bapak Robert Tanaka & Ibu Maria Santoso',
            ]],
            ['section_type' => 'event_details', 'props' => [
                'variant' => 'bordered-cards',
                'treatment' => 'contrast',
                'heading' => 'Rangkaian Acara',
                'events' => [
                    ['name' => 'Akad Nikah', 'date_text' => 'Sabtu, 1 Januari 2027', 'time_text' => '08.00 WITA', 'venue' => 'Nama Gedung', 'address' => '', 'maps_url' => ''],
                    ['name' => 'Resepsi', 'date_text' => 'Sabtu, 1 Januari 2027', 'time_text' => '18.00 WITA', 'venue' => 'Nama Gedung', 'address' => '', 'maps_url' => ''],
                ],
            ]],
            ['section_type' => 'countdown', 'props' => [
                'title' => 'Menuju Hari Bahagia',
                'padding_top' => 72, 'padding_bottom' => 72,
            ]],
            ['section_type' => 'text', 'props' => [
                'content' => 'Dengan penuh sukacita, kami mengundang Bapak/Ibu/Saudara/i untuk hadir di hari pernikahan kami.',
                'tag' => 'p', 'align' => 'center',
                'line_height' => 1.8,
            ]],
            ['section_type' => 'rsvp', 'props' => [
                'title' => 'RSVP', 'subtitle' => 'Mohon konfirmasi kehadiran Anda',
                'button_text' => 'Kirim Konfirmasi',
                'padding_top' => 80, 'padding_bottom' => 80,
            ]],
        ]);

        $this->seedTemplate($adminId, [
            'name' => 'Sage Garden',
            'slug' => 'sage-garden',
            'description' => 'Flagship v2 — hijau sage, nuansa taman.',
            'category' => 'garden',
            'theme' => [
                'colors' => ['primary' => '#4a5d43', 'accent' => '#93a686', 'surface' => '#f6f8f3', 'text' => '#2c332a'],
                'fonts' => ['heading' => 'Lora', 'body' => 'Open Sans'],
            ],
        ], [
            ['section_type' => 'cover', 'props' => [
                'title' => 'Undangan Pernikahan', 'button_text' => 'Buka Undangan',
            ]],
            ['section_type' => 'hero', 'props' => [
                'title' => 'Undangan Pernikahan', 'bg_overlay' => 35,
                'alignment' => 'center', 'padding_top' => 120, 'padding_bottom' => 120,
            ]],
            ['section_type' => 'text', 'props' => [
                'content' => 'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri.',
                'tag' => 'p', 'align' => 'center',
                'line_height' => 1.8,
            ]],
            ['section_type' => 'countdown', 'props' => [
                'title' => 'Counting Down',
                'padding_top' => 64, 'padding_bottom' => 64,
            ]],
            ['section_type' => 'gallery', 'props' => [
                'images' => [], 'layout' => 'grid', 'columns' => 3,
                'gap' => 16, 'lightbox' => true,
            ]],
            ['section_type' => 'rsvp', 'props' => [
                'title' => 'Konfirmasi Kehadiran', 'subtitle' => 'Kami menantikan kehadiran Anda',
                'button_text' => 'Kirim',
                'padding_top' => 80, 'padding_bottom' => 80,
            ]],
        ]);

        // Template B — "Modern Editorial": komponen SAMA dengan Template A, nilai berbeda —
        // cover foto+scroll-zoom-in, hero split, mempelai berselang-seling di atas latar
        // gelap charcoal (ink), acara berupa daftar bergaris di atas latar surface.
        $this->seedTemplate($adminId, [
            'name' => 'Modern Editorial',
            'slug' => 'modern-editorial',
            'description' => 'Flagship v2 — charcoal + editorial, sans modern. Modern Editorial.',
            'category' => 'modern',
            'theme' => [
                'colors' => [
                    'primary' => '#1c1c1c', 'accent' => '#bfa46f', 'surface' => '#f5f3ef',
                    'surface_alt' => '#e8e4dc', 'text' => '#1c1c1c', 'muted' => '#6b6b6b',
                    'ink' => '#141414', 'on_dark' => '#f5f3ef',
                ],
                'fonts' => ['heading' => 'Montserrat', 'body' => 'Open Sans'],
            ],
        ], [
            ['section_type' => 'cover', 'props' => [
                'title' => 'The Wedding Of', 'button_text' => 'Buka Undangan',
                'bg_image' => 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?q=80&w=2000&auto=format&fit=crop',
            ]],
            ['section_type' => 'hero', 'props' => [
                'variant' => 'split',
                'title' => 'The Wedding Of', 'bg_overlay' => 45,
                'alignment' => 'center', 'padding_top' => 120, 'padding_bottom' => 120,
            ]],
            ['section_type' => 'couple', 'props' => [
                'variant' => 'side-alternating',
                'treatment' => 'dark',
                'heading' => 'Mempelai',
                'groom_parents' => 'Putra dari Bapak Ahmad Wijaya & Ibu Sri Lestari',
                'bride_parents' => 'Putri dari Bapak Robert Tanaka & Ibu Maria Santoso',
            ]],
            ['section_type' => 'event_details', 'props' => [
                'variant' => 'divider-list',
                'treatment' => 'surface',
                'heading' => 'Rangkaian Acara',
                'events' => [
                    ['name' => 'Akad Nikah', 'date_text' => 'Sabtu, 1 Januari 2027', 'time_text' => '08.00 WITA', 'venue' => 'Nama Gedung', 'address' => '', 'maps_url' => ''],
                    ['name' => 'Resepsi', 'date_text' => 'Sabtu, 1 Januari 2027', 'time_text' => '18.00 WITA', 'venue' => 'Nama Gedung', 'address' => '', 'maps_url' => ''],
                ],
            ]],
            ['section_type' => 'countdown', 'props' => [
                'title' => 'Menuju Hari Bahagia',
                'padding_top' => 72, 'padding_bottom' => 72,
            ]],
            ['section_type' => 'text', 'props' => [
                'content' => 'Dengan penuh sukacita, kami mengundang Bapak/Ibu/Saudara/i untuk hadir di hari pernikahan kami.',
                'tag' => 'p', 'align' => 'center',
                'line_height' => 1.8,
            ]],
            ['section_type' => 'rsvp', 'props' => [
                'title' => 'RSVP', 'subtitle' => 'Mohon konfirmasi kehadiran Anda',
                'button_text' => 'Kirim Konfirmasi',
                'padding_top' => 80, 'padding_bottom' => 80,
            ]],
        ]);
    }

    private function seedTemplate(int $adminId, array $templateData, array $sections): void
    {
        $existing = InvitationTemplate::where('slug', $templateData['slug'])->first();
        if ($existing) {
            InvitationSection::where('template_id', $existing->id)->whereNull('page_id')->delete();
            $existing->delete();
        }

        $template = InvitationTemplate::create(array_merge($templateData, [
            'status' => 'published',
            'created_by' => $adminId,
        ]));

        foreach ($sections as $index => $section) {
            InvitationSection::create([
                'template_id' => $template->id,
                'page_id' => null,
                'section_type' => $section['section_type'],
                'order_index' => $index,
                'props' => $section['props'],
                'is_visible' => true,
            ]);
        }
    }
}
