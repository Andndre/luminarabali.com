@props(['props' => [], 'section' => null, 'page' => null])

@php
    $heading = $props['heading'] ?? 'Amplop Digital';
    $message = $props['message'] ?? '';
    $accounts = $props['accounts'] ?? [];
    $giftAddress = $props['gift_address'] ?? '';
    $copyLabel = $props['copy_label'] ?? 'Salin';
    $copiedLabel = $props['copied_label'] ?? 'Tersalin!';
@endphp

<section style="padding: var(--section-y, 64px) 16px;">
  <div class="container mx-auto max-w-xl text-center">
    <h2 class="mb-4" style="font-family: var(--font-heading, serif); font-size: var(--step-2xl, 32px);">
      {{ $heading }}
    </h2>
    @if($message)
      <p class="text-sm opacity-80 mb-8">{{ $message }}</p>
    @endif
    <div class="space-y-4">
      @foreach($accounts as $i => $account)
        <div class="p-5 text-left flex items-center gap-4"
            style="border: 1px solid var(--color-accent, #b5654d); border-radius: var(--radius, 12px);">
          <div class="flex-1 min-w-0">
            <p class="text-xs uppercase tracking-wide opacity-70">{{ $account['bank'] ?? '' }}</p>
            <p class="font-mono font-semibold truncate" id="gift-number-{{ $section->id }}-{{ $i }}">{{ $account['number'] ?? '' }}</p>
            @if(!empty($account['holder']))
              <p class="text-sm opacity-80">a.n. {{ $account['holder'] }}</p>
            @endif
          </div>
          @if(!empty($account['number']))
            <button type="button" class="gift-copy-{{ $section->id }} shrink-0 px-4 py-1.5 text-xs font-semibold"
                data-number="{{ $account['number'] }}"
                style="background: var(--color-accent, #b5654d); color: var(--color-surface, #ffffff); border-radius: var(--radius, 12px);">
              {{ $copyLabel }}
            </button>
          @endif
        </div>
      @endforeach
    </div>
    @if($giftAddress)
      <p class="mt-8 text-sm opacity-80">Kirim kado ke: <span class="font-medium">{{ $giftAddress }}</span></p>
    @endif
  </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.gift-copy-{{ $section->id }}').forEach(function (btn) {
    btn.addEventListener('click', function () {
      navigator.clipboard.writeText(btn.dataset.number).then(function () {
        const original = btn.textContent;
        btn.textContent = @js($copiedLabel);
        setTimeout(function () { btn.textContent = original; }, 1500);
      });
    });
  });
});
</script>
@endpush
