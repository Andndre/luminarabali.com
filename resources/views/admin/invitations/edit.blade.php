@extends('layouts.admin')

@section('title', 'Edit Undangan')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.invitations.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-4">Edit Undangan</h1>
        <p class="text-gray-600 mt-1">Ubah data undangan</p>
    </div>

    <form action="{{ route('admin.invitations.update', $invitation->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Invitation Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Detail Undangan</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Undangan *</label>
                    <input type="text" name="title" value="{{ old('title', $invitation->title) }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pria *</label>
                        <input type="text" name="groom_name" value="{{ old('groom_name', $invitation->groom_name) }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        @error('groom_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Wanita *</label>
                        <input type="text" name="bride_name" value="{{ old('bride_name', $invitation->bride_name) }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        @error('bride_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Acara *</label>
                    <input type="date" name="event_date" value="{{ old('event_date', $invitation->event_date?->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    @error('event_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug / URL Undangan *</label>
                        <div class="flex rounded-lg shadow-sm">
                          <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                            {{ rtrim(url('/invitation/'), '/') }}/
                          </span>
                          <input type="text" name="slug" value="{{ old('slug', $invitation->slug) }}" required
                                 class="flex-1 min-w-0 block w-full px-4 py-2 rounded-none rounded-r-lg border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        </div>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <div class="px-4 py-2 border rounded-lg bg-gray-50">
                            @if($invitation->published_status === 'published')
                                <span class="text-green-800 font-medium">Published</span>
                            @elseif($invitation->published_status === 'draft')
                                <span class="text-yellow-800 font-medium">Draft</span>
                            @else
                                <span class="text-gray-800 font-medium">Archived</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Data (JSON Override)</label>
                    <textarea name="meta_data" rows="5"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent font-mono text-sm"
                              placeholder="{'bg_music': '...', 'custom_image': '...'}">{{ old('meta_data', $invitation->meta_data ? json_encode($invitation->meta_data, JSON_PRETTY_PRINT) : '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Opsional. Gunakan format JSON untuk menimpa properti spesifik halaman ini (misal: bg_music atau image_url).</p>
                    @error('meta_data')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between">
            <a href="{{ route('invitation.show', $invitation->slug) }}" target="_blank" class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition flex items-center text-blue-600 border-blue-200 bg-blue-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Lihat Undangan (Live)
            </a>

            <div class="flex gap-3">
                <a href="{{ route('admin.invitations.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

    <!-- Link Generator Utility -->
    <div id="link-generator" class="bg-linear-to-br from-gray-900 to-black rounded-2xl shadow-lg border border-gray-800 p-6 text-white mt-8" x-data="linkGenerator('{{ url('/invitation/' . $invitation->slug) }}')">
        <h2 class="text-lg font-semibold mb-2 flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
            Guest Link Generator
        </h2>
        <p class="text-sm text-gray-400 mb-6">Buat tautan personal (VIP) untuk tamu Anda dan bagikan langsung via WhatsApp.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Nama Tamu</label>
                    <input type="text" x-model="guestName" @input="updateLink"
                           class="w-full px-4 py-2 border border-gray-700 bg-gray-800 text-white rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent placeholder-gray-500"
                           placeholder="Contoh: Bapak Jokowi">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Preview URL</label>
                    <div class="p-3 bg-gray-950 rounded-lg text-sm text-gray-300 font-mono break-all border border-gray-800" x-text="generatedLink"></div>
                </div>
            </div>
            
            <div class="space-y-3 flex flex-col justify-end">
                <button type="button" @click="copyLink" 
                        class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-white text-black font-medium rounded-xl hover:bg-gray-200 transition">
                    <svg x-show="!copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <svg x-show="copied" style="display: none;" class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span x-text="copied ? 'Tersalin!' : 'Copy Link'"></span>
                </button>
                
                <a :href="waLink" target="_blank"
                   class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-[#25D366] text-white font-medium rounded-xl hover:bg-[#20bd5a] transition"
                   :class="{'opacity-50 pointer-events-none': !guestName}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824zm-3.423-14.416c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm.029 18.88c-1.161 0-2.305-.292-3.318-.844l-3.677.964.984-3.595c-.607-1.052-.927-2.246-.926-3.468.001-3.825 3.113-6.937 6.937-6.937 3.825 0 6.938 3.112 6.938 6.937 0 3.825-3.113 6.938-6.938 6.938z"/></svg>
                    Kirim ke WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('linkGenerator', (baseUrl) => ({
            baseUrl: baseUrl,
            guestName: '',
            generatedLink: baseUrl,
            waLink: '',
            copied: false,
            
            init() {
                this.updateLink();
            },
            
            updateLink() {
                if (this.guestName.trim() === '') {
                    this.generatedLink = this.baseUrl;
                    this.waLink = '';
                    return;
                }
                
                const encodedName = encodeURIComponent(this.guestName);
                this.generatedLink = `${this.baseUrl}?to=${encodedName}`;
                
                const message = `Tanpa mengurangi rasa hormat, kami mengundang Bapak/Ibu/Saudara/i ${this.guestName} untuk hadir di acara pernikahan kami. Berikut tautan undangan Anda: \n\n${this.generatedLink}`;
                this.waLink = `https://wa.me/?text=${encodeURIComponent(message)}`;
            },
            
            copyLink() {
                navigator.clipboard.writeText(this.generatedLink).then(() => {
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                });
            }
        }))
    });
</script>
@endpush
@endsection
