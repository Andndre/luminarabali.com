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

        $this->seedTemplate($adminId, [
            'name' => 'Terracotta Dawn',
            'slug' => 'terracotta-dawn',
            'description' => 'Flagship v2 — warm terracotta, serif klasik.',
            'category' => 'classic',
            'theme' => [
                'colors' => ['primary' => '#8a4b32', 'accent' => '#d97b4f', 'surface' => '#fdf6ef', 'text' => '#33261f'],
                'fonts' => ['heading' => 'Playfair Display', 'body' => 'Lato'],
            ],
        ], [
            ['section_type' => 'cover', 'props' => [
                'title' => 'The Wedding Of', 'button_text' => 'Buka Undangan',
                'overlay_enabled' => true,
            ]],
            ['section_type' => 'hero', 'props' => [
                'title' => 'The Wedding Of', 'overlay_enabled' => true,
                'overlay_color' => '#33261f', 'overlay_opacity' => 45,
                'alignment' => 'center', 'padding_top' => 140, 'padding_bottom' => 140,
            ]],
            ['section_type' => 'countdown', 'props' => [
                'title' => 'Menuju Hari Bahagia',
                'padding_top' => 72, 'padding_bottom' => 72,
            ]],
            ['section_type' => 'text', 'props' => [
                'content' => 'Dengan penuh sukacita, kami mengundang Bapak/Ibu/Saudara/i untuk hadir di hari pernikahan kami.',
                'tag' => 'p', 'align' => 'center',
                'font_family' => 'playfair-display', 'line_height' => 1.8,
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
                'overlay_enabled' => true,
            ]],
            ['section_type' => 'hero', 'props' => [
                'title' => 'Undangan Pernikahan', 'overlay_enabled' => true,
                'overlay_color' => '#2c332a', 'overlay_opacity' => 35,
                'alignment' => 'center', 'padding_top' => 120, 'padding_bottom' => 120,
            ]],
            ['section_type' => 'text', 'props' => [
                'content' => 'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri.',
                'tag' => 'p', 'align' => 'center',
                'font_family' => 'open-sans', 'line_height' => 1.8,
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
    }

    private function seedTemplate(int $adminId, array $templateData, array $sections): void
    {
        $existing = InvitationTemplate::where('slug', $templateData['slug'])->first();
        if ($existing) {
            InvitationSection::where('template_id', $existing->id)->whereNull('page_id')->delete();
            $existing->delete();
        }

        $template = InvitationTemplate::create(array_merge($templateData, [
            'is_active' => true,
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
