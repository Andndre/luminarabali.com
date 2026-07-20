<?php

$containerFields = [
    ['key' => 'padding_top', 'type' => 'number', 'label' => 'Padding Atas', 'group' => 'design', 'panel' => 'spacing', 'default' => 60],
    ['key' => 'padding_bottom', 'type' => 'number', 'label' => 'Padding Bawah', 'group' => 'design', 'panel' => 'spacing', 'default' => 60],
    ['key' => 'padding_left', 'type' => 'number', 'label' => 'Padding Kiri', 'group' => 'design', 'panel' => 'spacing', 'default' => 20],
    ['key' => 'padding_right', 'type' => 'number', 'label' => 'Padding Kanan', 'group' => 'design', 'panel' => 'spacing', 'default' => 20],
    ['key' => 'max_width', 'type' => 'number', 'label' => 'Lebar Maksimum', 'group' => 'design', 'panel' => 'spacing', 'default' => 1200],
    // default null = ikuti tema (partial jatuh ke var(--color-surface)). Pilih warna = override eksplisit.
    ['key' => 'background_color', 'type' => 'color', 'label' => 'Warna Latar', 'group' => 'design', 'panel' => 'spacing', 'token' => 'surface', 'default' => null],
    ['key' => 'margin_top', 'type' => 'number', 'label' => 'Margin Atas', 'group' => 'design', 'panel' => 'spacing', 'default' => 0],
    ['key' => 'margin_bottom', 'type' => 'number', 'label' => 'Margin Bawah', 'group' => 'design', 'panel' => 'spacing', 'default' => 0],
    ['key' => 'margin_left', 'type' => 'number', 'label' => 'Margin Kiri', 'group' => 'design', 'panel' => 'spacing', 'default' => 0],
    ['key' => 'margin_right', 'type' => 'number', 'label' => 'Margin Kanan', 'group' => 'design', 'panel' => 'spacing', 'default' => 0],
    ['key' => 'margin_left_mode', 'type' => 'select', 'label' => 'Mode Margin Kiri', 'group' => 'design', 'panel' => 'spacing', 'options' => ['px', 'auto'], 'default' => 'px'],
    ['key' => 'margin_right_mode', 'type' => 'select', 'label' => 'Mode Margin Kanan', 'group' => 'design', 'panel' => 'spacing', 'options' => ['px', 'auto'], 'default' => 'px'],
    ['key' => 'border_width', 'type' => 'number', 'label' => 'Border Width', 'group' => 'design', 'panel' => 'spacing', 'default' => 0],
    ['key' => 'border_color', 'type' => 'color', 'label' => 'Warna Border', 'group' => 'design', 'panel' => 'spacing', 'token' => 'surface_alt', 'default' => null],
    ['key' => 'border_radius', 'type' => 'number', 'label' => 'Border Radius', 'group' => 'design', 'panel' => 'spacing', 'default' => 0],
    ['key' => 'shadow', 'type' => 'select', 'label' => 'Bayangan', 'group' => 'design', 'panel' => 'spacing', 'options' => ['none', 'sm', 'md', 'lg'], 'default' => 'none'],
];

// Radius sudut — satu angka, atau empat kalau centangnya aktif. show_if menyembunyikan
// yang tidak relevan di inspector, tapi keempat nilainya tetap tersimpan sehingga
// mematikan centang tidak menghapus setelan pojok yang sudah dibuat.
$radiusFields = [
    ['key' => 'radius_per_corner', 'type' => 'boolean', 'label' => 'Atur Tiap Pojok', 'group' => 'design', 'panel' => 'radius', 'default' => false],
    ['key' => 'border_radius', 'type' => 'number', 'label' => 'Border Radius', 'group' => 'design', 'panel' => 'radius', 'default' => 0, 'show_if' => ['radius_per_corner', false]],
    ['key' => 'radius_tl', 'type' => 'number', 'label' => 'Pojok Kiri Atas', 'group' => 'design', 'panel' => 'radius', 'default' => 0, 'show_if' => ['radius_per_corner', true]],
    ['key' => 'radius_tr', 'type' => 'number', 'label' => 'Pojok Kanan Atas', 'group' => 'design', 'panel' => 'radius', 'default' => 0, 'show_if' => ['radius_per_corner', true]],
    ['key' => 'radius_br', 'type' => 'number', 'label' => 'Pojok Kanan Bawah', 'group' => 'design', 'panel' => 'radius', 'default' => 0, 'show_if' => ['radius_per_corner', true]],
    ['key' => 'radius_bl', 'type' => 'number', 'label' => 'Pojok Kiri Bawah', 'group' => 'design', 'panel' => 'radius', 'default' => 0, 'show_if' => ['radius_per_corner', true]],
];

// Animasi entrance — di-merge ke SEMUA tipe section (dirender _section-shell).
$animationFields = [
    ['key' => 'animation', 'type' => 'select', 'label' => 'Animasi Masuk', 'group' => 'design', 'panel' => 'animation', 'options' => ['none', 'fade-up', 'fade-in', 'zoom-in', 'slide-left', 'slide-right'], 'default' => 'none'],
    ['key' => 'animation_delay', 'type' => 'number', 'label' => 'Delay Animasi (ms)', 'group' => 'design', 'panel' => 'animation', 'default' => 0],
];

