@props(['props' => [], 'section' => null, 'page' => null])

@php
    $heading = $props['heading'] ?? 'Live Streaming';
    $youtubeUrl = $props['youtube_url'] ?? '';
    $scheduleText = $props['schedule_text'] ?? '';
    $buttonText = $props['button_text'] ?? 'Tonton';

    $youtubeVideoId = null;
    if ($youtubeUrl && preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $youtubeUrl, $m)) {
        $youtubeVideoId = $m[1];
    }
@endphp

<section style="padding: var(--section-y, 64px) 16px;">
  <div class="container mx-auto max-w-2xl text-center">
    <h2 class="mb-4" style="font-family: var(--font-heading, serif); font-size: var(--step-2xl, 32px);">
      {{ $heading }}
    </h2>
    @if($scheduleText)
      <p class="text-sm opacity-80 mb-6">{{ $scheduleText }}</p>
    @endif
    @if($youtubeVideoId)
      <div style="position: relative; padding-bottom: 56.25%; height: 0; border-radius: var(--radius, 12px); overflow: clip;">
        <iframe src="https://www.youtube.com/embed/{{ $youtubeVideoId }}"
            frameborder="0" allowfullscreen
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
      </div>
    @elseif($youtubeUrl)
      <a href="{{ $youtubeUrl }}" target="_blank" rel="noopener"
          class="inline-block px-6 py-2.5 text-sm font-semibold"
          style="background: var(--color-accent, #b5654d); color: var(--color-surface, #ffffff); border-radius: var(--radius, 12px);">
        {{ $buttonText }}
      </a>
    @endif
  </div>
</section>
