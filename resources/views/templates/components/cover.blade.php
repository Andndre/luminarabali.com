@props(['props' => [], 'section' => null, 'page' => null])

@php
$title = $props['title'] ?? 'The Wedding Of';
$groomName = $page->groom_name ?? 'Groom';
$brideName = $page->bride_name ?? 'Bride';
$eventDate = $page->event_date ?? null;
$buttonText = $props['button_text'] ?? 'Buka Undangan';
$buttonColor = $props['button_color'] ?? 'var(--color-accent, #d4af37)';
$fontFamily = isset($props['font_family']) ? "'{$props['font_family']}', serif" : "var(--font-heading, 'Playfair Display'), serif";
$textColor = $props['text_color'] ?? 'var(--color-surface, #ffffff)';
$overlayEnabled = filter_var($props['overlay_enabled'] ?? 'true', FILTER_VALIDATE_BOOLEAN);

$bgImage = $props['background_image'] ?? null;

// Get target from search params
$targetName = request()->query('to') ?? null;
@endphp

<style>
  .cover-section-{{ $section->id ?? 'default' }} {
    background-color: #1a1a1a;
    @if($bgImage)
    background-image: url('{{ str_starts_with($bgImage, 'http') ? $bgImage : Storage::url($bgImage) }}');
    @endif
    background-size: cover;
    background-position: center;
    position: fixed;
    inset: 0;
    z-index: 50; /* Make sure it covers everything */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: opacity 1s ease-in-out, visibility 1s;
  }

  .cover-section-{{ $section->id ?? 'default' }}::before {
    content: '';
    position: absolute;
    inset: 0;
    @if($overlayEnabled)
      background: rgba(0, 0, 0, 0.5);
    @endif
    z-index: 1;
  }

  .cover-section-{{ $section->id ?? 'default' }} .cover-content {
    position: relative;
    z-index: 10;
    text-align: center;
    color: {{ $textColor }};
    padding: 2rem;
  }

  .cover-section-{{ $section->id ?? 'default' }} .cover-title {
    font-family: {{ $fontFamily }};
    margin-bottom: 1rem;
  }

  .cover-section-{{ $section->id ?? 'default' }} .cover-names {
    font-family: {{ $fontFamily }};
  }

  .cover-section-{{ $section->id ?? 'default' }} .cover-date {
    font-family: {{ $fontFamily }};
  }

  /* Prevent scrolling when cover is active */
  body.cover-active {
    overflow: hidden;
  }
</style>

<div x-data="coverComponent()" 
     x-init="init()"
     x-show="isOpen" 
     class="cover-section-{{ $section->id ?? 'default' }}"
     :style="isOpen ? '' : 'opacity: 0; visibility: hidden;'">
  
  <div class="cover-content">
    @if($title)
      <p class="cover-title text-xl md:text-2xl">{{ $title }}</p>
    @endif

    <h1 class="cover-names text-5xl md:text-7xl font-bold mb-6 mt-4">
      {{ $groomName }} & {{ $brideName }}
    </h1>

    @if($eventDate)
      <p class="cover-date text-lg md:text-xl mb-12">
        {!! \Illuminate\Support\Str::ucfirst(\Carbon\Carbon::parse($eventDate)->translatedFormat('d F Y')) !!}
      </p>
    @endif

    @if($targetName)
      <div class="mb-8">
        <p class="text-sm md:text-base opacity-80 mb-2">Kepada Yth.</p>
        <p class="text-xl md:text-2xl font-semibold">{{ $targetName }}</p>
      </div>
    @endif

    <button @click="openInvitation()" 
            class="px-8 py-3 rounded-full font-semibold transition transform hover:scale-105 flex items-center gap-2 mx-auto"
            style="background: {{ $buttonColor }}; color: #ffffff; box-shadow: 0 4px 14px 0 rgba(0,0,0,0.39);">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
      </svg>
      {{ $buttonText }}
    </button>
  </div>
</div>

@push('scripts')
<script>
  // Automatically add class to body when rendered to prevent scrolling
  document.body.classList.add('cover-active');

  function coverComponent() {
    return {
      isOpen: true,
      init() {
        // Find if there's any music component to preload
        const audios = document.querySelectorAll('audio[id^="bg-music-"]');
        if (audios.length > 0) {
            audios[0].load();
        }
      },
      openInvitation() {
        this.isOpen = false;
        document.body.classList.remove('cover-active');
        
        // Attempt to play the first audio element found
        const audios = document.querySelectorAll('audio[id^="bg-music-"]');
        if (audios.length > 0) {
            const audio = audios[0];
            const playPromise = audio.play();
            
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.log("Audio playback was prevented by browser:", error);
                });
            }
            
            // Sync with Alpine play state on the music button
            const musicBtn = document.querySelector('[x-data*="playing"]');
            if (musicBtn && musicBtn.__x) {
                musicBtn.__x.$data.playing = true;
            }
        }
      }
    }
  }
</script>
@endpush
