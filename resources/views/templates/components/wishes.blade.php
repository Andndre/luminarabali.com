@props(['props' => [], 'section' => null, 'page' => null])

@php
    $heading = $props['heading'] ?? 'Ucapan & Doa';
    $subheading = $props['subheading'] ?? '';
    $emptyText = $props['empty_text'] ?? 'Belum ada ucapan.';
    $variant = $props['variant'] ?? 'bubble';
    $limit = (int) ($props['limit'] ?? 50);

    // Page persisted → ucapan asli; stub studio (belum tersimpan) → placeholder preview.
    $wishes = ($page && $page->exists)
        ? $page->rsvpResponses()
            ->whereNotNull('message')->where('message', '!=', '')
            ->where('is_hidden', false)
            ->latest('submitted_at')->limit($limit)->get()
        : collect([
            (object) ['guest_name' => 'Budi Santoso', 'message' => 'Selamat menempuh hidup baru! Semoga bahagia selalu.'],
            (object) ['guest_name' => 'Siti Rahayu', 'message' => 'Turut berbahagia, semoga menjadi keluarga sakinah.'],
        ]);
@endphp

<section class="wishes wishes--{{ $variant }}" style="padding: var(--section-y, 64px) 16px;">
  <div class="container mx-auto max-w-xl">
    <div class="text-center">
      <h2 class="section-heading" data-editable="heading">{{ $heading }}</h2>
      @if($subheading)<p class="section-subheading">{{ $subheading }}</p>@endif
    </div>

    <div class="wishes-list">
      @forelse($wishes as $wish)
        <div class="wish">
          @if($variant === 'bubble')
            {{-- Inisial, bukan foto: tamu tidak mengunggah apa pun saat mengisi RSVP. --}}
            <span class="wish-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($wish->guest_name, 0, 1)) }}</span>
          @endif
          <div class="wish-body">
            <p class="wish-name">{{ $wish->guest_name }}</p>
            <p class="wish-message">{{ $wish->message }}</p>
          </div>
        </div>
      @empty
        <p class="wishes-empty">{{ $emptyText }}</p>
      @endforelse
    </div>
  </div>
</section>
