<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Curated fonts
    |--------------------------------------------------------------------------
    | The only fonts a template's theme (or a buyer's override) may reference.
    | "name" must exactly match a Google Fonts family name.
    */
    'fonts' => [
        ['name' => 'Playfair Display', 'url' => 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap'],
        ['name' => 'Lora', 'url' => 'https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&display=swap'],
        ['name' => 'Lato', 'url' => 'https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap'],
        ['name' => 'Montserrat', 'url' => 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap'],
        ['name' => 'Great Vibes', 'url' => 'https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap'],
        ['name' => 'Open Sans', 'url' => 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Unggahan video
    |--------------------------------------------------------------------------
    | Server tidak melakukan transcode (tak ada jaminan ffmpeg di produksi), jadi
    | berkas dipakai apa adanya — pengunggah yang wajib mengecilkan lebih dulu.
    | Batasnya dipaksakan di server; atribut accept di form hanya kenyamanan.
    |
    | Catatan kompatibilitas: webm tidak sepenuhnya aman di Safari/iOS lama.
    | Menambahkan 'mp4' ke daftar di bawah cukup untuk melonggarkannya.
    */
    'video_upload' => [
        'extensions' => ['webm'],
        'mimes' => ['video/webm'],
        'max_kb' => 8192,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default theme
    |--------------------------------------------------------------------------
    | Used when a template has no theme set, or a key is missing from it.
    */
    'default_theme' => [
        'colors' => [
            'primary' => '#3b2f2f',
            'accent' => '#b5654d',
            'surface' => '#fffaf3',
            'surface_alt' => '#f3ece1',
            'text' => '#2b2b2b',
            'muted' => '#7a6f66',
            'ink' => '#20302a',
            'on_dark' => '#f5f1e8',
        ],
        'fonts' => [
            'heading' => 'Playfair Display',
            'body' => 'Lato',
        ],
        'scales' => [
            'type_base' => 16,
            'type_ratio' => 1.25,
            'radius' => 12,
            'section_spacing' => 64,
            'shadow_level' => 'sm',
        ],
        // Ornamen milik tema, bukan section: satu pilihan mengubah semua judul sekaligus.
        // null = garis lurus bawaan.
        'ornaments' => [
            'heading_rule_top' => null,
            'heading_rule' => null,
            // Persen lebar judul; 100 = penuh. Terpisah per sisi.
            'heading_rule_top_width' => 80,
            'heading_rule_width' => 80,
            // Jarak vertikal antara judul dan ornamennya, dalam px.
            'heading_rule_gap' => 14,
        ],
    ],
];
