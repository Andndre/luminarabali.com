/**
 * Editor Application Entry Point (app.js)
 * 
 * File ini merupakan file utama (entry point) dari aplikasi editor digital invitation.
 * Berfungsi untuk memuat modul-modul editor, menginisialisasi AlpineJS,
 * mengonfigurasi Monaco Editor untuk pengeditan kode (HTML, CSS, Cover),
 * serta menyediakan fungsi sinkronisasi 2-arah (Visual Canvas <-> Monaco Editor)
 * dan integrasi perpustakaan media (Media Library).
 */

import EditorCore from './core';
import EditorHover from './hover';
import EditorInspector from './inspector';
import EditorBoxModel from './box-model';
import EditorInit from './init';

import Alpine from 'alpinejs';

// Hubungkan Alpine ke objek window global agar dapat diakses dari elemen HTML / template Blade
window.Alpine = Alpine;

/**
 * Komponen Utama AlpineJS: 'editorApp'
 * Menggabungkan state dan methods dari 5 modul terpisah:
 * - EditorCore: State dasar, panel samping, data mempelai/tamu, data inspektor node.
 * - EditorHover: Manajemen hover visual pada elemen makro (Section/Container).
 * - EditorInspector: Fitur detail edit elemen mikro (Teks, Kelas, Gambar, Warna).
 * - EditorBoxModel: Pengolah Padding, Margin, Border Width, dan Border Radius secara visual.
 * - EditorInit: Mengatur drag-and-drop sortable, double click media, contenteditable teks.
 */
Alpine.data('editorApp', () => {
    return {
        ...EditorCore(),
        ...EditorHover(),
        ...EditorInspector(),
        ...EditorBoxModel(),
        ...EditorInit(),

        /**
         * Lifecycle Init: Dijalankan otomatis saat Alpine menginisialisasi komponen ini.
         * Memanggil fungsi inisialisasi dari masing-masing modul jika tersedia.
         */
        init() {
            if (this.coreInit) this.coreInit();
            if (this.hoverInit) this.hoverInit();
            if (this.inspectorInit) this.inspectorInit();
            if (this.boxModelInit) this.boxModelInit();
            if (this.setupInit) this.setupInit();
        }
    };
});

/**
 * Komponen Pembantu AlpineJS: 'propertiesForm'
 * Mengelola form data meta-data halaman undangan (seperti konfigurasi musik,
 * efek animasi, background global, dll.) dan menyimpannya ke input tersembunyi
 * sebagai string JSON sebelum disubmit ke backend.
 */
Alpine.data('propertiesForm', (initialData) => ({
    formData: initialData,
    
    init() {
        this.updateJson();
    },

    /**
     * Mengubah object data form menjadi string JSON dan memasukkannya ke input tersembunyi
     * agar terkirim saat form utama disimpan.
     */
    updateJson() {
        document.getElementById('meta_data_input').value = JSON.stringify(this.formData);
    }
}));

/**
 * Komponen Pembantu AlpineJS: 'templateLibrary'
 * Mengelola pencarian, pemfilteran kategori, dan penyisipan (insert) komponen
 * dari perpustakaan komponen (Component Library) ke dalam Visual Canvas editor.
 */
