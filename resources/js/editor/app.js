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
    libraryComponents: [],       // Daftar komponen yang diambil dari backend API
    searchQuery: '',             // Kata kunci pencarian komponen
    selectedFilterCategory: '',  // Kategori komponen yang dipilih untuk filter
    isLibraryLoading: true,      // Indikator loading data

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
        this.isLibraryLoading = true;
        try {
            const response = await fetch('/admin/api/component-library');
            this.libraryComponents = await response.json();
        } catch (error) {
            console.error('Gagal memuat komponen pustaka:', error);
        } finally {
            this.isLibraryLoading = false;
        }
    },

    /**
     * Getter untuk menyaring komponen berdasarkan kata kunci pencarian dan kategori yang dipilih.
     */
    get filteredComponents() {
        return this.libraryComponents.filter(componentItem => {
            const isSearchMatch = componentItem.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                (componentItem.description && componentItem.description.toLowerCase().includes(this.searchQuery.toLowerCase()));
            const isCategoryMatch = this.selectedFilterCategory === '' || componentItem.category === this.selectedFilterCategory;
            return isSearchMatch && isCategoryMatch;
        });
    },

    /**
     * Memasukkan komponen terpilih secara langsung ke dalam Kanvas Visual.
     * Jika ada node target yang sedang aktif (diklik sebelumnya), komponen disisipkan setelahnya.
     * Jika tidak, komponen disisipkan di bagian paling akhir kanvas.
     */
    async insertComponent(componentId) {
        try {
            const response = await fetch(`/admin/api/component-library/${componentId}`);
            const component = await response.json();

            let componentHtmlCode = component.code;

            // Dapatkan referensi data Alpine dari editorApp utama
            const editorAppContainer = document.querySelector('[x-data="editorApp()"]');
            const editorAppData = editorAppContainer ? Alpine.$data(editorAppContainer) : null;
            if (!editorAppData) {
                console.error('editorApp tidak ditemukan');
                return;
            }
            
            const visualCanvasContainer = document.getElementById('visual-canvas');
            if (visualCanvasContainer) {
                // Sisipkan di posisi spesifik (insertTargetNode) atau paling bawah jika tidak ada target khusus
                if (editorAppData.insertTargetNode) {
                    editorAppData.insertTargetNode.insertAdjacentHTML('afterend', componentHtmlCode);
                    editorAppData.insertTargetNode = null; // Reset target setelah digunakan
                } else {
                    visualCanvasContainer.insertAdjacentHTML('beforeend', componentHtmlCode);
                }

                // Inisialisasi ulang properti edit dan sinkronkan perubahan ke Monaco Editor
                setTimeout(() => {
                    const containerElement = document.querySelector('[x-data="editorApp()"]');
                    if (containerElement) {
                        const editorAppDataObject = Alpine.$data(containerElement);
                        if (editorAppDataObject && typeof editorAppDataObject.initEditable === 'function') {
                            editorAppDataObject.initEditable();
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
var globalMonacoEditor = null;
var coverCodeModel, htmlCodeModel, cssCodeModel;
var mediaInsertionTarget = 'editor'; // Menentukan target sisipan media: 'editor' (kode Monaco), 'visual' (kanvas gambar), atau 'audio' (musik latar)

// Inisialisasi Monaco Editor menggunakan RequireJS
require.config({
    paths: {
        'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs'
    }
});

require(['vs/editor/editor.main'], function() {
    // Ambil konten mentah dari textareas tersembunyi yang disiapkan oleh Laravel Blade
    const rawCoverHtmlString = document.getElementById('raw_cover_content').value;
    const rawMainHtmlString = document.getElementById('raw_html_content').value;
    const rawCustomCssString = document.getElementById('raw_custom_css').value;

    // Buat model data terpisah untuk Cover (HTML), Main Content (HTML), dan Custom CSS (CSS)
    coverCodeModel = monaco.editor.createModel(rawCoverHtmlString, "html");
    htmlCodeModel = monaco.editor.createModel(rawMainHtmlString, "html");
    cssCodeModel = monaco.editor.createModel(rawCustomCssString, "css");

    // Inisialisasi Monaco Editor Instance
    globalMonacoEditor = monaco.editor.create(document.getElementById('monaco-container'), {
        model: htmlCodeModel, // Default model di awal adalah Main Content (HTML)
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
    const monacoResizeObserver = new ResizeObserver(() => {
        if (window.globalEditor) {
            window.globalEditor.layout();
        }
    });
    monacoResizeObserver.observe(document.getElementById('monaco-container'));

    // Synchronize globalEditor reference
    window.globalEditor = globalMonacoEditor;

    /**
     * Sinkronisasi 2-Arah: Dari Kode (Monaco Editor) ke Kanvas Visual.
     * Menggunakan debounce 500ms agar rendering tidak memberatkan browser selama pengguna mengetik.
     */
    window.syncToCanvas = function(monacoModel) {
        if (window.isSyncing) return;
        
        clearTimeout(window.typingTimer);
        window.typingTimer = setTimeout(() => {
            window.isSyncing = true;
            
            const rawHtmlFromEditor = monacoModel.getValue();
            const visualCanvasContainer = document.getElementById('visual-canvas');
            if (visualCanvasContainer) {
                visualCanvasContainer.innerHTML = rawHtmlFromEditor;
                
                // Inisialisasi ulang binder Alpine editable (contenteditable, double click img) pada DOM baru
                const editorAppContainer = document.querySelector('[x-data="editorApp()"]');
                if (editorAppContainer && Alpine.$data(editorAppContainer) && typeof Alpine.$data(editorAppContainer).initEditable === 'function') {
                    Alpine.$data(editorAppContainer).initEditable();
                }
            }
            
            setTimeout(() => { window.isSyncing = false; }, 50);
        }, 500); // 500ms debounce
    };

    // Dengarkan perubahan isi teks pada model untuk langsung menyinkronkan ke kanvas visual
    htmlCodeModel.onDidChangeContent(() => {
        if (window.activeTab === 'html') window.syncToCanvas(htmlCodeModel);
    });
    coverCodeModel.onDidChangeContent(() => {
        if (window.activeTab === 'cover') window.syncToCanvas(coverCodeModel);
    });

    /**
     * Mengirim dan menyimpan data editor ke server database Laravel melalui AJAX Fetch.
     */
    function handleSave(submitEvent) {
        if (submitEvent) submitEvent.preventDefault();

        // Salin isi kode dari model Monaco ke input form tersembunyi
        document.getElementById('cover_content_input').value = coverCodeModel.getValue();
        document.getElementById('html_content_input').value = htmlCodeModel.getValue();
        document.getElementById('global_custom_css_input').value = cssCodeModel.getValue();

        const editorFormElement = document.getElementById('editorForm');
        const editorFormData = new FormData(editorFormElement);

        const saveButtonElement = document.querySelector('button[form="editorForm"]');
        const originalButtonText = saveButtonElement.innerText;
        saveButtonElement.innerText = 'Menyimpan...';

        fetch(editorFormElement.action, {
            method: 'POST',
            body: editorFormData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(saveResponse => saveResponse.json())
        .then(saveResponseData => {
            saveButtonElement.innerText = 'Tersimpan ✓';
            setTimeout(() => saveButtonElement.innerText = originalButtonText, 2000);
        })
        .catch(saveError => {
            console.error('Gagal menyimpan:', saveError);
            saveButtonElement.innerText = 'Gagal Menyimpan!';
            setTimeout(() => saveButtonElement.innerText = originalButtonText, 2000);
        });
    }

    // Ekspos handleSave agar dapat diakses oleh elemen HTML luar
    window.handleSave = handleSave;

    // Tambahkan event listener submit pada form utama
    document.getElementById('editorForm').addEventListener('submit', handleSave);

    // Pintasan keyboard Ctrl + S atau Cmd + S di level window global untuk menyimpan
    window.addEventListener('keydown', function(keydownEvent) {
        if ((keydownEvent.ctrlKey || keydownEvent.metaKey) && keydownEvent.key === 's') {
            keydownEvent.preventDefault();
            handleSave();
        }
    });

    // Pintasan keyboard Ctrl + S di lingkup editor Monaco
    globalMonacoEditor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS, function() {
        handleSave();
    });
});

/**
 * Berpindah Tab Model di Monaco Editor (Cover Page / Main Content / Global CSS)
 * Serta memperbarui tampilan status tab yang aktif.
 */
window.switchTab = function(activeTabName) {
    window.activeTab = activeTabName;
    
    // Perbarui gaya CSS tab navigasi
    document.querySelectorAll('[id^="tab-"]').forEach(tabElement => {
        tabElement.classList.remove('border-blue-500', 'text-white');
        tabElement.classList.add('border-transparent');
    });
    document.getElementById('tab-' + activeTabName).classList.remove('border-transparent');
    document.getElementById('tab-' + activeTabName).classList.add('border-blue-500', 'text-white');

    // Ubah model aktif pada editor Monaco dan paksa sinkronisasi visual jika pindah ke tab HTML
    if (activeTabName === 'cover') {
        globalMonacoEditor.setModel(coverCodeModel);
        if (typeof window.syncToCanvas === 'function') window.syncToCanvas(coverCodeModel);
    } else if (activeTabName === 'html') {
        globalMonacoEditor.setModel(htmlCodeModel);
        if (typeof window.syncToCanvas === 'function') window.syncToCanvas(htmlCodeModel);
    } else if (activeTabName === 'css') {
        globalMonacoEditor.setModel(cssCodeModel);
    }
    
    // Pancarkan event tab-changed ke global untuk didengar oleh state Alpine jika diperlukan
    window.dispatchEvent(new CustomEvent('tab-changed', { detail: activeTabName }));
};

/**
 * Membuka Modal Media Library (Manajer Aset)
 * @param {string} libraryTargetType - Tujuan penyisipan: 'editor', 'visual', atau 'audio'
 */
window.openMediaLibrary = function(libraryTargetType = 'editor') {
    mediaInsertionTarget = libraryTargetType;
    const mediaModalElement = document.getElementById('mediaModal');
    const mediaIframeElement = document.getElementById('mediaIframe');

    // Muat halaman manajemen aset Laravel pada iframe jika belum pernah dimuat
    if (!mediaIframeElement.getAttribute('src')) {
        const resolvedAssetsRoute = (window.EditorConfig && window.EditorConfig.assetsRoute) 
            ? window.EditorConfig.assetsRoute 
            : '/admin/assets?modal=1';
        mediaIframeElement.setAttribute('src', resolvedAssetsRoute);
    }

    mediaModalElement.classList.remove('hidden');
    mediaModalElement.classList.add('flex');
};

/**
 * Menutup Modal Media Library
 */
window.closeMediaLibrary = function() {
    const mediaModalElement = document.getElementById('mediaModal');
    mediaModalElement.classList.add('hidden');
    mediaModalElement.classList.remove('flex');
};

/**
 * Memasukkan aset media (URL gambar/audio) dari Media Library iframe ke target yang sesuai.
 * @param {string} mediaAssetUrl - URL dari file media yang dipilih
 */
window.insertMedia = function(mediaAssetUrl) {
    if (mediaInsertionTarget === 'visual' && window.currentMediaTarget) {
        // Jika target adalah elemen visual di kanvas (double click img/bg)
        const targetedVisualElement = window.currentMediaTarget;
        if (targetedVisualElement.tagName === 'IMG') {
            targetedVisualElement.src = mediaAssetUrl;
        } else {
            targetedVisualElement.style.backgroundImage = `url('${mediaAssetUrl}')`;
        }
        targetedVisualElement.removeAttribute('title');

        // Sinkronisasikan perubahan dari kanvas ke Monaco Editor
        window.syncToMonaco();
        window.currentMediaTarget = null;
    } else if (mediaInsertionTarget === 'editor') {
        // Jika target adalah posisi kursor di Monaco Editor, sisipkan tag <img>
        if (!globalMonacoEditor) return;
        const imgHtmlTag = `<img src="${mediaAssetUrl}" alt="image" class="w-full h-auto">`;

        const editorCursorPosition = globalMonacoEditor.getPosition();
        globalMonacoEditor.executeEdits("media-insert", [{
            range: new monaco.Range(editorCursorPosition.lineNumber, editorCursorPosition.column, editorCursorPosition.lineNumber, editorCursorPosition.column),
            text: imgHtmlTag,
            forceMoveMarkers: true
        }]);
        globalMonacoEditor.focus();
    } else if (mediaInsertionTarget === 'audio') {
        // Jika target adalah isian input musik latar (bg music)
        const audioInputElement = document.getElementById('bg_music_input_field');
        if (audioInputElement) {
            audioInputElement.value = mediaAssetUrl;
            // Trigger event input secara manual agar dideteksi oleh AlpineJS
            audioInputElement.dispatchEvent(new Event('input', {
                bubbles: true
            }));
        }
    }

    window.closeMediaLibrary();
};
