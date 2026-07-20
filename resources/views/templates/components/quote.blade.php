@props(['props' => [], 'section' => null, 'page' => null])

@php
    $content = $props['content'] ?? '';
    $source = $props['attribution'] ?? '';
    $variant = $props['variant'] ?? 'plain';
@endphp

<section class="quote quote--{{ $variant }}" style="padding: var(--section-y, 64px) 16px;">
  <div class="container mx-auto quote-body">
    {{-- source-first membalik urutan: rujukan dibaca lebih dulu, jadi ia juga
         harus lebih dulu di DOM — bukan cuma dipindah lewat CSS order. --}}
    @if($variant === 'source-first' && $source)
      <p class="quote-source">{{ $source }}</p>
    @endif

    <p class="quote-text" data-editable="content">{{ $content }}</p>

    @if($variant !== 'source-first' && $source)
      <p class="quote-source">{{ $source }}</p>
    @endif
  </div>
</section>
