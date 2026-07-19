@props(['props' => [], 'section' => null, 'page' => null])

@php
    $message = $props['message'] ?? '';

    $photo = $props['photo'] ?? null;
    if (!empty($photo) && !\Illuminate\Support\Str::startsWith($photo, ['http://', 'https://', '/'])) {
        $photo = '/storage/' . ltrim($photo, '/');
    }

    $groom = $page->groom_name ?? 'Romeo';
    $bride = $page->bride_name ?? 'Juliet';
@endphp

<section style="padding: var(--section-y, 64px) 16px; text-align: center;">
  <div class="container mx-auto max-w-xl">
    @if($photo)
      <img src="{{ $photo }}" alt="{{ $groom }} & {{ $bride }}"
          class="w-full max-w-sm mx-auto object-cover mb-8" style="border-radius: var(--radius, 12px);">
    @endif
    <p class="text-base leading-relaxed opacity-90" data-editable="message">{{ $message }}</p>
    <h2 class="section-heading mt-8">
      {{ $groom }} &amp; {{ $bride }}
    </h2>
  </div>
</section>
