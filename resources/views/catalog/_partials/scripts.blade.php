<script>
(() => {
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

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
            iframe.className = 'catalog-liveframe__iframe'
                + (el.dataset.autoscroll && !reduce ? ' is-autoscroll' : '');
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
