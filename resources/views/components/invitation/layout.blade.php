@props(['skipCover' => false, 'page' => null, 'coverImage' => null])
<style>
/* Global Reveal Animations Utility */
[data-reveal] {
    opacity: 0;
    transition: all 1s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Base states before reveal */
[data-reveal="up"] { transform: translateY(40px); }
[data-reveal="down"] { transform: translateY(-40px); }
[data-reveal="left"] { transform: translateX(40px); }
[data-reveal="right"] { transform: translateX(-40px); }
[data-reveal="fade"] { transform: scale(0.95); }
[data-reveal="zoom"] { transform: scale(0.8); }

/* Revealed state */
[data-reveal].is-visible {
    opacity: 1;
    transform: translate(0) scale(1);
}
</style>

<div x-data="{
        ...(typeof window.invitationData !== 'undefined' ? window.invitationData : {}),
        isOpen: {{ $skipCover ? 'true' : 'false' }},
        isPlaying: false,
        init() {
            // Setup Intersection Observer for global reveal animations
            let observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

            // Tunggu sedikit agar DOM siap
            setTimeout(() => {
                document.querySelectorAll('[data-reveal]').forEach((el) => {
                    observer.observe(el);
                });
            }, 100);
        },
        openInvitation() {
            this.isOpen = true;

            // Play Audio if ref exists
            let audio = this.$refs.bgAudio;
            if(audio) {
                audio.play().then(() => {
                    this.isPlaying = true;
                }).catch(err => {
                    console.error('Audio play failed:', err);
                    this.isPlaying = false;
                });
            }

            if (!audio) {
                var bg = document.querySelector('audio[id^="bg-music-"]');
                if (bg) { bg.play().catch(function () {}); }
            }
        },
        toggleAudio() {
            let audio = this.$refs.bgAudio;
            if(!audio) return;
            if(this.isPlaying) {
                audio.pause();
                this.isPlaying = false;
            } else {
                audio.play().then(() => {
                    this.isPlaying = true;
                }).catch(err => console.error('Audio play failed:', err));
            }
        }
    }"
    class="invite-shell {{ $attributes->get('class', '') }}">

    {{-- Pane kiri: hanya desktop, tampil di belakang gate --}}
    <aside class="invite-hero" aria-hidden="true"
        @if($coverImage) style="background-image: url('{{ $coverImage }}')" @endif>
        <div class="invite-hero-overlay"></div>
        <div class="invite-hero-text">
            <p class="invite-hero-kicker">The Wedding Of</p>
            <h2 class="invite-hero-names">{{ $page->groom_name ?? 'Romeo' }} &amp; {{ $page->bride_name ?? 'Juliet' }}</h2>
            @if($page?->event_date)
                <p class="invite-hero-date">{{ \Illuminate\Support\Str::ucfirst(\Carbon\Carbon::parse($page->event_date)->translatedFormat('d F Y')) }}</p>
            @endif
        </div>
    </aside>

    {{-- Kartu kanan: SATU-SATUNYA scroll container --}}
    <main class="invite-card" :class="{ 'is-locked': !isOpen }" x-ref="card">
        {{ $slot }}
    </main>

    {{-- Preloader (skip di studio) --}}
    @if (!$skipCover)
        <div class="invite-preloader" id="invite-preloader">
            <p class="invite-preloader-names">{{ $page->groom_name ?? '' }} &amp; {{ $page->bride_name ?? '' }}</p>
            <div class="invite-preloader-spinner"></div>
        </div>
        <script>
            (function () {
                var img = new Promise(function (resolve) {
                    @if($coverImage)
                    var i = new Image();
                    i.onload = i.onerror = resolve;
                    i.src = @json($coverImage);
                    @else
                    resolve();
                    @endif
                });
                var fonts = (document.fonts && document.fonts.ready) || Promise.resolve();
                var timeout = new Promise(function (r) { setTimeout(r, 2500); });
                Promise.race([Promise.all([img, fonts]), timeout]).then(function () {
                    var el = document.getElementById('invite-preloader');
                    if (!el) return;
                    el.classList.add('is-done');
                    setTimeout(function () { el.remove(); }, 600);
                });
            })();
        </script>
    @endif

    <!-- Global Lightbox -->
    <div x-data="{ lightboxOpen: false, lightboxImage: '' }"
         @open-lightbox.window="lightboxImage = $event.detail; lightboxOpen = true"
         x-show="lightboxOpen"
         style="display: none;"
         x-transition.opacity.duration.300ms
         class="fixed inset-0 z-[100] flex items-center justify-center bg-[#2C1E16]/95 backdrop-blur-md p-4"
         @keydown.escape.window="lightboxOpen = false">

        <button @click="lightboxOpen = false" class="absolute top-6 right-6 text-white/50 hover:text-white transition bg-black/20 p-2 rounded-full backdrop-blur-sm z-50">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <img :src="lightboxImage" @click.away="lightboxOpen = false" class="max-w-full max-h-[90vh] object-contain rounded-sm shadow-2xl border-4 border-white/10" x-transition.scale.origin.center>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    // Custom Directive for Lightbox
    Alpine.directive('lightbox', (el) => {
        el.style.cursor = 'zoom-in';

        el.addEventListener('click', (event) => {
            window.dispatchEvent(new CustomEvent('open-lightbox', { detail: el.src }));
        });
    });

    Alpine.data('countdown', (targetDate) => ({
        days: '00', hours: '00', minutes: '00', seconds: '00',
        init() {
            let countDownDate;
            
            // Parse simpler format: "HH:mm DD-MM-YYYY" (e.g. "12:00 24-07-2026" or "12:00 24-7-2026")
            if (typeof targetDate === 'string' && targetDate.includes(' ') && targetDate.includes(':') && targetDate.includes('-')) {
                const parts = targetDate.split(' ');
                if (parts.length === 2) {
                    const [time, date] = parts;
                    const [hours, minutes] = time.split(':');
                    const [day, month, year] = date.split('-');
                    
                    // Reformat to ISO for safe Date parsing across browsers
                    const isoString = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}T${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}:00`;
                    countDownDate = new Date(isoString).getTime();
                }
            }
            
            // Fallback to standard Date parser
            if (!countDownDate) {
                countDownDate = new Date(targetDate).getTime();
            }

            setInterval(() => {
                const now = new Date().getTime();
                const distance = countDownDate - now;
                if (distance < 0) return;
                this.days = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
                this.hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                this.minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                this.seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
            }, 1000);
        }
    }));

    Alpine.data('rsvpForm', () => ({
        formData: { guest_name: '', status: 'Hadir', comments: '' },
        isSubmitting: false,
        isSuccess: false,
        errorMessage: '',
        async submitRsvp() {
            this.isSubmitting = true;
            this.errorMessage = '';
            try {
                const metaCsrf = document.querySelector('meta[name=\'csrf-token\']');
                const token = metaCsrf ? metaCsrf.getAttribute('content') : '';
                const response = await fetch(window.location.pathname + '/rsvp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                });
                
                if (response.ok) {
                    this.isSuccess = true;
                    this.formData = { guest_name: '', status: 'Hadir', comments: '' };
                } else {
                    const data = await response.json();
                    this.errorMessage = data.message || 'Terjadi kesalahan saat mengirim RSVP.';
                }
            } catch (err) {
                this.errorMessage = 'Gagal terhubung ke server.';
            } finally {
                this.isSubmitting = false;
            }
        }
    }));
});
</script>
