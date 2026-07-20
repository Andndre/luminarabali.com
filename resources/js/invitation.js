import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Latar YouTube: tampilkan iframe hanya setelah pemutarnya melapor sedang berjalan.
// Sebelum itu yang terlihat tombol play besar dan judul video — chrome yang tak bisa
// dimatikan lewat parameter. Statusnya didengar lewat postMessage ke iframe
// (enablejsapi=1), bukan dengan memuat skrip API YouTube: tidak ada berkas pihak ketiga
// tambahan, dan tidak ada tebakan berbasis lamanya waktu.
(function () {
    var ORIGINS = ['https://www.youtube-nocookie.com', 'https://www.youtube.com'];
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function frames() { return document.querySelectorAll('iframe.sec-bg-yt'); }

    function post(frame, msg) {
        try { frame.contentWindow.postMessage(JSON.stringify(msg), '*'); } catch (e) { /* belum siap */ }
    }

    function handshake() {
        frames().forEach(function (f) {
            post(f, { event: 'listening', id: 1, channel: 'widget' });
            // Hemat CPU dan data: yang minta gerakan dikurangi tidak melihatnya sama
            // sekali (CSS menyembunyikannya), jadi jangan biarkan tetap diputar.
            if (reduce) post(f, { event: 'command', func: 'pauseVideo', args: [] });
        });
    }

    window.addEventListener('message', function (e) {
        if (ORIGINS.indexOf(e.origin) === -1) return;
        var data;
        try { data = typeof e.data === 'string' ? JSON.parse(e.data) : e.data; } catch (err) { return; }
        if (!data || !data.info) return;

        frames().forEach(function (f) {
            if (f.contentWindow !== e.source) return;
            f.dataset.ytAlive = '1';
            // playerState 1 = sedang berjalan. Saat itu YouTube masih memajang kelompok
            // tombol di tengah — UI mode sentuh yang TIDAK dihormati controls=0 dan tidak
            // bisa dipotong zoom karena letaknya persis di tengah. Ia menghilang sendiri
            // sekitar 3 detik kemudian (diukur: masih ada di 3,6 detik sejak muat, bersih
            // di 4,8), jadi tirainya baru dibuka setelah itu. Yang terlihat selama jeda
            // ini foto poster; latar YouTube selalu punya satu (lihat _section-shell).
            if (data.info.playerState === 1 && !f.dataset.ytReveal) {
                f.dataset.ytReveal = '1';
                setTimeout(function () { f.classList.add('is-playing'); }, 4000);
            }
        });
    });

    // Iframe baru menerima pesan setelah dokumennya termuat, dan Studio menukar section
    // kapan saja — jadi salamnya diulang beberapa kali, bukan sekali di awal.
    var tries = 0;
    var timer = setInterval(function () { handshake(); if (++tries > 10) clearInterval(timer); }, 500);
    handshake();

    // Kalau saluran pesannya mati (diblokir ekstensi, versi browser aneh), statusnya tak
    // akan pernah sampai. Diam selamanya lebih buruk daripada chrome yang sekilas.
    setTimeout(function () {
        frames().forEach(function (f) { if (!f.dataset.ytAlive) f.classList.add('is-playing'); });
    }, 8000);
})();

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
