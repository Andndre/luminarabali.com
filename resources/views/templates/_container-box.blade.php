{{-- Kotak container bersama untuk section_one/two/three_col. Ketiganya cuma berbeda
     jumlah & lebar kolom, jadi seluruh perhitungan box hidup di sini.

     $widths: lebar tiap kolom dalam persen, urut. Jumlah elemennya = jumlah kolom. --}}
@props(['props' => [], 'section' => null, 'page' => null, 'elements' => [], 'widths' => [100]])

@php
    $num = fn (string $key, $fallback) => is_numeric($props[$key] ?? null) ? 0 + $props[$key] : $fallback;

    // Transparan, bukan surface: container adalah section, jadi shell sudah melukis
    // treatment-nya (contrast/dark/foto). Latar opak bawaan akan menutupi itu dan
    // membuat treatment container mustahil dipakai.
    $backgroundColor = ($props['background_color'] ?? null) ?: 'transparent';
    $borderWidth = $num('border_width', 0);
    $borderColor = ($props['border_color'] ?? null) ?: 'var(--color-surface_alt, #e5e7eb)';

    $shadow = [
        'none' => 'none',
        'sm' => '0 1px 2px rgba(0,0,0,0.08)',
        'md' => '0 8px 24px rgba(0,0,0,0.12)',
        'lg' => '0 14px 34px rgba(0,0,0,0.16)',
    ][$props['shadow'] ?? 'none'] ?? 'none';

    $side = fn (string $key) => ($props[$key.'_mode'] ?? 'px') === 'auto' ? 'auto' : $num($key, 0).'px';

    $align = match ($props['vertical_align'] ?? 'top') {
        'center' => 'center',
        'bottom' => 'flex-end',
        default => 'flex-start',
    };

    $box = implode('', [
        'padding:'.$num('padding_top', 60).'px '.$num('padding_right', 20).'px '
            .$num('padding_bottom', 60).'px '.$num('padding_left', 20).'px;',
        'max-width:'.$num('max_width', 1200).'px;',
        'margin:'.$num('margin_top', 0).'px '.$side('margin_right').' '
            .$num('margin_bottom', 0).'px '.$side('margin_left').';',
        'background-color:'.$backgroundColor.';',
        $borderWidth > 0 ? 'border:'.$borderWidth.'px solid '.$borderColor.';' : '',
        'border-radius:'.$num('border_radius', 0).'px;',
        'box-shadow:'.$shadow.';',
    ]);

    // column_index adalah sumber kebenaran editor; order_index cuma jaring pengaman
    // untuk baris lama yang belum pernah disentuh Studio.
    $byColumn = collect($elements)->groupBy(
        fn ($el) => (int) data_get($el->props ?? [], 'column_index', $el->order_index ?? 0)
    );
@endphp

{{-- Nama kelas dipertahankan dari sebelum partial ini ada (section-one-col, dst):
     custom_css milik template lama boleh saja menargetkannya. --}}
<div class="{{ str_replace('_', '-', $section->section_type) }}" style="{{ $box }}">
    <div class="section-col-row" style="display:flex;gap:{{ $num('column_gap', 20) }}px;align-items:{{ $align }}">
        @foreach ($widths as $i => $width)
            <div style="width:{{ $width }}%;min-width:0">
                @foreach ($byColumn->get($i, collect()) as $element)
                    {{-- Lewat shell, bukan @include langsung: kalau tidak, animasi masuk dan
                         custom_css milik blok anak tidak pernah dirender. Basic tidak punya
                         field treatment/ornamen, jadi shell hampir selalu jatuh ke pembungkus
                         display:contents yang ringan. --}}
                    @include('templates._section-shell', [
                        'section' => $element,
                        'page' => $page,
                        'elements' => [],
                    ])
                @endforeach
            </div>
        @endforeach
    </div>
</div>
