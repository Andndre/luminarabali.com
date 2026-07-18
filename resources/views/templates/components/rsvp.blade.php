@props(['props' => [], 'section' => null, 'page' => null])

@php
$variant = $props['variant'] ?? 'elevated';
$title = $props['title'] ?? 'RSVP';
$subtitle = $props['subtitle'] ?? 'Please confirm your attendance';
$buttonText = $props['button_text'] ?? 'Kirim Konfirmasi';
$successMessage = $props['success_message'] ?? 'Terima kasih atas konfirmasi Anda!';
$whatsappEnabled = $props['whatsapp_enabled'] ?? false;
$whatsappPhone = $props['whatsapp_phone'] ?? '';
$paddingTop = $props['padding_top'] ?? 80;
$paddingBottom = $props['padding_bottom'] ?? 80;
@endphp

<section class="rsvp rsvp--{{ $variant }}" style="padding: {{ $paddingTop }}px {{ $variant === 'elevated' ? 16 : 20 }}px {{ $paddingBottom }}px;">
  <div class="container mx-auto">
    <div class="rsvp-inner">
      <div class="rsvp-head">
        @if($title)
          <h2 class="rsvp-heading" style="font-family: var(--font-heading, serif);">{{ $title }}</h2>
        @endif
        @if($subtitle)
          <p class="rsvp-subtitle">{{ $subtitle }}</p>
        @endif
      </div>

      <div class="rsvp-card-outer">
        <div class="rsvp-card">
          <form id="rsvp-form-{{ $section->id }}">
            <div class="f-row">
              <label class="rsvp-label">Nama Lengkap *</label>
              <input type="text" name="guest_name" required class="rsvp-field">
            </div>

            <div class="f-row">
              <label class="rsvp-label">No. WhatsApp</label>
              <input type="tel" name="guest_phone" class="rsvp-field">
            </div>

            <div class="f-row">
              <label class="rsvp-label">Email</label>
              <input type="email" name="guest_email" class="rsvp-field">
            </div>

            <div class="f-row">
              <label class="rsvp-label">Konfirmasi Kehadiran *</label>
              @if($variant === 'custom-controls')
                <div class="rsvp-segmented">
                  <input type="radio" name="attendance_status" value="hadir" id="att-h-{{ $section->id }}" required>
                  <label for="att-h-{{ $section->id }}">Hadir</label>
                  <input type="radio" name="attendance_status" value="tidak_hadir" id="att-n-{{ $section->id }}">
                  <label for="att-n-{{ $section->id }}">Tidak Hadir</label>
                  <input type="radio" name="attendance_status" value="ragu" id="att-r-{{ $section->id }}">
                  <label for="att-r-{{ $section->id }}">Ragu</label>
                </div>
              @else
                <select name="attendance_status" required class="rsvp-field">
                  <option value="">Pilih Status</option>
                  <option value="hadir">Hadir</option>
                  <option value="tidak_hadir">Tidak Hadir</option>
                  <option value="ragu">Masih Ragu</option>
                </select>
              @endif
            </div>

            <div class="f-row">
              <label class="rsvp-label">Jumlah Tamu *</label>
              @if($variant === 'custom-controls')
                <div class="rsvp-stepper" x-data="{ n: 1 }">
                  <button type="button" @click="n = Math.max(1, n - 1)" aria-label="Kurangi tamu">&minus;</button>
                  <span x-text="n"></span>
                  <button type="button" @click="n++" aria-label="Tambah tamu">+</button>
                  <input type="hidden" name="number_of_guests" :value="n">
                </div>
              @else
                <input type="number" name="number_of_guests" min="1" value="1" required class="rsvp-field">
              @endif
            </div>

            <div class="f-row">
              <label class="rsvp-label">Pesan</label>
              <textarea name="message" rows="3" class="rsvp-field"></textarea>
            </div>

            <button type="submit" class="rsvp-button">{{ $buttonText }}</button>
          </form>

          <div id="rsvp-success-{{ $section->id }}" class="rsvp-success hidden">
            {{ $successMessage }}
          </div>
        </div>
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
