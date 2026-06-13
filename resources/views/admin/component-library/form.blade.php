@extends('layouts.admin')

@section('content')
<div x-data="componentEditor()" class="h-[calc(100vh-120px)] flex flex-col -mx-4 -mt-4 md:-mx-8 md:-mt-8">
    
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.component-library.index') }}" class="text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ isset($component) ? 'Edit Component: ' . $component->name : 'Create Component' }}</h1>
                <p class="text-xs text-gray-500 mt-1">Define reusable code snippet and its variables.</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <span x-show="isSaving" class="text-sm text-gray-500" x-cloak>Menyimpan...</span>
            <button @click="save()" class="px-6 py-2 bg-yellow-500 text-black font-semibold rounded-lg hover:bg-yellow-600 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Save
            </button>
        </div>
    </div>

    <!-- Main Content Split -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- Left Panel: Monaco Editor -->
        <div class="w-1/2 flex flex-col border-r border-gray-200 bg-[#1e1e1e]">
            <div class="bg-gray-900 text-gray-400 text-xs px-4 py-2 border-b border-gray-800 flex justify-between">
                <span>Code Editor (Blade/HTML)</span>
                <span>Use @{{ $variable_name }} for placeholders</span>
            </div>
            <div id="monaco-editor" class="flex-1 w-full relative"></div>
        </div>
        
        <!-- Right Panel: Variables & Settings -->
        <div class="w-1/2 flex flex-col bg-gray-50 overflow-y-auto">
            
            <div class="p-6 space-y-6">
                <!-- General Settings -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4 border-b pb-2">General Settings</h3>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" x-model="form.name" @input="generateSlug" class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <input type="text" x-model="form.slug" class="w-full rounded-md border-gray-300 shadow-sm bg-gray-50 text-sm" readonly>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select x-model="form.category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-sm">
                                <option value="cover">Cover</option>
                                <option value="hero">Hero</option>
                                <option value="text">Text & Typography</option>
                                <option value="event">Event Details</option>
                                <option value="gallery">Gallery</option>
                                <option value="countdown">Countdown</option>
                                <option value="rsvp">RSVP</option>
                                <option value="map">Map</option>
                                <option value="video">Video</option>
                                <option value="button">Button / CTA</option>
                                <option value="divider">Divider & Spacer</option>
                                <option value="footer">Footer</option>
                                <option value="section">Complete Section (Composition)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select x-model="form.type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-sm">
                                <option value="component">Component (Single Block)</option>
                                <option value="section">Section (Multiple Blocks)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea x-model="form.description" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-sm"></textarea>
                    </div>

                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="form.is_public" class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500">
                            <span class="text-sm font-medium text-gray-700">Public (Visible to other creators)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="form.is_active" class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500">
                            <span class="text-sm font-medium text-gray-700">Active</span>
                        </label>
                    </div>
                </div>

                <!-- Variable Builder -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="font-bold text-gray-900">Variables</h3>
                        <button @click="addVariable()" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Variable
                        </button>
                    </div>
                    
                    <div x-show="form.variables.length === 0" class="text-center py-6 text-gray-500 text-sm italic border border-dashed rounded-lg">
                        No variables defined yet. Add variables to make your component dynamic.
                    </div>
                    
                    <div class="space-y-4">
                        <template x-for="(variable, index) in form.variables" :key="index">
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 relative group">
                                <button @click="removeVariable(index)" class="absolute top-2 right-2 text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                
                                <div class="grid grid-cols-12 gap-3">
                                    <div class="col-span-4">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Key ($name)</label>
                                        <input type="text" x-model="variable.key" class="w-full rounded text-sm border-gray-300 py-1.5 px-2 font-mono">
                                    </div>
                                    <div class="col-span-4">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Label (UI)</label>
                                        <input type="text" x-model="variable.label" class="w-full rounded text-sm border-gray-300 py-1.5 px-2">
                                    </div>
                                    <div class="col-span-4">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                                        <select x-model="variable.type" class="w-full rounded text-sm border-gray-300 py-1.5 px-2">
                                            <option value="text">Text</option>
                                            <option value="textarea">Textarea</option>
                                            <option value="image">Image</option>
                                            <option value="image_list">Image List</option>
                                            <option value="boolean">Boolean</option>
                                            <option value="select">Select</option>
                                            <option value="color">Color</option>
                                            <option value="range">Range Slider</option>
                                        </select>
                                    </div>
                                    <div class="col-span-6">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Default Value</label>
                                        <input type="text" x-model="variable.default" class="w-full rounded text-sm border-gray-300 py-1.5 px-2">
                                    </div>
                                    <div class="col-span-6">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Help Text (Optional)</label>
                                        <input type="text" x-model="variable.description" class="w-full rounded text-sm border-gray-300 py-1.5 px-2">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Live Preview / Thumbnail Capture Area -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4 border-b pb-2">Thumbnail</h3>
                    
                    <div class="flex gap-4 items-start">
                        <div class="w-1/2 aspect-video bg-gray-100 rounded-lg overflow-hidden border border-gray-200 flex items-center justify-center relative">
                            <template x-if="thumbnailUrl">
                                <img :src="thumbnailUrl" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!thumbnailUrl">
                                <span class="text-sm text-gray-400">No thumbnail</span>
                            </template>
                        </div>
                        
                        <div class="flex-1 space-y-3">
                            <p class="text-xs text-gray-500">Thumbnail generated automatically via html2canvas when you save. You can also force generate it below.</p>
                            
                            <button @click="generateThumbnail()" class="px-4 py-2 border border-gray-300 bg-white text-sm font-medium rounded-lg hover:bg-gray-50 transition w-full">
                                Capture Thumbnail Now
                            </button>
                        </div>
                    </div>

                    <!-- Hidden div for html2canvas rendering -->
                    <div class="overflow-hidden h-0 w-0 absolute">
                        <div id="render-target" class="w-[800px] h-[450px] bg-white relative"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Monaco Editor -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/loader.min.js"></script>
