@props(['props' => [], 'section' => null, 'page' => null])

@php
$title = $props['title'] ?? 'RSVP';
$subtitle = $props['subtitle'] ?? 'Please confirm your attendance';
$buttonText = $props['button_text'] ?? 'Kirim Konfirmasi';
$successMessage = $props['success_message'] ?? 'Terima kasih atas konfirmasi Anda!';
$whatsappEnabled = $props['whatsapp_enabled'] ?? false;
$whatsappPhone = $props['whatsapp_phone'] ?? '';
$paddingTop = $props['padding_top'] ?? 80;
$paddingBottom = $props['padding_bottom'] ?? 80;
@endphp

<style>
  .rsvp-section-{{ $section->id }} {
    padding-top: {{ $paddingTop }}px;
    padding-bottom: {{ $paddingBottom }}px;
  }

  /* Field digayakan token, bukan utility: ring kuning bawaan tidak nyambung dengan
     tema mana pun, dan radius harus ikut --radius seperti komponen lain. */
  .rsvp-section-{{ $section->id }} .rsvp-field {
    width: 100%;
    padding: .7rem .9rem;
    font: inherit;
    color: var(--color-text, #2b2b2b);
    background: var(--color-surface, #ffffff);
    border: 1px solid color-mix(in srgb, var(--color-text, #2b2b2b) 18%, transparent);
    border-radius: var(--radius, 12px);
    transition: border-color .15s, box-shadow .15s;
  }

  .rsvp-section-{{ $section->id }} .rsvp-field:focus {
    outline: none;
    border-color: var(--color-accent, #b5654d);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-accent, #b5654d) 25%, transparent);
  }

  .rsvp-section-{{ $section->id }} .rsvp-button {
    background: var(--color-accent, #b5654d);
    color: var(--color-surface, #ffffff);
    border-radius: var(--radius, 12px);
  }

  .rsvp-section-{{ $section->id }} .rsvp-button:hover {
    filter: brightness(0.9);
  }
</style>

<section class="rsvp-section-{{ $section->id }}">
  <div class="container mx-auto px-4">
    <div class="max-w-md mx-auto">
      <div class="text-center mb-8">
        @if($title)
          <h2 class="text-3xl font-bold mb-2" style="font-family: var(--font-heading, serif);">{{ $title }}</h2>
        @endif
        @if($subtitle)
          <p class="opacity-80">{{ $subtitle }}</p>
        @endif
      </div>

      <form id="rsvp-form-{{ $section->id }}" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Nama Lengkap *</label>
          <input type="text" name="guest_name" required
                 class="rsvp-field">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">No. WhatsApp</label>
          <input type="tel" name="guest_phone"
                 class="rsvp-field">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Email</label>
          <input type="email" name="guest_email"
                 class="rsvp-field">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Konfirmasi Kehadiran *</label>
          <select name="attendance_status" required
                  class="rsvp-field">
            <option value="">Pilih Status</option>
            <option value="hadir">Hadir</option>
            <option value="tidak_hadir">Tidak Hadir</option>
            <option value="ragu">Masih Ragu</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Jumlah Tamu *</label>
          <input type="number" name="number_of_guests" min="1" value="1" required
                 class="rsvp-field">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Pesan</label>
          <textarea name="message" rows="3"
                    class="rsvp-field"></textarea>
        </div>

        <button type="submit" class="rsvp-button w-full py-3 font-semibold transition">
          {{ $buttonText }}
        </button>
      </form>

      <div id="rsvp-success-{{ $section->id }}" class="hidden mt-4 p-4 bg-green-100 text-green-700 rounded-lg text-center">
        {{ $successMessage }}
      </div>
    </div>
  </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('rsvp-form-{{ $section->id }}');
  const successDiv = document.getElementById('rsvp-success-{{ $section->id }}');
  const sectionId = '{{ $section->id }}';
  const pageSlug = '{{ $page->slug ?? '' }}';

  form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
      const response = await fetch(`/invitation/${pageSlug}/rsvp`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
      });

      if (response.ok) {
        form.classList.add('hidden');
        successDiv.classList.remove('hidden');

        @if($whatsappEnabled && $whatsappPhone)
          // Forward to WhatsApp
          const whatsappPhone = {!! \Illuminate\Support\Js::from($whatsappPhone) !!};
          const whatsappMessage = `RSVP dari ${data.guest_name}%0AStatus: ${data.attendance_status}%0AJumlah: ${data.number_of_guests}%0APesan: ${data.message || '-'}`;
          window.open(`https://wa.me/${whatsappPhone}?text=${whatsappMessage}`, '_blank');
        @endif
      } else {
        alert('Terjadi kesalahan. Silakan coba lagi.');
      }
    } catch (error) {
      console.error('RSVP error:', error);
      alert('Terjadi kesalahan. Silakan coba lagi.');
    }
  });
});
</script>
@endpush