Alpine.data('templateLibrary', () => ({
    components: [],         // Daftar komponen yang diambil dari backend API
    search: '',             // Kata kunci pencarian komponen
    selectedCategory: '',   // Kategori komponen yang dipilih untuk filter
    loading: true,          // Indikator loading data

    init() {
        this.fetchComponents();
        // Inisialisasi SortableJS setelah data komponen selesai dimuat
        setTimeout(() => this.initSortable(), 500);
    },

    /**
     * Menginisialisasi SortableJS pada daftar komponen pustaka agar komponen
     * dapat di-drag dari panel kiri dan di-drop ke dalam kanvas visual.
     */
    initSortable() {
        const container = document.getElementById('component-library-list');
        if (container) {
            new Sortable(container, {
                group: {
                    name: 'shared',
                    pull: 'clone', // Menduplikat komponen ketika di-drag keluar, bukan memindahkannya
                    put: false     // Mencegah elemen lain di-drop kembali ke panel pustaka
                },
                sort: false,       // Nonaktifkan penyusunan ulang di dalam panel pustaka itu sendiri
                animation: 150,
                ghostClass: 'opacity-50'
            });
        }
    },

    /**
     * Mengambil daftar komponen pustaka dari API Backend Laravel.
     */
    async fetchComponents() {
        this.loading = true;
        try {
            const response = await fetch('/admin/api/component-library');
            this.components = await response.json();
        } catch (error) {
            console.error('Gagal memuat komponen pustaka:', error);
        } finally {
            this.loading = false;
        }
    },

    /**
     * Getter untuk menyaring komponen berdasarkan kata kunci pencarian dan kategori yang dipilih.
     */
    get filteredComponents() {
        return this.components.filter(c => {
            const matchSearch = c.name.toLowerCase().includes(this.search.toLowerCase()) ||
                (c.description && c.description.toLowerCase().includes(this.search.toLowerCase()));
            const matchCategory = this.selectedCategory === '' || c.category === this.selectedCategory;
            return matchSearch && matchCategory;
        });
    },

    /**
     * Memasukkan komponen terpilih secara langsung ke dalam Kanvas Visual.
     * Jika ada node target yang sedang aktif (diklik sebelumnya), komponen disisipkan setelahnya.
     * Jika tidak, komponen disisipkan di bagian paling akhir kanvas.
     */
    async insertComponent(id) {
        try {
            const response = await fetch(`/admin/api/component-library/${id}`);
            const component = await response.json();

            let code = component.code;

            // Dapatkan referensi data Alpine dari editorApp utama
            const editorAppContainer = document.querySelector('[x-data="editorApp()"]');
            const editorApp = editorAppContainer ? Alpine.$data(editorAppContainer) : null;
            if (!editorApp) {
                console.error('editorApp tidak ditemukan');
                return;
            }
            
            const canvas = document.getElementById('visual-canvas');
            if (canvas) {
                // Sisipkan di posisi spesifik (insertTargetNode) atau paling bawah jika tidak ada target khusus
                if (editorApp.insertTargetNode) {
                    editorApp.insertTargetNode.insertAdjacentHTML('afterend', code);
                    editorApp.insertTargetNode = null; // Reset target setelah digunakan
                } else {
                    canvas.insertAdjacentHTML('beforeend', code);
                }

                // Inisialisasi ulang properti edit dan sinkronkan perubahan ke Monaco Editor
                setTimeout(() => {
                    const container = document.querySelector('[x-data="editorApp()"]');
                    if (container) {
                        const editorData = Alpine.$data(container);
                        if (editorData && typeof editorData.initEditable === 'function') {
                            editorData.initEditable();
                        }
                    }
                    window.syncToMonaco();
                }, 50);
            }

            // Notifikasi sukses berupa Toast tipis
            Swal.fire({
                icon: 'success',
                title: 'Komponen berhasil dimasukkan',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });

        } catch (error) {
            console.error('Gagal memasukkan komponen:', error);
            Swal.fire('Error', 'Gagal memuat komponen', 'error');
        }
    }
}));

// Mulai aplikasi AlpineJS
Alpine.start();

// Variable global untuk Monaco Editor
var globalEditor = null;
var coverModel, htmlModel, cssModel;
var mediaTarget = 'editor'; // Menentukan target sisipan media: 'editor' (kode Monaco), 'visual' (kanvas gambar), atau 'audio' (musik latar)

// Inisialisasi Monaco Editor menggunakan RequireJS
require.config({
    paths: {
        'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs'
    }
});

