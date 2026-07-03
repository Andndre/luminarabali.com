<?php

$containerFields = [
    ['key' => 'padding_top', 'type' => 'number', 'label' => 'Padding Atas', 'group' => 'design', 'default' => 60],
    ['key' => 'padding_bottom', 'type' => 'number', 'label' => 'Padding Bawah', 'group' => 'design', 'default' => 60],
    ['key' => 'padding_left', 'type' => 'number', 'label' => 'Padding Kiri', 'group' => 'design', 'default' => 20],
    ['key' => 'padding_right', 'type' => 'number', 'label' => 'Padding Kanan', 'group' => 'design', 'default' => 20],
    ['key' => 'max_width', 'type' => 'number', 'label' => 'Lebar Maksimum', 'group' => 'design', 'default' => 1200],
    ['key' => 'background_color', 'type' => 'color', 'label' => 'Warna Latar', 'group' => 'design', 'default' => '#ffffff'],
    ['key' => 'margin_top', 'type' => 'number', 'label' => 'Margin Atas', 'group' => 'design', 'default' => 0],
    ['key' => 'margin_bottom', 'type' => 'number', 'label' => 'Margin Bawah', 'group' => 'design', 'default' => 0],
    ['key' => 'margin_left', 'type' => 'number', 'label' => 'Margin Kiri', 'group' => 'design', 'default' => 0],
    ['key' => 'margin_right', 'type' => 'number', 'label' => 'Margin Kanan', 'group' => 'design', 'default' => 0],
    ['key' => 'margin_left_mode', 'type' => 'select', 'label' => 'Mode Margin Kiri', 'group' => 'design', 'options' => ['px', 'auto'], 'default' => 'px'],
    ['key' => 'margin_right_mode', 'type' => 'select', 'label' => 'Mode Margin Kanan', 'group' => 'design', 'options' => ['px', 'auto'], 'default' => 'px'],
    ['key' => 'border_width', 'type' => 'number', 'label' => 'Border Width', 'group' => 'design', 'default' => 0],
    ['key' => 'border_color', 'type' => 'color', 'label' => 'Warna Border', 'group' => 'design', 'default' => '#e5e7eb'],
    ['key' => 'border_radius', 'type' => 'number', 'label' => 'Border Radius', 'group' => 'design', 'default' => 0],
    ['key' => 'shadow', 'type' => 'select', 'label' => 'Bayangan', 'group' => 'design', 'options' => ['none', 'sm', 'md', 'lg'], 'default' => 'none'],
];

$curatedFontNames = ['Playfair Display', 'Lora', 'Lato', 'Montserrat', 'Great Vibes', 'Open Sans'];

