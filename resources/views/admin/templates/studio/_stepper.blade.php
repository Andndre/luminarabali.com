{{-- Input angka + tombol naik/turun kustom (ganti spinner native yang sempit).
     Pemanggil kirim: $bind (ekspresi Alpine untuk :value), $change (ekspresi @change),
     dan opsional $min $max $step. nudge() di studioApp yang menaik-turunkan nilainya. --}}
<div class="stepper {{ $class ?? 'mt-1' }}">
    <input type="number"
        @isset($min) min="{{ $min }}" @endisset
        @isset($max) max="{{ $max }}" @endisset
        step="{{ $step ?? 'any' }}"
        :value="{!! $bind !!}"
        @change="{!! $change !!}">
    <div class="stepper-btns">
        <button type="button" tabindex="-1" aria-label="Naik" @click="nudge($event, 1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 15l6-6 6 6"/></svg>
        </button>
        <button type="button" tabindex="-1" aria-label="Turun" @click="nudge($event, -1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/></svg>
        </button>
    </div>
</div>
