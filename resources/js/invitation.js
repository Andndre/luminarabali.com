import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Scroll-linked zoom untuk .sec-bg[data-effect^="scroll-zoom"] — relatif kartu .invite-card.
(function () {
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    function initScrollZoom() {
        var card = document.querySelector('.invite-card');
        var layers = Array.prototype.slice.call(document.querySelectorAll('.sec-bg[data-effect^="scroll-zoom"]'));
        if (!card || !layers.length) return;

        function update() {
            var vh = card.clientHeight || window.innerHeight;
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
        card.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll);
        update();
    }

    if (document.readyState !== 'loading') initScrollZoom();
    else document.addEventListener('DOMContentLoaded', initScrollZoom);
})();
