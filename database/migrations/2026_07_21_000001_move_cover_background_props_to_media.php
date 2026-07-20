<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Cover dulu punya field latar sendiri (background_image + overlay_enabled) yang terpisah
 * dari field media bersama (bg_media_type/bg_image/bg_images/bg_video + bg_overlay) yang
 * dipakai section lain. Akibatnya latar cover tidak bisa diatur slideshow atau video.
 * Sekarang cover memakai field media yang sama, jadi datanya ikut dipindah.
 *
 * background_image → bg_image (foto tunggal). overlay_enabled=false → bg_overlay=0;
 * selain itu overlay dibiarkan default (45) karena cover lama tidak punya angka opasitas
 * sendiri — hanya nyala/mati.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('invitation_sections')
            ->where('section_type', 'cover')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $props = json_decode($row->props ?? '[]', true);
                    if (! is_array($props)) {
                        continue;
                    }

                    $legacy = ['background_image', 'overlay_enabled'];
                    if (! array_intersect($legacy, array_keys($props))) {
                        continue; // sudah bersih — migration aman dijalankan ulang
                    }

                    $bg = $props['background_image'] ?? null;
                    if (is_string($bg) && $bg !== '' && ! isset($props['bg_image'])) {
                        $props['bg_image'] = $bg;
                    }

                    // overlay_enabled=false berarti "tanpa overlay" → opasitas 0.
                    if (array_key_exists('overlay_enabled', $props) && $props['overlay_enabled'] === false) {
                        $props['bg_overlay'] = 0;
                    }

                    foreach ($legacy as $key) {
                        unset($props[$key]);
                    }

                    DB::table('invitation_sections')
                        ->where('id', $row->id)
                        ->update(['props' => json_encode($props)]);
                }
            });
    }

    public function down(): void
    {
        // Sengaja tidak dibalik: setelah digabung, bg_image yang berasal dari
        // background_image lama tidak bisa dibedakan dari yang memang sudah bg_image.
    }
};
