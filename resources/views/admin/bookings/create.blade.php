@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.bookings.index') }}" class="text-gray-500 hover:text-gray-900 text-sm mb-4 inline-block">&larr; Kembali</a>
        <h1 class="text-3xl font-bold text-gray-900">Buat Booking Manual</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-4xl">
        <form action="{{ route('admin.bookings.store') }}" method="POST" enctype="multipart/form-data" class="p-4 md:p-8 space-y-6" x-data="bookingForm()">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Info -->
                <div class="md:col-span-2 border-b pb-4 mb-2">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Pelanggan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp</label>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                    </div>
                </div>

                <!-- Event Info -->
                <div class="md:col-span-2 border-b pb-4 mb-2">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Detail Acara</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Event</label>
                            <input type="date" name="event_date" value="{{ old('event_date') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                            <input type="time" name="event_time" value="{{ old('event_time') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (Jam)</label>
                            <input type="number" name="duration_hours" value="{{ old('duration_hours') }}" x-model="duration" @change="calculateTotal()" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required min="1">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                            <input type="text" name="event_location" value="{{ old('event_location') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Acara</label>
                            <input type="text" name="event_type" value="{{ old('event_type') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" placeholder="Contoh: Pernikahan Budi & Ani">
                        </div>
                    </div>
                </div>

                <!-- Package Info -->
                <div class="md:col-span-2 border-b pb-4 mb-2">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Paket & Harga</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Paket</label>
                            <select name="package_type" x-model="selectedPackageType" @change="calculateTotal()" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500">
                                <option value="">-- Pilih Paket --</option>
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg->type }}" {{ old('package_type') == $pkg->type ? 'selected' : '' }}>{{ $pkg->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Harga (Rp)</label>
                            <input type="number" name="price_total" value="{{ old('price_total') }}" x-model="totalPrice" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                    </div>
                </div>

                <!-- Media Info -->
                <div class="md:col-span-2 border-b pb-4 mb-2">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Media & Link</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Link Google Drive</label>
                            <input type="url" name="link_drive" value="{{ old('link_drive') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" placeholder="https://drive.google.com/...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail (Gambar)</label>
                            <input type="file" name="thumbnail" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" accept="image/*">
                            <p class="mt-1 text-xs text-gray-400">Format: JPG, PNG, WEBP. Maks: 5MB. Gambar akan dikonversi otomatis ke format WebP.</p>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="pt-6 border-t flex justify-end">
                <button type="submit" class="bg-black text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:bg-gray-800 transition">
                    Simpan Booking
                </button>
            </div>
        </form>
    </div>

    <script>
        function bookingForm() {
            return {
                duration: 2,
                selectedPackageType: '',
                totalPrice: 0,
                packages: {!! json_encode($packages->mapWithKeys(function ($item) {
                    return [$item->type => [
                        'base_price' => $item->base_price,
                        'prices' => $item->prices->mapWithKeys(fn($p) => [$p->duration_hours => $p->price])
                    ]];
                })) !!},
                calculateTotal() {
                    if (!this.selectedPackageType || !this.packages[this.selectedPackageType]) {
                        this.totalPrice = 0;
                        return;
                    }

                    const pkg = this.packages[this.selectedPackageType];
                    // Check if explicit price exists for duration
                    if (pkg.prices[this.duration]) {
                        this.totalPrice = pkg.prices[this.duration];
                    } else {
                        // Simple fallback estimation logic (base price + extra hours) - adjust as per your business logic
                        // For now just use base price if duration not found
                        this.totalPrice = pkg.base_price;
                    }
                }
            }
        }
    </script>
@endsection
