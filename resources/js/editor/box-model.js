/**
 * Modul Box Model Editor (box-model.js)
 * 
 * Modul ini berfungsi sebagai pengelola state tata letak fisik elemen (Margin, Padding,
 * Border Width, dan Border Radius/Corners). Modul ini melakukan parsing dinamis terhadap
 * kelas Tailwind CSS yang menempel pada elemen DOM, lalu merekonstruksi nilai masing-masing sisi,
 * serta merakit kembali kelas-kelas baru dengan menerapkan aturan singkatan Tailwind secara cerdas
 * (misal jika atas/bawah/kiri/kanan bernilai sama, dirakit menjadi shorthand 'p-4' daripada 'pt-4 pb-4 pl-4 pr-4').
 */
export default function EditorBoxModel() {
    return {
        /**
         * Memperbarui daftar kelas box model (Padding/Margin/Border/Radius) pada elemen terpilih.
         * Metode ini menyatukan updates baru dengan state lama elemen, lalu menganalisis kesamaan nilai antar-sisi
         * untuk merakit kelas shorthand Tailwind yang ringkas (seperti -x, -y, atau global shorthand).
         * 
         * @param {string} type - Jenis Box Model ('p' untuk padding, 'm' untuk margin, 'border' untuk border width, 'rounded' untuk border radius)
         * @param {Object} updates - Objek berisi pasangan kunci-nilai sisi yang diperbarui (misal {t: '4', b: '4'})
         */
        updateBoxState(type, updates) {
            if (!this.selectedNode) return;

            let state = {};
            // Rounded corners menggunakan singkatan sudut (top-left, dsb), sedangkan yang lainnya menggunakan t, b, l, r (top, bottom, left, right)
            const sides = (type === 'rounded') ? ['tl', 'tr', 'bl', 'br'] : ['t', 'b', 'l', 'r'];
            
            // Baca nilai yang ada saat ini dari elemen DOM
            sides.forEach(s => { 
                const val = this.getBoxValue(type, s);
                if (val && val !== '') state[s] = val;
            });

            // Timpa state lama dengan perubahan (updates) yang dikirim
            Object.keys(updates).forEach(k => {
                if (updates[k] === null || updates[k] === '') delete state[k];
                else state[k] = updates[k];
            });

            let newClasses = [];

            // Helper untuk merakit kelas Tailwind. Menangani penempatan awalan tanda minus untuk nilai negatif (misal -mt-4 atau -mt-[15px])
            const buildClass = (prefix, val) => {
                if (val === 'default') return prefix;
                const strVal = String(val);
                const isNeg = strVal.startsWith('-');
                const absVal = isNeg ? strVal.substring(1) : strVal;
                return (isNeg ? '-' : '') + prefix + '-' + absVal;
            };

            // PROSES SHORTHAND SINKRONISASI UNTUK PADDING (p), MARGIN (m), DAN BORDER WIDTH (border)
            if (type === 'p' || type === 'm' || type === 'border') {
                const prefix = (type === 'border') ? 'border' : type;
                const sep = (type === 'border') ? '-' : '';
                
                // Aturan 1: Jika keempat sisi bernilai sama -> dirakit menjadi kelas global (misal: p-4)
                if (state.t && state.t === state.b && state.t === state.l && state.t === state.r) {
                    newClasses.push(buildClass(prefix, state.t));
                } else {
                    // Aturan 2: Jika atas dan bawah sama -> gunakan shorthand sumbu Y (misal: py-2)
                    if (state.t && state.t === state.b) {
                        newClasses.push(buildClass(prefix + sep + 'y', state.t));
                        delete state.t; delete state.b;
                    }
                    // Aturan 3: Jika kiri dan kanan sama -> gunakan shorthand sumbu X (misal: px-4)
                    if (state.l && state.l === state.r) {
                        newClasses.push(buildClass(prefix + sep + 'x', state.l));
                        delete state.l; delete state.r;
                    }
                    // Aturan 4: Sisi yang tersisa ditulis secara individual (misal: pt-1, pb-3)
                    if (state.t) newClasses.push(buildClass(prefix + sep + 't', state.t));
                    if (state.b) newClasses.push(buildClass(prefix + sep + 'b', state.b));
                    if (state.l) newClasses.push(buildClass(prefix + sep + 'l', state.l));
                    if (state.r) newClasses.push(buildClass(prefix + sep + 'r', state.r));
                }
            } 
            // PROSES SHORTHAND SINKRONISASI UNTUK CORNERS / BORDER RADIUS (rounded)
            else if (type === 'rounded') {
                // Aturan 1: Jika keempat sudut sama -> gunakan rounded global (misal: rounded-lg)
                if (state.tl && state.tl === state.tr && state.tl === state.bl && state.tl === state.br) {
                    newClasses.push(buildClass('rounded', state.tl));
                } else {
                    // Aturan 2: Jika sudut-sudut atas sama -> gunakan rounded-t (misal: rounded-t-xl)
                    if (state.tl && state.tl === state.tr) {
                        newClasses.push(buildClass('rounded-t', state.tl));
                        delete state.tl; delete state.tr;
                    } 
                    // Aturan 3: Jika sudut-sudut bawah sama -> gunakan rounded-b (misal: rounded-b-md)
                    else if (state.bl && state.bl === state.br) {
                        newClasses.push(buildClass('rounded-b', state.bl));
                        delete state.bl; delete state.br;
                    }
                    // Aturan 4: Jika sudut-sudut kiri sama -> gunakan rounded-l
                    if (state.tl && state.tl === state.bl) {
                        newClasses.push(buildClass('rounded-l', state.tl));
                        delete state.tl; delete state.bl;
                    } 
                    // Aturan 5: Jika sudut-sudut kanan sama -> gunakan rounded-r
                    else if (state.tr && state.tr === state.br) {
                        newClasses.push(buildClass('rounded-r', state.tr));
                        delete state.tr; delete state.br;
                    }
                    // Aturan 6: Sudut tersisa ditulis secara individual (misal: rounded-tl-sm)
                    if (state.tl) newClasses.push(buildClass('rounded-tl', state.tl));
                    if (state.tr) newClasses.push(buildClass('rounded-tr', state.tr));
                    if (state.bl) newClasses.push(buildClass('rounded-bl', state.bl));
                    if (state.br) newClasses.push(buildClass('rounded-br', state.br));
                }
            }

            // Dapatkan daftar seluruh kelas elemen saat ini, lalu saring (buang) semua kelas Box Model sejenis yang lama
            let classes = (this.nodeData.classes || '').split(' ').filter(c => c.trim() !== '');
            classes = classes.filter(c => {
                const cleanC = c.startsWith('-') ? c.substring(1) : c;
                const cleanBase = cleanC.split('-')[0];
                
                if (type === 'rounded') {
                    return !cleanC.startsWith('rounded');
                } else if (type === 'p' || type === 'm' || type === 'border') {
                    return cleanBase !== type && !cleanC.startsWith(type + '-');
                }
                return true;
            });

            // Masukkan gabungan kelas shorthand/individual baru ke dalam elemen
            classes.push(...newClasses);
            this.nodeData.classes = classes.join(' ');
            
            // Perbarui visual kanvas & sinkronkan ke Monaco Editor
            this.updateNodeProperty('classes', this.nodeData.classes);
        },

        /**
         * Mengatur nilai box model untuk sisi tertentu berdasarkan input pengguna.
         * Mendeteksi constraint kunci global (Global Lock) maupun penekanan tombol keyboard Alt/Option
         * untuk memproses X/Y locks secara instan (seperti di Webflow).
         * Juga secara cerdas mengonversi angka mentah tanpa unit menjadi format kurung siku arbitrer Tailwind,
         * misalnya input '12px' otomatis menjadi '[12px]', sedangkan input utility seperti '4' atau 'auto' tetap dibiarkan.
         * 
         * @param {string} type - Jenis Box Model ('p', 'm', 'border', 'rounded')
         * @param {string} side - Nama sisi yang diubah (misal 't', 'b', 'tl', dsb)
         * @param {string|null} val - Nilai input baru yang dimasukkan pengguna
         * @param {Event|null} event - Event dari input element (untuk mendeteksi penekanan tombol Alt)
         */
        setBoxValue(type, side, val, event = null) {
            if (!val || val.trim() === '') {
                val = null;
            } else {
                val = val.trim();
                let isNegative = false;

                // Tangani input bernilai negatif (biasanya pada margin)
                if (val.startsWith('-')) {
                    isNegative = true;
                    val = val.substring(1);
                }

                // Cek format input. Jika input berisi angka mentah yang bukan utility bawaan Tailwind,
                // bungkus dalam tanda kurung siku arbitrer (contoh: 15px -> [15px], 3rem -> [3rem])
                if (!val.includes('[')) {
                    if (/^\d+(\.\d+)?$/.test(val) || ['auto', 'none', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', 'full'].includes(val)) {
                        // Biarkan nilai angka mentah (seperti 4, 1.5) atau utility bawaan Tailwind tetap bersih
                    } else if (val !== 'default') {
                        val = '[' + val + ']';
                    }
                }
                
                // Kembalikan awalan tanda minus jika nilai awal adalah negatif
                if (isNegative && val !== 'default') {
                    val = '-' + val;
                }
            }

            let updates = {};
            // Periksa apakah Global Lock untuk jenis Box Model ini sedang aktif
            let isGlobal = this.constraints[type + 'Global'];
            // Periksa penekanan tombol Alt pada keyboard untuk penguncian simetris (Y/X lock)
            let isAlt = event && event.altKey;
            const sides = (type === 'rounded') ? ['tl', 'tr', 'bl', 'br'] : ['t', 'b', 'l', 'r'];

            if (isGlobal) {
                // Aturan 1: Global Lock aktif -> Terapkan nilai yang sama ke seluruh 4 sisi
                sides.forEach(s => updates[s] = val);
            } else if (isAlt) {
                // Aturan 2: Alt Key ditekan -> Penguncian Simetris aktif (Y/X lock)
                if (type === 'rounded') {
                    if (side === 'tl' || side === 'tr') { updates.tl = val; updates.tr = val; }
                    else if (side === 'bl' || side === 'br') { updates.bl = val; updates.br = val; }
                } else {
                    if (side === 't' || side === 'b') { updates.t = val; updates.b = val; }
                    else if (side === 'l' || side === 'r') { updates.l = val; updates.r = val; }
                }
            } else {
                // Aturan 3: Normal -> Hanya perbarui sisi yang diklik/diubah saja
                updates[side] = val;
            }

            this.updateBoxState(type, updates);
        },

        /**
         * Mengekstrak nilai Box Model dari kelas Tailwind CSS yang menempel pada elemen terpilih.
         * Mendukung pembacaan kelas individual maupun kelas shorthand (sumbu X/Y atau global).
         * Contoh: Jika elemen memiliki kelas `px-4`, maka getBoxValue('p', 'l') dan getBoxValue('p', 'r') keduanya akan mengembalikan '4'.
         * 
         * @param {string} type - Jenis Box Model ('p', 'm', 'border', 'rounded')
         * @param {string} side - Nama sisi (misal 't', 'b', 'tl', dsb)
         * @returns {string} Nilai sisi yang berhasil di-parse (atau string kosong jika tidak ditemukan)
         */
        getBoxValue(type, side) {
            const classes = (this.nodeData.classes || '').split(' ');

            // Helper internal untuk mencocokkan regex dan mengekstrak nilai di belakang nama kelas
            const extractValue = (prefix) => {
                // Regex mencocokkan awalan kelas (bisa negatif) diikuti nama prefix
                const regex = new RegExp('^-?' + prefix + '(?:-|$)');
                const match = classes.find(c => regex.test(c));
                if (!match) return null;
                
                const isNegative = match.startsWith('-');
                const replaceRegex = new RegExp('^-?' + prefix + '-?');
                let val = match.replace(replaceRegex, '');
                
                if (val === '') val = 'default';
                return (isNegative ? '-' : '') + val;
            };

            // PARSING UNTUK PADDING (p) DAN MARGIN (m)
            if (type === 'p' || type === 'm') {
                const axis = (side === 't' || side === 'b') ? 'y' : 'x';
                
                // 1. Cek kelas spesifik sisi (misal: pt-4 atau -mr-2)
                let val = extractValue(type + side);
                if (val !== null) return val;
                
                // 2. Cek kelas shorthand sumbu (misal: py-4 atau mx-2)
                val = extractValue(type + axis);
                if (val !== null) return val;
                
                // 3. Cek kelas global (misal: p-4 atau m-auto)
                val = extractValue(type);
                return val !== null ? val : '';
            }

            // PARSING UNTUK BORDER WIDTH (border)
            if (type === 'border') {
                const axis = (side === 't' || side === 'b') ? 'y' : 'x';
                
                // 1. Cek kelas spesifik sisi (misal: border-t-2)
                let val = extractValue('border-' + side);
                if (val !== null) return val;
                
                // 2. Cek kelas shorthand sumbu (misal: border-y-2)
                val = extractValue('border-' + axis);
                if (val !== null) return val;
                
                // 3. Cek kelas global (misal: border-4 atau border)
                val = extractValue('border');
                return val !== null ? val : '';
            }

            // PARSING UNTUK BORDER RADIUS CORNERS (rounded)
            if (type === 'rounded') {
                // 1. Cek kelas sudut individual (misal: rounded-tl-lg)
                let val = extractValue('rounded-' + side);
                if (val !== null) return val;
                
                // 2. Cek kelas tepi horizontal/vertikal (misal: jika mencari 'tl', cek rounded-t dan rounded-l)
                let edge1 = null, edge2 = null;
                if (side === 'tl') { edge1 = 't'; edge2 = 'l'; }
                else if (side === 'tr') { edge1 = 't'; edge2 = 'r'; }
                else if (side === 'bl') { edge1 = 'b'; edge2 = 'l'; }
                else if (side === 'br') { edge1 = 'b'; edge2 = 'r'; }

                if (edge1 !== null) {
                    val = extractValue('rounded-' + edge1);
                    if (val !== null) return val;
                }
                if (edge2 !== null) {
                    val = extractValue('rounded-' + edge2);
                    if (val !== null) return val;
                }
                
                // 3. Cek kelas global (misal: rounded-3xl atau rounded)
                val = extractValue('rounded');
                return val !== null ? val : '';
            }

            return '';
        }
    };
}
