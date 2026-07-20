<?php

// Klasifikasi kelas komponen (guideline §2.1/§2.2). Sumber kebenaran tunggal untuk
// gating "Mode Lanjutan" di Studio DAN aturan nesting server-side.
//
// - feature   : blok penuh-lebar terkurasi, selalu top-level (parent_id = null)
// - container : Layout section, menampung Basic di dalam kolom
// - basic     : blok kecil, boleh jadi anak container
return [
    'feature' => ['cover', 'hero', 'couple', 'event_details', 'gallery', 'countdown',
        'rsvp', 'gift', 'quote', 'love_story', 'closing', 'wishes', 'map', 'live_stream'],

    'container' => ['section_one_col', 'section_two_col', 'section_three_col'],

    'basic' => ['text', 'image', 'button', 'divider', 'spacer', 'video', 'music', 'code'],

    // Jumlah kolom per container — batas atas column_index yang sah.
    'container_columns' => [
        'section_one_col' => 1,
        'section_two_col' => 2,
        'section_three_col' => 3,
    ],
];
