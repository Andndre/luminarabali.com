@props(['props' => [], 'section' => null, 'page' => null])

@php
    $content = $props['content'] ?? 'Tulis teks anda di sini...';
    $tag = $props['tag'] ?? 'p'; // h1, h2, h3, h4, h5, h6, p
    $align = $props['align'] ?? 'left'; // left, center, right
    $color = $props['color'] ?? '#000000';
    $fontSize = $props['font_size'] ?? null;
    $marginBottom = $props['margin_bottom'] ?? 0;
    $fontFamily = $props['font_family'] ?? 'lato';
    $lineHeight = $props['line_height'] ?? 1.5;
    $letterSpacing = $props['letter_spacing'] ?? 0;
    $elementId = $props['element_id'] ?? null;
    $customClass = $props['custom_class'] ?? '';
    $customCss = $props['custom_css'] ?? '';

    // Font family mapping
    $fontFamilyMap = [
        'lato' => "'Lato', sans-serif",
        'montserrat' => "'Montserrat', sans-serif",
        'playfair-display' => "'Playfair Display', serif",
        'great-vibes' => "'Great Vibes', cursive",
        'open-sans' => "'Open Sans', sans-serif",
    ];
    $fontFamilyValue = $fontFamilyMap[$fontFamily] ?? "'Lato', sans-serif";

    // Build inline style
    $inlineStyle =
        'font-family: ' .
        $fontFamilyValue .
        '; color: ' .
        $color .
        '; margin-bottom: ' .
        $marginBottom .
        'px; line-height: ' .
        $lineHeight .
        '; letter-spacing: ' .
        $letterSpacing .
        'px;';
    if ($fontSize) {
        $inlineStyle .= ' font-size: ' . $fontSize . 'px;';
    }
    if ($align === 'center') {
        $inlineStyle .= ' text-align: center;';
    } elseif ($align === 'right') {
        $inlineStyle .= ' text-align: right;';
    }
    if (!empty($customCss)) {
        $inlineStyle .= ' ' . $customCss;
    }
@endphp

<section class="text-block-{{ $section->id ?? 'default' }}">
    @if ($tag === 'h1')
        <h1 @if ($elementId) id="{{ $elementId }}" @endif
            @if ($customClass) class="{{ $customClass }}" @endif style="{{ $inlineStyle }}">
            {!! $content !!}
        </h1>
    @elseif($tag === 'h2')
        <h2 @if ($elementId) id="{{ $elementId }}" @endif
            @if ($customClass) class="{{ $customClass }}" @endif style="{{ $inlineStyle }}">
            {!! $content !!}
        </h2>
    @elseif($tag === 'h3')
        <h3 @if ($elementId) id="{{ $elementId }}" @endif
            @if ($customClass) class="{{ $customClass }}" @endif style="{{ $inlineStyle }}">
            {!! $content !!}
        </h3>
    @elseif($tag === 'h4')
        <h4 @if ($elementId) id="{{ $elementId }}" @endif
            @if ($customClass) class="{{ $customClass }}" @endif style="{{ $inlineStyle }}">
            {!! $content !!}
        </h4>
    @elseif($tag === 'h5')
        <h5 @if ($elementId) id="{{ $elementId }}" @endif
            @if ($customClass) class="{{ $customClass }}" @endif style="{{ $inlineStyle }}">
            {!! $content !!}
        </h5>
    @elseif($tag === 'h6')
        <h6 @if ($elementId) id="{{ $elementId }}" @endif
            @if ($customClass) class="{{ $customClass }}" @endif style="{{ $inlineStyle }}">
            {!! $content !!}
        </h6>
    @else
        <p @if ($elementId) id="{{ $elementId }}" @endif
            @if ($customClass) class="{{ $customClass }}" @endif style="{{ $inlineStyle }}">
            {!! $content !!}
        </p>
    @endif
</section>
