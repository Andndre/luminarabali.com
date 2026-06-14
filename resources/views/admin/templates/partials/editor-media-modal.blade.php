{{-- Modal Media Library --}}
<div id="mediaModal"
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
    <div class="flex h-[90vh] w-full max-w-6xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b bg-gray-50 p-4">
            <h3 class="font-bold text-gray-800">Media Library</h3>
            <button onclick="closeMediaLibrary()" class="text-gray-500 transition hover:text-red-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        <div class="relative flex-1 overflow-hidden">
            <iframe id="mediaIframe" src="" class="h-full w-full border-0"></iframe>
        </div>
    </div>
</div>

<script>
    // Listen for asset selection from iframe
    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'assetSelected') {
            const asset = event.data.asset;
            const url = '/storage/' + asset.file_path;

            // Re-use insertMedia function from editor scripts
            insertMedia(url);
        }
    });
</script>