<!-- html2canvas for thumbnail generation -->
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('componentEditor', () => ({
        isSaving: false,
        editor: null,
        thumbnailUrl: '{{ isset($component) && $component->thumbnail ? asset($component->thumbnail) : '' }}',
        thumbnailBlob: null,
        
        form: {
            id: {{ $component->id ?? 'null' }},
            name: @json($component->name ?? ''),
            slug: @json($component->slug ?? ''),
            category: @json($component->category ?? 'hero'),
            type: @json($component->type ?? 'component'),
            description: @json($component->description ?? ''),
            is_public: {{ isset($component) ? ($component->is_public ? 'true' : 'false') : 'true' }},
            is_active: {{ isset($component) ? ($component->is_active ? 'true' : 'false') : 'true' }},
            variables: @json(isset($component) ? $component->variables : []),
            code: @json($component->code ?? '<section class="relative">\n    <!-- write blade code here -->\n</section>')
        },

        init() {
            this.initMonaco();
        },

        generateSlug() {
            if(!this.form.id) { // Only auto-generate on create
                this.form.slug = this.form.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
            }
        },

        addVariable() {
            this.form.variables.push({
                key: 'var_' + (this.form.variables.length + 1),
                label: 'New Variable',
                type: 'text',
                default: '',
                description: ''
            });
        },

        removeVariable(index) {
            this.form.variables.splice(index, 1);
        },

        initMonaco() {
            require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' }});
            require(['vs/editor/editor.main'], () => {
                this.editor = monaco.editor.create(document.getElementById('monaco-editor'), {
                    value: this.form.code,
                    language: 'html',
                    theme: 'vs-dark',
                    automaticLayout: true,
                    minimap: { enabled: false },
                    wordWrap: 'on',
                    fontSize: 14,
                    padding: { top: 16 }
                });

                // Ctrl+S to save
                this.editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS, () => {
                    this.save();
                });
            });
        },

        async generateThumbnail() {
            if(!this.editor) return;
            
            // 1. Prepare render target
            const target = document.getElementById('render-target');
            let html = this.editor.getValue();
            
            // Inject default variables for preview
            this.form.variables.forEach(v => {
                const regex = new RegExp(`\\{\\{\\s*\\$${v.key}\\s*\\}\\}`, 'g');
                html = html.replace(regex, v.default || `[${v.label}]`);
            });
            
            // Basic Tailwind injection for preview
            target.innerHTML = `<script src="https://cdn.tailwindcss.com"><\/script><div class="relative w-full h-full overflow-hidden">${html}</div>`;
            
            // Wait for tailwind to process
            await new Promise(r => setTimeout(r, 500));
            
            try {
                // 2. Capture with html2canvas
                const canvas = await html2canvas(target, {
                    width: 800,
                    height: 450,
                    scale: 1, // keep it small
                    useCORS: true,
                    allowTaint: true
                });
                
                // 3. Convert to blob
                return new Promise(resolve => {
                    canvas.toBlob(blob => {
                        this.thumbnailBlob = blob;
                        this.thumbnailUrl = URL.createObjectURL(blob);
                        resolve(blob);
                    }, 'image/jpeg', 0.8);
                });
            } catch (err) {
                console.error('Failed to generate thumbnail', err);
            }
        },

        async save() {
            if(this.isSaving) return;
            
            // Validate
            if(!this.form.name || !this.form.slug) {
                Swal.fire('Error', 'Name and Slug are required.', 'error');
                return;
            }

            this.isSaving = true;
            this.form.code = this.editor.getValue();
            
            // Try generating thumbnail if we don't have one
            await this.generateThumbnail();

            const isUpdate = !!this.form.id;
            const url = isUpdate 
                ? `/admin/component-library/${this.form.id}` 
                : `/admin/component-library`;
                
            const method = isUpdate ? 'PUT' : 'POST';
            
            // Prepare data
            const data = {
                ...this.form,
                variables: JSON.stringify(this.form.variables),
                _token: document.querySelector('meta[name="csrf-token"]').content
            };

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if(result.success) {
                    this.form.id = result.component.id;
                    
                    // Upload thumbnail if we generated one
                    if(this.thumbnailBlob) {
                        const formData = new FormData();
                        formData.append('thumbnail', this.thumbnailBlob, 'thumb.jpg');
                        formData.append('_token', data._token);
                        
                        await fetch(`/admin/api/component-library/${this.form.id}/thumbnail`, {
                            method: 'POST',
                            body: formData
                        });
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    
                    if(!isUpdate) {
                        // Update URL without reload to change to edit mode
                        window.history.replaceState({}, '', `/admin/component-library/${this.form.id}/edit`);
                    }
                } else {
                    throw new Error(result.message || 'Validation failed');
                }
            } catch (error) {
                Swal.fire('Error', 'Failed to save component. ' + error.message, 'error');
            } finally {
                this.isSaving = false;
            }
        }
    }));
});
</script>
@endsection
