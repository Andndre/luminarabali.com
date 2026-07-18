@props(['props' => [], 'section' => null, 'page' => null])

@php
    $content = $props['content'] ?? '';
    $attribution = $props['attribution'] ?? '';
    $fontFamily = $props['font_family'] ?? null;
    $fontFamilyValue = $fontFamily ? "'{$fontFamily}', serif" : 'var(--font-heading, serif)';
@endphp

<section style="padding: var(--section-y, 64px) 16px;">
  <div class="container mx-auto max-w-2xl text-center">
    <div class="text-4xl mb-2" style="color: var(--color-accent, #b5654d); font-family: {{ $fontFamilyValue }};" aria-hidden="true">&ldquo;</div>
    <blockquote class="text-lg md:text-xl italic leading-relaxed" style="font-family: {{ $fontFamilyValue }};">
      <span data-editable="content">{{ $content }}</span>
    </blockquote>
    @if($attribution)
      <p class="mt-4 text-sm font-semibold opacity-80">&mdash; {{ $attribution }}</p>
    @endif
  </div>
</section>
