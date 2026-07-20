<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Hero dulu punya sistem latar sendiri (background_image + overlay_enabled/overlay_opacity)
 * di samping sistem treatment (bg_image + bg_overlay). Dua kontrol untuk satu hal, dan
 * overlay treatment tidak berlaku atas foto milik hero. Sekarang hero memakai treatment
 * sepenuhnya, jadi datanya ikut dipindah.
 *
 * background_image MENANG atas bg_image yang sudah ada: hero menggambar background_image
 * sendiri, sedangkan bg_image hanya tampil kalau treatment='image'. Pada section yang
 * punya keduanya dengan treatment='surface', yang terlihat pemilik undangan selama ini
 * adalah background_image — itu yang dipertahankan.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('invitation_sections')
            ->where('section_type', 'hero')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $props = json_decode($row->props ?? '[]', true);
                    if (! is_array($props)) {
                        continue;
                    }

                    $legacy = ['background_image', 'overlay_enabled', 'overlay_opacity'];
                    if (! array_intersect($legacy, array_keys($props))) {
                        continue; // sudah bersih — migration aman dijalankan ulang
                    }

                    $bg = $props['background_image'] ?? null;
                    if (is_string($bg) && $bg !== '') {
                        $props['bg_image'] = $bg;
                        $props['treatment'] = 'image';
                    }

                    // overlay_enabled=false berarti "tanpa overlay" → opasitas 0.
                    if (array_key_exists('overlay_enabled', $props) && $props['overlay_enabled'] === false) {
                        $props['bg_overlay'] = 0;
                    } elseif (is_numeric($props['overlay_opacity'] ?? null)) {
                        $props['bg_overlay'] = max(0, min(100, (int) $props['overlay_opacity']));
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
        // Sengaja tidak dibalik. Setelah digabung, tidak ada cara membedakan section yang
        // bg_image-nya berasal dari background_image lama dari yang memang sudah memakai
        // bg_image sejak awal — membalikkannya akan merusak yang kedua.
    }
};