require(['vs/editor/editor.main'], function() {
    // Ambil konten mentah dari textareas tersembunyi yang disiapkan oleh Laravel Blade
    const rawCover = document.getElementById('raw_cover_content').value;
    const rawHtml = document.getElementById('raw_html_content').value;
    const rawCss = document.getElementById('raw_custom_css').value;

    // Buat model data terpisah untuk Cover (HTML), Main Content (HTML), dan Custom CSS (CSS)
    coverModel = monaco.editor.createModel(rawCover, "html");
    htmlModel = monaco.editor.createModel(rawHtml, "html");
    cssModel = monaco.editor.createModel(rawCss, "css");

    // Inisialisasi Monaco Editor Instance
    globalEditor = monaco.editor.create(document.getElementById('monaco-container'), {
        model: htmlModel, // Default model di awal adalah Main Content (HTML)
        theme: 'vs-dark',
        automaticLayout: true,
        wordWrap: 'on',
        minimap: {
            enabled: false
        },
        fontSize: 14,
        lineHeight: 24,
        padding: {
            top: 16
        }
    });

    // ResizeObserver untuk menyesuaikan lebar Monaco secara otomatis ketika panel visual/kode bergeser atau beranimasi
    const resizeObserver = new ResizeObserver(() => {
        if (window.globalEditor) {
            window.globalEditor.layout();
        }
    });
    resizeObserver.observe(document.getElementById('monaco-container'));

    /**
     * Sinkronisasi 2-Arah: Dari Kode (Monaco Editor) ke Kanvas Visual.
     * Menggunakan debounce 500ms agar rendering tidak memberatkan browser selama pengguna mengetik.
     */
    window.syncToCanvas = function(model) {
        if (window.isSyncing) return;
        
        clearTimeout(window.typingTimer);
        window.typingTimer = setTimeout(() => {
            window.isSyncing = true;
            
            const rawHTML = model.getValue();
            const canvas = document.getElementById('visual-canvas');
            if (canvas) {
                canvas.innerHTML = rawHTML;
                
                // Inisialisasi ulang binder Alpine editable (contenteditable, double click img) pada DOM baru
                const container = document.querySelector('[x-data="editorApp()"]');
                if (container && Alpine.$data(container) && typeof Alpine.$data(container).initEditable === 'function') {
                    Alpine.$data(container).initEditable();
                }
            }
            
            setTimeout(() => { window.isSyncing = false; }, 50);
        }, 500); // 500ms debounce
    };

    // Dengarkan perubahan isi teks pada model untuk langsung menyinkronkan ke kanvas visual
    htmlModel.onDidChangeContent(() => {
        if (window.activeTab === 'html') window.syncToCanvas(htmlModel);
    });
    coverModel.onDidChangeContent(() => {
        if (window.activeTab === 'cover') window.syncToCanvas(coverModel);
    });

    /**
     * Mengirim dan menyimpan data editor ke server database Laravel melalui AJAX Fetch.
     */
    function handleSave(e) {
        if (e) e.preventDefault();

        // Salin isi kode dari model Monaco ke input form tersembunyi
        document.getElementById('cover_content_input').value = coverModel.getValue();
        document.getElementById('html_content_input').value = htmlModel.getValue();
        document.getElementById('global_custom_css_input').value = cssModel.getValue();

        const form = document.getElementById('editorForm');
        const formData = new FormData(form);

        const saveBtn = document.querySelector('button[form="editorForm"]');
        const originalText = saveBtn.innerText;
        saveBtn.innerText = 'Menyimpan...';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            saveBtn.innerText = 'Tersimpan ✓';
            setTimeout(() => saveBtn.innerText = originalText, 2000);
        })
        .catch(err => {
            console.error('Gagal menyimpan:', err);
            saveBtn.innerText = 'Gagal Menyimpan!';
            setTimeout(() => saveBtn.innerText = originalText, 2000);
        });
    }

    // Ekspos handleSave agar dapat diakses oleh elemen HTML luar
    window.handleSave = handleSave;

    // Tambahkan event listener submit pada form utama
    document.getElementById('editorForm').addEventListener('submit', handleSave);

    // Pintasan keyboard Ctrl + S atau Cmd + S di level window global untuk menyimpan
    window.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            handleSave();
        }
    });

    // Pintasan keyboard Ctrl + S di lingkup editor Monaco
    globalEditor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS, function() {
        handleSave();
    });
});

