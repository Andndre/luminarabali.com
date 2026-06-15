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
         * @param {Object} updatedValues - Objek berisi pasangan kunci-nilai sisi yang diperbarui (misal {t: '4', b: '4'})
         */
        updateBoxState(type, updatedValues) {
            if (!this.selectedNode) return;

            let boxModelState = {};
            // Rounded corners menggunakan singkatan sudut (top-left, dsb), sedangkan yang lainnya menggunakan t, b, l, r (top, bottom, left, right)
            const boxSides = (type === 'rounded') ? ['tl', 'tr', 'bl', 'br'] : ['t', 'b', 'l', 'r'];
            
            // Baca nilai yang ada saat ini dari elemen DOM
            boxSides.forEach(sideKey => { 
                const sideValue = this.getBoxValue(type, sideKey);
                if (sideValue && sideValue !== '') boxModelState[sideKey] = sideValue;
            });

            // Timpa state lama dengan perubahan (updatedValues) yang dikirim
            Object.keys(updatedValues).forEach(updatedSideKey => {
                if (updatedValues[updatedSideKey] === null || updatedValues[updatedSideKey] === '') delete boxModelState[updatedSideKey];
                else boxModelState[updatedSideKey] = updatedValues[updatedSideKey];
            });

            let newClasses = [];

            // Helper untuk merakit kelas Tailwind. Menangani penempatan awalan tanda minus untuk nilai negatif (misal -mt-4 atau -mt-[15px])
            const buildTailwindClass = (tailwindClassPrefix, targetValue) => {
                if (targetValue === 'default') return tailwindClassPrefix;
                const strVal = String(targetValue);
                const isNegativeValue = strVal.startsWith('-');
                const absoluteValue = isNegativeValue ? strVal.substring(1) : strVal;
                return (isNegativeValue ? '-' : '') + tailwindClassPrefix + '-' + absoluteValue;
            };

            // PROSES SHORTHAND SINKRONISASI UNTUK PADDING (p), MARGIN (m), DAN BORDER WIDTH (border)
            if (type === 'p' || type === 'm' || type === 'border') {
                const tailwindClassPrefix = (type === 'border') ? 'border' : type;
                const prefixSeparator = (type === 'border') ? '-' : '';
                
                // Aturan 1: Jika keempat sisi bernilai sama -> dirakit menjadi kelas global (misal: p-4)
                if (boxModelState.t && boxModelState.t === boxModelState.b && boxModelState.t === boxModelState.l && boxModelState.t === boxModelState.r) {
                    newClasses.push(buildTailwindClass(tailwindClassPrefix, boxModelState.t));
                } else {
                    // Aturan 2: Jika atas dan bawah sama -> gunakan shorthand sumbu Y (misal: py-2)
                    if (boxModelState.t && boxModelState.t === boxModelState.b) {
                        newClasses.push(buildTailwindClass(tailwindClassPrefix + prefixSeparator + 'y', boxModelState.t));
                        delete boxModelState.t; delete boxModelState.b;
                    }
                    // Aturan 3: Jika kiri dan kanan sama -> gunakan shorthand sumbu X (misal: px-4)
                    if (boxModelState.l && boxModelState.l === boxModelState.r) {
                        newClasses.push(buildTailwindClass(tailwindClassPrefix + prefixSeparator + 'x', boxModelState.l));
                        delete boxModelState.l; delete boxModelState.r;
                    }
                    // Aturan 4: Sisi yang tersisa ditulis secara individual (misal: pt-1, pb-3)
                    if (boxModelState.t) newClasses.push(buildTailwindClass(tailwindClassPrefix + prefixSeparator + 't', boxModelState.t));
                    if (boxModelState.b) newClasses.push(buildTailwindClass(tailwindClassPrefix + prefixSeparator + 'b', boxModelState.b));
                    if (boxModelState.l) newClasses.push(buildTailwindClass(tailwindClassPrefix + prefixSeparator + 'l', boxModelState.l));
                    if (boxModelState.r) newClasses.push(buildTailwindClass(tailwindClassPrefix + prefixSeparator + 'r', boxModelState.r));
                }
            } 
            // PROSES SHORTHAND SINKRONISASI UNTUK CORNERS / BORDER RADIUS (rounded)
            else if (type === 'rounded') {
                // Aturan 1: Jika keempat sudut sama -> gunakan rounded global (misal: rounded-lg)
                if (boxModelState.tl && boxModelState.tl === boxModelState.tr && boxModelState.tl === boxModelState.bl && boxModelState.tl === boxModelState.br) {
                    newClasses.push(buildTailwindClass('rounded', boxModelState.tl));
                } else {
                    // Aturan 2: Jika sudut-sudut atas sama -> gunakan rounded-t (misal: rounded-t-xl)
                    if (boxModelState.tl && boxModelState.tl === boxModelState.tr) {
                        newClasses.push(buildTailwindClass('rounded-t', boxModelState.tl));
                        delete boxModelState.tl; delete boxModelState.tr;
                    } 
                    // Aturan 3: Jika sudut-sudut bawah sama -> gunakan rounded-b (misal: rounded-b-md)
                    else if (boxModelState.bl && boxModelState.bl === boxModelState.br) {
                        newClasses.push(buildTailwindClass('rounded-b', boxModelState.bl));
                        delete boxModelState.bl; delete boxModelState.br;
                    }
                    // Aturan 4: Jika sudut-sudut kiri sama -> gunakan rounded-l
                    if (boxModelState.tl && boxModelState.tl === boxModelState.bl) {
                        newClasses.push(buildTailwindClass('rounded-l', boxModelState.tl));
                        delete boxModelState.tl; delete boxModelState.bl;
                    } 
                    // Aturan 5: Jika sudut-sudut kanan sama -> gunakan rounded-r
                    else if (boxModelState.tr && boxModelState.tr === boxModelState.br) {
                        newClasses.push(buildTailwindClass('rounded-r', boxModelState.tr));
                        delete boxModelState.tr; delete boxModelState.br;
                    }
                    // Aturan 6: Sudut tersisa ditulis secara individual (misal: rounded-tl-sm)
                    if (boxModelState.tl) newClasses.push(buildTailwindClass('rounded-tl', boxModelState.tl));
                    if (boxModelState.tr) newClasses.push(buildTailwindClass('rounded-tr', boxModelState.tr));
                    if (boxModelState.bl) newClasses.push(buildTailwindClass('rounded-bl', boxModelState.bl));
                    if (boxModelState.br) newClasses.push(buildTailwindClass('rounded-br', boxModelState.br));
                }
            }

            // Dapatkan daftar seluruh kelas elemen saat ini, lalu saring (buang) semua kelas Box Model sejenis yang lama
            let tailwindClassesList = (this.nodeData.classes || '').split(' ').filter(tailwindClass => tailwindClass.trim() !== '');
            tailwindClassesList = tailwindClassesList.filter(tailwindClass => {
                const normalizedClass = tailwindClass.startsWith('-') ? tailwindClass.substring(1) : tailwindClass;
                const classPrefixBase = normalizedClass.split('-')[0];
                
                if (type === 'rounded') {
                    return !normalizedClass.startsWith('rounded');
                } else if (type === 'p' || type === 'm' || type === 'border') {
                    return classPrefixBase !== type && !normalizedClass.startsWith(type + '-');
                }
                return true;
            });

            // Masukkan gabungan kelas shorthand/individual baru ke dalam elemen
            tailwindClassesList.push(...newClasses);
            this.nodeData.classes = tailwindClassesList.join(' ');
            
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
         * @param {string|null} inputValue - Nilai input baru yang dimasukkan pengguna
         * @param {Event|null} event - Event dari input element (untuk mendeteksi penekanan tombol Alt)
         */
        setBoxValue(type, side, inputValue, event = null) {
            if (!inputValue || inputValue.trim() === '') {
                inputValue = null;
            } else {
                inputValue = inputValue.trim();
                let isNegativeValue = false;

                // Tangani input bernilai negatif (biasanya pada margin)
                if (inputValue.startsWith('-')) {
                    isNegativeValue = true;
                    inputValue = inputValue.substring(1);
                }

                // Cek format input. Jika input berisi angka mentah yang bukan utility bawaan Tailwind,
                // bungkus dalam tanda kurung siku arbitrer (contoh: 15px -> [15px], 3rem -> [3rem])
                if (!inputValue.includes('[')) {
                    if (/^\d+(\.\d+)?$/.test(inputValue) || ['auto', 'none', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', 'full'].includes(inputValue)) {
                        // Biarkan nilai angka mentah (seperti 4, 1.5) atau utility bawaan Tailwind tetap bersih
                    } else if (inputValue !== 'default') {
                        inputValue = '[' + inputValue + ']';
                    }
                }
                
                // Kembalikan awalan tanda minus jika nilai awal adalah negatif
                if (isNegativeValue && inputValue !== 'default') {
                    inputValue = '-' + inputValue;
                }
            }

            let updates = {};
            // Periksa apakah Global Lock untuk jenis Box Model ini sedang aktif
            let isGlobalLockActive = this.constraints[type + 'Global'];
            // Periksa penekanan tombol Alt pada keyboard untuk penguncian simetris (Y/X lock)
            let isAltKeyPressed = event && event.altKey;
            const sides = (type === 'rounded') ? ['tl', 'tr', 'bl', 'br'] : ['t', 'b', 'l', 'r'];

            if (isGlobalLockActive) {
                // Aturan 1: Global Lock aktif -> Terapkan nilai yang sama ke seluruh 4 sisi
                sides.forEach(s => updates[s] = inputValue);
            } else if (isAltKeyPressed) {
                // Aturan 2: Alt Key ditekan -> Penguncian Simetris aktif (Y/X lock)
                if (type === 'rounded') {
                    if (side === 'tl' || side === 'tr') { updates.tl = inputValue; updates.tr = inputValue; }
                    else if (side === 'bl' || side === 'br') { updates.bl = inputValue; updates.br = inputValue; }
                } else {
                    if (side === 't' || side === 'b') { updates.t = inputValue; updates.b = inputValue; }
                    else if (side === 'l' || side === 'r') { updates.l = inputValue; updates.r = inputValue; }
                }
            } else {
                // Aturan 3: Normal -> Hanya perbarui sisi yang diklik/diubah saja
                updates[side] = inputValue;
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
            const tailwindClassesList = (this.nodeData.classes || '').split(' ');

            // Helper internal untuk mencocokkan regex dan mengekstrak nilai di belakang nama kelas
            const extractValueFromClasses = (tailwindClassPrefix) => {
                // Regex mencocokkan awalan kelas (bisa negatif) diikuti nama prefix
                const regex = new RegExp('^-?' + tailwindClassPrefix + '(?:-|$)');
                const matchingClass = tailwindClassesList.find(classString => regex.test(classString));
                if (!matchingClass) return null;
                
                const isNegativeClass = matchingClass.startsWith('-');
                const stripPrefixRegex = new RegExp('^-?' + tailwindClassPrefix + '-?');
                let tailwindClassValue = matchingClass.replace(stripPrefixRegex, '');
                
                if (tailwindClassValue === '') tailwindClassValue = 'default';
                return (isNegativeClass ? '-' : '') + tailwindClassValue;
            };

            // PARSING UNTUK PADDING (p) DAN MARGIN (m)
            if (type === 'p' || type === 'm') {
                const shorthandAxis = (side === 't' || side === 'b') ? 'y' : 'x';
                
                // 1. Cek kelas spesifik sisi (misal: pt-4 atau -mr-2)
                let inputValue = extractValueFromClasses(type + side);
                if (inputValue !== null) return inputValue;
                
                // 2. Cek kelas shorthand sumbu (misal: py-4 atau mx-2)
                inputValue = extractValueFromClasses(type + shorthandAxis);
                if (inputValue !== null) return inputValue;
                
                // 3. Cek kelas global (misal: p-4 atau m-auto)
                inputValue = extractValueFromClasses(type);
                return inputValue !== null ? inputValue : '';
            }

            // PARSING UNTUK BORDER WIDTH (border)
            if (type === 'border') {
                const shorthandAxis = (side === 't' || side === 'b') ? 'y' : 'x';
                
                // 1. Cek kelas spesifik sisi (misal: border-t-2)
                let inputValue = extractValueFromClasses('border-' + side);
                if (inputValue !== null) return inputValue;
                
                // 2. Cek kelas shorthand sumbu (misal: border-y-2)
                inputValue = extractValueFromClasses('border-' + shorthandAxis);
                if (inputValue !== null) return inputValue;
                
                // 3. Cek kelas global (misal: border-4 atau border)
                inputValue = extractValueFromClasses('border');
                return inputValue !== null ? inputValue : '';
            }

            // PARSING UNTUK BORDER RADIUS CORNERS (rounded)
            if (type === 'rounded') {
                // 1. Cek kelas sudut individual (misal: rounded-tl-lg)
                let inputValue = extractValueFromClasses('rounded-' + side);
                if (inputValue !== null) return inputValue;
                
                // 2. Cek kelas tepi horizontal/vertikal (misal: jika mencari 'tl', cek rounded-t dan rounded-l)
                let adjacentEdge1 = null, adjacentEdge2 = null;
                if (side === 'tl') { adjacentEdge1 = 't'; adjacentEdge2 = 'l'; }
                else if (side === 'tr') { adjacentEdge1 = 't'; adjacentEdge2 = 'r'; }
                else if (side === 'bl') { adjacentEdge1 = 'b'; adjacentEdge2 = 'l'; }
                else if (side === 'br') { adjacentEdge1 = 'b'; adjacentEdge2 = 'r'; }

                if (adjacentEdge1 !== null) {
                    inputValue = extractValueFromClasses('rounded-' + adjacentEdge1);
                    if (inputValue !== null) return inputValue;
                }
                if (adjacentEdge2 !== null) {
                    inputValue = extractValueFromClasses('rounded-' + adjacentEdge2);
                    if (inputValue !== null) return inputValue;
                }
                
                // 3. Cek kelas global (misal: rounded-3xl atau rounded)
                inputValue = extractValueFromClasses('rounded');
                return inputValue !== null ? inputValue : '';
            }

            return '';
        }
    };
}
