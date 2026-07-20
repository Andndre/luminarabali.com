@props(['props' => [], 'section' => null, 'page' => null])

@php
    $heading = $props['heading'] ?? 'Amplop Digital';
    $message = $props['message'] ?? '';
    $accounts = $props['accounts'] ?? [];
    $giftAddress = $props['gift_address'] ?? '';
    $copyLabel = $props['copy_label'] ?? 'Salin';
    $copiedLabel = $props['copied_label'] ?? 'Tersalin!';
    $addressLabel = $props['address_label'] ?? 'Kirim kado ke';
    $variant = $props['variant'] ?? 'bordered-cards';
@endphp

{{-- Alpine, bukan @push('scripts'): <script> hasil innerHTML (preview Studio) tak dieksekusi. --}}
<section class="gift gift--{{ $variant }}" style="padding: var(--section-y, 64px) 16px;"
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
    <h2 class="section-heading">{{ $heading }}</h2>
    @if($message)
      <p class="section-subheading">{{ $message }}</p>
    @endif

    <div class="gift-list">
      @foreach($accounts as $i => $account)
        <div class="gift-account">
          <div class="gift-account-info">
            @if(!empty($account['bank']))<p class="gift-bank">{{ $account['bank'] }}</p>@endif
            <p class="gift-number">{{ $account['number'] ?? '' }}</p>
            @if(!empty($account['holder']))<p class="gift-holder">a.n. {{ $account['holder'] }}</p>@endif
          </div>
          @if(!empty($account['number']))
            <button type="button" class="gift-copy" @click="copy(@js($account['number']), {{ $i }})">
              <span x-text="copied === {{ $i }} ? @js($copiedLabel) : @js($copyLabel)">{{ $copyLabel }}</span>
            </button>
          @endif
        </div>
      @endforeach
    </div>

    @if($giftAddress)
      <p class="gift-address">{{ $addressLabel }}: <span class="gift-address-value">{{ $giftAddress }}</span></p>
    @endif
  </div>
</section>
