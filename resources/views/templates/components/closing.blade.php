@props(['props' => [], 'section' => null, 'page' => null])

@php
    $message = $props['message'] ?? '';
    $salutation = $props['salutation'] ?? '';
    $variant = $props['variant'] ?? 'signature';

    $photo = $props['photo'] ?? null;
    if (!empty($photo) && !\Illuminate\Support\Str::startsWith($photo, ['http://', 'https://', '/'])) {
        $photo = '/storage/' . ltrim($photo, '/');
    }

    $groom = $page->groom_name ?? 'Romeo';
    $bride = $page->bride_name ?? 'Juliet';

    // photo-cover menaruh nama di atas foto, jadi kotaknya harus tetap ada meski
    // fotonya kosong — latarnya jatuh ke --color-ink lewat CSS.
    $showPhotoBox = $variant === 'photo-cover' || ($photo && $variant !== 'quiet' && $variant !== 'band');
@endphp

<section class="closing closing--{{ $variant }}">
  @if($showPhotoBox)
    <div class="closing-photo">
      @if($photo)
        <img src="{{ $photo }}" alt="{{ $groom }} &amp; {{ $bride }}" loading="lazy">
      @endif
    </div>
  @endif

  <div class="closing-body">
    <p class="closing-message" data-editable="message">{{ $message }}</p>
    <div class="closing-sign">
      @if($salutation)
        <p class="closing-salutation">{{ $salutation }}</p>
      @endif
      <h2 class="section-heading closing-names">{{ $groom }} &amp; {{ $bride }}</h2>
    </div>
  </div>
</section>
