{{-- Page Properties Panel (Template Config: music, etc.) --}}
<div x-show="panels.properties" class="order-3 flex h-full w-[400px] shrink-0 flex-col overflow-y-auto border-l border-gray-800 bg-[#1e1e1e]"
    x-data="propertiesForm({{ json_encode($template->meta_data ?: ['bg_music' => '', 'rsvp_enabled' => true]) }})">
    <div class="mx-auto max-w-3xl p-8 text-gray-300">
        <div class="mb-8 border-b border-gray-800 pb-4">
            <h2 class="text-xl font-medium text-white">Template Properties</h2>
            <p class="mt-1 text-sm text-gray-500">Konfigurasi dasar dari template ini.</p>
        </div>

        <div class="space-y-6">
            {{-- Background Music --}}
            <div class="rounded border border-gray-800 bg-[#252526] p-6">
                <label class="mb-2 block text-sm font-medium text-gray-300">Background Music (MP3 URL)</label>
                <div class="flex gap-2">
                    <input type="text" x-model="formData.bg_music" @input="updateJson"
                        id="bg_music_input_field"
                        class="flex-1 rounded border border-gray-700 bg-[#1e1e1e] px-3 py-2 text-gray-300 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm"
                        placeholder="https://example.com/audio.mp3">
                    <button type="button" onclick="openMediaLibrary('audio')"
                        class="rounded border border-gray-700 bg-[#2d2d2d] px-4 py-2 text-sm text-gray-300 transition hover:bg-[#3d3d3d]">Browse</button>
                </div>
            </div>
        </div>
    </div>
</div>
