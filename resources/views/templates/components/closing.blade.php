@props(['props' => [], 'section' => null, 'page' => null])

@php
    $message = $props['message'] ?? '';
    $backgroundColor = $props['background_color'] ?? 'var(--color-surface, #ffffff)';
    $accentColor = $props['accent_color'] ?? 'var(--color-accent, #d4af37)';
    $textColor = $props['text_color'] ?? 'var(--color-text, #212529)';

    $photo = $props['photo'] ?? null;
    if (!empty($photo) && !\Illuminate\Support\Str::startsWith($photo, ['http://', 'https://', '/'])) {
        $photo = '/storage/' . ltrim($photo, '/');
    }

    $groom = $page->groom_name ?? 'Romeo';
    $bride = $page->bride_name ?? 'Juliet';
@endphp

<section style="background: {{ $backgroundColor }}; color: {{ $textColor }}; padding: 80px 16px; text-align: center;">
  <div class="container mx-auto max-w-xl">
    @if($photo)
      <img src="{{ $photo }}" alt="{{ $groom }} & {{ $bride }}"
          class="w-full max-w-sm mx-auto rounded-xl object-cover mb-8">
    @endif
    <p class="text-base leading-relaxed opacity-90" data-editable="message">{{ $message }}</p>
    <h2 class="mt-8 text-3xl font-bold" style="font-family: var(--font-heading, serif); color: {{ $accentColor }};">
      {{ $groom }} &amp; {{ $bride }}
    </h2>
  </div>
</section>
