@extends('layouts.admin')

@section('content')
    <div x-data="bookingManager()">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Daftar Booking</h1>
                <p class="text-gray-500 mt-0.5">Kelola semua pesanan masuk di sini.</p>
            </div>
            <a href="{{ route('admin.bookings.create') }}"
               class="bg-black text-white hover:bg-gray-800 font-bold py-2 px-5 rounded-lg transition shadow-sm flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Booking Manual
            </a>
        </div>

        {{-- Stats Row --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <a href="{{ route('admin.bookings.index', array_merge(request()->except('filter'), ['filter' => 'hari_ini'])) }}"
               class="group bg-white border rounded-xl p-4 hover:border-red-300 hover:shadow-md transition-all relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-red-50 to-transparent opacity-0 group-hover:opacity-100 transition"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['hari_ini'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.bookings.index', array_merge(request()->except('filter'), ['filter' => 'mendatang'])) }}"
               class="group bg-white border rounded-xl p-4 hover:border-blue-300 hover:shadow-md transition-all relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-50 to-transparent opacity-0 group-hover:opacity-100 transition"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Mendatang</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['mendatang'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.bookings.index', array_merge(request()->except('filter'), ['filter' => 'belum_lunas'])) }}"
               class="group bg-white border rounded-xl p-4 hover:border-amber-300 hover:shadow-md transition-all relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-amber-50 to-transparent opacity-0 group-hover:opacity-100 transition"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Belum Lunas</p>
                        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['belum_lunas'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.bookings.index', array_merge(request()->except('filter'), ['filter' => 'lunas'])) }}"
               class="group bg-white border rounded-xl p-4 hover:border-green-300 hover:shadow-md transition-all relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-green-50 to-transparent opacity-0 group-hover:opacity-100 transition"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Lunas</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['lunas'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </a>
        </div>

        {{-- Filter Tabs + Search + Division --}}
        <div class="bg-white border rounded-2xl p-4 mb-4 shadow-sm">
            <div class="flex flex-col lg:flex-row gap-3">

                {{-- Filter Tabs --}}
                <div class="flex flex-wrap gap-1.5 flex-1" x-data="{ open: false, selected: '{{ $filter }}' }">
                    @php
                        $filters = [
                            'semua'       => 'Semua',
                            'hari_ini'    => 'Hari Ini',
                            'besok'       => 'Besok',
                            'mendatang'   => 'Mendatang',
                            'pending'     => 'Pending',
                            'dp'          => 'DP Dibayar',
                            'belum_lunas' => 'Belum Lunas',
                            'lunas'       => 'Lunas',
                            'dibatalkan'  => 'Dibatalkan',
                        ];
                    @endphp
                    @foreach($filters as $key => $label)
                        <a href="{{ route('admin.bookings.index', array_merge(request()->except('filter'), ['filter' => $key])) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-semibold border transition-all whitespace-nowrap
                           {{ $filter === $key
                              ? 'bg-black text-white border-black'
                              : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100' }}">
                            {{ $label }}
                            @if(isset($stats[$key]) && $stats[$key] > 0)
                                <span class="ml-1 opacity-60">{{ $stats[$key] }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>

                {{-- Search + Division --}}
                <div class="flex gap-2 items-center shrink-0">
                    @if(auth()->user()->division === 'super_admin')
                        <select name="division" onchange="window.location.href=this.value"
                                class="text-sm border border-gray-200 rounded-lg px-3 py-2 text-gray-700 focus:ring-2 focus:ring-black focus:outline-none bg-white">
                            @php
                                $divRoutes = [
                                    'semua'      => route('admin.bookings.index', array_merge(request()->except(['filter','division']), ['division' => 'semua',   'filter' => $filter])),
                                    'photobooth' => route('admin.bookings.index', array_merge(request()->except(['filter','division']), ['division' => 'photobooth', 'filter' => $filter])),
                                    'visual'     => route('admin.bookings.index', array_merge(request()->except(['filter','division']), ['division' => 'visual',     'filter' => $filter])),
                                ];
                            @endphp
                            <option value="{{ $divRoutes['semua'] }}"      {{ $division === 'semua'      ? 'selected' : '' }}>Semua Unit</option>
                            <option value="{{ $divRoutes['photobooth'] }}" {{ $division === 'photobooth' ? 'selected' : '' }}>Photobooth</option>
                            <option value="{{ $divRoutes['visual'] }}"     {{ $division === 'visual'     ? 'selected' : '' }}>Visual</option>
                        </select>
                    @endif

                    <form method="GET" action="{{ route('admin.bookings.index') }}" class="relative">
                        @foreach(request()->except('search') as $key => $val)
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endforeach
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Cari nama, WA, lokasi..."
                               class="w-52 text-sm border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-gray-700 focus:ring-2 focus:ring-black focus:outline-none placeholder-gray-400">
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </form>
                </div>
            </div>
        </div>

        {{-- Active filter banner --}}
        @if($filter !== 'semua' || $search)
            <div class="flex items-center gap-2 mb-4 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Menampilkan:
                @if($filter !== 'semua')
                    <span class="bg-black text-white text-xs font-semibold px-2 py-0.5 rounded-full">{{ $filters[$filter] ?? $filter }}</span>
                @endif
                @if($search)
                    <span class="bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-0.5 rounded-full">Pencarian: "{{ $search }}"</span>
                @endif
                <a href="{{ route('admin.bookings.index') }}" class="ml-1 text-gray-400 hover:text-gray-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="py-3.5 px-5 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                <a href="{{ route('admin.bookings.index', array_merge(request()->except('sort','direction'), ['sort' => 'created_at', 'direction' => $sort === 'created_at' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                   class="flex items-center gap-1 hover:text-gray-700">
                                    Booking
                                    @if($sort === 'created_at') <span class="text-gray-700">{{ $direction === 'asc' ? '↑' : '↓' }}</span> @endif
                                </a>
                            </th>
                            <th class="py-3.5 px-5 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                <a href="{{ route('admin.bookings.index', array_merge(request()->except('sort','direction'), ['sort' => 'event_date', 'direction' => $sort === 'event_date' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                   class="flex items-center gap-1 hover:text-gray-700">
                                    Event
                                    @if($sort === 'event_date') <span class="text-gray-700">{{ $direction === 'asc' ? '↑' : '↓' }}</span> @endif
                                </a>
                            </th>
                            <th class="py-3.5 px-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                            <th class="py-3.5 px-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Paket</th>
                            <th class="py-3.5 px-5 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                <a href="{{ route('admin.bookings.index', array_merge(request()->except('sort','direction'), ['sort' => 'price_total', 'direction' => $sort === 'price_total' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                   class="flex items-center gap-1 hover:text-gray-700">
                                    Total
                                    @if($sort === 'price_total') <span class="text-gray-700">{{ $direction === 'asc' ? '↑' : '↓' }}</span> @endif
                                </a>
                            </th>
                            <th class="py-3.5 px-5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Bukti</th>
                            <th class="py-3.5 px-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="py-3.5 px-5 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50/70 transition group">
                            {{-- Booking date --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $booking->created_at->timezone('Asia/Makassar')->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $booking->created_at->timezone('Asia/Makassar')->format('H:i') }} WITA</div>
                            </td>

                            {{-- Event date --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                @php
                                    $isToday = $booking->event_date->isToday();
                                    $isTomorrow = $booking->event_date->isTomorrow();
                                    $isPast = $booking->event_date->isPast();
                                @endphp
                                <div class="flex items-center gap-1.5">
                                    @if($isToday)
                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                                            Hari Ini
                                        </span>
                                    @elseif($isTomorrow)
                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-orange-600 bg-orange-50 px-1.5 py-0.5 rounded">
                                            Besok
                                        </span>
                                    @elseif(!$isPast)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">
                                            {{ round($booking->event_date->diffInDays(now())) }} hari lagi
                                        </span>
                                    @endif
                                </div>
                                <div class="font-semibold text-gray-900 text-sm mt-0.5">{{ $booking->event_date->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $booking->event_time ? substr($booking->event_time, 0, 5) : '—' }} · {{ $booking->duration_hours ?? '-' }} jam</div>
                            </td>

                            {{-- Customer --}}
                            <td class="py-3.5 px-5 whitespace-nowrap cursor-pointer" @click="openDetail({{ $booking }})">
                                <div class="font-semibold text-gray-900 text-sm hover:text-blue-600 transition">{{ Str::limit($booking->customer_name, 18) }}</div>
                                <div class="text-xs text-gray-400">{{ $booking->customer_phone }}</div>
                                @if($booking->business_unit)
                                    <span class="inline-block mt-0.5 text-[10px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 uppercase tracking-wide">
                                        {{ $booking->business_unit }}
                                    </span>
                                @endif
                            </td>

                            {{-- Package --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ Str::limit($booking->package_name ?? '-', 22) }}</div>
                                <div class="text-xs text-gray-400">{{ $booking->duration_hours ?? '-' }} Jam</div>
                            </td>

                            {{-- Total --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <div class="font-bold text-gray-900 text-sm">Rp {{ number_format($booking->price_total ?? 0, 0, ',', '.') }}</div>
                                @if(($booking->dp_amount ?? 0) > 0)
                                    <div class="text-xs text-gray-400">DP: Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</div>
                                @endif
                            </td>

                            {{-- Payment proof --}}
                            <td class="py-3.5 px-5 text-center">
                                @if($booking->payment_proof)
                                    <button @click="openImageModal('{{ asset('storage/' . $booking->payment_proof) }}')"
                                            type="button" class="text-blue-500 hover:text-blue-700 transition" title="Lihat Bukti Transfer">
                                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                @else
                                    <span class="text-gray-300" title="Belum ada bukti transfer">
                                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="py-3.5 px-5">
                                <form action="{{ route('admin.bookings.update-status', $booking->id) }}" method="POST" id="status-form-{{ $booking->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="document.getElementById('status-form-{{ $booking->id }}').submit()"
                                        class="text-xs font-bold rounded-full px-3 py-1.5 border-0 cursor-pointer focus:ring-2 focus:ring-offset-0
                                        @if($booking->status === 'LUNAS')        bg-green-50 text-green-700 focus:ring-green-400
                                        @elseif($booking->status === 'DP_DIBAYAR') bg-blue-50 text-blue-700 focus:ring-blue-400
                                        @elseif($booking->status === 'PENDING')   bg-yellow-50 text-yellow-700 focus:ring-yellow-400
                                        @else                                     bg-gray-100 text-gray-600 focus:ring-gray-300
                                        @endif">
                                        <option value="PENDING"     {{ $booking->status === 'PENDING'     ? 'selected' : '' }}>Pending</option>
                                        <option value="DP_DIBAYAR"  {{ $booking->status === 'DP_DIBAYAR'  ? 'selected' : '' }}>DP Dibayar</option>
                                        <option value="LUNAS"       {{ $booking->status === 'LUNAS'       ? 'selected' : '' }}>Lunas</option>
                                        <option value="DIBATALKAN"  {{ $booking->status === 'DIBATALKAN'  ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </form>
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-5 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-70 group-hover:opacity-100 transition">
                                    <button @click="openDetail({{ $booking }})" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', $booking->customer_phone ?? '') }}" target="_blank"
                                       class="p-1.5 rounded-lg text-green-500 hover:bg-green-50 hover:text-green-700 transition" title="Chat WA">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.bookings.edit', $booking->id) }}"
                                       class="p-1.5 rounded-lg text-blue-500 hover:bg-blue-50 hover:text-blue-700 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.bookings.invoice', $booking->id) }}" target="_blank"
                                       class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition" title="Invoice">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)"
                                                class="p-1.5 rounded-lg text-red-400 hover:bg-red-50 hover:text-red-600 transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-700">Belum ada booking</p>
                                        <p class="text-sm text-gray-400 mt-0.5">Data akan muncul di sini setelah ada yang memesan.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="block md:hidden divide-y divide-gray-100">
                @forelse($bookings as $booking)
                <div class="p-4 space-y-3">
                    <div class="flex justify-between items-start">
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-gray-900">{{ Str::limit($booking->customer_name, 20) }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $booking->customer_phone }}</div>
                            @if($booking->business_unit)
                                <span class="inline-block mt-1 text-[10px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 uppercase">{{ $booking->business_unit }}</span>
                            @endif
                        </div>
                        <form action="{{ route('admin.bookings.update-status', $booking->id) }}" method="POST" id="sm-status-{{ $booking->id }}">
                            @csrf @method('PATCH')
                            <select name="status" onchange="document.getElementById('sm-status-{{ $booking->id }}').submit()"
                                class="text-[10px] font-bold rounded-full px-2 py-1 border-0
                                @if($booking->status === 'LUNAS')        bg-green-50 text-green-700
                                @elseif($booking->status === 'DP_DIBAYAR') bg-blue-50 text-blue-700
                                @elseif($booking->status === 'PENDING')   bg-yellow-50 text-yellow-700
                                @else                                     bg-gray-100 text-gray-600 @endif">
                                <option value="PENDING"    {{ $booking->status === 'PENDING'    ? 'selected' : '' }}>Pending</option>
                                <option value="DP_DIBAYAR" {{ $booking->status === 'DP_DIBAYAR' ? 'selected' : '' }}>DP</option>
                                <option value="LUNAS"      {{ $booking->status === 'LUNAS'      ? 'selected' : '' }}>Lunas</option>
                                <option value="DIBATALKAN" {{ $booking->status === 'DIBATALKAN' ? 'selected' : '' }}>Batal</option>
                            </select>
                        </form>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-gray-50 rounded-lg p-2">
                            <div class="text-gray-400 text-[10px] uppercase font-bold">Event</div>
                            <div class="font-semibold text-gray-800">{{ $booking->event_date->format('d M Y') }}</div>
                            <div class="text-gray-500">{{ substr($booking->event_time ?? '', 0, 5) }} · {{ $booking->duration_hours ?? '-' }} jam</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-2">
                            <div class="text-gray-400 text-[10px] uppercase font-bold">Total</div>
                            <div class="font-bold text-gray-800">Rp {{ number_format($booking->price_total ?? 0, 0, ',', '.') }}</div>
                            @if(($booking->dp_amount ?? 0) > 0)
                                <div class="text-gray-500">DP: Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-gray-50">
                        <div class="flex gap-2">
                            @if($booking->payment_proof)
                                <button @click="openImageModal('{{ asset('storage/' . $booking->payment_proof) }}')" class="text-blue-500 text-xs flex items-center gap-1 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Bukti TF
                                </button>
                            @endif
                            <button @click="openDetail({{ $booking }})" class="text-gray-500 text-xs flex items-center gap-1 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Detail
                            </button>
                        </div>
                        <div class="flex gap-3">
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', $booking->customer_phone ?? '') }}" target="_blank" class="text-green-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            </a>
                            <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="text-blue-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <a href="{{ route('admin.bookings.invoice', $booking->id) }}" target="_blank" class="text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </a>
                            <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete(this)" class="text-red-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-400">Belum ada data booking.</div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        @if($bookings->hasPages())
            <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                <div class="hidden md:block">
                    Menampilkan {{ $bookings->firstItem() ?? 0 }}–{{ $bookings->lastItem() ?? 0 }} dari {{ $bookings->total() }} booking
                </div>
                <div class="hidden md:block">
                    <div class="flex gap-1">
                        @foreach($bookings->getUrlRange(max(1, $bookings->currentPage() - 2), min($bookings->lastPage(), $bookings->currentPage() + 2)) as $page => $url)
                            @if($page == $bookings->currentPage())
                                <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-black text-white text-sm font-semibold">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-700 text-sm hover:bg-gray-50">{{ $page }}</a>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="block md:hidden">
                    {{ $bookings->links() }}
                </div>
            </div>
            <div class="hidden md:block">
                {{ $bookings->links() }}
            </div>
        @endif

        {{-- Image Modal --}}
        <div x-show="showImageModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70" style="display: none;"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full flex flex-col max-h-[90vh]" @click.away="showImageModal = false">
                <div class="flex justify-between items-center p-5 border-b">
                    <h3 class="text-lg font-bold text-gray-900">Bukti Transfer</h3>
                    <button @click="showImageModal = false" class="text-gray-400 hover:text-gray-700 rounded-full p-1 hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-5 flex-1 overflow-auto flex justify-center bg-gray-50">
                    <img :src="imgSrc" alt="Bukti Transfer" class="max-w-full h-auto object-contain rounded-xl">
                </div>
                <div class="p-5 border-t flex justify-end gap-3">
                    <a :href="imgSrc" download class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-xl inline-flex items-center gap-2 transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download
                    </a>
                </div>
            </div>
        </div>

        {{-- Detail Modal --}}
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" style="display: none;"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full flex flex-col max-h-[90vh]" @click.away="showDetailModal = false">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-xl font-bold text-gray-900">Detail Booking</h3>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-700 rounded-full p-1 hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-xl flex flex-col items-center justify-center border-2 border-gray-200 shadow-sm">
                            <span class="text-xs text-red-500 font-bold uppercase" x-text="formatDate(selectedBooking.event_date, 'month')"></span>
                            <span class="text-3xl font-bold text-gray-900 leading-none" x-text="formatDate(selectedBooking.event_date, 'day')"></span>
                            <span class="text-[10px] text-gray-500" x-text="formatDate(selectedBooking.event_date, 'year')"></span>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900" x-text="selectedBooking.event_type || 'Acara'"></h4>
                            <p class="text-sm text-gray-600 mt-0.5" x-text="selectedBooking.customer_name"></p>
                            <span x-show="selectedBooking.business_unit"
                                  class="inline-block mt-1 text-[10px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 uppercase tracking-wide"
                                  x-text="selectedBooking.business_unit"></span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-100">
                            <h5 class="text-xs font-bold text-yellow-800 uppercase mb-3 tracking-wider">Detail Paket</h5>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="block text-gray-400 text-xs">Paket</span>
                                    <span class="font-bold text-gray-900" x-text="selectedBooking.package_name"></span>
                                </div>
                                <div>
                                    <span class="block text-gray-400 text-xs">Durasi</span>
                                    <span class="font-bold text-gray-900"><span x-text="selectedBooking.duration_hours"></span> Jam</span>
                                </div>
                                <div>
                                    <span class="block text-gray-400 text-xs">Waktu</span>
                                    <span class="font-bold text-gray-900"><span x-text="formatTime(selectedBooking.event_time)"></span> – <span x-text="calculateEndTime(selectedBooking.event_time, selectedBooking.duration_hours)"></span></span>
                                </div>
                                <div>
                                    <span class="block text-gray-400 text-xs">Status</span>
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold"
                                          :class="{
                                              'bg-green-50 text-green-700': selectedBooking.status === 'LUNAS',
                                              'bg-blue-50 text-blue-700': selectedBooking.status === 'DP_DIBAYAR',
                                              'bg-yellow-50 text-yellow-700': selectedBooking.status === 'PENDING',
                                              'bg-gray-100 text-gray-600': selectedBooking.status === 'DIBATALKAN' || !selectedBooking.status
                                          }"
                                          x-text="selectedBooking.status || 'PENDING'"></span>
                                </div>
                                <div>
                                    <span class="block text-gray-400 text-xs">Total</span>
                                    <span class="font-bold text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedBooking.price_total || 0)"></span>
                                </div>
                                <div x-show="selectedBooking.dp_amount > 0">
                                    <span class="block text-gray-400 text-xs">DP Terbayar</span>
                                    <span class="font-bold text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedBooking.dp_amount || 0)"></span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h5 class="text-xs font-bold text-gray-400 uppercase mb-2 tracking-wider">Lokasi Acara</h5>
                            <p class="text-sm text-gray-900 mb-2" x-text="selectedBooking.event_location || '-'"></p>
                            <template x-if="selectedBooking.event_maps_link">
                                <a :href="selectedBooking.event_maps_link" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Buka di Google Maps
                                </a>
                            </template>
                        </div>

                        <template x-if="selectedBooking.notes">
                            <div>
                                <h5 class="text-xs font-bold text-gray-400 uppercase mb-2 tracking-wider">Catatan</h5>
                                <p class="text-sm text-gray-600 italic" x-text="selectedBooking.notes"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="p-6 border-t bg-gray-50 rounded-b-2xl grid grid-cols-2 gap-3">
                    <a :href="'https://wa.me/' + formatWhatsApp(selectedBooking.customer_phone)" target="_blank"
                       class="flex justify-center items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold py-2.5 px-4 rounded-xl transition text-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        Chat WA
                    </a>
                    <button @click="copyBookingData()" class="flex justify-center items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-2.5 px-4 rounded-xl transition text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        Salin Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bookingManager() {
            return {
                showImageModal: false,
                showDetailModal: false,
                imgSrc: '',
                selectedBooking: {},

                openImageModal(src) {
                    this.imgSrc = src;
                    this.showImageModal = true;
                },

                openDetail(booking) {
                    this.selectedBooking = booking;
                    this.showDetailModal = true;
                },

                formatDate(dateStr, type) {
                    if (!dateStr) return '';
                    const date = new Date(dateStr);
                    if (type === 'day') return date.getDate();
                    if (type === 'month') return date.toLocaleDateString('id-ID', { month: 'short' });
                    if (type === 'year') return date.getFullYear();
                    return '';
                },

                formatTime(timeStr) {
                    if (!timeStr) return '';
                    return timeStr.substring(0, 5);
                },

                calculateEndTime(startTime, duration) {
                    if (!startTime || !duration) return '';
                    const [h, m] = startTime.split(':').map(Number);
                    const d = new Date();
                    d.setHours(h + parseInt(duration));
                    d.setMinutes(m);
                    return d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');
                },

                formatWhatsApp(phone) {
                    if (!phone) return '';
                    return phone.replace(/^0/, '62').replace(/[^0-9]/g, '');
                },

                copyBookingData() {
                    const b = this.selectedBooking;
                    const eventDate = new Date(b.event_date).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                    const startTime = this.formatTime(b.event_time);
                    const endTime = this.calculateEndTime(b.event_time, b.duration_hours);

                    const text = `*Detail Booking Luminara Photobooth*\n\n` +
                        `Nama: ${b.customer_name}\n` +
                        `Acara: ${b.event_type || '-'}\n` +
                        `Tanggal: ${eventDate}\n` +
                        `Waktu: ${startTime} - ${endTime} WITA\n` +
                        `Paket: ${b.package_name} (${b.duration_hours} Jam)\n` +
                        `Lokasi: ${b.event_location || '-'}\n` +
                        `Maps: ${b.event_maps_link || '-'}\n\n` +
                        `Status: ${b.status}\n` +
                        `Total: Rp ${new Intl.NumberFormat('id-ID').format(b.price_total)}`;

                    navigator.clipboard.writeText(text).then(() => {
                        Swal.fire({ icon: 'success', title: 'Berhasil Disalin', text: 'Data booking telah disalin ke clipboard', showConfirmButton: false, timer: 1500 });
                    }).catch(() => {
                        Swal.fire({ icon: 'error', title: 'Gagal Menyalin', text: 'Perizinan clipboard ditolak.' });
                    });
                }
            }
        }

        function confirmDelete(button) {
            Swal.fire({
                title: 'Hapus Booking?',
                text: "Data yang dihapus tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) button.closest('form').submit();
            })
        }
    </script>
@endsection
