@props(['props' => [], 'section' => null, 'page' => null])

@php
    $heading = $props['heading'] ?? 'Ucapan & Doa';
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

<section style="padding: var(--section-y, 64px) 16px;">
  <div class="container mx-auto max-w-xl">
    <h2 class="mb-8 text-center" style="font-family: var(--font-heading, serif); font-size: var(--step-2xl, 32px);"
      data-editable="heading">
      {{ $heading }}
    </h2>
    <div class="space-y-3 max-h-96 overflow-y-auto">
      @forelse($wishes as $wish)
        <div class="p-4" style="border: 1px solid var(--color-accent, #b5654d); border-radius: var(--radius, 12px);">
          <p class="font-semibold text-sm" style="color: var(--color-accent, #b5654d);">{{ $wish->guest_name }}</p>
          <p class="mt-1 text-sm opacity-80">{{ $wish->message }}</p>
        </div>
      @empty
        <p class="text-center text-sm opacity-60">Belum ada ucapan.</p>
      @endforelse
    </div>
  </div>
</section>