$components = [
    // Latar cover memakai field media bersama (bg_media_type/bg_image/bg_images/bg_video)
    // dan bg_overlay, sama seperti section lain. Field background_image/overlay_enabled
    // khusus cover dihapus; datanya dipindah oleh migration 2026_07_21_000001.
    // Perbedaannya diatur di bawah lewat $coverFieldOverrides: cover selalu menggambar
    // medianya sendiri, jadi tidak punya pilihan "Latar Section" maupun efek latar.
    'cover' => [
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Varian Layout', 'group' => 'design', 'options' => ['fullscreen', 'split', 'minimal', 'arch'], 'default' => 'fullscreen'],
        ['key' => 'title', 'type' => 'text', 'label' => 'Judul Kecil', 'group' => 'content', 'default' => 'The Wedding Of'],
        ['key' => 'button_text', 'type' => 'text', 'label' => 'Teks Tombol', 'group' => 'content', 'default' => 'Buka Undangan'],
    ],

    'hero' => [
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Varian Layout', 'group' => 'design', 'options' => ['fullscreen', 'split', 'minimal'], 'default' => 'fullscreen'],
        ['key' => 'title', 'type' => 'text', 'label' => 'Judul Kecil', 'group' => 'content', 'default' => 'The Wedding Of'],
        // Foto latar hero memakai bg_image milik treatment (tab Konten) dan overlaynya
        // bg_overlay di panel Latar & Treatment — satu foto, satu overlay, satu tempat.
        // Field background_image/overlay_enabled/overlay_opacity khusus hero dihapus;
        // datanya dipindah oleh migration 2026_07_20_000001.
        ['key' => 'alignment', 'type' => 'select', 'label' => 'Perataan', 'group' => 'design', 'options' => ['left', 'center', 'right'], 'default' => 'center'],
        ['key' => 'padding_top', 'type' => 'number', 'label' => 'Padding Atas', 'group' => 'design', 'panel' => 'spacing', 'default' => 120],
        ['key' => 'padding_bottom', 'type' => 'number', 'label' => 'Padding Bawah', 'group' => 'design', 'panel' => 'spacing', 'default' => 120],
    ],

    'text' => [
        ['key' => 'content', 'type' => 'text', 'label' => 'Isi Teks', 'group' => 'content', 'default' => 'Tulis teks anda di sini...'],
        ['key' => 'tag', 'type' => 'select', 'label' => 'Jenis Elemen', 'group' => 'design', 'options' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'], 'default' => 'p'],
        ['key' => 'align', 'type' => 'select', 'label' => 'Perataan', 'group' => 'design', 'options' => ['left', 'center', 'right'], 'default' => 'left'],
        ['key' => 'font_size', 'type' => 'number', 'label' => 'Ukuran Font', 'group' => 'design', 'default' => null],
        ['key' => 'margin_bottom', 'type' => 'number', 'label' => 'Margin Bawah', 'group' => 'design', 'panel' => 'spacing', 'default' => 0],
        ['key' => 'line_height', 'type' => 'number', 'label' => 'Line Height', 'group' => 'design', 'default' => 1.5],
        ['key' => 'letter_spacing', 'type' => 'number', 'label' => 'Letter Spacing', 'group' => 'design', 'default' => 0],
        ['key' => 'element_id', 'type' => 'text', 'label' => 'Element ID', 'group' => 'advanced', 'default' => null],
        ['key' => 'custom_css', 'type' => 'text', 'label' => 'Custom CSS', 'group' => 'advanced', 'default' => ''],
    ],

    'image' => [
        ['key' => 'src', 'type' => 'image', 'label' => 'Gambar', 'group' => 'content', 'default' => '', 'required' => true],
        ['key' => 'alt', 'type' => 'text', 'label' => 'Teks Alt', 'group' => 'content', 'default' => ''],
        ['key' => 'width', 'type' => 'number', 'label' => 'Lebar (%)', 'group' => 'design', 'default' => 100],
        ...$radiusFields,
        ['key' => 'border_width', 'type' => 'number', 'label' => 'Border Width', 'group' => 'design', 'default' => 0],
        ['key' => 'shadow', 'type' => 'select', 'label' => 'Bayangan', 'group' => 'design', 'options' => ['none', 'sm', 'md', 'lg'], 'default' => 'none'],
        ['key' => 'align', 'type' => 'select', 'label' => 'Perataan', 'group' => 'design', 'options' => ['left', 'center', 'right'], 'default' => 'center'],
        ['key' => 'margin_top', 'type' => 'number', 'label' => 'Margin Atas', 'group' => 'design', 'panel' => 'spacing', 'default' => 0],
        ['key' => 'margin_bottom', 'type' => 'number', 'label' => 'Margin Bawah', 'group' => 'design', 'panel' => 'spacing', 'default' => 24],
        ['key' => 'element_id', 'type' => 'text', 'label' => 'Element ID', 'group' => 'advanced', 'default' => null],
        ['key' => 'custom_css', 'type' => 'text', 'label' => 'Custom CSS', 'group' => 'advanced', 'default' => ''],
    ],

    'button' => [
        ['key' => 'text', 'type' => 'text', 'label' => 'Teks Tombol', 'group' => 'content', 'default' => 'Click Me'],
        ['key' => 'url', 'type' => 'url', 'label' => 'Tautan', 'group' => 'content', 'default' => '#'],
        // primary/secondary/ghost tidak lagi ditawarkan tapi tetap dirender (dipetakan di
        // button.blade.php) supaya baris lama tidak berubah drastis.
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Gaya Tombol', 'group' => 'design', 'options' => ['solid', 'soft', 'outline', 'round', 'link', 'rule'], 'default' => 'solid'],
        ['key' => 'size', 'type' => 'select', 'label' => 'Ukuran', 'group' => 'design', 'options' => ['small', 'medium', 'large'], 'default' => 'medium'],
        ['key' => 'align', 'type' => 'select', 'label' => 'Perataan', 'group' => 'design', 'options' => ['left', 'center', 'right'], 'default' => 'center'],
        // null = ikut --radius tema. Angka eksplisit menimpanya (kecuali pill/rule).
        ['key' => 'border_radius', 'type' => 'number', 'label' => 'Border Radius', 'group' => 'design', 'default' => null],
        ['key' => 'border_width', 'type' => 'number', 'label' => 'Border Width', 'group' => 'design', 'default' => 0],
        ['key' => 'shadow', 'type' => 'select', 'label' => 'Bayangan', 'group' => 'design', 'options' => ['none', 'sm', 'md', 'lg'], 'default' => 'none'],
        ['key' => 'margin_top', 'type' => 'number', 'label' => 'Margin Atas', 'group' => 'design', 'panel' => 'spacing', 'default' => 0],
        ['key' => 'margin_bottom', 'type' => 'number', 'label' => 'Margin Bawah', 'group' => 'design', 'panel' => 'spacing', 'default' => 24],
        ['key' => 'element_id', 'type' => 'text', 'label' => 'Element ID', 'group' => 'advanced', 'default' => null],
        ['key' => 'custom_css', 'type' => 'text', 'label' => 'Custom CSS', 'group' => 'advanced', 'default' => ''],
    ],

    'divider' => [
        ['key' => 'type', 'type' => 'select', 'label' => 'Tipe', 'group' => 'design', 'options' => ['line', 'spacer'], 'default' => 'line'],
        ['key' => 'height', 'type' => 'number', 'label' => 'Tinggi', 'group' => 'design', 'default' => 1],
        ['key' => 'style', 'type' => 'select', 'label' => 'Gaya Garis', 'group' => 'design', 'options' => ['solid', 'dashed', 'dotted'], 'default' => 'solid'],
        ['key' => 'width', 'type' => 'number', 'label' => 'Lebar (%)', 'group' => 'design', 'default' => 100],
        ['key' => 'margin_top', 'type' => 'number', 'label' => 'Margin Atas', 'group' => 'design', 'panel' => 'spacing', 'default' => 24],
        ['key' => 'margin_bottom', 'type' => 'number', 'label' => 'Margin Bawah', 'group' => 'design', 'panel' => 'spacing', 'default' => 24],
        ['key' => 'element_id', 'type' => 'text', 'label' => 'Element ID', 'group' => 'advanced', 'default' => null],
        ['key' => 'custom_css', 'type' => 'text', 'label' => 'Custom CSS', 'group' => 'advanced', 'default' => ''],
    ],

    'spacer' => [
        ['key' => 'height', 'type' => 'number', 'label' => 'Tinggi', 'group' => 'design', 'default' => 50],
        ['key' => 'element_id', 'type' => 'text', 'label' => 'Element ID', 'group' => 'advanced', 'default' => null],
        ['key' => 'custom_css', 'type' => 'text', 'label' => 'Custom CSS', 'group' => 'advanced', 'default' => ''],
    ],

    'countdown' => [
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Varian Layout', 'group' => 'design', 'options' => ['cards', 'minimal-line', 'ring'], 'default' => 'cards'],
        ['key' => 'title', 'type' => 'text', 'label' => 'Judul', 'group' => 'content', 'default' => 'Counting Down To'],
        ['key' => 'passed_text', 'type' => 'text', 'label' => 'Teks Saat Hari-H Lewat', 'group' => 'content', 'default' => 'Hari bahagia telah tiba'],
        ['key' => 'padding_top', 'type' => 'number', 'label' => 'Padding Atas', 'group' => 'design', 'panel' => 'spacing', 'default' => 64],
        ['key' => 'padding_bottom', 'type' => 'number', 'label' => 'Padding Bawah', 'group' => 'design', 'panel' => 'spacing', 'default' => 64],
    ],

    'gallery' => [
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Varian Galeri', 'group' => 'design', 'options' => ['grid', 'masonry', 'slider'], 'default' => 'grid'],
        ['key' => 'heading', 'type' => 'text', 'label' => 'Judul', 'group' => 'content', 'default' => 'Galeri'],
        ['key' => 'subheading', 'type' => 'text', 'label' => 'Sub Judul', 'group' => 'content', 'default' => ''],
        ['key' => 'video_url', 'type' => 'url', 'label' => 'URL Video YouTube (opsional, tampil di atas foto)', 'group' => 'content', 'default' => ''],
        ['key' => 'images', 'type' => 'image_list', 'label' => 'Foto', 'group' => 'content', 'default' => []],
        ['key' => 'layout', 'type' => 'select', 'label' => 'Layout', 'group' => 'design', 'options' => ['grid', 'masonry', 'slider'], 'default' => 'grid'],
        ['key' => 'columns', 'type' => 'number', 'label' => 'Jumlah Kolom', 'group' => 'design', 'default' => 2],
        ['key' => 'gap', 'type' => 'number', 'label' => 'Jarak Antar Foto', 'group' => 'design', 'default' => 16],
        ['key' => 'lightbox', 'type' => 'boolean', 'label' => 'Aktifkan Lightbox', 'group' => 'design', 'default' => true],
    ],

    'map' => [
        // no-embed tidak memuat iframe: field Zoom dan Tinggi Peta tidak berpengaruh di sana.
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Varian Peta', 'group' => 'design', 'options' => ['framed', 'bar', 'full-bleed', 'address-first', 'no-embed'], 'default' => 'framed'],
        ['key' => 'title', 'type' => 'text', 'label' => 'Judul', 'group' => 'content', 'default' => null],
        ['key' => 'address', 'type' => 'text', 'label' => 'Alamat', 'group' => 'content', 'default' => ''],
        // Label kecil di atas alamat ("Akad", "Resepsi"). Paling terlihat di varian address-first.
        ['key' => 'venue_label', 'type' => 'text', 'label' => 'Label Lokasi', 'group' => 'content', 'default' => ''],
        ['key' => 'latitude', 'type' => 'text', 'label' => 'Latitude', 'group' => 'content', 'default' => ''],
        ['key' => 'longitude', 'type' => 'text', 'label' => 'Longitude', 'group' => 'content', 'default' => ''],
        ['key' => 'button_text', 'type' => 'text', 'label' => 'Teks Tombol', 'group' => 'content', 'default' => 'Petunjuk Arah'],
        ['key' => 'zoom', 'type' => 'number', 'label' => 'Level Zoom', 'group' => 'design', 'default' => 15],
        ['key' => 'height', 'type' => 'number', 'label' => 'Tinggi Peta', 'group' => 'design', 'default' => 400],
        ['key' => 'show_button', 'type' => 'boolean', 'label' => 'Tampilkan Tombol Arah', 'group' => 'design', 'default' => true],
    ],

    'music' => [
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Varian Pemutar', 'group' => 'design', 'options' => ['disc', 'minimal', 'pill'], 'default' => 'disc'],
        ['key' => 'src', 'type' => 'audio', 'label' => 'File Musik', 'group' => 'content', 'default' => ''],
        ['key' => 'autoplay', 'type' => 'boolean', 'label' => 'Putar Saat Undangan Dibuka', 'group' => 'design', 'default' => true],
        ['key' => 'loop', 'type' => 'boolean', 'label' => 'Ulangi', 'group' => 'design', 'default' => true],
        ['key' => 'show_controls', 'type' => 'boolean', 'label' => 'Tampilkan Tombol Kontrol', 'group' => 'design', 'default' => true],
    ],

    'rsvp' => [
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Varian Layout', 'group' => 'design', 'options' => ['elevated', 'custom-controls', 'underline'], 'default' => 'elevated'],
        ['key' => 'title', 'type' => 'text', 'label' => 'Judul', 'group' => 'content', 'default' => 'RSVP'],
        ['key' => 'subtitle', 'type' => 'text', 'label' => 'Subjudul', 'group' => 'content', 'default' => 'Please confirm your attendance'],
        ['key' => 'name_label', 'type' => 'text', 'label' => 'Label Nama', 'group' => 'content', 'default' => 'Nama Lengkap'],
        ['key' => 'phone_label', 'type' => 'text', 'label' => 'Label WhatsApp', 'group' => 'content', 'default' => 'No. WhatsApp'],
        ['key' => 'email_label', 'type' => 'text', 'label' => 'Label Email', 'group' => 'content', 'default' => 'Email'],
        ['key' => 'attendance_label', 'type' => 'text', 'label' => 'Label Konfirmasi Kehadiran', 'group' => 'content', 'default' => 'Konfirmasi Kehadiran'],
        ['key' => 'attend_yes_label', 'type' => 'text', 'label' => 'Label Hadir', 'group' => 'content', 'default' => 'Hadir'],
        ['key' => 'attend_no_label', 'type' => 'text', 'label' => 'Label Tidak Hadir', 'group' => 'content', 'default' => 'Tidak Hadir'],
        ['key' => 'attend_maybe_label', 'type' => 'text', 'label' => 'Label Masih Ragu', 'group' => 'content', 'default' => 'Masih Ragu'],
        ['key' => 'guests_label', 'type' => 'text', 'label' => 'Label Jumlah Tamu', 'group' => 'content', 'default' => 'Jumlah Tamu'],
        ['key' => 'message_label', 'type' => 'text', 'label' => 'Label Pesan', 'group' => 'content', 'default' => 'Pesan'],
        ['key' => 'button_text', 'type' => 'text', 'label' => 'Teks Tombol', 'group' => 'content', 'default' => 'Kirim Konfirmasi'],
        ['key' => 'success_message', 'type' => 'text', 'label' => 'Pesan Sukses', 'group' => 'content', 'default' => 'Terima kasih atas konfirmasi Anda!'],
        ['key' => 'whatsapp_phone', 'type' => 'text', 'label' => 'Nomor WhatsApp', 'group' => 'content', 'default' => ''],
        ['key' => 'whatsapp_enabled', 'type' => 'boolean', 'label' => 'Teruskan ke WhatsApp', 'group' => 'design', 'default' => false],
        ['key' => 'padding_top', 'type' => 'number', 'label' => 'Padding Atas', 'group' => 'design', 'panel' => 'spacing', 'default' => 80],
        ['key' => 'padding_bottom', 'type' => 'number', 'label' => 'Padding Bawah', 'group' => 'design', 'panel' => 'spacing', 'default' => 80],
    ],

    'video' => [
        ['key' => 'src', 'type' => 'video', 'label' => 'File Video', 'group' => 'content', 'default' => ''],
        ['key' => 'youtube_url', 'type' => 'url', 'label' => 'URL YouTube', 'group' => 'content', 'default' => ''],
        ['key' => 'type', 'type' => 'select', 'label' => 'Sumber Video', 'group' => 'design', 'options' => ['upload', 'youtube'], 'default' => 'upload'],
        ['key' => 'autoplay', 'type' => 'boolean', 'label' => 'Putar Otomatis', 'group' => 'design', 'default' => false],
        ['key' => 'muted', 'type' => 'boolean', 'label' => 'Bisukan', 'group' => 'design', 'default' => true],
        ['key' => 'controls', 'type' => 'boolean', 'label' => 'Tampilkan Kontrol', 'group' => 'design', 'default' => true],
        ['key' => 'width', 'type' => 'number', 'label' => 'Lebar (%)', 'group' => 'design', 'default' => 100],
        // Sebelumnya video dipatok border-radius: 8px di Blade tanpa field sama sekali.
        ...array_map(
            fn ($f) => $f['key'] === 'border_radius' ? [...$f, 'default' => 8] : $f,
            $radiusFields
        ),
        ['key' => 'margin_top', 'type' => 'number', 'label' => 'Margin Atas', 'group' => 'design', 'panel' => 'spacing', 'default' => 0],
        ['key' => 'margin_bottom', 'type' => 'number', 'label' => 'Margin Bawah', 'group' => 'design', 'panel' => 'spacing', 'default' => 24],
    ],

    'couple' => [
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Varian Layout', 'group' => 'design', 'options' => ['centered-stacked', 'centered-portrait', 'side-alternating', 'arch-pair', 'portrait-overlay', 'label-vertical'], 'default' => 'centered-stacked'],
        ['key' => 'heading', 'type' => 'text', 'label' => 'Judul', 'group' => 'content', 'default' => 'Mempelai'],
        ['key' => 'groom_label', 'type' => 'text', 'label' => 'Label Mempelai Pria', 'group' => 'content', 'default' => 'Mempelai Pria'],
        ['key' => 'bride_label', 'type' => 'text', 'label' => 'Label Mempelai Wanita', 'group' => 'content', 'default' => 'Mempelai Wanita'],
        ['key' => 'groom_photo', 'type' => 'image', 'label' => 'Foto Mempelai Pria', 'group' => 'content', 'default' => null],
        ['key' => 'bride_photo', 'type' => 'image', 'label' => 'Foto Mempelai Wanita', 'group' => 'content', 'default' => null],
        ['key' => 'groom_parents', 'type' => 'text', 'label' => 'Orang Tua Mempelai Pria', 'group' => 'content', 'default' => 'Putra dari Bapak … & Ibu …'],
        ['key' => 'bride_parents', 'type' => 'text', 'label' => 'Orang Tua Mempelai Wanita', 'group' => 'content', 'default' => 'Putri dari Bapak … & Ibu …'],
        ['key' => 'groom_instagram', 'type' => 'url', 'label' => 'Instagram Mempelai Pria', 'group' => 'design', 'default' => null],
        ['key' => 'bride_instagram', 'type' => 'url', 'label' => 'Instagram Mempelai Wanita', 'group' => 'design', 'default' => null],
        ['key' => 'groom_text_align', 'type' => 'select', 'label' => 'Perataan Teks Mempelai Pria', 'group' => 'design', 'options' => ['left', 'center', 'right'], 'default' => 'left', 'variant' => ['portrait-overlay']],
        ['key' => 'bride_text_align', 'type' => 'select', 'label' => 'Perataan Teks Mempelai Wanita', 'group' => 'design', 'options' => ['left', 'center', 'right'], 'default' => 'right', 'variant' => ['portrait-overlay']],
        ['key' => 'padding_top', 'type' => 'number', 'label' => 'Padding Atas', 'group' => 'design', 'panel' => 'spacing', 'default' => 64],
        ['key' => 'padding_bottom', 'type' => 'number', 'label' => 'Padding Bawah', 'group' => 'design', 'panel' => 'spacing', 'default' => 64],
    ],

    'event_details' => [
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Varian Layout', 'group' => 'design', 'options' => ['bordered-cards', 'divider-list'], 'default' => 'bordered-cards'],
        ['key' => 'heading', 'type' => 'text', 'label' => 'Judul', 'group' => 'content', 'default' => 'Rangkaian Acara'],
        ['key' => 'maps_label', 'type' => 'text', 'label' => 'Label Tombol Lokasi', 'group' => 'content', 'default' => 'Lihat Lokasi'],
        ['key' => 'events', 'type' => 'repeater', 'label' => 'Acara', 'group' => 'content', 'fields' => [
            ['key' => 'name', 'type' => 'text', 'label' => 'Nama Acara', 'default' => 'Acara'],
            ['key' => 'date_text', 'type' => 'text', 'label' => 'Tanggal', 'default' => ''],
            ['key' => 'time_text', 'type' => 'text', 'label' => 'Waktu', 'default' => ''],
            ['key' => 'venue', 'type' => 'text', 'label' => 'Tempat', 'default' => ''],
            ['key' => 'address', 'type' => 'text', 'label' => 'Alamat', 'default' => ''],
            ['key' => 'maps_url', 'type' => 'url', 'label' => 'Link Google Maps', 'default' => ''],
        ], 'default' => [
            ['name' => 'Akad Nikah', 'date_text' => 'Sabtu, 1 Januari 2027', 'time_text' => '08.00 WITA', 'venue' => 'Nama Gedung', 'address' => '', 'maps_url' => ''],
            ['name' => 'Resepsi', 'date_text' => 'Sabtu, 1 Januari 2027', 'time_text' => '18.00 WITA', 'venue' => 'Nama Gedung', 'address' => '', 'maps_url' => ''],
        ]],
    ],

    'gift' => [
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Varian Kartu', 'group' => 'design', 'options' => ['bordered-cards', 'elevated', 'divider-list'], 'default' => 'bordered-cards'],
        ['key' => 'heading', 'type' => 'text', 'label' => 'Judul', 'group' => 'content', 'default' => 'Amplop Digital'],
        ['key' => 'accounts', 'type' => 'repeater', 'label' => 'Rekening', 'group' => 'content', 'fields' => [
            ['key' => 'bank', 'type' => 'text', 'label' => 'Bank / E-Wallet', 'default' => 'BCA'],
            ['key' => 'number', 'type' => 'text', 'label' => 'Nomor Rekening', 'default' => ''],
            ['key' => 'holder', 'type' => 'text', 'label' => 'Atas Nama', 'default' => ''],
        ], 'default' => [
            ['bank' => 'BCA', 'number' => '', 'holder' => ''],
        ]],
        // Keduanya teks yang dibaca tamu, bukan setelan tampilan — tempatnya di tab Konten.
        ['key' => 'message', 'type' => 'text', 'label' => 'Pesan', 'group' => 'content', 'default' => 'Tanpa mengurangi rasa hormat, bagi Anda yang ingin memberikan tanda kasih:'],
        ['key' => 'gift_address', 'type' => 'text', 'label' => 'Alamat Kirim Kado', 'group' => 'content', 'default' => ''],
        ['key' => 'copy_label', 'type' => 'text', 'label' => 'Label Tombol Salin', 'group' => 'content', 'default' => 'Salin'],
        ['key' => 'copied_label', 'type' => 'text', 'label' => 'Label Setelah Tersalin', 'group' => 'content', 'default' => 'Tersalin!'],
        ['key' => 'address_label', 'type' => 'text', 'label' => 'Label Alamat Kado', 'group' => 'content', 'default' => 'Kirim kado ke'],
    ],

    'quote' => [
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Varian Kutipan', 'group' => 'design', 'options' => ['plain', 'rules', 'panel', 'initial', 'source-first'], 'default' => 'plain'],
        ['key' => 'content', 'type' => 'text', 'label' => 'Kutipan', 'group' => 'content', 'default' => 'Dan di antara tanda-tanda kekuasaan-Nya ialah Dia menciptakan untukmu pasangan hidup dari jenismu sendiri, supaya kamu mendapat ketenangan hati.'],
        ['key' => 'attribution', 'type' => 'text', 'label' => 'Sumber', 'group' => 'content', 'default' => 'Q.S. Ar-Rum: 21'],
    ],

    'love_story' => [
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Varian Kisah', 'group' => 'design', 'options' => ['marginalia', 'center-line', 'cards', 'book'], 'default' => 'marginalia'],
        ['key' => 'heading', 'type' => 'text', 'label' => 'Judul', 'group' => 'content', 'default' => 'Kisah Kami'],
        ['key' => 'subheading', 'type' => 'text', 'label' => 'Sub Judul', 'group' => 'content', 'default' => ''],
        // Satu foto untuk seluruh section, tampil di atas daftar. Boleh dikosongkan.
        ['key' => 'image', 'type' => 'image', 'label' => 'Foto (opsional)', 'group' => 'content', 'default' => null],
        ['key' => 'stories', 'type' => 'repeater', 'label' => 'Kisah', 'group' => 'content', 'fields' => [
            ['key' => 'year', 'type' => 'text', 'label' => 'Tahun / Waktu', 'default' => ''],
            ['key' => 'title', 'type' => 'text', 'label' => 'Judul', 'default' => ''],
            ['key' => 'story', 'type' => 'text', 'label' => 'Cerita', 'default' => ''],
        ], 'default' => [
            ['year' => '2020', 'title' => 'Pertama Bertemu', 'story' => 'Ceritakan momen pertama kalian bertemu…'],
        ]],
    ],

    'live_stream' => [
        // Nama varian sengaja tidak memakai 'framed'/'full-bleed': kunci skematik di Studio
        // dipakai bersama semua komponen, dan map sudah memakai kedua nama itu.
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Varian Siaran', 'group' => 'design', 'options' => ['player', 'wide', 'marquee', 'card'], 'default' => 'player'],
        ['key' => 'heading', 'type' => 'text', 'label' => 'Judul', 'group' => 'content', 'default' => 'Live Streaming'],
        ['key' => 'youtube_url', 'type' => 'url', 'label' => 'URL YouTube', 'group' => 'content', 'default' => ''],
        ['key' => 'schedule_text', 'type' => 'text', 'label' => 'Jadwal', 'group' => 'content', 'default' => ''],
        ['key' => 'button_text', 'type' => 'text', 'label' => 'Teks Tombol', 'group' => 'content', 'default' => 'Tonton'],
    ],

    'closing' => [
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Varian Penutup', 'group' => 'design', 'options' => ['signature', 'arch', 'photo-cover', 'quiet', 'band'], 'default' => 'signature'],
        ['key' => 'message', 'type' => 'text', 'label' => 'Pesan', 'group' => 'content', 'default' => 'Merupakan suatu kebahagiaan dan kehormatan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu.'],
        // Kalimat ini dipakai hampir semua undangan di atas nama mempelai, tapi sebelumnya
        // tidak punya tempat sama sekali.
        ['key' => 'salutation', 'type' => 'text', 'label' => 'Salam Penutup', 'group' => 'content', 'default' => 'Kami yang berbahagia'],
        // Foto mempelai itu isi undangan, bukan setelan tampilan.
        ['key' => 'photo', 'type' => 'image', 'label' => 'Foto', 'group' => 'content', 'default' => null],
    ],

    'wishes' => [
        ['key' => 'variant', 'type' => 'variant', 'label' => 'Varian Daftar', 'group' => 'design', 'options' => ['bordered-cards', 'bubble', 'divider-list'], 'default' => 'bubble'],
        ['key' => 'heading', 'type' => 'text', 'label' => 'Judul', 'group' => 'content', 'default' => 'Ucapan & Doa'],
        ['key' => 'subheading', 'type' => 'text', 'label' => 'Sub Judul', 'group' => 'content', 'default' => ''],
        ['key' => 'empty_text', 'type' => 'text', 'label' => 'Teks Saat Belum Ada Ucapan', 'group' => 'content', 'default' => 'Belum ada ucapan.'],
        ['key' => 'limit', 'type' => 'number', 'label' => 'Jumlah Ucapan Ditampilkan', 'group' => 'design', 'default' => 50],
    ],

    'code' => [
        ['key' => 'html', 'type' => 'code', 'label' => 'HTML', 'group' => 'advanced', 'default' => ''],
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

// Treatment latar — hanya untuk kelas Section (lihat $sectionTypes di bawah).
//
// Selain "Latar Section" sendiri, semua field di sini hanya berpengaruh saat latarnya
// image: surface/contrast/dark tidak menggambar media apa pun. Jadi semuanya digerbangi
// $bgOn, dan tiap jenis media cuma memunculkan field miliknya sendiri — foto tunggal,
// daftar slideshow, atau video. Untuk video, bg_image berperan sebagai poster.
//
// Jenis media milik tab Desain sedangkan medianya di tab Konten: treatment, overlay, dan
// efek adalah keputusan desain template, foto/videonya isi yang sering diganti pemilik.
$bgOn = ['treatment', 'image'];
$treatmentFields = [
    ['key' => 'treatment', 'type' => 'select', 'label' => 'Latar Section', 'group' => 'design', 'panel' => 'treatment', 'options' => ['surface', 'contrast', 'dark', 'image'], 'default' => 'surface'],
    ['key' => 'bg_media_type', 'type' => 'select', 'label' => 'Jenis Media Latar', 'group' => 'design', 'panel' => 'treatment', 'options' => ['image', 'slideshow', 'video'], 'default' => 'image', 'show_if' => $bgOn],
    ['key' => 'bg_image', 'type' => 'image', 'label' => 'Foto Latar', 'group' => 'content', 'default' => null, 'show_if' => [$bgOn, ['bg_media_type', 'image']]],
    ['key' => 'bg_images', 'type' => 'image_list', 'label' => 'Foto Slideshow', 'group' => 'content', 'default' => [], 'show_if' => [$bgOn, ['bg_media_type', 'slideshow']]],
    // Poster punya kolom sendiri, bukan menumpang bg_image: perannya beda, dan satu label
    // "Foto Latar" di mode video tidak menjelaskan bahwa fotonya cuma tampil sebelum
    // video jalan. Terlihat juga saat autoplay ditolak browser dan saat gerakan dikurangi.
    ['key' => 'bg_poster', 'type' => 'image', 'label' => 'Foto Poster (sebelum video jalan)', 'group' => 'content', 'default' => null, 'show_if' => [$bgOn, ['bg_media_type', 'video']]],
    // Satu kolom untuk dua sumber: kontrol video sudah punya tombol unggah DAN kotak
    // tempel URL. Tautan YouTube dikenali dari isinya, jadi tidak perlu saklar sumber
    // terpisah yang harus diingat pemakai.
    ['key' => 'bg_video', 'type' => 'video', 'label' => 'Video Latar (unggah atau tautan YouTube)', 'group' => 'content', 'default' => null, 'show_if' => [$bgOn, ['bg_media_type', 'video']]],
    ['key' => 'bg_slide_seconds', 'type' => 'number', 'label' => 'Durasi per Foto (detik)', 'group' => 'design', 'panel' => 'treatment', 'default' => 5, 'show_if' => [$bgOn, ['bg_media_type', 'slideshow']]],
    ['key' => 'bg_overlay', 'type' => 'number', 'label' => 'Opasitas Overlay (%)', 'group' => 'design', 'panel' => 'treatment', 'default' => 45, 'show_if' => $bgOn],
    // Berlaku untuk ketiga jenis media. Satu-satunya kecuali: pinned pada latar YouTube,
    // yang memaksa tinggi elemen dan merusak perhitungan cover 16:9 iframe — digugurkan
    // di _section-shell, bukan disembunyikan di sini (pilihannya bergantung isi kolom
    // video, bukan pada nilai prop yang bisa dibaca show_if).
    ['key' => 'bg_effect', 'type' => 'select', 'label' => 'Efek Latar', 'group' => 'design', 'panel' => 'treatment', 'options' => ['none', 'pinned', 'kenburns', 'scroll-zoom-in', 'scroll-zoom-out'], 'default' => 'none', 'show_if' => $bgOn],
    ['key' => 'bg_effect_strength', 'type' => 'number', 'label' => 'Kekuatan Efek (%)', 'group' => 'design', 'panel' => 'treatment', 'default' => 130, 'show_if' => $bgOn],
];

// Treatment hanya untuk kelas Section (guideline §9) — Basic (text/music/…) tidak
// dapat "Foto Latar". Animasi masuk tetap untuk semua tipe. Daftar kelas hidup di
// config/invitation_component_classes.php (sumber kebenaran tunggal, §2.1/§2.2).
$classes = require __DIR__ . '/invitation_component_classes.php';
$sectionTypes = array_merge($classes['feature'], $classes['container']);

// column_index: posisi kolom saat section jadi anak container. Hidden = tak muncul di
// loop form generik, tapi tetap tervalidasi & ikut tersimpan (pola yang sama dipakai
// field {key}_color ornamen). Renderer container membacanya lewat props.column_index.
foreach ($classes['basic'] as $basicType) {
    $components[$basicType][] = [
        'key' => 'column_index', 'type' => 'number', 'label' => 'Kolom',
        'group' => 'advanced', 'hidden' => true, 'default' => 0,
    ];
}

foreach ($components as $type => $fields) {
    $components[$type] = array_merge($fields, $animationFields);
    if (in_array($type, $sectionTypes, true)) {
        $components[$type] = array_merge($components[$type], $treatmentFields);
    }
}

// Cover memakai field media yang sama, tapi dua hal tidak berlaku untuknya:
// - "Latar Section": cover SELALU menggambar medianya sendiri (gate + layar sticky), tidak
//   pernah memakai permukaan surface/contrast/dark. Karena pilihannya tidak ada, gerbang
//   show_if yang menunjuk ke treatment juga harus dilepas — kalau tidak, seluruh field
//   medianya tidak akan pernah muncul.
// - Efek latar: pinned dan scroll-zoom dihitung dari posisi scroll section, sedangkan gate
//   cover position:fixed dan layar stickynya tidak ikut scroll seperti section biasa.
$components['cover'] = array_values(array_filter(
    array_map(function (array $field) {
        if (($field['show_if'] ?? null) === ['treatment', 'image']) {
            unset($field['show_if']);
        } elseif (is_array($field['show_if'][0] ?? null)) {
            $rest = array_values(array_filter($field['show_if'], fn ($c) => $c !== ['treatment', 'image']));
            $field['show_if'] = count($rest) === 1 ? $rest[0] : $rest;
        }

        return $field;
    }, $components['cover']),
    fn (array $field) => ! in_array($field['key'], ['treatment', 'bg_effect', 'bg_effect_strength'], true)
));

// Ornamen — hanya section "utama" (bukan blok generic text/image/spacer/divider/kontainer).
$ornamentFields = [
    ['key' => 'ornaments_top', 'type' => 'ornament_list', 'label' => 'Ornamen Atas', 'group' => 'design', 'panel' => 'ornament', 'default' => []],
    ['key' => 'ornaments_bottom', 'type' => 'ornament_list', 'label' => 'Ornamen Bawah', 'group' => 'design', 'panel' => 'ornament', 'default' => []],
];
foreach (['cover', 'hero', 'quote', 'couple', 'event_details', 'gift', 'rsvp', 'countdown', 'closing', 'love_story'] as $type) {
    if (isset($components[$type])) {
        $components[$type] = array_merge($components[$type], $ornamentFields);
    }
}

return $components;
