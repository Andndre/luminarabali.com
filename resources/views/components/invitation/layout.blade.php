@props(['skipCover' => false])
<div x-data="{ 
        isOpen: {{ $skipCover ? 'true' : 'false' }}, 
        isPlaying: false,
        init() {
            if (!this.isOpen) {
                document.body.classList.add('no-scroll');
            }
            
            // Setup Intersection Observer for reveal animations
            let observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
            
            // Tunggu sedikit agar DOM siap
            setTimeout(() => {
                document.querySelectorAll('.reveal-on-scroll').forEach((el) => {
                    observer.observe(el);
                });
            }, 100);
        },
        openInvitation() { 
            this.isOpen = true; 
            document.body.classList.remove('no-scroll');
            
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
    class="relative min-h-screen {{ $attributes->get('class', 'font-light selection:bg-[#C5A059] selection:text-white') }}"
>
    {{ $slot }}
</div>
