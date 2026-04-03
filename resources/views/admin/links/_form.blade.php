@php
    $isEdit = isset($link) && $link !== null;
    $route = $isEdit ? route('admin.links.update', $link->id) : route('admin.links.store');
    $method = $isEdit ? 'PUT' : 'POST';

    $iconOptions = [
        'instagram', 'whatsapp', 'tiktok', 'youtube', 'facebook',
        'twitter', 'website', 'email', 'phone', 'shop',
        'location', 'telegram', 'linkedin', 'youtube-music', 'shopee',
    ];
@endphp

<form action="{{ $route }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="linkForm()">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $link?->title) }}"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('title') border-red-500 @enderror"
                placeholder="Contoh: Galeri Prewedding" required>
            @error('title')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">URL <span class="text-red-500">*</span></label>
            <input type="url" name="url" value="{{ old('url', $link?->url) }}"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('url') border-red-500 @enderror"
                placeholder="https://example.com" required>
            @error('url')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        @if(auth()->user()->division === 'super_admin')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Division</label>
            <select name="business_unit"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('business_unit') border-red-500 @enderror">
                <option value="photobooth" {{ old('business_unit', $link?->business_unit) === 'photobooth' ? 'selected' : '' }}>Photobooth</option>
                <option value="visual" {{ old('business_unit', $link?->business_unit) === 'visual' ? 'selected' : '' }}>Visual</option>
            </select>
            @error('business_unit')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
            <input type="file" name="thumbnail" id="thumbnailInput"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('thumbnail') border-red-500 @enderror"
                accept="image/*" @change="previewThumbnail()">
            <p class="mt-1 text-xs text-gray-400">Format: JPG, PNG, WEBP. Maks: 2MB.</p>
            @error('thumbnail')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
            <input type="number" name="order" value="{{ old('order', $link?->order ?? 0) }}" min="0"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('order') border-red-500 @enderror">
            <p class="mt-1 text-xs text-gray-400">Angka kecil akan tampil lebih dulu.</p>
            @error('order')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Icon Picker --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
        <input type="hidden" name="icon" :value="selectedIcon">

        <div class="mb-3 flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white border border-gray-200 shrink-0 overflow-hidden">
                <template x-if="selectedIcon">
                    <img :src="'/images/icons/' + selectedIcon + '.svg'" class="h-6 w-6 text-gray-700" alt="">
                </template>
                <template x-if="!selectedIcon">
                    <svg class="h-6 w-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                </template>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700" x-text="selectedIcon ? selectedIcon.charAt(0).toUpperCase() + selectedIcon.slice(1) : 'Tidak ada icon'"></p>
                <p class="text-xs text-gray-400" x-show="!selectedIcon && !selectedThumbnail">Thumbnail akan selalu digunakan jika tersedia; icon hanya digunakan jika thumbnail tidak ada.</p>
                <button type="button" @click="selectedIcon = null" x-show="selectedIcon" x-transition class="text-xs text-red-500 hover:text-red-700">Hapus icon</button>
            </div>
        </div>

        <div class="grid grid-cols-6 gap-2 rounded-lg border border-gray-200 bg-white p-3 max-h-48 overflow-y-auto">
            @foreach($iconOptions as $key)
                <button type="button"
                    @click="selectedIcon = selectedIcon === '{{ $key }}' ? null : '{{ $key }}'"
                    :class="selectedIcon === '{{ $key }}' ? 'ring-2 ring-yellow-500 bg-yellow-50' : 'hover:bg-gray-50'"
                    class="flex aspect-square items-center justify-center rounded-lg border border-gray-200 transition-all p-2"
                    title="{{ ucfirst(str_replace('-', ' ', $key)) }}">
                    <img src="{{ asset('images/icons/' . $key . '.svg') }}"
                        class="h-5 w-5 text-gray-700" alt="{{ $key }}">
                </button>
            @endforeach
        </div>
        <p class="mt-1 text-xs text-gray-400">Klik icon untuk memilih. Thumbnail akan digunakan sebagai prioritas jika ada.</p>
    </div>

    <div class="flex items-start gap-3">
        <input type="checkbox" name="is_active" id="is_active" value="1"
            {{ old('is_active', $link?->is_active ?? true) ? 'checked' : '' }}
            class="mt-1 h-4 w-4 rounded border-gray-300 text-yellow-500 focus:ring-yellow-500">
        <div>
            <label for="is_active" class="text-sm font-medium text-gray-700">Tampilkan link ini</label>
            <p class="text-xs text-gray-400">Jika diceklis, link akan muncul di halaman publik.</p>
        </div>
    </div>

    {{-- Thumbnail Preview --}}
    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4" x-show="thumbnailPreview || '{{ $link?->thumbnail }}'">
        <p class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-400">Preview Thumbnail</p>
        <div class="flex items-center gap-4">
            <template x-if="thumbnailPreview">
                <img :src="thumbnailPreview" class="h-20 w-20 rounded-lg object-cover border border-gray-200">
            </template>
            <template x-if="!thumbnailPreview && '{{ $link?->thumbnail }}'">
                <img src="{{ $link?->thumbnail ? asset('storage/' . $link->thumbnail) : '' }}"
                    class="h-20 w-20 rounded-lg object-cover border border-gray-200">
            </template>
            <template x-if="!thumbnailPreview && '{{ $link?->thumbnail }}'">
                <div class="flex flex-col gap-1">
                    <p class="text-xs text-gray-500">Thumbnail saat ini</p>
                    <p class="text-xs text-gray-400">Upload file baru untuk mengganti.</p>
                </div>
            </template>
        </div>
    </div>

    <div class="pt-4 border-t flex justify-end gap-3">
        <a href="{{ route('admin.links.index') }}"
            class="rounded-xl border border-gray-300 bg-white px-6 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
            Batal
        </a>
        <button type="submit"
            class="rounded-xl bg-black px-6 py-2.5 text-sm font-bold text-white shadow-lg transition hover:bg-gray-800">
            {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Link' }}
        </button>
    </div>
</form>

<script>
    function linkForm() {
        return {
            thumbnailPreview: null,
            selectedIcon: '{{ old('icon', $link?->icon ?? '') }}',
            get selectedThumbnail() {
                return this.thumbnailPreview || '{{ $link?->thumbnail }}';
            },

            previewThumbnail() {
                const input = document.getElementById('thumbnailInput');
                const file = input.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.thumbnailPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.thumbnailPreview = null;
                }
            }
        }
    }
</script>
