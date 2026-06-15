/**
 * Modul Inisialisasi & Event Binder (init.js)
 * 
 * Modul ini menangani pengaturan awal (bootstrapping) elemen-elemen di Kanvas Visual.
 * Meliputi konversi otomatis breakpoint responsive Tailwind ke Container Queries (@md:),
 * konfigurasi SortableJS untuk penyusunan ulang (drag-and-drop) komponen,
 * pemrosesan komponen baru yang ditambahkan ke kanvas (mengambil source code HTML dari API laravel),
 * serta pemasangan contenteditable pada elemen teks dan double-click handler pada media/background.
 */
export default function EditorInit() {
    return {
        /**
         * Fungsi Bootstrapping Utama.
         * Dijalankan sekali saat editor pertama kali dimuat.
         */
        setupInit() {
            const canvas = document.getElementById('visual-canvas');
            if (canvas) {
                // KONVERSI BREAKPOINT KE CONTAINER QUERIES:
                // Menjelajahi seluruh elemen di kanvas visual dan mengganti kelas breakpoint standar (seperti md:, lg:)
                // dengan container queries (seperti @md:, @lg:) agar pratinjau responsif di dalam iframe/kontainer
                // berukuran maksimal 480px berjalan dengan sempurna sesuai ukuran layar perangkat simulasi.
                const els = canvas.querySelectorAll('*');
                els.forEach(el => {
                    if (el.className && typeof el.className === 'string') {
                        el.className = el.className.replace(/\b(sm|md|lg|xl|2xl):/g, '@$1:');
                    }
                });

                // KONFIGURASI DRAG & DROP UTAMA (SortableJS):
                // Mengatur agar kanvas visual dapat menerima drag komponen dari pustaka eksternal (panel kiri)
                // dan mengizinkan penyusunan ulang urutan section di dalam kanvas itu sendiri.
                new Sortable(canvas, {
                    group: 'shared',
                    animation: 150,
                    ghostClass: 'bg-blue-50', // Kelas styling bayangan saat elemen sedang dipindah
                    
                    // Callback saat elemen dipindah posisinya (drag-and-drop selesai)
                    onEnd: function(evt) {
                        window.syncToMonaco();
                    },
                    
                    // Callback saat komponen baru dilempar (drop) ke kanvas dari panel pustaka komponen
                    onAdd: async function(evt) {
                        if (evt.item.classList.contains('library-item')) {
                            const id = evt.item.dataset.id;
                            
                            // 1. Bersihkan elemen pembantu drag secara sinkron dengan menampilkan spinner loading
                            //    untuk mencegah AlpineJS mengevaluasi konten mentah sebelum data dari server siap.
                            evt.item.innerHTML = '<div class="flex flex-col items-center justify-center gap-2 rounded border border-blue-200 bg-blue-50 p-6 text-sm text-blue-600"><svg class="h-6 w-6 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memuat komponen...</div>';
                            
                            // Hapus atribut direktif AlpineJS yang menyamar agar tidak memicu error inisialisasi ganda
                            const attrs = Array.from(evt.item.attributes);
                            attrs.forEach(attr => {
                                if (attr.name.startsWith('x-') || attr.name.startsWith('@') || attr.name.startsWith(':')) {
                                    evt.item.removeAttribute(attr.name);
                                }
                            });

                            try {
                                // 2. Ambil source code HTML asli komponen dari API Backend Laravel
                                const response = await fetch(`/admin/api/component-library/${id}`);
                                const component = await response.json();
                                
                                let code = component.code;
                                // Ganti placeholder item dengan HTML utuh komponen asli
                                evt.item.outerHTML = code;
                                
                                // 3. Inisialisasi ulang bindings edit teks dan gambar pada elemen yang baru saja dimasukkan
                                setTimeout(() => {
                                    const editorContainer = document.querySelector('[x-data="editorApp()"]');
                                    if (editorContainer) {
                                        const editorData = Alpine.$data(editorContainer);
                                        if (editorData && typeof editorData.initEditable === 'function') {
                                            editorData.initEditable();
                                        }
                                    }
                                    window.syncToMonaco();
                                }, 100);
                            } catch (e) {
                                console.error('Gagal memuat komponen hasil drop:', e);
                            }
                        } else {
                            window.syncToMonaco();
                        }
                    }
                });
            }
            // Aktifkan mode pengeditan teks dan klik ganti gambar
            this.initEditable();
            this.initMediaEditable();
        },

        /**
         * Mengaktifkan mode pengeditan teks langsung (inline text editing) pada Kanvas Visual.
         * Menandai elemen h1, h2, p, span (yang bukan dinamis x-text) dengan contenteditable="true"
         * dan memasang event listener keydown serta auto-save on blur.
         */
        initEditable() {
            // Ambil semua elemen teks statis di dalam kanvas
            const textElements = this.$el.querySelectorAll(
                'h1:not([x-text]), h2:not([x-text]), p:not([x-text]), span:not([x-text])'
            );
            
            textElements.forEach(el => {
                // Aktifkan fitur contenteditable bawaan browser
                el.setAttribute('contenteditable', 'true');
                
                // Beri efek outline melayang (hover) agar admin tahu teks ini bisa diedit secara langsung
                el.classList.add('hover:outline', 'hover:outline-1',
                    'hover:outline-blue-400', 'focus:outline-2',
                    'focus:outline-blue-500', 'transition-all');

                // Custom enter behavior: Mencegah enter membuat elemen paragraf baru (<p>),
                // melainkan hanya menyisipkan line break biasa (<br>)
                el.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        document.execCommand('insertLineBreak');
                    }
                });

                // Auto-Save: Saat fokus keluar dari teks (blur), sinkronisasikan teks baru ke Monaco Editor secara instan
                el.addEventListener('blur', () => {
                    window.syncToMonaco();
                });
            });

            // ATURAN UNTUK VARIABEL DINAMIS (x-text):
            // Elemen dinamis (seperti nama mempelai atau nama tamu VIP) tidak boleh diedit langsung secara inline
            // agar sintaks template Blade / direktif Alpine tidak rusak. Kita tandai secara visual dan kunci.
            const dynamicElements = this.$el.querySelectorAll('[x-text]');
            dynamicElements.forEach(el => {
                el.setAttribute('contenteditable', 'false');
                el.classList.add('border-b', 'border-dashed', 'border-blue-400',
                    'cursor-not-allowed', 'select-none');
                el.setAttribute('title', 'Variabel Dinamis (Pengeditan Langsung Dinonaktifkan)');
            });
        },

        /**
         * Mengaktifkan mode penyuntingan media.
         * Menandai elemen gambar (<img>) dan kontainer yang memiliki background-image
         * agar merespon klik ganda (double-click) untuk membuka jendela Media Library.
         */
        initMediaEditable() {
            const mediaElements = this.$el.querySelectorAll('img, section, div');
            mediaElements.forEach(el => {
                const isImg = el.tagName === 'IMG';
                const hasBg = el.style.backgroundImage !== '';

                if (isImg || hasBg) {
                    // Daftarkan double click event
                    el.addEventListener('dblclick', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        // Simpan referensi elemen media yang diklik ke variabel window global
                        window.currentMediaTarget = el;
                        // Buka panel pencarian media dengan target penyisipan visual
                        openMediaLibrary('visual');
                    });

                    // Tambahkan penanda visual kursor berbentuk tangan (pointer)
                    el.classList.add('cursor-pointer', 'transition-opacity');
                    el.setAttribute('title', 'Klik 2x untuk mengganti gambar/background');
                }
            });
        }
    };
}