/**
 * Berpindah Tab Model di Monaco Editor (Cover Page / Main Content / Global CSS)
 * Serta memperbarui tampilan status tab yang aktif.
 */
window.switchTab = function(tab) {
    window.activeTab = tab;
    
    // Perbarui gaya CSS tab navigasi
    document.querySelectorAll('[id^="tab-"]').forEach(el => {
        el.classList.remove('border-blue-500', 'text-white');
        el.classList.add('border-transparent');
    });
    document.getElementById('tab-' + tab).classList.remove('border-transparent');
    document.getElementById('tab-' + tab).classList.add('border-blue-500', 'text-white');

    // Ubah model aktif pada editor Monaco dan paksa sinkronisasi visual jika pindah ke tab HTML
    if (tab === 'cover') {
        globalEditor.setModel(coverModel);
        if (typeof window.syncToCanvas === 'function') window.syncToCanvas(coverModel);
    } else if (tab === 'html') {
        globalEditor.setModel(htmlModel);
        if (typeof window.syncToCanvas === 'function') window.syncToCanvas(htmlModel);
    } else if (tab === 'css') {
        globalEditor.setModel(cssModel);
    }
    
    // Pancarkan event tab-changed ke global untuk didengar oleh state Alpine jika diperlukan
    window.dispatchEvent(new CustomEvent('tab-changed', { detail: tab }));
};

/**
 * Membuka Modal Media Library (Manajer Aset)
 * @param {string} target - Tujuan penyisipan: 'editor', 'visual', atau 'audio'
 */
window.openMediaLibrary = function(target = 'editor') {
    mediaTarget = target;
    const modal = document.getElementById('mediaModal');
    const iframe = document.getElementById('mediaIframe');

    // Muat halaman manajemen aset Laravel pada iframe jika belum pernah dimuat
    if (!iframe.getAttribute('src')) {
        const assetsRoute = (window.EditorConfig && window.EditorConfig.assetsRoute) 
            ? window.EditorConfig.assetsRoute 
            : '/admin/assets?modal=1';
        iframe.setAttribute('src', assetsRoute);
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
};

/**
 * Menutup Modal Media Library
 */
window.closeMediaLibrary = function() {
    const modal = document.getElementById('mediaModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
};

/**
 * Memasukkan aset media (URL gambar/audio) dari Media Library iframe ke target yang sesuai.
 * @param {string} url - URL dari file media yang dipilih
 */
window.insertMedia = function(url) {
    if (mediaTarget === 'visual' && window.currentMediaTarget) {
        // Jika target adalah elemen visual di kanvas (double click img/bg)
        const el = window.currentMediaTarget;
        if (el.tagName === 'IMG') {
            el.src = url;
        } else {
            el.style.backgroundImage = `url('${url}')`;
        }
        el.removeAttribute('title');

        // Sinkronisasikan perubahan dari kanvas ke Monaco Editor
        window.syncToMonaco();
        window.currentMediaTarget = null;
    } else if (mediaTarget === 'editor') {
        // Jika target adalah posisi kursor di Monaco Editor, sisipkan tag <img>
        if (!globalEditor) return;
        const imgTag = `<img src="${url}" alt="image" class="w-full h-auto">`;

        const position = globalEditor.getPosition();
        globalEditor.executeEdits("media-insert", [{
            range: new monaco.Range(position.lineNumber, position.column, position.lineNumber, position.column),
            text: imgTag,
            forceMoveMarkers: true
        }]);
        globalEditor.focus();
    } else if (mediaTarget === 'audio') {
        // Jika target adalah isian input musik latar (bg music)
        const audioField = document.getElementById('bg_music_input_field');
        if (audioField) {
            audioField.value = url;
            // Trigger event input secara manual agar dideteksi oleh AlpineJS
            audioField.dispatchEvent(new Event('input', {
                bubbles: true
            }));
        }
    }

    window.closeMediaLibrary();
};
