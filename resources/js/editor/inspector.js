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
         * @param {MouseEvent} clickEvent - Event click dari kanvas visual
         */
        inspectElement(clickEvent) {
            // Abaikan klik pada kanvas pembungkus utama itu sendiri atau body
            if (clickEvent.target.id === 'visual-canvas' || clickEvent.target.tagName.toLowerCase() === 'body') {
                return;
            }

            // Mencegah browser mengikuti tautan (href) saat mode pengeditan sedang aktif
            const closestAnchorTag = clickEvent.target.closest('a');
            if (closestAnchorTag) {
                clickEvent.preventDefault();
            }

            // Tentukan node target penyeleksian
            let clickedTargetNode = clickEvent.target;

            // Jika mengklik bagian dari gambar vektor (path/g/circle di dalam SVG), arahkan seleksi ke elemen <svg>
            if (clickedTargetNode.closest('svg')) {
                clickedTargetNode = clickedTargetNode.closest('svg');
            }

            // SMART FALLBACK: Jika mengklik DIV kosong dengan posisi absolute/fixed (biasanya overlay dekoratif),
            // arahkan seleksi ke kontainer makro terdekat agar mempermudah edit tata letak.
            if (clickedTargetNode.tagName === 'DIV' && !clickedTargetNode.isContentEditable) {
                if ((clickedTargetNode.classList.contains('absolute') || clickedTargetNode.classList.contains('fixed')) && clickedTargetNode.textContent.trim() === '') {
                    const closestMacroBlock = clickedTargetNode.closest('section, header, footer, [class*="section"]');
                    if (closestMacroBlock) {
                        clickedTargetNode = closestMacroBlock;
                    }
                }
            }

            // Serahkan node target ke metode penyeleksian terpadu
            this.selectNode(clickedTargetNode);
        },

        /**
         * Menyeleksi elemen DOM tertentu.
         * Menambahkan ring highlight biru, mengekstrak data teks, tautan, sumber gambar, kelas Tailwind CSS,
         * mem-parsing warna teks dan latar belakang arbitrary (kustom hex), membangun breadcrumbs DOM,
         * serta membuka laci panel Inspector di sisi kanan.
         * 
         * @param {HTMLElement} selectedElementNode - Elemen DOM yang ingin diseleksi
         */
        selectNode(selectedElementNode) {
            if (!selectedElementNode) return;

            // Bersihkan highlight dari node sebelumnya
            this.removeHighlight();
            
            // Set node aktif saat ini
            this.selectedNode = selectedElementNode;
            
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
            let cleanedClassesString = this.selectedNode.getAttribute('class') || '';
            cleanedClassesString = cleanedClassesString.replace(/\bring-2\b|\bring-blue-500\b|\bring-inset\b|\boutline-none\b/g, '')
                .replace(/\s+/g, ' ').trim();

            // Saring dan buang karakter '@' pada breakpoint (container queries) agar pengguna hanya melihat format standar 'md:class'
            cleanedClassesString = cleanedClassesString.replace(/@(sm|md|lg|xl|2xl):/g, '$1:');

            this.nodeData.classes = cleanedClassesString;
            this.nodeData.href = this.selectedNode.getAttribute('href') || '';
            this.nodeData.src = this.selectedNode.getAttribute('src') || '';

            // Ekstrak warna teks kustom (format arbitrer Tailwind, contoh: text-[#ff0000]) menggunakan regex
            const textColorMatchResult = cleanedClassesString.match(/text-\[\s*(#[0-9a-fA-F]{3,8})\s*\]/);
            this.nodeData.textColor = textColorMatchResult ? textColorMatchResult[1] : '#000000';

            // Ekstrak warna background kustom (format arbitrer Tailwind, contoh: bg-[#ffffff]) menggunakan regex
            const bgColorMatchResult = cleanedClassesString.match(/bg-\[\s*(#[0-9a-fA-F]{3,8})\s*\]/);
            this.nodeData.bgColor = bgColorMatchResult ? bgColorMatchResult[1] : '#ffffff';

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
            let currentNode = this.selectedNode;

            // Lakukan perulangan naik ke atas DOM tree
            while (currentNode && currentNode.id !== 'visual-canvas' && currentNode.tagName.toLowerCase() !== 'body') {
                let breadcrumbsSignature = '';
                let classNamesString = currentNode.getAttribute('class');
                if (classNamesString) {
                    // Bersihkan kelas highlight agar nama breadcrumbs bersih
                    classNamesString = classNamesString.replace(/\bring-2\b|\bring-blue-500\b|\bring-inset\b|\boutline-none\b|\bis-visible\b/g, '')
                        .replace(/\s+/g, ' ').trim();
                    
                    // Ambil maksimal 2 nama kelas pertama sebagai pembeda visual (contoh: .flex.flex-col)
                    const significantClassesList = classNamesString.split(' ').filter(classString => classString.length > 0).slice(0, 2);
                    if (significantClassesList.length > 0) {
                        breadcrumbsSignature = '.' + significantClassesList.join('.');
                    }
                }

                // Tambahkan di awal array agar urutannya dari terluar ke terdalam (breadcrumbs standard)
                this.breadcrumbs.unshift({
                    tagName: currentNode.tagName.toLowerCase(),
                    signature: breadcrumbsSignature,
                    node: currentNode // Simpan referensi ke elemen asli untuk navigasi klik
                });

                currentNode = currentNode.parentElement;
            }
        },

        /**
         * Menghapus kelas outline highlight biru (ring) dari elemen yang sedang aktif dipilih.
         * Juga membersihkan atribut class jika menjadi kosong.
         */
        removeHighlight() {
            if (this.selectedNode) {
                this.selectedNode.classList.remove('ring-2', 'ring-blue-500', 'ring-inset', 'outline-none');
                let classNamesString = this.selectedNode.getAttribute('class') || '';
                if (classNamesString.trim() === '') {
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
         * @param {string} propertyName - Nama properti ('text', 'classes', 'href', 'src')
         * @param {string} propertyValue - Nilai baru yang ingin diterapkan
         */
        updateNodeProperty(propertyName, propertyValue) {
            if (!this.selectedNode) return;

            if (propertyName === 'text' && !this.nodeData.isDynamic) {
                // Perbarui konten teks
                this.selectedNode.textContent = propertyValue;
            } else if (propertyName === 'classes') {
                // Perbarui daftar kelas Tailwind CSS
                this.removeHighlight();
                if (propertyValue.trim() === '') {
                    this.selectedNode.removeAttribute('class');
                } else {
                    let classValueForCanvas = propertyValue;
                    // Bersihkan tanda '@' terlebih dahulu (jika ada) untuk mencegah penambahan ganda
                    classValueForCanvas = classValueForCanvas.replace(/@(sm|md|lg|xl|2xl):/g, '$1:');
                    // Tambahkan kembali '@' di depan breakpoint agar dikenali sebagai Container Queries di kanvas visual
                    classValueForCanvas = classValueForCanvas.replace(/\b(sm|md|lg|xl|2xl):/g, '@$1:');
                    
                    this.selectedNode.setAttribute('class', classValueForCanvas);
                }
                // Kembalikan ring highlight agar penanda fokus tidak hilang
                this.selectedNode.classList.add('ring-2', 'ring-blue-500', 'ring-inset', 'outline-none');
            } else if (propertyName === 'href') {
                // Perbarui tautan URL
                if (propertyValue) this.selectedNode.setAttribute('href', propertyValue);
                else this.selectedNode.removeAttribute('href');
            } else if (propertyName === 'src') {
                // Perbarui sumber gambar
                if (propertyValue) this.selectedNode.setAttribute('src', propertyValue);
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
                const clonedElementNode = this.selectedNode.cloneNode(true);

                // Bersihkan penanda ring seleksi biru pada anak elemen di dalam clone
                const highlightedChildNode = clonedElementNode.querySelector('.ring-blue-500');
                if (highlightedChildNode) {
                    highlightedChildNode.classList.remove('ring-2', 'ring-blue-500', 'ring-inset', 'outline-none');
                    let classString = highlightedChildNode.getAttribute('class') || '';
                    if (classString.trim() === '') {
                        highlightedChildNode.removeAttribute('class');
                    }
                }
                
                // Bersihkan penanda ring seleksi biru pada elemen terluar clone itu sendiri
                if (clonedElementNode.classList.contains('ring-blue-500')) {
                    clonedElementNode.classList.remove('ring-2', 'ring-blue-500', 'ring-inset', 'outline-none');
                    let classString = clonedElementNode.getAttribute('class') || '';
                    if (classString.trim() === '') clonedElementNode.removeAttribute('class');
                }

                // Sisipkan clone sebagai string HTML agar AlpineJS memprosesnya dengan bersih
                this.selectedNode.insertAdjacentHTML('afterend', clonedElementNode.outerHTML);

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
        async deleteSelectedNode() {
            if (!this.selectedNode) return;

            const result = await Swal.fire({
                title: 'Hapus Elemen Ini?',
                text: 'Elemen yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            this.selectedNode.remove();
            this.closeInspector();
            window.syncToMonaco();
        },

        /**
         * Menambah atau menghapus kelas Tailwind secara spesifik.
         * Berguna untuk tombol toggle/switch pada panel properties (misal font-weight, text-align).
         * 
         * @param {string} tailwindClassToToggle - Kelas CSS baru yang ingin di-toggle (tambah jika belum ada, hapus jika sudah ada)
         * @param {Array<string>} tailwindClassesToRemove - Daftar kelas yang harus dihapus jika classToAdd sedang ditambahkan (misalnya untuk reset opsi alignment yang bertabrakan)
         */
        toggleTailwindClass(tailwindClassToToggle, tailwindClassesToRemove = []) {
            if (!this.selectedNode) return;

            // Pecah kelas saat ini menjadi array
            const currentClassesList = (this.nodeData.classes || '').split(' ').filter(classString => classString.trim() !== '');

            // Buang semua kelas yang saling bertabrakan (ada di daftar classesToRemove)
            const updatedClassesList = currentClassesList.filter(classString => !tailwindClassesToRemove.includes(classString));

            const targetClassIndex = updatedClassesList.indexOf(tailwindClassToToggle);
            if (targetClassIndex > -1) {
                // Hapus jika sudah ada (toggle off)
                updatedClassesList.splice(targetClassIndex, 1);
            } else {
                // Tambah jika belum ada (toggle on)
                updatedClassesList.push(tailwindClassToToggle);
            }

            this.nodeData.classes = updatedClassesList.join(' ');
            this.updateNodeProperty('classes', this.nodeData.classes);
        },

        /**
         * Memperbarui warna elemen menggunakan sintaks arbitrer warna kustom Tailwind CSS.
         * Misalnya mengubah warna teks menjadi text-[#ff0000] atau background menjadi bg-[#00ff00].
         * Fungsi ini membersihkan warna kustom lama dengan awalan prefix yang sama sebelum menambahkan yang baru.
         * 
         * @param {string} tailwindColorPrefix - Awalan properti ('text' atau 'bg')
         * @param {string} hexColorCode - Kode warna hex kustom (contoh: '#ffaadd')
         */
        updateArbitraryColor(tailwindColorPrefix, hexColorCode) {
            if (!this.selectedNode) return;
            let currentClassesList = (this.nodeData.classes || '').split(' ').filter(classString => classString.trim() !== '');
            
            // Regex untuk mendeteksi warna arbitrer format kustom lama (contoh: text-[#ffffff])
            const arbitraryColorRegex = new RegExp('^' + tailwindColorPrefix + '-\\\\[#[0-9a-fA-F]{3,8}\\\\]$');
            
            // Warna static bawaan Tailwind yang juga harus dibuang agar tidak terjadi konflik
            const standardTailwindColors = ['white', 'black', 'transparent'];
            
            // Saring kelas untuk membuang warna arbitrer atau static lama pada prefix ini
            currentClassesList = currentClassesList.filter(classString => {
                const colorNamePart = classString.replace(tailwindColorPrefix + '-', '');
                return !arbitraryColorRegex.test(classString) && !standardTailwindColors.includes(colorNamePart);
            });
            
            // Masukkan kelas warna baru dalam format arbitrer Tailwind
            currentClassesList.push(tailwindColorPrefix + '-[' + hexColorCode + ']');
            
            this.nodeData.classes = currentClassesList.join(' ');
            this.updateNodeProperty('classes', this.nodeData.classes);
        }
    };
}
