/**
 * Modul Inspector Elemen Mikro (inspector.js)
 * 
 * Modul ini menangani deteksi dan manipulasi elemen-elemen mikro (seperti h1, p, img, a, span)
 * saat diklik oleh user di Kanvas Visual. Menyediakan fungsionalitas panel Inspector sisi kanan,
 * pembacaan data elemen (text, classes, href, src, warna), pembuatan breadcrumbs penelusuran DOM,
 * serta mutasi real-time dari properti elemen kembali ke DOM kanvas dan Monaco Editor.
 */
export default function EditorInspector() {
    return {
        /**
         * Event Handler saat user mengklik elemen di dalam Kanvas Visual.
         * Menolak penyeleksian kontainer kanvas utama, mencegah navigasi link <a>,
         * menstabilkan target (misal klik isi SVG akan menyeleksi parent <svg>),
         * dan melakukan fallback ke kontainer makro jika mengklik div kosong/absolut.
         * 
         * @param {MouseEvent} event - Event click dari kanvas visual
         */
        inspectElement(event) {
            // Abaikan klik pada kanvas pembungkus utama itu sendiri atau body
            if (event.target.id === 'visual-canvas' || event.target.tagName.toLowerCase() === 'body') {
                return;
            }

            // Mencegah browser mengikuti tautan (href) saat mode pengeditan sedang aktif
            const aTag = event.target.closest('a');
            if (aTag) {
                event.preventDefault();
            }

            // Tentukan node target penyeleksian
            let targetNode = event.target;

            // Jika mengklik bagian dari gambar vektor (path/g/circle di dalam SVG), arahkan seleksi ke elemen <svg>
            if (targetNode.closest('svg')) {
                targetNode = targetNode.closest('svg');
            }

            // SMART FALLBACK: Jika mengklik DIV kosong dengan posisi absolute/fixed (biasanya overlay dekoratif),
            // arahkan seleksi ke kontainer makro terdekat agar mempermudah edit tata letak.
            if (targetNode.tagName === 'DIV' && !targetNode.isContentEditable) {
                if ((targetNode.classList.contains('absolute') || targetNode.classList.contains('fixed')) && targetNode.textContent.trim() === '') {
                    const macro = targetNode.closest('section, header, footer, [class*="section"]');
                    if (macro) {
                        targetNode = macro;
                    }
                }
            }

            // Serahkan node target ke metode penyeleksian terpadu
            this.selectNode(targetNode);
        },

        /**
         * Menyeleksi elemen DOM tertentu.
         * Menambahkan ring highlight biru, mengekstrak data teks, tautan, sumber gambar, kelas Tailwind CSS,
         * mem-parsing warna teks dan latar belakang arbitrary (kustom hex), membangun breadcrumbs DOM,
         * serta membuka laci panel Inspector di sisi kanan.
         * 
         * @param {HTMLElement} node - Elemen DOM yang ingin diseleksi
         */
        selectNode(node) {
            if (!node) return;

            // Bersihkan highlight dari node sebelumnya
            this.removeHighlight();
            
            // Set node aktif saat ini
            this.selectedNode = node;
            
            // Beri visual border/ring biru pada elemen yang aktif diedit
            this.selectedNode.classList.add('ring-2', 'ring-blue-500', 'ring-inset', 'outline-none');

            // Ambil nama tag elemen (misalnya: 'H1', 'SPAN', dsb)
            this.nodeData.tagName = this.selectedNode.tagName.toUpperCase();
            
            // Periksa apakah elemen menggunakan direktif teks dinamis x-text milik AlpineJS
            this.nodeData.isDynamic = this.selectedNode.hasAttribute('x-text') || this.selectedNode.closest('[x-text]') !== null;

            // Hanya izinkan edit teks mentah langsung jika elemen tersebut adalah daun DOM (tidak memiliki anak bersarang)
            // dan bukan berupa elemen dinamis (x-text)
            if (!this.nodeData.isDynamic && this.selectedNode.children.length === 0) {
                this.nodeData.text = this.selectedNode.textContent;
            } else {
                this.nodeData.text = '';
            }

            // Bersihkan daftar kelas CSS dari kelas temporary highlight milik editor agar tidak tersimpan secara permanen
            let cleanClasses = this.selectedNode.getAttribute('class') || '';
            cleanClasses = cleanClasses.replace(/\bring-2\b|\bring-blue-500\b|\bring-inset\b|\boutline-none\b/g, '')
                .replace(/\s+/g, ' ').trim();

            this.nodeData.classes = cleanClasses;
            this.nodeData.href = this.selectedNode.getAttribute('href') || '';
            this.nodeData.src = this.selectedNode.getAttribute('src') || '';

            // Ekstrak warna teks kustom (format arbitrer Tailwind, contoh: text-[#ff0000]) menggunakan regex
            const textMatch = cleanClasses.match(/text-\[\s*(#[0-9a-fA-F]{3,8})\s*\]/);
            this.nodeData.textColor = textMatch ? textMatch[1] : '#000000';

            // Ekstrak warna background kustom (format arbitrer Tailwind, contoh: bg-[#ffffff]) menggunakan regex
            const bgMatch = cleanClasses.match(/bg-\[\s*(#[0-9a-fA-F]{3,8})\s*\]/);
            this.nodeData.bgColor = bgMatch ? bgMatch[1] : '#ffffff';

            // Perbarui jejak jalur DOM (breadcrumbs) di bagian bawah layar
            this.updateBreadcrumbs();
            
            // Buka drawer Inspector di panel kanan
            this.isInspectorOpen = true;
        },

        /**
         * Menelusuri silsilah induk DOM dari elemen terpilih ke atas (sampai mentok di visual-canvas)
         * untuk membangun bar navigasi breadcrumbs interaktif di bagian bawah editor.
         */
        updateBreadcrumbs() {
            this.breadcrumbs = [];
            let current = this.selectedNode;

            // Lakukan perulangan naik ke atas DOM tree
            while (current && current.id !== 'visual-canvas' && current.tagName.toLowerCase() !== 'body') {
                let clsStr = '';
                let cls = current.getAttribute('class');
                if (cls) {
                    // Bersihkan kelas highlight agar nama breadcrumbs bersih
                    cls = cls.replace(/\bring-2\b|\bring-blue-500\b|\bring-inset\b|\boutline-none\b|\bis-visible\b/g, '')
                        .replace(/\s+/g, ' ').trim();
                    
                    // Ambil maksimal 2 nama kelas pertama sebagai pembeda visual (contoh: .flex.flex-col)
                    const classes = cls.split(' ').filter(c => c.length > 0).slice(0, 2);
                    if (classes.length > 0) {
                        clsStr = '.' + classes.join('.');
                    }
                }

                // Tambahkan di awal array agar urutannya dari terluar ke terdalam (breadcrumbs standard)
                this.breadcrumbs.unshift({
                    tagName: current.tagName.toLowerCase(),
                    signature: clsStr,
                    node: current // Simpan referensi ke elemen asli untuk navigasi klik
                });

                current = current.parentElement;
            }
        },

        /**
         * Menghapus kelas outline highlight biru (ring) dari elemen yang sedang aktif dipilih.
         * Juga membersihkan atribut class jika menjadi kosong.
         */
        removeHighlight() {
            if (this.selectedNode) {
                this.selectedNode.classList.remove('ring-2', 'ring-blue-500', 'ring-inset', 'outline-none');
                let cls = this.selectedNode.getAttribute('class') || '';
                if (cls.trim() === '') {
                    this.selectedNode.removeAttribute('class');
                }
            }
        },

        /**
         * Menutup panel mikro Inspector dan membersihkan highlight visual elemen.
         */
        closeInspector() {
            this.removeHighlight();
            this.selectedNode = null;
            this.isInspectorOpen = false;
        },

        /**
         * Menavigasi seleksi ke elemen induk (parent) dari elemen yang saat ini terpilih,
         * asalkan induk tersebut masih berada di dalam cakupan kanvas visual.
         */
        selectParentNode() {
            if (!this.selectedNode || !this.selectedNode.parentElement) return;

            const parent = this.selectedNode.parentElement;
            if (parent.id === 'visual-canvas' || parent.tagName.toLowerCase() === 'body') {
                return;
            }

            this.selectNode(parent);
        },

        /**
         * Memperbarui properti elemen DOM terpilih secara real-time berdasarkan input dari panel Inspector.
         * Mendukung perubahan isi teks, kelas CSS, atribut tautan (href), dan sumber gambar (src).
         * 
         * @param {string} property - Nama properti ('text', 'classes', 'href', 'src')
         * @param {string} value - Nilai baru yang ingin diterapkan
         */
        updateNodeProperty(property, value) {
            if (!this.selectedNode) return;

            if (property === 'text' && !this.nodeData.isDynamic) {
                // Perbarui konten teks
                this.selectedNode.textContent = value;
            } else if (property === 'classes') {
                // Perbarui daftar kelas Tailwind CSS
                this.removeHighlight();
                if (value.trim() === '') {
                    this.selectedNode.removeAttribute('class');
                } else {
                    this.selectedNode.setAttribute('class', value);
                }
                // Kembalikan ring highlight agar penanda fokus tidak hilang
                this.selectedNode.classList.add('ring-2', 'ring-blue-500', 'ring-inset', 'outline-none');
            } else if (property === 'href') {
                // Perbarui tautan URL
                if (value) this.selectedNode.setAttribute('href', value);
                else this.selectedNode.removeAttribute('href');
            } else if (property === 'src') {
                // Perbarui sumber gambar
                if (value) this.selectedNode.setAttribute('src', value);
                else this.selectedNode.removeAttribute('src');
            }

            // Sinkronisasikan perubahan visual DOM kembali ke editor kode Monaco
            window.syncToMonaco();
        },

        /**
         * Menduplikasi (clone) elemen mikro yang sedang aktif dipilih.
         * Hasil duplikat disisipkan langsung setelah elemen asli di DOM.
         */
        duplicateSelectedNode() {
            if (!this.selectedNode) return;

            try {
                // Lakukan deep copy pada node
                const clone = this.selectedNode.cloneNode(true);

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

                // Sisipkan clone sebagai string HTML agar AlpineJS memprosesnya dengan bersih
                this.selectedNode.insertAdjacentHTML('afterend', clone.outerHTML);

                // Inisialisasi ulang properti pengeditan pada teks baru dan sinkronkan ke Monaco Editor
                setTimeout(() => {
                    this.initEditable();
                    window.syncToMonaco();
                }, 50);
            } catch (e) {
                console.error("Gagal menduplikasi node terpilih:", e);
            }
        },

        /**
         * Menghapus elemen mikro terpilih dari DOM kanvas visual dan menutup drawer Inspector.
         */
        deleteSelectedNode() {
            if (!this.selectedNode) return;

            this.selectedNode.remove();
            this.closeInspector();
            window.syncToMonaco();
        },

        /**
         * Menambah atau menghapus kelas Tailwind secara spesifik.
         * Berguna untuk tombol toggle/switch pada panel properties (misal font-weight, text-align).
         * 
         * @param {string} classToAdd - Kelas CSS baru yang ingin di-toggle (tambah jika belum ada, hapus jika sudah ada)
         * @param {Array<string>} classesToRemove - Daftar kelas yang harus dihapus jika classToAdd sedang ditambahkan (misalnya untuk reset opsi alignment yang bertabrakan)
         */
        toggleTailwindClass(classToAdd, classesToRemove = []) {
            if (!this.selectedNode) return;

            // Pecah kelas saat ini menjadi array
            const classes = (this.nodeData.classes || '').split(' ').filter(c => c.trim() !== '');

            // Buang semua kelas yang saling bertabrakan (ada di daftar classesToRemove)
            const newClasses = classes.filter(c => !classesToRemove.includes(c));

            const idx = newClasses.indexOf(classToAdd);
            if (idx > -1) {
                // Hapus jika sudah ada (toggle off)
                newClasses.splice(idx, 1);
            } else {
                // Tambah jika belum ada (toggle on)
                newClasses.push(classToAdd);
            }

            this.nodeData.classes = newClasses.join(' ');
            this.updateNodeProperty('classes', this.nodeData.classes);
        },

        /**
         * Memperbarui warna elemen menggunakan sintaks arbitrer warna kustom Tailwind CSS.
         * Misalnya mengubah warna teks menjadi text-[#ff0000] atau background menjadi bg-[#00ff00].
         * Fungsi ini membersihkan warna kustom lama dengan awalan prefix yang sama sebelum menambahkan yang baru.
         * 
         * @param {string} prefix - Awalan properti ('text' atau 'bg')
         * @param {string} hex - Kode warna hex kustom (contoh: '#ffaadd')
         */
        updateArbitraryColor(prefix, hex) {
            if (!this.selectedNode) return;
            let classes = (this.nodeData.classes || '').split(' ').filter(c => c.trim() !== '');
            
            // Regex untuk mendeteksi warna arbitrer format kustom lama (contoh: text-[#ffffff])
            const regex = new RegExp('^' + prefix + '-\\\\[#[0-9a-fA-F]{3,8}\\\\]$');
            
            // Warna static bawaan Tailwind yang juga harus dibuang agar tidak terjadi konflik
            const staticColors = ['white', 'black', 'transparent'];
            
            // Saring kelas untuk membuang warna arbitrer atau static lama pada prefix ini
            classes = classes.filter(c => {
                const base = c.replace(prefix + '-', '');
                return !regex.test(c) && !staticColors.includes(base);
            });
            
            // Masukkan kelas warna baru dalam format arbitrer Tailwind
            classes.push(prefix + '-[' + hex + ']');
            
            this.nodeData.classes = classes.join(' ');
            this.updateNodeProperty('classes', this.nodeData.classes);
        }
    };
}
