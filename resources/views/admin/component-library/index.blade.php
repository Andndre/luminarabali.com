@extends('layouts.admin')

@section('content')
<div x-data="thumbnailGenerator()" class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Component Library</h1>
        <p class="text-gray-500 text-sm mt-1">Manage reusable blocks and sections for the template editor.</p>
    </div>
    <div class="flex items-center gap-3">
        <template x-if="missingCount > 0">
            <button @click="generateAll()" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition flex items-center gap-2">
                <svg x-show="!isGenerating" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <svg x-show="isGenerating" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span x-text="isGenerating ? `Generating (${current}/${missingCount})...` : 'Generate Missing Thumbnails'"></span>
            </button>
        </template>
        
        <a href="{{ route('admin.component-library.create') }}" class="px-4 py-2 bg-yellow-500 text-black font-semibold rounded-lg hover:bg-yellow-600 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create New
        </a>
    </div>
</div>

<!-- Hidden area for thumbnail rendering -->
<div class="overflow-hidden h-0 w-0 absolute opacity-0" style="pointer-events: none;">
    <div id="render-target" class="w-[800px] h-[450px] bg-white relative"></div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($components as $component)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition">
        <!-- Thumbnail -->
        <div class="aspect-video bg-gray-100 border-b border-gray-100 relative overflow-hidden group">
            @if($component->thumbnail)
                <img src="{{ asset($component->thumbnail) }}" alt="{{ $component->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="text-xs">No Thumbnail</span>
                </div>
            @endif
            
            <!-- Type Badge -->
            <div class="absolute top-2 left-2 flex gap-1">
                <span class="px-2 py-1 text-xs font-semibold rounded-md shadow-sm {{ $component->type === 'section' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ ucfirst($component->type) }}
                </span>
                <span class="px-2 py-1 text-xs font-semibold rounded-md shadow-sm {{ $component->is_public ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $component->is_public ? 'Public' : 'Private' }}
                </span>
            </div>
            
            <!-- Category Badge -->
            <div class="absolute top-2 right-2">
                <span class="px-2 py-1 text-xs font-semibold bg-white/90 backdrop-blur text-gray-800 rounded-md shadow-sm">
                    {{ ucfirst($component->category) }}
                </span>
            </div>
            
            <!-- Hover Actions -->
            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                <a href="{{ route('admin.component-library.edit', $component->id) }}" class="p-2 bg-white text-gray-900 rounded-lg hover:bg-gray-100" title="Edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </a>
                <form action="{{ route('admin.component-library.destroy', $component->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this component?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600" title="Delete">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Info -->
        <div class="p-4 flex-1 flex flex-col">
            <h3 class="font-bold text-gray-900 leading-tight mb-1">{{ $component->name }}</h3>
            <p class="text-xs text-gray-500 font-mono mb-2 truncate" title="{{ $component->slug }}">{{ $component->slug }}</p>
            <p class="text-sm text-gray-600 line-clamp-2 mt-auto">{{ $component->description ?: 'No description provided.' }}</p>
            
            <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    {{ $component->creator->name ?? 'Unknown' }}
                </span>
                <span>{{ count($component->variables ?? []) }} Variables</span>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full py-16 flex flex-col items-center justify-center text-center bg-white rounded-xl border border-gray-100 border-dashed">
        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">No Components Yet</h3>
        <p class="text-gray-500 mb-4 max-w-sm">Create your first reusable component or section to use in the visual template editor.</p>
        <a href="{{ route('admin.component-library.create') }}" class="px-4 py-2 bg-gray-900 text-white font-medium rounded-lg hover:bg-black transition">
            Create Component
        </a>
    </div>
    @endforelse
</div>
</div>

<!-- html2canvas -->
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('thumbnailGenerator', () => ({
        components: @json($components->filter(fn($c) => empty($c->thumbnail))->values()),
        isGenerating: false,
        missingCount: 0,
        current: 0,

        init() {
            this.missingCount = this.components.length;
        },

        async generateAll() {
            if (this.isGenerating || this.missingCount === 0) return;
            this.isGenerating = true;
            this.current = 0;
            
            const target = document.getElementById('render-target');
            
            for (const component of this.components) {
                this.current++;
                let html = component.code;
                
                // Replace variables with default values
                if (component.variables && Array.isArray(component.variables)) {
                    component.variables.forEach(v => {
                        const regex = new RegExp(`\\{\\{\\s*\\$${v.key}\\s*\\}\\}`, 'g');
                        html = html.replace(regex, v.default || `[${v.label}]`);
                    });
                }
                
                target.innerHTML = `<script src="https://cdn.tailwindcss.com"><\/script><div class="relative w-full h-full overflow-hidden">${html}</div>`;
                
                // Wait for Tailwind to process
                await new Promise(r => setTimeout(r, 800));
                
                try {
                    const canvas = await html2canvas(target, {
                        width: 800,
                        height: 450,
                        scale: 1,
                        useCORS: true,
                        allowTaint: true
                    });
                    
                    const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.8));
                    
                    if (blob) {
                        const formData = new FormData();
                        formData.append('thumbnail', blob, 'thumb.jpg');
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                        
                        await fetch(`/admin/api/component-library/${component.id}/thumbnail`, {
                            method: 'POST',
                            body: formData
                        });
                    }
                } catch (err) {
                    console.error('Failed to generate thumbnail for ' + component.name, err);
                }
            }
            
            this.isGenerating = false;
            Swal.fire('Sukses', 'Thumbnail berhasil digenerate!', 'success').then(() => {
                window.location.reload();
            });
        }
    }));
});
</script>
@endsection
