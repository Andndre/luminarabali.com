@props(['props' => [], 'section' => null, 'page' => null])

@php
    $heading = $props['heading'] ?? 'Ucapan & Doa';
    $limit = (int) ($props['limit'] ?? 50);
    $backgroundColor = $props['background_color'] ?? 'var(--color-surface, #ffffff)';
    $accentColor = $props['accent_color'] ?? 'var(--color-accent, #d4af37)';
    $textColor = $props['text_color'] ?? 'var(--color-text, #212529)';

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

<section style="background: {{ $backgroundColor }}; color: {{ $textColor }}; padding: 64px 16px;">
  <div class="container mx-auto max-w-xl">
    <h2 class="text-2xl md:text-3xl font-bold mb-8 text-center" style="font-family: var(--font-heading, serif); color: {{ $accentColor }};"
      data-editable="heading">
      {{ $heading }}
    </h2>
    <div class="space-y-3 max-h-96 overflow-y-auto">
      @forelse($wishes as $wish)
        <div class="rounded-xl p-4" style="border: 1px solid {{ $accentColor }};">
          <p class="font-semibold text-sm" style="color: {{ $accentColor }};">{{ $wish->guest_name }}</p>
          <p class="mt-1 text-sm opacity-80">{{ $wish->message }}</p>
        </div>
      @empty
        <p class="text-center text-sm opacity-60">Belum ada ucapan.</p>
      @endforelse
    </div>
  </div>
</section>
