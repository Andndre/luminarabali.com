import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Video latar itu gerak terus-menerus. CSS tidak bisa menghentikannya, jadi di sini:
// yang minta gerakan dikurangi dapat frame pertama saja (poster tetap terlihat).
(function () {
    if (!window.matchMedia || !window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    function pauseAll() {
        document.querySelectorAll('video.sec-bg-video').forEach(function (v) {
            v.autoplay = false;
            v.pause();
        });
    }
    pauseAll();
    document.addEventListener('DOMContentLoaded', pauseAll);
})();

// Efek latar scroll-linked (zoom & pinned) — semua relatif kartu .invite-card,
// karena kartu itulah scroll container-nya, bukan window.
(function () {
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    function initScrollZoom() {
        function update() {
            // Query ulang tiap frame: Studio menukar fragment section, jadi daftar yang
            // di-cache saat load menunjuk ke node yang sudah dibuang (efek diam).
            var card = document.querySelector('.invite-card');
            if (!card) return;
            var vh = card.clientHeight || window.innerHeight;
            var cardTop = card.getBoundingClientRect().top;

            // pinned: media dipaksa menempati kotak layar kartu, apa pun posisi section-nya.
            // Pembungkus YouTube ikut, tapi memakai --pin-h untuk menurunkan LEBAR bingkai
            // 16:9-nya, bukan sebagai tinggi langsung (lihat .sec-treat--pinned .sec-bg-ytwrap).
            document.querySelectorAll('.sec-treat--pinned .sec-bg-img, .sec-treat--pinned .sec-bg-video, .sec-treat--pinned .sec-bg-ytwrap').forEach(function (img) {
                var offset = img.parentElement.getBoundingClientRect().top - cardTop;
                img.style.setProperty('--pin-h', vh + 'px');
                img.style.setProperty('--pin-y', (-offset).toFixed(1) + 'px');
            });

            var layers = document.querySelectorAll('.sec-bg[data-effect^="scroll-zoom"]');
            layers.forEach(function (layer) {
                // Slideshow punya banyak slide; --sz-scale cukup diset di wadahnya sekali,
                // dan tiap slide mewarisinya lewat var().
                var img = layer.querySelector('.sec-bg-video, .sec-bg-ytwrap, .sec-bg-img');
                if (!img) return;
                if (layer.classList.contains('sec-bg--slideshow')) img = layer;
                var dir = layer.getAttribute('data-effect') === 'scroll-zoom-in' ? 'in' : 'out';
                var max = (parseInt(layer.getAttribute('data-strength'), 10) || 130) / 100;
                var rect = layer.getBoundingClientRect();
                // progres 0..1 saat layer melintasi viewport kartu
                var p = 1 - Math.min(1, Math.max(0, (rect.top + rect.height) / (vh + rect.height)));
                var scale = dir === 'in' ? (1 + (max - 1) * p) : (max - (max - 1) * p);
                img.style.setProperty('--sz-scale', scale.toFixed(3));
            });

            document.querySelectorAll('[data-reveal]:not(.is-in)').forEach(function (el) {
                if (el.getBoundingClientRect().top < vh * 0.92) el.classList.add('is-in');
            });
        }

        var ticking = false;
        function onScroll() {
            if (!ticking) { ticking = true; requestAnimationFrame(function () { update(); ticking = false; }); }
        }
        // Capture di document: scroll tidak menggelembung, dan .invite-card bisa diganti
        // oleh re-render Studio — listener yang dipasang ke node lama ikut hilang.
        document.addEventListener('scroll', onScroll, { passive: true, capture: true });
        window.addEventListener('resize', onScroll);
        update();
    }

    if (document.readyState !== 'loading') initScrollZoom();
    else document.addEventListener('DOMContentLoaded', initScrollZoom);
})();
