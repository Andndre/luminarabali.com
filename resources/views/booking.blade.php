<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <x-seo title="Booking Photobooth - Luminara Photobooth Bali"
        description="Booking photobooth dan 360 video booth untuk acara Anda di Bali. Proses mudah, pembayaran aman via Midtrans, dan konfirmasi cepat."
        keywords="booking photobooth bali, sewa photobooth, pesan photobooth online, booking 360 video booth"
        og_image="/images/logo.png" :noindex="true" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .flatpickr-day.selected {
            background: #D4AF37 !important;
            border-color: #D4AF37 !important;
        }

        .day-marker {
            position: absolute;
            bottom: 2px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 2px;
        }

        .dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
        }

        .dot-red {
            background-color: #ef4444;
        }

        .dot-green {
            background-color: #22c55e;
        }

        .dot-yellow {
            background-color: #eab308;
        }

        .flatpickr-day.blocked {
            background-color: #fee2e2 !important;
            color: #ef4444 !important;
            text-decoration: line-through;
        }

        .flatpickr-day.full-booked {
            background-color: #fee2e2 !important;
            color: #ef4444 !important;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        luminara: {
                            gold: '#D4AF37',
                            dark: '#1a1a1a',
                            light: '#f8f9fa',
                        }
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 font-sans text-gray-800 antialiased">

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white shadow-sm">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between md:h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="/images/logo.png" alt="Luminara Logo" class="h-8 w-auto md:h-10">
                    <span class="font-serif text-xl font-bold tracking-tight text-gray-900 md:text-2xl">Luminara</span>
                </a>
                <a href="{{ route('home') }}"
                    class="hover:text-luminara-gold flex items-center gap-1 text-xs font-medium text-gray-500 md:text-sm">
                    <span>&larr;</span> <span class="hidden sm:inline">Kembali ke Beranda</span><span
                        class="sm:hidden">Beranda</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="mx-auto max-w-7xl px-3 py-6 sm:px-6 md:py-12 lg:px-8">
        <div class="lg:grid lg:grid-cols-12 lg:gap-12">

            <!-- Kolom Kiri: Form -->
            <div class="lg:col-span-8">
                <div
                    class="overflow-hidden rounded-2xl border border-gray-100 bg-white p-5 shadow-xl md:rounded-3xl md:p-12">
                    <h1 class="mb-2 font-serif text-2xl font-bold md:text-3xl">Formulir Pemesanan</h1>
                    <p class="mb-6 text-sm text-gray-500 md:mb-8 md:text-base">Lengkapi detail acara Anda untuk
                        mengamankan tanggal.</p>

                    @if ($errors->any())
                        <div class="mb-8 rounded-r border-l-4 border-red-500 bg-red-50 p-4">
                            <div class="flex">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-700">Mohon perbaiki kesalahan berikut:</p>
                                    <ul class="mt-1 list-inside list-disc text-sm text-red-600">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('booking.store') }}" method="POST" id="bookingForm"
                        class="space-y-6 md:space-y-8" enctype="multipart/form-data">
                        @csrf

                        <!-- Bagian 1: Jadwal Event -->
                        <div>
                            <h2 class="mb-4 border-b pb-2 text-base font-bold text-gray-900 md:text-lg">1. Jadwal Acara
                            </h2>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Pilih Tanggal</label>
                                    <input type="text" name="event_date" id="event_date"
                                        class="focus:ring-luminara-gold focus:border-luminara-gold w-full rounded-xl border border-gray-300 bg-white px-4 py-3"
                                        required placeholder="Pilih tanggal..." readonly>
                                    <p class="mt-2 text-xs text-gray-500" id="date-status">Silakan pilih tanggal di
                                        kalender.</p>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Jam Mulai Sesi</label>
                                    <input type="time" name="event_time"
                                        class="focus:ring-luminara-gold focus:border-luminara-gold w-full rounded-xl border border-gray-300 px-4 py-3"
                                        required>
                                </div>
                            </div>
                        </div>

                        <!-- Bagian 2: Paket -->
                        <div>
                            <h2 class="mb-4 border-b pb-2 text-base font-bold text-gray-900 md:text-lg">2. Pilih Paket
                            </h2>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                                <div class="md:col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Tipe Paket</label>
                                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 md:gap-4">

                                        @foreach ($packages as $pkg)
                                            <label class="group relative cursor-pointer">
                                                <input type="radio" name="package_type_select"
                                                    value="{{ $pkg->type }}" class="peer sr-only" required
                                                    onchange="updatePackage('{{ $pkg->name }}', '{{ $pkg->type }}', {{ $pkg->base_price }})">
                                                <div
                                                    class="hover:border-luminara-gold peer-checked:border-luminara-gold flex h-full flex-col justify-between rounded-xl border-2 border-gray-200 p-4 text-center transition peer-checked:bg-yellow-50">
                                                    @if (str_contains(strtolower($pkg->name), 'combo'))
                                                        <div
                                                            class="bg-luminara-gold absolute right-0 top-0 rounded-bl-lg px-2 py-0.5 text-[10px] font-bold text-white">
                                                            BEST VALUE</div>
                                                    @endif
                                                    <div>
                                                        <div class="font-bold text-gray-900">{{ $pkg->name }}</div>
                                                        <div class="mt-1 text-xs text-gray-500">
                                                            {{ Str::limit($pkg->description, 50) }}</div>
                                                    </div>
                                                    <div
                                                        class="text-luminara-gold mt-2 rounded bg-yellow-100/50 py-1.5 text-xs font-bold">
                                                        Rp {{ number_format($pkg->base_price / 1000, 0) }}k / 2 jam</div>
                                                </div>
                                            </label>
                                        @endforeach

                                    </div>
                                    <input type="hidden" name="package_name" id="package_name">
                                    <input type="hidden" name="package_type" id="package_type">
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Durasi (Jam)</label>
                                    <select name="duration_hours" id="duration_hours" onchange="calculateTotal()"
                                        class="focus:ring-luminara-gold focus:border-luminara-gold w-full rounded-xl border border-gray-300 px-4 py-3 text-sm md:text-base">
                                        <option value="2">2 Jam (Min)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Estimasi Total</label>
                                    <div class="relative">
                                        <span
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 font-bold text-gray-500">Rp</span>
                                        <input type="text" id="display_price"
                                            class="w-full rounded-xl border border-gray-300 bg-gray-100 py-3 pl-10 pr-4 text-sm font-bold text-gray-900 md:text-base"
                                            readonly value="0">
                                        <input type="hidden" name="price_total" id="price_total" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bagian 3: Data Pemesan -->
                        <div>
                            <h2 class="mb-4 border-b pb-2 text-base font-bold text-gray-900 md:text-lg">3. Data Diri &
                                Acara</h2>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                    <input type="text" name="customer_name"
                                        class="focus:ring-luminara-gold focus:border-luminara-gold w-full rounded-xl border border-gray-300 px-4 py-3"
                                        required placeholder="Nama Anda">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">WhatsApp</label>
                                    <input type="tel" name="customer_phone"
                                        class="focus:ring-luminara-gold focus:border-luminara-gold w-full rounded-xl border border-gray-300 px-4 py-3"
                                        required placeholder="08xxxxxxxxxx">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Nama Acara</label>
                                    <input type="text" name="event_type"
                                        class="focus:ring-luminara-gold focus:border-luminara-gold w-full rounded-xl border border-gray-300 px-4 py-3"
                                        required placeholder="Contoh: Pernikahan Budi & Ani">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Lokasi Acara</label>
                                    <textarea name="event_location" rows="2"
                                        class="focus:ring-luminara-gold focus:border-luminara-gold mb-3 w-full rounded-xl border border-gray-300 px-4 py-3"
                                        required placeholder="Nama Gedung / Hotel & Alamat Lengkap"></textarea>

                                    <div class="relative">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <input type="url" name="event_maps_link"
                                            class="focus:ring-luminara-gold focus:border-luminara-gold w-full rounded-xl border border-gray-300 py-3 pl-10 pr-4 text-sm"
                                            placeholder="Paste Link Google Maps di sini (Opsional)">
                                    </div>
                                    <p class="ml-1 mt-1 text-[10px] text-gray-500 md:text-xs">*Buka Google Maps > Cari
                                        Lokasi > Share > Copy Link</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Catatan
                                        (Opsional)</label>
                                    <textarea name="notes" rows="2"
                                        class="focus:ring-luminara-gold focus:border-luminara-gold w-full rounded-xl border border-gray-300 px-4 py-3"
                                        placeholder="Request khusus atau tema acara"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Bagian 4: Pembayaran DP -->
                        <div>
                            <h2 class="mb-4 border-b pb-2 text-base font-bold text-gray-900 md:text-lg">4. Pembayaran
                                DP</h2>
                            <div class="relative mb-6 rounded-2xl border border-yellow-100 bg-yellow-50 p-4 md:p-6">
                                <p class="mb-3 flex items-center gap-2 text-sm font-bold text-gray-900">
                                    <svg class="text-luminara-gold h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Informasi Rekening (Minimal DP Rp 500.000)
                                </p>
                                <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                                    <div class="space-y-1">
                                        <p class="text-gray-500">Bank BRI:</p>
                                        <div class="group flex cursor-pointer items-center gap-2"
                                            onclick="copyToClipboard('460701039843530')">
                                            <p
                                                class="group-hover:text-luminara-gold font-mono text-lg font-bold text-gray-800 transition">
                                                460701039843530</p>
                                            <svg class="group-hover:text-luminara-gold h-4 w-4 text-gray-400"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                        <p class="text-xs">a.n Ida Bagus Yudhi Priyatna</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-gray-500">SeaBank:</p>
                                        <div class="group flex cursor-pointer items-center gap-2"
                                            onclick="copyToClipboard('901207048574')">
                                            <p
                                                class="group-hover:text-luminara-gold font-mono text-lg font-bold text-gray-800 transition">
                                                901207048574</p>
                                            <svg class="group-hover:text-luminara-gold h-4 w-4 text-gray-400"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="space-y-1 border-t border-yellow-200 pt-2 md:col-span-2">
                                        <p class="text-gray-500">Shopeepay, OVO, Gopay:</p>
                                        <div class="group flex cursor-pointer items-center gap-2"
                                            onclick="copyToClipboard('081993009930')">
                                            <p
                                                class="group-hover:text-luminara-gold font-mono text-lg font-bold text-gray-800 transition">
                                                081993009930</p>
                                            <svg class="group-hover:text-luminara-gold h-4 w-4 text-gray-400"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Toast Notification -->
                            <div id="toast-copy"
                                class="pointer-events-none fixed bottom-20 left-1/2 z-50 flex -translate-x-1/2 transform items-center gap-2 rounded-full bg-gray-900 px-4 py-2 text-sm font-medium text-white opacity-0 shadow-lg transition-opacity duration-300">
                                <svg class="h-4 w-4 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Nomor rekening disalin!
                            </div>

                            <div class="mb-6">
                                <label class="mb-2 block text-sm font-medium text-gray-700">Jumlah Uang DP /
                                    Pelunasan</label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 font-bold text-gray-500">Rp</span>
                                    <input type="text" id="display_dp"
                                        class="focus:ring-luminara-gold focus:border-luminara-gold w-full rounded-xl border border-gray-300 bg-white py-3 pl-10 pr-10 text-sm font-bold text-gray-900 md:text-base"
                                        placeholder="0"
                                        oninput="formatRupiah(this, 'dp_amount'); checkPaymentStatus()">
                                    <input type="hidden" name="dp_amount" id="dp_amount" value="0">

                                    <!-- Green Checkmark Indicator -->
                                    <div id="payment-check"
                                        class="absolute inset-y-0 right-0 hidden items-center pr-3 text-green-500">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="mt-1 text-[10px] text-gray-500" id="payment-status-text">Masukkan jumlah
                                    yang ditransfer (DP Minimal 500rb atau Pelunasan).</p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Upload Bukti Transfer DP <span
                                        class="font-normal text-gray-400">(Opsional)</span></label>
                                <div class="group relative">
                                    <input type="file" name="payment_proof" id="payment_proof" accept="image/*"
                                        class="focus:ring-luminara-gold focus:border-luminara-gold file:text-luminara-gold w-full rounded-xl border border-gray-300 px-4 py-3 file:mr-4 file:rounded-full file:border-0 file:bg-yellow-50 file:px-4 file:py-2 file:text-sm file:font-semibold hover:file:bg-yellow-100">
                                </div>
                                <p class="text-[10px] text-gray-500 md:text-xs">Format: JPG, PNG. Maksimal 5MB. <br>
                                    <span class="text-luminara-gold">Tips:</span> Upload sekarang untuk mempercepat
                                    verifikasi jadwal.</p>
                            </div>
                        </div>

                        <div class="pt-6">
                            <button type="submit"
                                class="bg-luminara-gold w-full transform rounded-xl py-4 text-lg font-bold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-yellow-600">
                                Konfirmasi
                            </button>
                            <div id="package-error"
                                class="mt-2 hidden animate-bounce text-center text-sm font-bold text-red-600">
                                Silakan pilih salah satu paket terlebih dahulu!
                            </div>
                            <p class="mt-4 text-center text-xs text-gray-500">
                                Pesanan akan diteruskan ke WhatsApp Admin untuk validasi jadwal dan pembayaran.
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Info -->
            <div class="mt-8 lg:col-span-4 lg:mt-0">
                <div class="mb-8 rounded-3xl bg-gray-900 p-8 text-white shadow-xl">
                    <h3 class="mb-6 font-serif text-2xl font-bold">Cara Memesan</h3>
                    <ul class="space-y-6">
                        <li class="flex gap-4">
                            <span
                                class="bg-luminara-gold flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full font-bold text-black">1</span>
                            <p class="text-sm">Isi formulir dengan detail acara Anda.</p>
                        </li>
                        <li class="flex gap-4">
                            <span
                                class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-700 font-bold text-white">2</span>
                            <p class="text-sm">Klik tombol konfirmasi untuk mengirim pesan ke Admin.</p>
                        </li>
                        <li class="flex gap-4">
                            <span
                                class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-700 font-bold text-white">3</span>
                            <p class="text-sm">Lakukan pembayaran DP Rp 500.000 untuk mengunci jadwal.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        let basePrice = 0;
        let availabilityData = [];

        // Build packages object from Backend
        const availablePackages = {
            @foreach ($packages as $pkg)
                '{{ $pkg->type }}': {
                    name: '{{ $pkg->name }}',
                    base: {{ $pkg->base_price }},
                    prices: {
                        @foreach ($pkg->prices as $price)
                            {{ $price->duration_hours }}: {{ $price->price }},
                        @endforeach
                    }
                },
            @endforeach
        };

        // Validation and AJAX Submit for Form
        document.getElementById('bookingForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const packageName = document.getElementById('package_name').value;
            if (!packageName) {
                const errorDiv = document.getElementById('package-error');
                errorDiv.classList.remove('hidden');
                document.querySelector('h2').scrollIntoView({
                    behavior: 'smooth'
                }); // Scroll to Package section
                return;
            }

            // Show Loading
            Swal.fire({
                title: 'Memproses Pesanan...',
                text: 'Mohon tunggu sebentar data Anda sedang kami simpan.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const formData = new FormData(this);
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    Swal.fire({
                        title: 'Booking Berhasil!',
                        text: 'Data Anda telah tersimpan. Silakan klik tombol di bawah untuk konfirmasi akhir melalui WhatsApp.',
                        icon: 'success',
                        confirmButtonColor: '#D4AF37',
                        confirmButtonText: 'Lanjut ke WhatsApp',
                        allowOutsideClick: false
                    }).then((btn) => {
                        if (btn.isConfirmed) {
                            window.location.href = result.wa_url;
                        }
                    });
                } else {
                    // Handle Validation Errors
                    let errorMessages = '';
                    if (result.errors) {
                        errorMessages = Object.values(result.errors).flat().join('<br>');
                    } else {
                        errorMessages = result.message || 'Terjadi kesalahan saat menyimpan data.';
                    }

                    Swal.fire({
                        title: 'Oops!',
                        html: errorMessages,
                        icon: 'error',
                        confirmButtonColor: '#D4AF37'
                    });
                }
            } catch (error) {
                Swal.fire({
                    title: 'Kesalahan Sistem',
                    text: 'Gagal terhubung ke server. Silakan coba lagi.',
                    icon: 'error',
                    confirmButtonColor: '#D4AF37'
                });
            }
        });

        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast();
                }).catch(err => {
                    fallbackCopyTextToClipboard(text);
                });
            } else {
                fallbackCopyTextToClipboard(text);
            }
        }

        function fallbackCopyTextToClipboard(text) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                var successful = document.execCommand('copy');
                if (successful) showToast();
            } catch (err) {
                console.error('Fallback: Oops, unable to copy', err);
            }

            document.body.removeChild(textArea);
        }

        function showToast() {
            const toast = document.getElementById('toast-copy');
            toast.classList.remove('opacity-0');
            setTimeout(() => {
                toast.classList.add('opacity-0');
            }, 2000);
        }

        function updatePackage(name, type, price) {
            document.getElementById('package_name').value = name;
            document.getElementById('package_type').value = type;

            // Get base price from object to be safe, though passed in param
            if (availablePackages[type]) {
                basePrice = availablePackages[type].base;
            } else {
                basePrice = price;
            }

            updateDurationOptions(type);
            calculateTotal();
        }

        function updateDurationOptions(type) {
            const select = document.getElementById('duration_hours');
            const currentVal = parseInt(select.value) || 2;

            select.innerHTML = ''; // Clear options

            if (availablePackages[type] && availablePackages[type].prices) {
                const durations = Object.keys(availablePackages[type].prices).map(Number).sort((a, b) => a - b);

                durations.forEach(hours => {
                    const option = document.createElement('option');
                    option.value = hours;
                    option.text = hours + ' Jam' + (hours === Math.min(...durations) ? ' (Min)' : '');
                    select.appendChild(option);
                });

                // Restore selection if valid, else set to min
                if (durations.includes(currentVal)) {
                    select.value = currentVal;
                } else {
                    select.value = durations[0]; // min duration
                }
            } else {
                // Fallback default
                const option = document.createElement('option');
                option.value = 2;
                option.text = "2 Jam";
                select.appendChild(option);
            }
        }

        function calculateTotal() {
            const duration = parseInt(document.getElementById('duration_hours').value) || 2;
            const type = document.getElementById('package_type').value;
            let total = 0;

            if (type && availablePackages[type] && availablePackages[type].prices && availablePackages[type].prices[
                    duration]) {
                // Exact match from DB
                total = availablePackages[type].prices[duration];
            } else {
                // Fallback (should not happen if select options are correct)
                total = basePrice;
            }

            document.getElementById('price_total').value = total;
            document.getElementById('display_price').value = new Intl.NumberFormat('id-ID').format(total);
            checkPaymentStatus();
        }

        function formatRupiah(element, targetId) {
            let value = element.value.replace(/[^,\d]/g, '').toString();
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            element.value = rupiah;

            // Update hidden input with numeric value only
            document.getElementById(targetId).value = value;
        }

        function checkPaymentStatus() {
            const totalPrice = parseInt(document.getElementById('price_total').value) || 0;
            const inputAmount = parseInt(document.getElementById('dp_amount').value) || 0;
            const checkIcon = document.getElementById('payment-check');
            const statusText = document.getElementById('payment-status-text');

            if (totalPrice > 0 && inputAmount >= totalPrice) {
                // Lunas
                checkIcon.classList.remove('hidden');
                checkIcon.classList.add('flex');
                statusText.textContent = "Status: LUNAS (Pembayaran Penuh)";
                statusText.classList.add('text-green-600', 'font-bold');
                statusText.classList.remove('text-gray-500');
            } else if (inputAmount > 0) {
                // DP
                checkIcon.classList.remove('flex');
                checkIcon.classList.add('hidden');
                statusText.textContent = "Status: DP DIBAYAR (Belum Lunas)";
                statusText.classList.remove('text-green-600', 'font-bold', 'text-gray-500');
                statusText.classList.add('text-yellow-600', 'font-bold');
            } else {
                // Empty
                checkIcon.classList.remove('flex');
                checkIcon.classList.add('hidden');
                statusText.textContent = "Masukkan jumlah yang ditransfer (DP Minimal 500rb atau Pelunasan).";
                statusText.classList.remove('text-green-600', 'font-bold', 'text-yellow-600');
                statusText.classList.add('text-gray-500');
            }
        }

        // Initialize Flatpickr
        document.addEventListener('DOMContentLoaded', async function() {
            // Fetch Availability Data first
            try {
                // Fetch for current and next month to populate markers
                const currentMonth = new Date().toISOString().slice(0, 7);
                // Simple fetch for current month for now, ideally fetch based on calendar view change
                const response = await fetch(`/calendar/availability?month=${currentMonth}`);
                availabilityData = await response.json();
            } catch (e) {
                console.error("Failed to load availability", e);
            }

            const fp = flatpickr("#event_date", {
                locale: "id",
                minDate: "today",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j F Y",
                disable: [
                    function(date) {
                        // Check if date is blocked or full (Using local date components to avoid UTC shift)
                        const offsetDate = new Date(date.getTime() - (date.getTimezoneOffset() *
                            60000));
                        const dateStr = offsetDate.toISOString().slice(0, 10);

                        const data = availabilityData.find(item => item.date === dateStr);
                        if (data) {
                            return data.is_blocked || data.booking_count >= data.max_booking;
                        }
                        return false;
                    }
                ],
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    const date = dayElem.dateObj;
                    const offsetDate = new Date(date.getTime() - (date.getTimezoneOffset() *
                    60000));
                    const dateStr = offsetDate.toISOString().slice(0, 10);

                    const data = availabilityData.find(item => item.date === dateStr);

                    if (data) {
                        if (data.is_blocked) {
                            dayElem.classList.add("blocked");
                            dayElem.title = "Tidak Tersedia";
                        } else if (data.booking_count >= data.max_booking) {
                            dayElem.classList.add("full-booked");
                            dayElem.title = "Penuh";
                        } else if (data.booking_count > 0) {
                            // Add dots for existing bookings
                            const marker = document.createElement("div");
                            marker.className = "day-marker";

                            // Show dots equal to booking count (max 3 for visual)
                            const count = Math.min(data.booking_count, 3);
                            for (let i = 0; i < count; i++) {
                                const dot = document.createElement("span");
                                dot.className = "dot dot-green"; // Green for booked slots
                                marker.appendChild(dot);
                            }
                            dayElem.appendChild(marker);
                        }
                    }
                },
                onMonthChange: async function(selectedDates, dateStr, instance) {
                    // Refetch data when month changes
                    const year = instance.currentYear;
                    const month = String(instance.currentMonth + 1).padStart(2, '0');
                    try {
                        const response = await fetch(
                            `/calendar/availability?month=${year}-${month}`);
                        availabilityData = await response.json();
                        instance.redraw();
                    } catch (e) {
                        console.error(e);
                    }
                },
                onChange: function(selectedDates, dateStr, instance) {
                    const statusText = document.getElementById('date-status');
                    if (selectedDates.length > 0) {
                        statusText.textContent = "Tanggal tersedia!";
                        statusText.className = "mt-2 text-xs text-green-600 font-bold";
                    } else {
                        statusText.textContent = "Silakan pilih tanggal.";
                        statusText.className = "mt-2 text-xs text-gray-500";
                    }
                }
            });

            // Auto-select package from URL
            const params = new URLSearchParams(window.location.search);
            const type = params.get('type');
            if (type) {
                const radio = document.querySelector(`input[value="${type}"]`);
                if (radio) {
                    radio.click();
                    // Scroll to form
                    document.getElementById('bookingForm').scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    </script>
</body>

</html>
