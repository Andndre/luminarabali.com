<script>
(() => {
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Undangan memakai `.invite-card` sebagai satu-satunya scroll container.
    // Isinya bergulir DI DALAM iframe, bukan bersama <body>-nya, jadi menggeser
    // elemen iframe (animasi CSS .is-autoscroll) hanya menyingkap ruang kosong.
    // Kalau bisa dijangkau (same-origin), gulirkan container itu langsung;
    // kalau tidak, jatuh kembali ke animasi CSS lama.
    const autoscroll = (iframe) => {
        let card = null;
        try { card = iframe.contentDocument.querySelector('.invite-card'); } catch (e) { /* cross-origin */ }

        const span = card ? card.scrollHeight - card.clientHeight : 0;
        if (span < 80) { iframe.classList.add('is-autoscroll'); return; }

        // Gulir sampai akhir undangan, lalu balik arah. Selalu MULAI dari 0
        // (cover) supaya kesan pertama tetap sampul.
        const reach = span;

        // Kecepatan konstan (px/detik), bukan durasi tetap: undangan panjang
        // dan pendek jadi terasa sama lajunya. Dikunci minimal 8 detik supaya
        // undangan pendek tak berkedip.
        const SPEED = 110;
        const leg = Math.max(8000, (reach / SPEED) * 1000);
        const t0 = performance.now();
        card.scrollTop = 0;
        const tick = (now) => {
            if (!card.isConnected) return;
            const phase = ((now - t0) % (leg * 2)) / leg;
            const p = phase <= 1 ? phase : 2 - phase;   // bolak-balik 0→1→0
            card.scrollTop = reach * (p * p * (3 - 2 * p)); // smoothstep
            requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    };

    // Lazy-mount iframe preview saat frame masuk viewport. Unobserve setelah
    // mount supaya tak pernah dibuat dua kali.
    const frameIo = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (!e.isIntersecting) return;
            const el = e.target;
            frameIo.unobserve(el);
            const iframe = document.createElement('iframe');
            iframe.src = el.dataset.src;
            iframe.loading = 'lazy';
            iframe.tabIndex = -1;
            iframe.title = 'Preview undangan';
            iframe.className = 'catalog-liveframe__iframe';
            if (el.dataset.autoscroll && !reduce) {
                iframe.addEventListener('load', () => autoscroll(iframe));
            }
            el.querySelector('.catalog-liveframe__device').appendChild(iframe);
        });
    }, { rootMargin: '200px' });
    document.querySelectorAll('[data-liveframe]').forEach((el) => frameIo.observe(el));

    const revealIo = new IntersectionObserver((entries) => {
        entries.forEach((e) => e.isIntersecting && e.target.classList.add('is-in'));
    }, { threshold: .15 });
    document.querySelectorAll('.catalog-reveal').forEach((el) => revealIo.observe(el));
})();
</script>