return [
    'cover' => [
        ['key' => 'title', 'type' => 'text', 'label' => 'Judul Kecil', 'group' => 'content', 'default' => 'The Wedding Of'],
        ['key' => 'background_image', 'type' => 'image', 'label' => 'Foto Sampul', 'group' => 'content', 'default' => null],
        ['key' => 'button_text', 'type' => 'text', 'label' => 'Teks Tombol', 'group' => 'content', 'default' => 'Buka Undangan'],
        ['key' => 'button_color', 'type' => 'color', 'label' => 'Warna Tombol', 'group' => 'design', 'default' => '#d4af37'],
        ['key' => 'font_family', 'type' => 'select', 'label' => 'Font', 'group' => 'design', 'options' => $curatedFontNames, 'default' => 'Playfair Display'],
        ['key' => 'text_color', 'type' => 'color', 'label' => 'Warna Teks', 'group' => 'design', 'default' => '#ffffff'],
        ['key' => 'overlay_enabled', 'type' => 'boolean', 'label' => 'Overlay Gelap', 'group' => 'design', 'default' => true],
    ],

    'hero' => [
        ['key' => 'title', 'type' => 'text', 'label' => 'Judul Kecil', 'group' => 'content', 'default' => 'The Wedding Of'],
        ['key' => 'background_image', 'type' => 'image', 'label' => 'Foto Latar', 'group' => 'content', 'default' => null],
        ['key' => 'overlay_enabled', 'type' => 'boolean', 'label' => 'Overlay Gelap', 'group' => 'design', 'default' => false],
        ['key' => 'overlay_color', 'type' => 'color', 'label' => 'Warna Overlay', 'group' => 'design', 'default' => '#000000'],
        ['key' => 'overlay_opacity', 'type' => 'number', 'label' => 'Opasitas Overlay (%)', 'group' => 'design', 'default' => 40],
        ['key' => 'font_family', 'type' => 'select', 'label' => 'Font', 'group' => 'design', 'options' => $curatedFontNames, 'default' => 'Playfair Display'],
        ['key' => 'text_color', 'type' => 'color', 'label' => 'Warna Teks', 'group' => 'design', 'default' => '#ffffff'],
        ['key' => 'alignment', 'type' => 'select', 'label' => 'Perataan', 'group' => 'design', 'options' => ['left', 'center', 'right'], 'default' => 'center'],
        ['key' => 'padding_top', 'type' => 'number', 'label' => 'Padding Atas', 'group' => 'design', 'default' => 120],
        ['key' => 'padding_bottom', 'type' => 'number', 'label' => 'Padding Bawah', 'group' => 'design', 'default' => 120],
    ],

    'text' => [
        ['key' => 'content', 'type' => 'text', 'label' => 'Isi Teks', 'group' => 'content', 'default' => 'Tulis teks anda di sini...'],
        ['key' => 'tag', 'type' => 'select', 'label' => 'Jenis Elemen', 'group' => 'design', 'options' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'], 'default' => 'p'],
        ['key' => 'align', 'type' => 'select', 'label' => 'Perataan', 'group' => 'design', 'options' => ['left', 'center', 'right'], 'default' => 'left'],
        ['key' => 'color', 'type' => 'color', 'label' => 'Warna Teks', 'group' => 'design', 'default' => '#000000'],
        ['key' => 'font_size', 'type' => 'number', 'label' => 'Ukuran Font', 'group' => 'design', 'default' => null],
        ['key' => 'margin_bottom', 'type' => 'number', 'label' => 'Margin Bawah', 'group' => 'design', 'default' => 0],
        ['key' => 'font_family', 'type' => 'select', 'label' => 'Font', 'group' => 'design', 'options' => ['lato', 'montserrat', 'playfair-display', 'great-vibes', 'open-sans'], 'default' => 'lato'],
        ['key' => 'line_height', 'type' => 'number', 'label' => 'Line Height', 'group' => 'design', 'default' => 1.5],
        ['key' => 'letter_spacing', 'type' => 'number', 'label' => 'Letter Spacing', 'group' => 'design', 'default' => 0],
        ['key' => 'element_id', 'type' => 'text', 'label' => 'Element ID', 'group' => 'design', 'default' => null],
        ['key' => 'custom_css', 'type' => 'text', 'label' => 'Custom CSS', 'group' => 'design', 'default' => ''],
    ],

    'image' => [
        ['key' => 'src', 'type' => 'image', 'label' => 'Gambar', 'group' => 'content', 'default' => ''],
        ['key' => 'alt', 'type' => 'text', 'label' => 'Teks Alt', 'group' => 'content', 'default' => ''],
        ['key' => 'width', 'type' => 'number', 'label' => 'Lebar (%)', 'group' => 'design', 'default' => 100],
        ['key' => 'border_radius', 'type' => 'number', 'label' => 'Border Radius', 'group' => 'design', 'default' => 0],
        ['key' => 'border_width', 'type' => 'number', 'label' => 'Border Width', 'group' => 'design', 'default' => 0],
        ['key' => 'border_color', 'type' => 'color', 'label' => 'Warna Border', 'group' => 'design', 'default' => '#e5e7eb'],
        ['key' => 'shadow', 'type' => 'select', 'label' => 'Bayangan', 'group' => 'design', 'options' => ['none', 'sm', 'md', 'lg'], 'default' => 'none'],
        ['key' => 'align', 'type' => 'select', 'label' => 'Perataan', 'group' => 'design', 'options' => ['left', 'center', 'right'], 'default' => 'center'],
        ['key' => 'margin_top', 'type' => 'number', 'label' => 'Margin Atas', 'group' => 'design', 'default' => 0],
        ['key' => 'margin_bottom', 'type' => 'number', 'label' => 'Margin Bawah', 'group' => 'design', 'default' => 24],
        ['key' => 'element_id', 'type' => 'text', 'label' => 'Element ID', 'group' => 'design', 'default' => null],
        ['key' => 'custom_css', 'type' => 'text', 'label' => 'Custom CSS', 'group' => 'design', 'default' => ''],
    ],

    'button' => [
        ['key' => 'text', 'type' => 'text', 'label' => 'Teks Tombol', 'group' => 'content', 'default' => 'Click Me'],
        ['key' => 'url', 'type' => 'url', 'label' => 'Tautan', 'group' => 'content', 'default' => '#'],
        ['key' => 'variant', 'type' => 'select', 'label' => 'Gaya', 'group' => 'design', 'options' => ['primary', 'secondary', 'outline', 'ghost'], 'default' => 'primary'],
        ['key' => 'size', 'type' => 'select', 'label' => 'Ukuran', 'group' => 'design', 'options' => ['small', 'medium', 'large'], 'default' => 'medium'],
        ['key' => 'align', 'type' => 'select', 'label' => 'Perataan', 'group' => 'design', 'options' => ['left', 'center', 'right'], 'default' => 'center'],
        ['key' => 'background_color', 'type' => 'color', 'label' => 'Warna Latar', 'group' => 'design', 'default' => '#d4af37'],
        ['key' => 'text_color', 'type' => 'color', 'label' => 'Warna Teks', 'group' => 'design', 'default' => '#ffffff'],
        ['key' => 'border_radius', 'type' => 'number', 'label' => 'Border Radius', 'group' => 'design', 'default' => 8],
        ['key' => 'border_width', 'type' => 'number', 'label' => 'Border Width', 'group' => 'design', 'default' => 0],
        ['key' => 'border_color', 'type' => 'color', 'label' => 'Warna Border', 'group' => 'design', 'default' => '#d4af37'],
        ['key' => 'shadow', 'type' => 'select', 'label' => 'Bayangan', 'group' => 'design', 'options' => ['none', 'sm', 'md', 'lg'], 'default' => 'none'],
        ['key' => 'margin_top', 'type' => 'number', 'label' => 'Margin Atas', 'group' => 'design', 'default' => 0],
        ['key' => 'margin_bottom', 'type' => 'number', 'label' => 'Margin Bawah', 'group' => 'design', 'default' => 24],
        ['key' => 'element_id', 'type' => 'text', 'label' => 'Element ID', 'group' => 'design', 'default' => null],
        ['key' => 'custom_css', 'type' => 'text', 'label' => 'Custom CSS', 'group' => 'design', 'default' => ''],
    ],

    'divider' => [
        ['key' => 'type', 'type' => 'select', 'label' => 'Tipe', 'group' => 'design', 'options' => ['line', 'spacer'], 'default' => 'line'],
        ['key' => 'height', 'type' => 'number', 'label' => 'Tinggi', 'group' => 'design', 'default' => 1],
        ['key' => 'color', 'type' => 'color', 'label' => 'Warna', 'group' => 'design', 'default' => '#e5e7eb'],
        ['key' => 'style', 'type' => 'select', 'label' => 'Gaya Garis', 'group' => 'design', 'options' => ['solid', 'dashed', 'dotted'], 'default' => 'solid'],
        ['key' => 'width', 'type' => 'number', 'label' => 'Lebar (%)', 'group' => 'design', 'default' => 100],
        ['key' => 'margin_top', 'type' => 'number', 'label' => 'Margin Atas', 'group' => 'design', 'default' => 24],
        ['key' => 'margin_bottom', 'type' => 'number', 'label' => 'Margin Bawah', 'group' => 'design', 'default' => 24],
        ['key' => 'element_id', 'type' => 'text', 'label' => 'Element ID', 'group' => 'design', 'default' => null],
        ['key' => 'custom_css', 'type' => 'text', 'label' => 'Custom CSS', 'group' => 'design', 'default' => ''],
    ],

    'spacer' => [
        ['key' => 'height', 'type' => 'number', 'label' => 'Tinggi', 'group' => 'design', 'default' => 50],
        ['key' => 'element_id', 'type' => 'text', 'label' => 'Element ID', 'group' => 'design', 'default' => null],
        ['key' => 'custom_css', 'type' => 'text', 'label' => 'Custom CSS', 'group' => 'design', 'default' => ''],
    ],

    'countdown' => [
        ['key' => 'title', 'type' => 'text', 'label' => 'Judul', 'group' => 'content', 'default' => 'Counting Down To'],
        ['key' => 'background_color', 'type' => 'color', 'label' => 'Warna Latar', 'group' => 'design', 'default' => '#f8f9fa'],
        ['key' => 'text_color', 'type' => 'color', 'label' => 'Warna Teks', 'group' => 'design', 'default' => '#212529'],
        ['key' => 'accent_color', 'type' => 'color', 'label' => 'Warna Aksen', 'group' => 'design', 'default' => '#d4af37'],
        ['key' => 'padding_top', 'type' => 'number', 'label' => 'Padding Atas', 'group' => 'design', 'default' => 64],
        ['key' => 'padding_bottom', 'type' => 'number', 'label' => 'Padding Bawah', 'group' => 'design', 'default' => 64],
    ],

    'gallery' => [
        ['key' => 'images', 'type' => 'image_list', 'label' => 'Foto', 'group' => 'content', 'default' => []],
        ['key' => 'layout', 'type' => 'select', 'label' => 'Layout', 'group' => 'design', 'options' => ['grid', 'masonry', 'slider'], 'default' => 'grid'],
        ['key' => 'columns', 'type' => 'number', 'label' => 'Jumlah Kolom', 'group' => 'design', 'default' => 3],
        ['key' => 'gap', 'type' => 'number', 'label' => 'Jarak Antar Foto', 'group' => 'design', 'default' => 16],
        ['key' => 'lightbox', 'type' => 'boolean', 'label' => 'Aktifkan Lightbox', 'group' => 'design', 'default' => true],
        ['key' => 'background_color', 'type' => 'color', 'label' => 'Warna Latar', 'group' => 'design', 'default' => '#ffffff'],
    ],

    'map' => [
        ['key' => 'title', 'type' => 'text', 'label' => 'Judul', 'group' => 'content', 'default' => null],
        ['key' => 'address', 'type' => 'text', 'label' => 'Alamat', 'group' => 'content', 'default' => ''],
        ['key' => 'latitude', 'type' => 'text', 'label' => 'Latitude', 'group' => 'content', 'default' => ''],
        ['key' => 'longitude', 'type' => 'text', 'label' => 'Longitude', 'group' => 'content', 'default' => ''],
        ['key' => 'button_text', 'type' => 'text', 'label' => 'Teks Tombol', 'group' => 'content', 'default' => 'Petunjuk Arah'],
        ['key' => 'zoom', 'type' => 'number', 'label' => 'Level Zoom', 'group' => 'design', 'default' => 15],
        ['key' => 'height', 'type' => 'number', 'label' => 'Tinggi Peta', 'group' => 'design', 'default' => 400],
        ['key' => 'show_button', 'type' => 'boolean', 'label' => 'Tampilkan Tombol Arah', 'group' => 'design', 'default' => true],
        ['key' => 'title_color', 'type' => 'color', 'label' => 'Warna Judul', 'group' => 'design', 'default' => '#333333'],
        ['key' => 'background_color', 'type' => 'color', 'label' => 'Warna Latar', 'group' => 'design', 'default' => '#f8f9fa'],
    ],

    'music' => [
        ['key' => 'src', 'type' => 'audio', 'label' => 'File Musik', 'group' => 'content', 'default' => ''],
        ['key' => 'autoplay', 'type' => 'boolean', 'label' => 'Putar Otomatis', 'group' => 'design', 'default' => true],
        ['key' => 'loop', 'type' => 'boolean', 'label' => 'Ulangi', 'group' => 'design', 'default' => true],
        ['key' => 'show_controls', 'type' => 'boolean', 'label' => 'Tampilkan Tombol Kontrol', 'group' => 'design', 'default' => true],
        ['key' => 'button_color', 'type' => 'color', 'label' => 'Warna Tombol', 'group' => 'design', 'default' => '#d4af37'],
    ],

    'rsvp' => [
        ['key' => 'title', 'type' => 'text', 'label' => 'Judul', 'group' => 'content', 'default' => 'RSVP'],
        ['key' => 'subtitle', 'type' => 'text', 'label' => 'Subjudul', 'group' => 'content', 'default' => 'Please confirm your attendance'],
        ['key' => 'button_text', 'type' => 'text', 'label' => 'Teks Tombol', 'group' => 'content', 'default' => 'Kirim Konfirmasi'],
        ['key' => 'success_message', 'type' => 'text', 'label' => 'Pesan Sukses', 'group' => 'content', 'default' => 'Terima kasih atas konfirmasi Anda!'],
        ['key' => 'whatsapp_phone', 'type' => 'text', 'label' => 'Nomor WhatsApp', 'group' => 'content', 'default' => ''],
        ['key' => 'button_color', 'type' => 'color', 'label' => 'Warna Tombol', 'group' => 'design', 'default' => '#d4af37'],
        ['key' => 'whatsapp_enabled', 'type' => 'boolean', 'label' => 'Teruskan ke WhatsApp', 'group' => 'design', 'default' => false],
        ['key' => 'background_color', 'type' => 'color', 'label' => 'Warna Latar', 'group' => 'design', 'default' => '#ffffff'],
        ['key' => 'padding_top', 'type' => 'number', 'label' => 'Padding Atas', 'group' => 'design', 'default' => 80],
        ['key' => 'padding_bottom', 'type' => 'number', 'label' => 'Padding Bawah', 'group' => 'design', 'default' => 80],
    ],

    'video' => [
        ['key' => 'src', 'type' => 'video', 'label' => 'File Video', 'group' => 'content', 'default' => ''],
        ['key' => 'youtube_url', 'type' => 'url', 'label' => 'URL YouTube', 'group' => 'content', 'default' => ''],
        ['key' => 'type', 'type' => 'select', 'label' => 'Sumber Video', 'group' => 'design', 'options' => ['upload', 'youtube'], 'default' => 'upload'],
        ['key' => 'autoplay', 'type' => 'boolean', 'label' => 'Putar Otomatis', 'group' => 'design', 'default' => false],
        ['key' => 'muted', 'type' => 'boolean', 'label' => 'Bisukan', 'group' => 'design', 'default' => true],
        ['key' => 'controls', 'type' => 'boolean', 'label' => 'Tampilkan Kontrol', 'group' => 'design', 'default' => true],
        ['key' => 'width', 'type' => 'number', 'label' => 'Lebar (%)', 'group' => 'design', 'default' => 100],
        ['key' => 'margin_top', 'type' => 'number', 'label' => 'Margin Atas', 'group' => 'design', 'default' => 0],
        ['key' => 'margin_bottom', 'type' => 'number', 'label' => 'Margin Bawah', 'group' => 'design', 'default' => 24],
    ],

    'section_one_col' => $containerFields,

    'section_two_col' => array_merge($containerFields, [
        ['key' => 'column_gap', 'type' => 'number', 'label' => 'Jarak Kolom', 'group' => 'design', 'default' => 20],
        ['key' => 'column_ratio', 'type' => 'select', 'label' => 'Rasio Kolom', 'group' => 'design', 'options' => ['50-50', '60-40', '40-60', '70-30', '30-70'], 'default' => '50-50'],
        ['key' => 'vertical_align', 'type' => 'select', 'label' => 'Perataan Vertikal', 'group' => 'design', 'options' => ['top', 'center', 'bottom'], 'default' => 'top'],
    ]),

    'section_three_col' => array_merge($containerFields, [
        ['key' => 'column_gap', 'type' => 'number', 'label' => 'Jarak Kolom', 'group' => 'design', 'default' => 20],
        ['key' => 'vertical_align', 'type' => 'select', 'label' => 'Perataan Vertikal', 'group' => 'design', 'options' => ['top', 'center', 'bottom'], 'default' => 'top'],
    ]),
];
