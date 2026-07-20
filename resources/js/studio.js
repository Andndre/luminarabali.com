// Bundel Studio: sebelumnya Alpine, SweetAlert, dan Sortable dimuat dari tiga CDN
// terpisah di layouts/studio.blade.php dan studio.blade.php. Ketiganya sudah jadi
// dependensi npm, jadi tak ada alasan editor internal bergantung pada jaringan luar.
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import Sortable from 'sortablejs';

// studioApp() memanggil ketiganya lewat nama global — tetap global, bukan diimpor
// per pemakaian, karena logikanya hidup di <script> inline di dalam Blade.
window.Alpine = Alpine;
window.Swal = Swal;
window.Sortable = Sortable;

// Aman dijalankan di sini: modul ini defer, jadi <script> inline yang mendefinisikan
// studioApp() sudah dieksekusi saat parsing badan halaman, sebelum baris ini.
Alpine.start();
