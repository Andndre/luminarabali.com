@props(['props' => [], 'section' => null, 'page' => null])

@php
    $heading = $props['heading'] ?? 'Amplop Digital';
    $message = $props['message'] ?? '';
    $accounts = $props['accounts'] ?? [];
    $giftAddress = $props['gift_address'] ?? '';
    $copyLabel = $props['copy_label'] ?? 'Salin';
    $copiedLabel = $props['copied_label'] ?? 'Tersalin!';
@endphp

{{-- Alpine, bukan @push('scripts'): <script> hasil innerHTML (preview Studio) tak dieksekusi. --}}
<section style="padding: var(--section-y, 64px) 16px;"
    x-data="{
      copied: null,
      async copy(text, i) {
        try {
          // navigator.clipboard hanya ada di secure context (HTTPS/localhost).
          // Di http:// nilainya undefined — memanggilnya langsung melempar TypeError.
          if (navigator.clipboard) {
            await navigator.clipboard.writeText(text);
          } else {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.cssText = 'position:fixed;opacity:0';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            ta.remove();
          }
          this.copied = i;
          setTimeout(() => { if (this.copied === i) this.copied = null; }, 1500);
        } catch {
          this.copied = null;
        }
      },
    }">
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
            <p class="font-mono font-semibold truncate">{{ $account['number'] ?? '' }}</p>
            @if(!empty($account['holder']))
              <p class="text-sm opacity-80">a.n. {{ $account['holder'] }}</p>
            @endif
          </div>
          @if(!empty($account['number']))
            <button type="button" class="shrink-0 px-4 py-1.5 text-xs font-semibold transition"
                @click="copy(@js($account['number']), {{ $i }})"
                style="background: var(--color-accent, #b5654d); color: var(--color-surface, #ffffff); border-radius: var(--radius, 12px);">
              <span x-text="copied === {{ $i }} ? @js($copiedLabel) : @js($copyLabel)">{{ $copyLabel }}</span>
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
