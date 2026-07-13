@extends('layouts.admin')

@section('title', 'Media Library')

@section('content')
<div class="p-6" x-data="mediaLibrary()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Media Library</h1>
            <p class="text-gray-600 mt-1">Kelola foto dan video untuk undangan digital</p>
        </div>
        <button @click="openUploadModal()" class="mt-4 md:mt-0 bg-black text-white font-semibold py-2 px-6 rounded-xl shadow-lg hover:bg-gray-800 transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Upload Media
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <input type="text" x-model="search" @input="debouncedSearch()" placeholder="Cari media..." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
            </div>

            <!-- Filter by Type -->
            <div class="w-full md:w-48">
                <select x-model="filterType" @change="loadAssets()" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    <option value="">Semua Tipe</option>
                    <option value="image">Gambar</option>
                    <option value="video">Video</option>
                    <option value="audio">Audio</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Media Grid -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div x-show="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-yellow-500"></div>
            <p class="mt-4 text-gray-600">Memuat media...</p>
        </div>

        <div x-show="!loading && assets.length === 0" class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-600">Belum ada media. Upload media pertama Anda!</p>
        </div>

        <div x-show="!loading && assets.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            <template x-for="asset in assets" :key="asset.id">
                <div class="relative group aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer border-2 border-transparent hover:border-yellow-500 transition" @click="selectAsset(asset)">
                    <!-- Image -->
                    <template x-if="asset.file_type === 'image'">
                        <img :src="'/storage/' + asset.file_path" :alt="asset.alt_text || asset.asset_name" class="w-full h-full object-cover" loading="lazy">
                    </template>

                    <!-- Video -->
                    <template x-if="asset.file_type === 'video'">
                        <div class="w-full h-full flex items-center justify-center bg-gray-900">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </template>

                    <!-- Audio -->
                    <template x-if="asset.file_type === 'audio'">
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-purple-500 to-pink-500">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                            </svg>
                        </div>
                    </template>

                    <!-- Overlay with actions -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <div class="flex gap-2">
                            <button @click.stop="previewAsset(asset)" class="p-2 bg-white rounded-full hover:bg-gray-200 transition" title="Preview">
                                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <button @click.stop="openRenameModal(asset)" class="p-2 bg-white rounded-full hover:bg-gray-200 transition" title="Rename">
                                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button @click.stop="confirmDelete(asset)" class="p-2 bg-red-500 rounded-full hover:bg-red-600 transition" title="Hapus">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Asset info badge -->
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2 opacity-0 group-hover:opacity-100 transition">
                        <p class="text-white text-xs truncate" x-text="asset.asset_name"></p>
                    </div>
                </div>
            </template>
        </div>

        <!-- Pagination -->
        <div x-show="!loading && assets.length > 0" class="mt-6 flex justify-center">
            <nav class="flex gap-2">
                <button x-show="pagination.current_page > 1" @click="loadAssets(pagination.current_page - 1)" class="px-4 py-2 border rounded-lg hover:bg-gray-100 transition">Previous</button>
                <template x-for="page in pagination.last_page" :key="page">
                    <button x-text="page" @click="loadAssets(page)" :class="pagination.current_page === page ? 'bg-yellow-500 text-black' : 'hover:bg-gray-100'" class="px-4 py-2 border rounded-lg transition"></button>
                </template>
                <button x-show="pagination.current_page < pagination.last_page" @click="loadAssets(pagination.current_page + 1)" class="px-4 py-2 border rounded-lg hover:bg-gray-100 transition">Next</button>
            </nav>
        </div>
    </div>

    <!-- Upload Modal -->
    <div x-show="uploadModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="absolute inset-0 bg-black bg-opacity-50" @click="closeUploadModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6">
            <h2 class="text-xl font-bold mb-4">Upload Media</h2>

            <!-- Drop Zone -->
            <div
                class="border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition"
                :class="dragging ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300 hover:border-yellow-500'"
                @dragover.prevent="dragging = true"
                @dragleave.prevent="dragging = false"
                @drop.prevent="handleDrop($event)"
                @click="$refs.fileInput.click()"
            >
                <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" multiple accept="image/*,video/*,audio/*" class="hidden">

                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p class="text-gray-600 mb-2">Drag & drop files di sini</p>
                <p class="text-gray-400 text-sm">atau klik untuk memilih file</p>
                <p class="text-gray-400 text-xs mt-2">Maksimal 10MB per file. Format: JPG, PNG, WEBP, MP4, MP3</p>
            </div>

            <!-- Koleksi (mis. ornament → muncul di picker ornamen Studio) -->
            <div class="mt-4">
                <label class="block text-sm text-gray-600 mb-1">Koleksi (opsional)</label>
                <select x-model="uploadCollection" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">— Tanpa koleksi —</option>
                    <option value="ornament">Ornamen</option>
                </select>
            </div>

            <!-- Upload Progress -->
            <div x-show="uploading" class="mt-4">
                <div class="flex justify-between text-sm mb-2">
                    <span x-text="uploadStatus"></span>
                    <span x-text="uploadProgress + '%'"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-500 h-2 rounded-full transition-all duration-300" :style="'width: ' + uploadProgress + '%'"></div>
                </div>
            </div>

            <!-- Uploaded Files List -->
            <div x-show="uploadedFiles.length > 0" class="mt-4 max-h-48 overflow-y-auto">
                <template x-for="file in uploadedFiles" :key="file.name">
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg mb-2">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm truncate" x-text="file.name"></span>
                        </div>
                        <span class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></span>
                    </div>
                </template>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button @click="closeUploadModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-100 transition">Tutup</button>
                <button x-show="!uploading && uploadedFiles.length > 0" @click="resetAndClose()" class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition">Selesai</button>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div x-show="previewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="absolute inset-0 bg-black bg-opacity-75" @click="closePreviewModal()"></div>
        <div class="relative max-w-4xl w-full max-h-screen overflow-auto">
            <!-- Image Preview -->
            <template x-if="selectedAsset?.file_type === 'image'">
                <img :src="'/storage/' + selectedAsset?.file_path" class="max-w-full h-auto rounded-lg shadow-xl">
            </template>

            <!-- Video Preview -->
            <template x-if="selectedAsset?.file_type === 'video'">
                <video :src="'/storage/' + selectedAsset?.file_path" controls class="max-w-full h-auto rounded-lg shadow-xl"></video>
            </template>

            <!-- Audio Preview -->
            <template x-if="selectedAsset?.file_type === 'audio'">
                <audio :src="'/storage/' + selectedAsset?.file_path" controls class="w-full"></audio>
            </template>

            <button @click="closePreviewModal()" class="absolute top-4 right-4 p-2 bg-white rounded-full hover:bg-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Rename Modal -->
    <div x-show="renameModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="absolute inset-0 bg-black bg-opacity-50" @click="closeRenameModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <h2 class="text-xl font-bold mb-4">Rename File</h2>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">New Name</label>
                <input type="text" x-model="newAssetName" @keyup.enter="saveRename()" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" placeholder="Enter new name">
            </div>

            <div class="flex justify-end gap-3">
                <button @click="closeRenameModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-100 transition">Cancel</button>
                <button @click="saveRename()" :disabled="!newAssetName || renaming" class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!renaming">Save</span>
                    <span x-show="renaming">Saving...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function mediaLibrary() {
    return {
        assets: [],
        loading: true,
        search: '',
        filterType: '',
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 50
        },
        uploadModal: false,
        previewModal: false,
        renameModal: false,
        selectedAsset: null,
        dragging: false,
        uploading: false,
        uploadProgress: 0,
        uploadStatus: '',
        uploadedFiles: [],
        uploadCollection: '',
        searchTimeout: null,
        pageId: new URLSearchParams(window.location.search).get('page_id') || '',
        newAssetName: '',
        renaming: false,

        init() {
            this.loadAssets();
        },

        async loadAssets(page = 1) {
            this.loading = true;

            try {
                const params = new URLSearchParams({
                    page: page,
                    ...(this.filterType && { file_type: this.filterType }),
                    ...(this.search && { search: this.search }),
                    ...(this.pageId && { page_id: this.pageId })
                });

                const response = await fetch(`/admin/api/assets?${params}`);
                const data = await response.json();

                this.assets = data.data;
                this.pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    per_page: data.per_page
                };
            } catch (error) {
                console.error('Error loading assets:', error);
                Swal.fire('Error', 'Gagal memuat media', 'error');
            } finally {
                this.loading = false;
            }
        },

        debouncedSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.loadAssets(1);
            }, 500);
        },

        openUploadModal() {
            this.uploadModal = true;
            this.uploadedFiles = [];
            this.uploadProgress = 0;
        },

        closeUploadModal() {
            this.uploadModal = false;
            this.uploading = false;
            this.uploadProgress = 0;
            this.uploadStatus = '';
        },

        async resetAndClose() {
            this.uploadedFiles = [];
            this.closeUploadModal();
            await this.loadAssets(1);
        },

        handleDrop(event) {
            this.dragging = false;
            const files = event.dataTransfer.files;
            this.uploadFiles(files);
        },

        handleFileSelect(event) {
            const files = event.target.files;
            this.uploadFiles(files);
        },

        async uploadFiles(files) {
            this.uploading = true;
            this.uploadProgress = 0;

            const totalFiles = files.length;
            let uploadedCount = 0;

            for (let i = 0; i < totalFiles; i++) {
                const file = files[i];
                const formData = new FormData();
                formData.append('file', file);
                if (this.pageId) {
                    formData.append('page_id', this.pageId);
                }
                if (this.uploadCollection) {
                    formData.append('collection', this.uploadCollection);
                }

                try {
                    this.uploadStatus = `Uploading ${file.name}...`;

                    const response = await fetch('/admin/api/assets/upload', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    });

                    if (response.ok) {
                        this.uploadedFiles.push({
                            name: file.name,
                            size: file.size
                        });
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    Swal.fire('Error', `Gagal upload ${file.name}`, 'error');
                }

                uploadedCount++;
                this.uploadProgress = Math.round((uploadedCount / totalFiles) * 100);
            }

            this.uploadStatus = 'Upload complete!';

            setTimeout(() => {
                this.uploading = false;
            }, 1000);
        },

        selectAsset(asset) {
            // If opened in modal/iframe, send message to parent
            if (window.opener || window.parent !== window) {
                // Alpine proxies cannot be cloned by postMessage, so we need to unwrap it using JSON parse
                const rawAsset = JSON.parse(JSON.stringify(asset));
                window.parent.postMessage({
                    type: 'assetSelected',
                    asset: rawAsset
                }, window.location.origin);
            }
        },

        previewAsset(asset) {
            this.selectedAsset = asset;
            this.previewModal = true;
        },

        closePreviewModal() {
            this.previewModal = false;
            this.selectedAsset = null;
        },

        openRenameModal(asset) {
            this.selectedAsset = asset;
            this.newAssetName = asset.asset_name;
            this.renameModal = true;
        },

        closeRenameModal() {
            this.renameModal = false;
            this.newAssetName = '';
            this.selectedAsset = null;
        },

        async saveRename() {
            if (!this.newAssetName || !this.selectedAsset) return;

            this.renaming = true;

            try {
                const response = await fetch(`/admin/api/assets/${this.selectedAsset.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        asset_name: this.newAssetName
                    })
                });

                if (response.ok) {
                    // Update local asset name
                    const assetIndex = this.assets.findIndex(a => a.id === this.selectedAsset.id);
                    if (assetIndex !== -1) {
                        this.assets[assetIndex].asset_name = this.newAssetName;
                    }
                    Swal.fire('Success', 'File renamed successfully', 'success');
                    this.closeRenameModal();
                }
            } catch (error) {
                console.error('Rename error:', error);
                Swal.fire('Error', 'Failed to rename file', 'error');
            } finally {
                this.renaming = false;
            }
        },

        async confirmDelete(asset) {
            const result = await Swal.fire({
                title: 'Hapus Media?',
                text: `Anda yakin ingin menghapus "${asset.asset_name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/admin/api/assets/${asset.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (response.ok) {
                        Swal.fire('Terhapus!', 'Media berhasil dihapus', 'success');
                        await this.loadAssets(this.pagination.current_page);
                    }
                } catch (error) {
                    console.error('Delete error:', error);
                    Swal.fire('Error', 'Gagal menghapus media', 'error');
                }
            }
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    };
}
</script>
@endsection

