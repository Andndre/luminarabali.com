import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

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

            // pinned: gambar dipaksa menempati kotak layar kartu, apa pun posisi section-nya.
            document.querySelectorAll('.sec-treat--pinned .sec-bg-img').forEach(function (img) {
                var offset = img.parentElement.getBoundingClientRect().top - cardTop;
                img.style.setProperty('--pin-h', vh + 'px');
                img.style.setProperty('--pin-y', (-offset).toFixed(1) + 'px');
            });

            var layers = document.querySelectorAll('.sec-bg[data-effect^="scroll-zoom"]');
            layers.forEach(function (layer) {
                var img = layer.querySelector('.sec-bg-img');
                if (!img) return;
                var dir = layer.getAttribute('data-effect') === 'scroll-zoom-in' ? 'in' : 'out';
                var max = (parseInt(layer.getAttribute('data-strength'), 10) || 130) / 100;
                var rect = layer.getBoundingClientRect();
                // progres 0..1 saat layer melintasi viewport kartu
                var p = 1 - Math.min(1, Math.max(0, (rect.top + rect.height) / (vh + rect.height)));
                var scale = dir === 'in' ? (1 + (max - 1) * p) : (max - (max - 1) * p);
                img.style.setProperty('--sz-scale', scale.toFixed(3));
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
