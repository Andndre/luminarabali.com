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
$nameLabel = $props['name_label'] ?? 'Nama Lengkap';
$phoneLabel = $props['phone_label'] ?? 'No. WhatsApp';
$emailLabel = $props['email_label'] ?? 'Email';
$attendanceLabel = $props['attendance_label'] ?? 'Konfirmasi Kehadiran';
$attendYes = $props['attend_yes_label'] ?? 'Hadir';
$attendNo = $props['attend_no_label'] ?? 'Tidak Hadir';
$attendMaybe = $props['attend_maybe_label'] ?? 'Masih Ragu';
$guestsLabel = $props['guests_label'] ?? 'Jumlah Tamu';
$messageLabel = $props['message_label'] ?? 'Pesan';
@endphp

{{-- Alpine, bukan @push('scripts'): <script> hasil innerHTML (preview Studio) tak dieksekusi. --}}
<section class="rsvp rsvp--{{ $variant }}" style="padding: {{ $paddingTop }}px {{ $variant === 'elevated' ? 16 : 20 }}px {{ $paddingBottom }}px;"
  x-data="{
    sending: false,
    sent: false,
    error: '',
    async submit(e) {
      if (this.sending) return; // klik ganda = dua baris RSVP
      this.sending = true;
      this.error = '';
      const data = Object.fromEntries(new FormData(e.target).entries());
      try {
        const res = await fetch('/invitation/' + @js($page->slug ?? '') + '/rsvp', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]')?.content ?? '',
          },
          body: JSON.stringify(data),
        });
        if (!res.ok) {
          const body = await res.json().catch(() => ({}));
          throw new Error(body.message || 'Terjadi kesalahan. Silakan coba lagi.');
        }
        this.sent = true;
@if($whatsappEnabled && $whatsappPhone)
        // encodeURIComponent, bukan %0A manual: nama/pesan tamu bisa memuat & atau #
        // yang tanpa encoding memotong query string WhatsApp.
        const teks = `RSVP dari ${data.guest_name}\n` +
          `Status: ${data.attendance_status}\n` +
          `Jumlah: ${data.number_of_guests}\n` +
          `Pesan: ${data.message || '-'}`;
        window.open('https://wa.me/' + @js($whatsappPhone) + '?text=' + encodeURIComponent(teks), '_blank');
@endif
      } catch (err) {
        this.error = err.message;
      } finally {
        this.sending = false;
      }
    },
  }">
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
          <form @submit.prevent="submit($event)" x-show="!sent">
            <div class="f-row">
              <label class="rsvp-label">{{ $nameLabel }} *</label>
              <input type="text" name="guest_name" required class="rsvp-field">
            </div>

            <div class="f-row">
              <label class="rsvp-label">{{ $phoneLabel }}</label>
              <input type="tel" name="guest_phone" class="rsvp-field">
            </div>

            <div class="f-row">
              <label class="rsvp-label">{{ $emailLabel }}</label>
              <input type="email" name="guest_email" class="rsvp-field">
            </div>

            <div class="f-row">
              <label class="rsvp-label">{{ $attendanceLabel }} *</label>
              @if($variant === 'custom-controls')
                <div class="rsvp-segmented">
                  <input type="radio" name="attendance_status" value="hadir" id="att-h-{{ $section->id }}" required>
                  <label for="att-h-{{ $section->id }}">{{ $attendYes }}</label>
                  <input type="radio" name="attendance_status" value="tidak_hadir" id="att-n-{{ $section->id }}">
                  <label for="att-n-{{ $section->id }}">{{ $attendNo }}</label>
                  <input type="radio" name="attendance_status" value="ragu" id="att-r-{{ $section->id }}">
                  <label for="att-r-{{ $section->id }}">{{ $attendMaybe }}</label>
                </div>
              @else
                <select name="attendance_status" required class="rsvp-field">
                  <option value="">Pilih Status</option>
                  <option value="hadir">{{ $attendYes }}</option>
                  <option value="tidak_hadir">{{ $attendNo }}</option>
                  <option value="ragu">{{ $attendMaybe }}</option>
                </select>
              @endif
            </div>

            <div class="f-row">
              <label class="rsvp-label">{{ $guestsLabel }} *</label>
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
              <label class="rsvp-label">{{ $messageLabel }}</label>
              <textarea name="message" rows="3" class="rsvp-field"></textarea>
            </div>

            <p class="rsvp-error" x-show="error" x-cloak x-text="error"></p>

            <button type="submit" class="rsvp-button" :disabled="sending"
                    x-text="sending ? 'Mengirim…' : @js($buttonText)">{{ $buttonText }}</button>
          </form>

          <div class="rsvp-success" x-show="sent" x-cloak>
            {{ $successMessage }}
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

