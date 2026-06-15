/**
 * Modul Hover Editor (hover.js)
 * 
 * Modul ini mengelola interaksi visual tingkat tinggi (makro) pada Kanvas Visual,
 * khususnya menu melayang (hover overlay controls) yang muncul ketika kursor berada
 * di atas elemen-elemen penampung struktural (seperti section, header, footer, div.flex, dll.).
 * Menyediakan aksi cepat seperti duplikasi blok, hapus blok, pemindahan posisi (atas/bawah),
 * serta penyisipan komponen pustaka tepat di bawah blok terpilih.
 */
export default function EditorHover() {
    return {
        // Node makro/blok yang saat ini sedang disorot (hover) oleh kursor
        hoveredNode: null,
        
        // Status apakah menu hover melayang harus ditampilkan atau tidak
        hoverMenuVisible: false,
        
        // Posisi dan dimensi koordinat untuk menaruh overlay menu hover tepat di atas blok yang disorot
        hoverMenuPos: {
            top: '0px',
            left: '0px',
            width: '0px',
            height: '0px'
        },
        
        // Penampung breadcrumbs DOM dari elemen terpilih ke atas (diisi di modul inspector)
        breadcrumbs: [],

        /**
         * Mendeteksi dan memperbarui pergerakan kursor di atas kanvas visual.
         * Menemukan kontainer makro terdekat (section, header, footer, dll.) dan memposisikan
         * menu hover overlay di atas koordinat elemen tersebut.
         * 
         * @param {MouseEvent} event - Event mousemove/mouseover dari browser
         */
        trackHover(event) {
            // Abaikan jika user sedang menekan tombol mouse (misalnya saat sedang drag-and-drop atau menyeleksi teks)
            if (event.buttons > 0) return;

            const el = event.target;
            if (!el || el.id === 'visual-canvas') return;

            // Cari elemen blok/makro terdekat yang memenuhi kriteria selektor struktural
            const block = el.closest(
                'section, header, footer, div.flex, div.grid, div.container, [class*="section"]'
            );
            
            // Jika tidak ada blok makro atau kursor menunjuk kanvas utama secara langsung, sembunyikan menu hover
            if (!block || block.id === 'visual-canvas') {
                return;
            }

            this.hoveredNode = block;

            // Hitung posisi koordinat relatif terhadap kontainer induk visual-canvas
            this.hoverMenuPos = {
                top: block.offsetTop + 'px',
                left: block.offsetLeft + 'px',
                width: block.offsetWidth + 'px',
                height: block.offsetHeight + 'px'
            };

            this.hoverMenuVisible = true;
        },

        /**
         * Menduplikasi (clone) blok makro yang sedang di-hover.
         * Komponen hasil duplikasi dibersihkan dari sisa-sisa kelas highlight editor (ring-blue-500)
         * lalu disisipkan tepat di bawah blok asli.
         */
        duplicateHoveredNode() {
            if (this.hoveredNode) {
                // Lakukan deep copy pada node
                const clone = this.hoveredNode.cloneNode(true);

                // Bersihkan penanda ring seleksi biru pada anak elemen di dalam clone
                const highlighted = clone.querySelector('.ring-blue-500');
                if (highlighted) {
                    highlighted.classList.remove('ring-2', 'ring-blue-500', 'ring-inset', 'outline-none');
                    let cls = highlighted.getAttribute('class') || '';
                    if (cls.trim() === '') {
                        highlighted.removeAttribute('class');
                    }
                }
                
                // Bersihkan penanda ring seleksi biru pada elemen terluar clone itu sendiri
                if (clone.classList.contains('ring-blue-500')) {
                    clone.classList.remove('ring-2', 'ring-blue-500', 'ring-inset', 'outline-none');
                    let cls = clone.getAttribute('class') || '';
                    if (cls.trim() === '') clone.removeAttribute('class');
                }

                // Sisipkan clone sebagai string HTML agar AlpineJS dapat memproses ulang direktifnya secara bersih
                this.hoveredNode.insertAdjacentHTML('afterend', clone.outerHTML);

                // Inisialisasi ulang atribut contenteditable pada elemen teks di kanvas dan sinkronkan ke Monaco Editor
                setTimeout(() => {
                    this.initEditable();
                    window.syncToMonaco();
                }, 50);
            }
        },

        /**
         * Menghapus blok makro yang sedang di-hover dari kanvas visual.
         * Jika blok yang dihapus sedang dibuka di panel Inspector mikro, panel Inspector akan ditutup terlebih dahulu.
         */
        deleteHoveredNode() {
            if (this.hoveredNode) {
                // Jika elemen yang dihapus adalah elemen yang sedang aktif di-inspect (atau berisi elemen tersebut), tutup inspector
                if (this.selectedNode === this.hoveredNode || this.hoveredNode.contains(this.selectedNode)) {
                    this.closeInspector();
                }
                this.hoveredNode.remove();
                this.hoverMenuVisible = false;
                this.hoveredNode = null;
                window.syncToMonaco();
            }
        },

        /**
         * Mempersiapkan penargetan penyisipan komponen tepat di bawah blok makro ini.
         * Menyimpan node ini ke variabel global insertTargetNode dan membuka panel perpustakaan (library).
         */
        prepareInsertBelow() {
            if (this.hoveredNode) {
                // Set target sisipan agar saat user klik komponen di panel pustaka, komponen ditaruh di bawah blok ini
                this.insertTargetNode = this.hoveredNode;
                // Buka panel perpustakaan komponen (library panel) di sisi kiri
                this.panels.library = true;
            }
        },

        /**
         * Memindahkan urutan blok makro satu langkah ke atas (swap dengan elemen saudara sebelumnya).
         * Setelah dipindah, koordinat menu hover diperbarui secara instan.
         */
        moveNodeUp() {
            if (this.hoveredNode && this.hoveredNode.previousElementSibling) {
                this.hoveredNode.parentNode.insertBefore(this.hoveredNode, this.hoveredNode.previousElementSibling);

                // Perbarui posisi koordinat menu hover
                this.hoverMenuPos = {
                    top: this.hoveredNode.offsetTop + 'px',
                    left: this.hoveredNode.offsetLeft + 'px',
                    width: this.hoveredNode.offsetWidth + 'px',
                    height: this.hoveredNode.offsetHeight + 'px'
                };
                window.syncToMonaco();
            }
        },

        /**
         * Memindahkan urutan blok makro satu langkah ke bawah (swap dengan elemen saudara setelahnya).
         * Setelah dipindah, koordinat menu hover diperbarui secara instan.
         */
        moveNodeDown() {
            if (this.hoveredNode && this.hoveredNode.nextElementSibling) {
                // insertBefore(node_yang_mau_ditaruh_sebelum_target, target)
                this.hoveredNode.parentNode.insertBefore(this.hoveredNode.nextElementSibling, this.hoveredNode);

                // Perbarui posisi koordinat menu hover
                this.hoverMenuPos = {
                    top: this.hoveredNode.offsetTop + 'px',
                    left: this.hoveredNode.offsetLeft + 'px',
                    width: this.hoveredNode.offsetWidth + 'px',
                    height: this.hoveredNode.offsetHeight + 'px'
                };
                window.syncToMonaco();
            }
        }
    };
}
