@props(['props' => [], 'section' => null, 'page' => null])

@php
    $content = $props['content'] ?? '';
    $attribution = $props['attribution'] ?? '';
    $textColor = $props['text_color'] ?? 'var(--color-text, #212529)';
    $backgroundColor = $props['background_color'] ?? 'var(--color-surface, #ffffff)';
    $fontFamily = $props['font_family'] ?? null;
    $fontFamilyValue = $fontFamily ? "'{$fontFamily}', serif" : 'var(--font-heading, serif)';
@endphp

<section style="background: {{ $backgroundColor }}; color: {{ $textColor }}; padding: 64px 16px;">
  <div class="container mx-auto max-w-2xl text-center">
    <blockquote class="text-lg md:text-xl italic leading-relaxed" style="font-family: {{ $fontFamilyValue }};">
      &ldquo;<span data-editable="content">{{ $content }}</span>&rdquo;
    </blockquote>
    @if($attribution)
      <p class="mt-4 text-sm font-semibold opacity-80">&mdash; {{ $attribution }}</p>
    @endif
  </div>
</section>
