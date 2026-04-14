@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Halo, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-500">Ringkasan aktivitas booking bulan ini.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Booking</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">{{ $totalBookings }}</h3>
            <p class="text-sm text-gray-500 mt-1">Bulan Ini</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Estimasi Pendapatan</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">Rp {{ number_format($revenue, 0, ',', '.') }}</h3>
            <p class="text-sm text-gray-500 mt-1">Bulan Ini &mdash; Total Harga Booking (Lunas/DP)</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Menunggu Konfirmasi</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">{{ $pendingCount }}</h3>
            <p class="text-sm text-gray-500 mt-1">Status PENDING</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100" x-data="{ visible: false }">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-purple-50 text-purple-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Penghasilan</span>
            </div>
            <div class="flex items-center justify-between">
                <h3 class="text-3xl font-bold text-gray-900">
                    <span x-show="visible" x-cloak>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    <span x-show="!visible" x-cloak>Rp ••••••••</span>
                </h3>
                <button @click="visible = !visible" class="text-gray-400 hover:text-gray-600 transition cursor-pointer shrink-0" :title="visible ? 'Sembunyikan' : 'Tampilkan'">
                    <svg x-show="!visible" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <svg x-show="visible" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                </button>
            </div>
            <p class="text-sm text-gray-500 mt-1">Semua Waktu (Lunas/DP)</p>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 md:mb-6">Acara Mendatang (7 Hari ke Depan)</h2>
        
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs font-bold text-gray-400 uppercase border-b border-gray-100">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Jam</th>
                        <th class="py-3 px-4">Nama</th>
                        <th class="py-3 px-4">Paket</th>
                        <th class="py-3 px-4">Lokasi</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($upcomingEvents as $event)
                        <tr class="hover:bg-gray-50 border-b border-gray-50 last:border-0 transition">
                            <td class="py-4 px-4 font-medium text-gray-900">{{ $event->event_date->format('d/m/Y') }}</td>
                            <td class="py-4 px-4 text-gray-600">{{ \Carbon\Carbon::parse($event->event_time)->format('H:i') }} WITA</td>
                            <td class="py-4 px-4 text-gray-900 font-medium">{{ $event->customer_name }}</td>
                            <td class="py-4 px-4 text-gray-600">{{ $event->package_name }}</td>
                            <td class="py-4 px-4 text-gray-600 truncate max-w-xs">{{ $event->event_location }}</td>
                            <td class="py-4 px-4">
                                @if($event->status == 'LUNAS')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">LUNAS</span>
                                @elseif($event->status == 'DP_DIBAYAR')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">DP</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ $event->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">Belum ada acara dalam waktu dekat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List -->
        <div class="block md:hidden space-y-4">
            @forelse($upcomingEvents as $event)
                <div class="border border-gray-100 rounded-xl p-4 bg-gray-50">
                    <div class="flex justify-between items-start mb-2">
                        <div class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">
                            {{ $event->event_date->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($event->event_time)->format('H:i') }}
                        </div>
                        <div>
                            @if($event->status == 'LUNAS')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800">LUNAS</span>
                            @elseif($event->status == 'DP_DIBAYAR')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">DP</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-800">{{ $event->status }}</span>
                            @endif
                        </div>
                    </div>
                    <h3 class="font-bold text-gray-900 text-sm">{{ $event->customer_name }}</h3>
                    <p class="text-xs text-gray-600 mt-1">{{ $event->package_name }}</p>
                    <div class="flex items-center gap-1 mt-2 text-xs text-gray-500">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="truncate">{{ $event->event_location ?? 'Lokasi belum diisi' }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-4 text-sm">Belum ada acara dalam waktu dekat.</div>
            @endforelse
        </div>

        <div class="mt-4 text-right">
            <a href="{{ route('admin.bookings.index') }}" class="text-sm font-medium text-yellow-600 hover:text-yellow-700">Lihat Semua Booking &rarr;</a>
        </div>
    </div>
@endsection
