@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.bookings.index') }}" class="text-gray-500 hover:text-gray-900 text-sm mb-4 inline-block">&larr; Kembali</a>
        <h1 class="text-3xl font-bold text-gray-900">Edit Booking</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-4xl">
        <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST" enctype="multipart/form-data" class="p-4 md:p-8 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Info -->
                <div class="md:col-span-2 border-b pb-4 mb-2">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Pelanggan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name', $booking->customer_name) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp</label>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone', $booking->customer_phone) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                    </div>
                </div>

                <!-- Event Info -->
                <div class="md:col-span-2 border-b pb-4 mb-2">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Detail Acara</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Event</label>
                            <input type="date" name="event_date" value="{{ old('event_date', $booking->event_date->format('Y-m-d')) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                            <input type="time" name="event_time" value="{{ old('event_time', \Carbon\Carbon::parse($booking->event_time)->format('H:i')) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (Jam)</label>
                            <input type="number" name="duration_hours" value="{{ old('duration_hours', $booking->duration_hours) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required min="1">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Acara</label>
                            <input type="text" name="event_type" value="{{ old('event_type', $booking->event_type) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" placeholder="Contoh: Pernikahan Budi & Ani">
                        </div>
                    </div>
                </div>

                <!-- Package Info -->
                <div class="md:col-span-2 border-b pb-4 mb-2">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Paket & Harga</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Paket</label>
                            <select name="package_type" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500">
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg->type }}" {{ $booking->package_type == $pkg->type ? 'selected' : '' }}>
                                        {{ $pkg->name }}
                                    </option>
                                @endforeach
                                <!-- Fallback if current package is not in list (e.g. deleted/inactive) -->
                                @if(!$packages->contains('type', $booking->package_type))
                                    <option value="{{ $booking->package_type }}" selected>{{ $booking->package_name }} (Legacy)</option>
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Harga (Rp)</label>
                            <input type="number" name="price_total" value="{{ old('price_total', $booking->price_total) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                    </div>
                </div>

                <!-- Media Info -->
                <div class="md:col-span-2 border-b pb-4 mb-2">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Media & Link</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Link Google Drive</label>
                            <input type="url" name="link_drive" value="{{ old('link_drive', $booking->link_drive) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" placeholder="https://drive.google.com/...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail (Gambar)</label>
                            
                            @if($booking->thumbnail)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $booking->thumbnail) }}" alt="Current Thumbnail" class="w-32 h-32 object-cover rounded shadow-sm border">
                                </div>
                            @endif

                            <input type="file" name="thumbnail" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" accept="image/*">
                            <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah thumbnail. Format: JPG, PNG, WEBP. Maks: 5MB. Gambar akan dikonversi otomatis ke format WebP.</p>
                        </div>
                    </div>
                </div>

                <!-- Status & Notes -->
                <div class="md:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                            <select name="status" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500">
                                <option value="PENDING" {{ $booking->status == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                <option value="DP_BAYAR" {{ $booking->status == 'DP_BAYAR' ? 'selected' : '' }}>DP DIBAYAR</option>
                                <option value="LUNAS" {{ $booking->status == 'LUNAS' ? 'selected' : '' }}>LUNAS</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                            <textarea name="notes" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500">{{ old('notes', $booking->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t flex justify-end">
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 px-8 rounded-xl shadow-lg transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
