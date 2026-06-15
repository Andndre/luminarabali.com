/**
 * Modul Inti Editor (core.js)
 * 
 * Modul ini mendefinisikan state (keadaan) dasar dan metode fundamental untuk aplikasi editor,
 * seperti kontrol panel (tampilan perpustakaan, kanvas visual, editor kode, properti halaman),
 * penampung data dummy untuk variabel AlpineJS (x-text) agar mempermudah pratinjau,
 * serta state penyeleksian elemen (selectedNode, nodeData) dan lock global untuk box model constraints.
 */
export default function EditorCore() {
    return {
        // Status visibilitas panel-panel di halaman Editor
        panels: {
            library: true,      // Panel perpustakaan komponen (sisi kiri)
            visual: true,       // Panel kanvas pratinjau visual (tengah)
            code: false,        // Panel editor kode Monaco (sisi kanan)
            properties: false   // Panel properti halaman/meta-data (sisi kanan)
        },
        
        // Status sinkronisasi agar tidak terjadi infinite loop saat sinkronisasi visual <-> kode
        isSyncing: false,
        
        // Timer debounce untuk mendeteksi jeda ketik pengguna di Monaco Editor
        typingTimer: null,
        
        // Status visibilitas panel Inspector elemen mikro (sisi kanan melayang)
        isInspectorOpen: false,

        /**
         * Mengalihkan status tampil/sembunyi suatu panel (library, visual, code, atau properties).
         * Khusus panel 'code', jika dibuka maka Monaco Editor akan diperintahkan untuk me-layout ulang
         * ukuran dimensinya agar pas dengan sisa ruang layar yang tersedia setelah transisi selesai.
         * 
         * @param {string} panelIdentifier - Nama panel yang ingin di-toggle
         */
        toggleView(panelIdentifier) {
            this.panels[panelIdentifier] = !this.panels[panelIdentifier];
            if (panelIdentifier === 'code' && this.panels.code && window.globalEditor) {
                // Berikan jeda waktu 350ms agar animasi transisi layout flexbox selesai sebelum mendeteksi ukuran baru
                setTimeout(() => window.globalEditor.layout(), 350);
            }
        },

        // Node target visual tempat komponen perpustakaan akan disisipkan (insert after)
        insertTargetNode: null,

        /**
         * Sinkronisasi pra-simpan (pre-save sync). Dipanggil sebelum form disubmit
         * untuk memastikan konten visual terbaru sudah ditulis ulang ke dalam Monaco Editor.
         */
        preSaveSync() {
            window.syncToMonaco();
        },
        
        // --- INVITATION EDITOR STATE MERGED ---
        // Data palsu (dummy data) yang digunakan untuk mengisi variabel dinamis x-text di dalam editor
        // sehingga admin bisa melihat tampilan akhir dengan teks yang realistis
        groom_name: 'Romeo',
        bride_name: 'Juliet',
        event_date: '2026-12-12T08:00:00',
        guest_name: 'Budi (Tamu VIP)',

        // State untuk elemen mikro yang sedang dipilih/diklik oleh user di kanvas visual
        selectedNode: null,
        
        // Objek penampung properti elemen mikro yang terpilih (digunakan di Inspector Panel)
        nodeData: {
            tagName: '',        // Nama tag elemen (misalnya: 'DIV', 'H1', 'P', 'IMG')
            text: '',           // Teks konten di dalam elemen (hanya jika elemen tidak memiliki anak/leaf node)
            classes: '',        // Daftar kelas Tailwind CSS yang menempel pada elemen
            href: '',           // Atribut tautan URL (jika berupa elemen anchor <a>)
            src: '',            // Sumber media URL (jika berupa elemen gambar <img>)
            isDynamic: false,   // Penanda apakah elemen menggunakan teks dinamis (x-text)
            textColor: '#000000', // Warna teks saat ini (hex format dari inline arbitrary class, misal text-[#aabbcc])
            bgColor: '#ffffff'    // Warna latar saat ini (hex format dari inline arbitrary class, misal bg-[#aabbcc])
        },
        
        // Status lock (kunci) global untuk Box Model (Margin, Padding, Border Width, Border Radius).
        // Jika true (terkunci), pengisian nilai pada satu sisi (misalnya top) akan diduplikasikan ke seluruh sisi lainnya.
        constraints: {
            paddingGlobal: false,
            marginGlobal: false,
            radiusGlobal: false,
            borderGlobal: false
        }
    };
}
