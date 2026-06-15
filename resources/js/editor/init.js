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
                const canvasChildElements = canvas.querySelectorAll('*');
                canvasChildElements.forEach(childElement => {
                    if (childElement.className && typeof childElement.className === 'string') {
                        childElement.className = childElement.className.replace(/\b(sm|md|lg|xl|2xl):/g, '@$1:');
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
                    onEnd: function(sortableDropEvent) {
                        window.syncToMonaco();
                    },
                    
                    // Callback saat komponen baru dilempar (drop) ke kanvas dari panel pustaka komponen
                    onAdd: async function(sortableDropEvent) {
                        if (sortableDropEvent.item.classList.contains('library-item')) {
                            const componentLibraryId = sortableDropEvent.item.dataset.id;
                            
                            // 1. Bersihkan elemen pembantu drag secara sinkron dengan menampilkan spinner loading
                            //    untuk mencegah AlpineJS mengevaluasi konten mentah sebelum data dari server siap.
                            sortableDropEvent.item.innerHTML = '<div class="flex flex-col items-center justify-center gap-2 rounded border border-blue-200 bg-blue-50 p-6 text-sm text-blue-600"><svg class="h-6 w-6 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memuat komponen...</div>';
                            
                            // Hapus atribut direktif AlpineJS yang menyamar agar tidak memicu error inisialisasi ganda
                            const droppedElementAttributes = Array.from(sortableDropEvent.item.attributes);
                            droppedElementAttributes.forEach(attributeNode => {
                                if (attributeNode.name.startsWith('x-') || attributeNode.name.startsWith('@') || attributeNode.name.startsWith(':')) {
                                    sortableDropEvent.item.removeAttribute(attributeNode.name);
                                }
                            });

                            try {
                                // 2. Ambil source code HTML asli komponen dari API Backend Laravel
                                const response = await fetch(`/admin/api/component-library/${componentLibraryId}`);
                                const component = await response.json();
                                
                                let componentHtmlCode = component.code;
                                // Ganti placeholder item dengan HTML utuh komponen asli
                                sortableDropEvent.item.outerHTML = componentHtmlCode;
                                
                                // 3. Inisialisasi ulang bindings edit teks dan gambar pada elemen yang baru saja dimasukkan
                                setTimeout(() => {
                                    const editorAppContainer = document.querySelector('[x-data="editorApp()"]');
                                    if (editorAppContainer) {
                                        const editorAppData = Alpine.$data(editorAppContainer);
                                        if (editorAppData && typeof editorAppData.initEditable === 'function') {
                                            editorAppData.initEditable();
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
            const editableTextElements = this.$el.querySelectorAll(
                'h1:not([x-text]), h2:not([x-text]), p:not([x-text]), span:not([x-text])'
            );
            
            editableTextElements.forEach(textElement => {
                // Aktifkan fitur contenteditable bawaan browser
                textElement.setAttribute('contenteditable', 'true');
                
                // Beri efek outline melayang (hover) agar admin tahu teks ini bisa diedit secara langsung
                textElement.classList.add('hover:outline', 'hover:outline-1',
                    'hover:outline-blue-400', 'focus:outline-2',
                    'focus:outline-blue-500', 'transition-all');

                // Custom enter behavior: Mencegah enter membuat elemen paragraf baru (<p>),
                // melainkan hanya menyisipkan line break biasa (<br>)
                textElement.addEventListener('keydown', (keyEvent) => {
                    if (keyEvent.key === 'Enter') {
                        keyEvent.preventDefault();
                        document.execCommand('insertLineBreak');
                    }
                });

                // Auto-Save: Saat fokus keluar dari teks (blur), sinkronisasikan teks baru ke Monaco Editor secara instan
                textElement.addEventListener('blur', () => {
                    window.syncToMonaco();
                });
            });

            // ATURAN UNTUK VARIABEL DINAMIS (x-text):
            // Elemen dinamis (seperti nama mempelai atau nama tamu VIP) tidak boleh diedit langsung secara inline
            // agar sintaks template Blade / direktif Alpine tidak rusak. Kita tandai secara visual dan kunci.
            const dynamicVariableElements = this.$el.querySelectorAll('[x-text]');
            dynamicVariableElements.forEach(dynamicElement => {
                dynamicElement.setAttribute('contenteditable', 'false');
                dynamicElement.classList.add('border-b', 'border-dashed', 'border-blue-400',
                    'cursor-not-allowed', 'select-none');
                dynamicElement.setAttribute('title', 'Variabel Dinamis (Pengeditan Langsung Dinonaktifkan)');
            });
        },

        /**
         * Mengaktifkan mode penyuntingan media.
         * Menandai elemen gambar (<img>) dan kontainer yang memiliki background-image
         * agar merespon klik ganda (double-click) untuk membuka jendela Media Library.
         */
        initMediaEditable() {
            const mediaElementsList = this.$el.querySelectorAll('img, section, div');
            mediaElementsList.forEach(mediaElement => {
                const isImageTag = mediaElement.tagName === 'IMG';
                const hasBackgroundImageStyle = mediaElement.style.backgroundImage !== '';

                if (isImageTag || hasBackgroundImageStyle) {
                    // Daftarkan double click event
                    mediaElement.addEventListener('dblclick', (mouseDoubleClickEvent) => {
                        mouseDoubleClickEvent.preventDefault();
                        mouseDoubleClickEvent.stopPropagation();
                        // Simpan referensi elemen media yang diklik ke variabel window global
                        window.currentMediaTarget = mediaElement;
                        // Buka panel pencarian media dengan target penyisipan visual
                        openMediaLibrary('visual');
                    });

                    // Tambahkan penanda visual kursor berbentuk tangan (pointer)
                    mediaElement.classList.add('cursor-pointer', 'transition-opacity');
                    mediaElement.setAttribute('title', 'Klik 2x untuk mengganti gambar/background');
                }
            });
        }
    };
}
