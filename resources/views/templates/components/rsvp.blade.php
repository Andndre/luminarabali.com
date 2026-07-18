@props(['props' => [], 'section' => null, 'page' => null])

@php
$title = $props['title'] ?? 'RSVP';
$subtitle = $props['subtitle'] ?? 'Please confirm your attendance';
$buttonText = $props['button_text'] ?? 'Kirim Konfirmasi';
$buttonColor = $props['button_color'] ?? 'var(--color-accent, #b5654d)';
$successMessage = $props['success_message'] ?? 'Terima kasih atas konfirmasi Anda!';
$whatsappEnabled = $props['whatsapp_enabled'] ?? false;
$whatsappPhone = $props['whatsapp_phone'] ?? '';
$titleColor = $props['title_color'] ?? 'var(--color-primary, #111827)';
$subtitleColor = $props['subtitle_color'] ?? 'var(--color-text, #4b5563)';
$paddingTop = $props['padding_top'] ?? 80;
$paddingBottom = $props['padding_bottom'] ?? 80;
@endphp

<style>
  .rsvp-section-{{ $section->id }} {
    padding-top: {{ $paddingTop }}px;
    padding-bottom: {{ $paddingBottom }}px;
  }

  .rsvp-section-{{ $section->id }} .rsvp-button {
    background: {{ $buttonColor }};
    color: #ffffff;
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
          <h2 class="text-3xl font-bold mb-2" style="font-family: var(--font-heading, serif); color: {{ $titleColor }};">{{ $title }}</h2>
        @endif
        @if($subtitle)
          <p style="color: {{ $subtitleColor }};">{{ $subtitle }}</p>
        @endif
      </div>

      <form id="rsvp-form-{{ $section->id }}" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1" style="color: var(--color-text, #2b2b2b);">Nama Lengkap *</label>
          <input type="text" name="guest_name" required
                 class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1" style="color: var(--color-text, #2b2b2b);">No. WhatsApp</label>
          <input type="tel" name="guest_phone"
                 class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1" style="color: var(--color-text, #2b2b2b);">Email</label>
          <input type="email" name="guest_email"
                 class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1" style="color: var(--color-text, #2b2b2b);">Konfirmasi Kehadiran *</label>
          <select name="attendance_status" required
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
            <option value="">Pilih Status</option>
            <option value="hadir">Hadir</option>
            <option value="tidak_hadir">Tidak Hadir</option>
            <option value="ragu">Masih Ragu</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1" style="color: var(--color-text, #2b2b2b);">Jumlah Tamu *</label>
          <input type="number" name="number_of_guests" min="1" value="1" required
                 class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1" style="color: var(--color-text, #2b2b2b);">Pesan</label>
          <textarea name="message" rows="3"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"></textarea>
        </div>

        <button type="submit" class="rsvp-button w-full py-3 rounded-lg font-semibold transition">
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
