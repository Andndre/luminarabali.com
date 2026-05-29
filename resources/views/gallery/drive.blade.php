<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luminara Photobooth - Digital Client Gallery</title>

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS v4 CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    },
                    scale: {
                        102: '1.02',
                    }
                }
            }
        }
    </script>

    <style>
        .bg-maroon {
            background-color: #450000;
        }

        .bg-maroon-dark {
            background-color: #491919;
        }

        /* Custom scrollbar matching premium theme */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #491919;
        }

        ::-webkit-scrollbar-thumb {
            background: #450000;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #ec4899;
        }
    </style>
</head>

<body
    class="bg-maroon-dark min-h-screen overflow-x-hidden font-sans text-slate-100 antialiased selection:bg-pink-600 selection:text-white">

    <!-- Ambient Glowing Accents -->
    <div
        class="pointer-events-none fixed z-0 left-[-10%] top-[-10%] h-[50vw] w-[50vw] rounded-full bg-pink-900/10 blur-[120px]">
    </div>
    <div
        class="pointer-events-none fixed z-0 bottom-[-10%] right-[-10%] h-[50vw] w-[50vw] rounded-full bg-rose-900/10 blur-[120px]">
    </div>

    <!-- Header Section -->
    <header class="relative z-10 mx-auto max-w-7xl px-6 pb-8 pt-10 text-center">
        <!-- Logo Branding matching Linktree -->
        <div
            class="mx-auto mb-4 flex h-20 w-20 items-center justify-center overflow-hidden rounded-full border border-white/20 bg-white shadow-xl ring-4 ring-pink-500/10 transition-all duration-300 active:scale-95">
            <img src="{{ asset('images/logo.png') }}" alt="Luminara Logo" class="h-full w-full object-contain p-1">
        </div>

        <!-- Sleek Digital Badge -->
        <div
            class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 shadow-inner shadow-white/5 backdrop-blur-md">
            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-pink-500 shadow-md shadow-pink-500"></span>
            <span class="font-outfit text-[10px] font-bold uppercase tracking-wider text-pink-300">Live Client
                Gallery</span>
        </div>

        <!-- High-Impact Title -->
        <h1 class="font-outfit mb-3 text-3xl font-extrabold tracking-tight text-white md:text-4xl">
            Luminara <span
                class="bg-linear-to-r from-pink-400 via-rose-400 to-amber-300 bg-clip-text text-transparent">Photobooth</span>
        </h1>

        <!-- Compact Responsive Subtitle -->
        <p class="mx-auto max-w-md px-4 text-xs leading-relaxed text-slate-300/90 md:text-sm">
            Temukan dan unduh seluruh file foto kolase (<span class="font-semibold text-pink-300">Prints</span>), foto
            satuan (<span class="font-semibold text-pink-300">Originals</span>), dan video animasi (<span
                class="font-semibold text-pink-300">Animated</span>) dari sesi photobooth Anda.
        </p>
    </header>

    <!-- Main Workspace Container -->
    <main class="relative z-10 mx-auto max-w-7xl px-6 pb-24">

        <!-- Folder Status Indicator -->
        <div id="loading-status"
            class="bg-maroon/40 mx-auto mb-10 flex max-w-sm items-center justify-center gap-3 rounded-2xl border border-white/10 px-5 py-3 text-sm text-slate-300 backdrop-blur-md transition-all">
            <svg class="h-5 w-5 animate-spin text-pink-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="font-outfit font-semibold">Menghubungkan ke Google Drive...</span>
        </div>

        <!-- Empty & Error States -->
        <div id="error-container"
            class="mx-auto my-8 hidden max-w-2xl rounded-2xl border border-rose-900/20 bg-rose-900/10 p-6 text-center">
            <span class="mb-4 inline-block rounded-full bg-rose-900/20 p-3 text-rose-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </span>
            <h3 class="font-outfit mb-2 text-lg font-bold text-rose-300">Gagal Memuat Galeri</h3>
            <p id="error-message" class="mb-4 text-sm text-rose-400/80">Terjadi kesalahan saat mencoba membaca file dari
                folder utama.</p>
            <button onclick="initGallery()"
                class="bg-maroon hover:bg-maroon-dark rounded-lg border border-white/10 px-4 py-2 text-xs font-semibold transition-all">
                Coba Lagi
            </button>
        </div>

        <!-- Main Media Grid -->
        <div id="media-grid" class="grid grid-cols-2 gap-4 sm:grid-cols-2 md:grid-cols-3 md:gap-6 lg:grid-cols-4">
            <!-- Grid Items Will Be Rendered Dynamically -->
        </div>

        <!-- Load More Container -->
        <div id="load-more-container" class="mt-12 hidden text-center">
            <button onclick="loadMoreItems()"
                class="font-outfit rounded-xl border border-slate-100 bg-white px-8 py-3 text-xs font-semibold text-slate-800 shadow-lg transition-all hover:bg-slate-100 active:scale-95">
                Muat Lebih Banyak
            </button>
        </div>

    </main>

    <!-- Modal Lightbox (Modal Layar Penuh) -->
    <div id="lightbox"
        class="bg-black/95 fixed inset-0 z-50 hidden select-none grid-rows-[auto_1fr_auto] h-full w-full overflow-hidden backdrop-blur-2xl transition-all duration-300">

        <!-- Lightbox Header Controls -->
        <div class="w-full max-w-7xl mx-auto flex items-center justify-between px-4 md:px-6 py-4">
            <div class="text-xs tracking-wider text-slate-400">
                <span id="lightbox-index" class="font-semibold text-white">0</span> / <span id="lightbox-total">0</span>
                <span class="mx-2">|</span>
                <span id="lightbox-filename" class="hidden sm:inline">file_name.jpg</span>
            </div>

            <div class="flex items-center gap-3">
                <a id="lightbox-download" href="#" target="_blank" download
                    class="bg-maroon hover:bg-maroon-dark flex items-center gap-2 rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold text-white transition-all">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span class="hidden sm:inline">Download</span>
                </a>
                <button onclick="closeLightbox()"
                    class="bg-maroon flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 text-lg text-white transition-all hover:scale-105 hover:bg-pink-600 active:scale-95">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Lightbox Main Content Slot -->
        <div class="relative w-full h-full min-h-0 min-w-0 flex items-center justify-center overflow-hidden px-0 md:px-12">

            <!-- Navigation Left -->
            <button onclick="prevMedia()"
                class="bg-maroon absolute left-2 z-10 flex h-10 w-10 cursor-pointer items-center justify-center rounded-xl border border-white/15 text-white shadow-lg transition-all active:scale-95 active:bg-pink-600 md:left-4 md:h-12 md:w-12">
                <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <!-- Media Container -->
            <div id="lightbox-media-container" class="w-full h-full flex items-center justify-center p-2">
                <!-- Large Image or Video Player Will Be Rendered Dynamically -->
            </div>

            <!-- Navigation Right -->
            <button onclick="nextMedia()"
                class="bg-maroon absolute right-2 z-10 flex h-10 w-10 cursor-pointer items-center justify-center rounded-xl border border-white/15 text-white shadow-lg transition-all active:scale-95 active:bg-pink-600 md:right-4 md:h-12 md:w-12">
                <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

        </div>

        <!-- Lightbox Bottom Gallery (Session Items Sub-Gallery) -->
        <div class="w-full max-w-7xl mx-auto px-4 md:px-6 pb-6 pt-2">
            <div id="originals-box" class="hidden border-t border-white/10 pt-4 transition-all duration-300">
                <h4
                    class="font-outfit mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400">
                    <svg class="h-4 w-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    File Sesi Ini (Prints ➔ Originals ➔ Animated):
                </h4>
                <div id="originals-list" class="flex gap-4 overflow-x-auto pb-2">
                    <!-- Dynamic session items will be appended here -->
                </div>
            </div>
        </div>

    </div>

    <!-- Core Scripting -->
    <script>
        // =========================================
        // 1. CONFIGURATION (DYNAMICALLY INJECTED)
        // =========================================
        const API_KEY = '{{ config('services.google.drive_api_key') }}';
        const PARENT_FOLDER_ID = '{{ $folderId }}';

        // Global Cache & State Registry
        let printsFiles = [];
        let originalsFilesCache = [];
        let animatedFilesCache = [];

        let itemsToShow = 12; // Render initially 12 items to prevent Google CDN rate limits
        const ITEMS_PER_PAGE = 12;

        let currentTab = 'prints'; // Active filters
        let currentMediaList = []; // Active list for navigation in lightbox
        let currentMediaIndex = 0; // Lightbox pointer

        // Subfolder ID registry
        let folderIds = {
            prints: null,
            originals: null,
            animated: null
        };

        // Initialize gallery on load
        window.addEventListener('DOMContentLoaded', () => {
            if (!API_KEY || API_KEY === '' || !PARENT_FOLDER_ID || PARENT_FOLDER_ID === '') {
                showError(
                    "Kunci API Google Drive atau ID folder tidak terkonfigurasi. Pastikan GOOGLE_DRIVE_API_KEY telah diset di file .env"
                );
                return;
            }
            initGallery();
        });

        // =========================================
        // 2. DATA SYNCHRONIZATION & FETCHING
        // =========================================
        async function initGallery() {
            setLoading(true, "Menghubungkan ke Google Drive...");
            hideError();

            try {
                // Step 1: Detect Sub-folders under Parent ID
                const query = encodeURIComponent(
                    `'${PARENT_FOLDER_ID}' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false`
                );
                const response = await fetch(`https://www.googleapis.com/drive/v3/files?q=${query}&key=${API_KEY}`);
                const data = await response.json();

                if (data.error) {
                    throw new Error(data.error.message);
                }

                const folders = data.files || [];
                folders.forEach(f => {
                    const name = f.name.toLowerCase();
                    if (name.includes('print')) folderIds.prints = f.id;
                    else if (name.includes('original')) folderIds.originals = f.id;
                    else if (name.includes('animated') || name.includes('video')) folderIds.animated = f.id;
                });

                if (!folderIds.prints) {
                    throw new Error("Folder 'Prints' tidak ditemukan di bawah Parent Folder.");
                }

                setLoading(true, "Mengunduh metadata foto dan video...");

                // Step 2: Fetch all files from folders concurrently
                const promises = [
                    fetchFilesFromFolder(folderIds.prints).then(res => printsFiles = res)
                ];

                if (folderIds.originals) {
                    promises.push(fetchFilesFromFolder(folderIds.originals).then(res => originalsFilesCache = res));
                }
                if (folderIds.animated) {
                    promises.push(fetchFilesFromFolder(folderIds.animated).then(res => animatedFilesCache = res));
                }

                await Promise.all(promises);

                // Initial render
                setLoading(false);
                renderGrid();

            } catch (err) {
                console.error(err);
                showError(
                    `Gagal membaca folder Google Drive: ${err.message}. Pastikan folder di-set ke Publik ("Anyone with the link can view").`
                );
                setLoading(false);
            }
        }

        async function fetchFilesFromFolder(folderId) {
            if (!folderId) return [];

            const query = encodeURIComponent(`'${folderId}' in parents and trashed = false`);
            const fields = 'files(id,name,thumbnailLink,createdTime,mimeType)';
            // Order chronologically by creation time
            const response = await fetch(
                `https://www.googleapis.com/drive/v3/files?q=${query}&fields=${fields}&orderBy=createdTime%20desc&pageSize=500&key=${API_KEY}`
            );
            const data = await response.json();

            if (data.error) {
                throw new Error(data.error.message);
            }

            return data.files || [];
        }

        // =========================================
        // 3. GRID RENDERING
        // =========================================
        function renderGrid() {
            const grid = document.getElementById('media-grid');
            grid.innerHTML = '';

            const files = printsFiles;
            currentMediaList = files;

            if (files.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full py-16 text-center border border-dashed border-white/10 rounded-3xl backdrop-blur-md">
                        <svg class="h-10 w-10 mx-auto mb-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <h4 class="text-sm font-bold tracking-wider text-slate-400 uppercase font-outfit">Belum Ada File</h4>
                        <p class="text-xs text-slate-500 mt-1">Tidak ada file yang ditemukan dalam kategori ini.</p>
                    </div>
                `;
                document.getElementById('load-more-container').classList.add('hidden');
                return;
            }

            // Slice only visible files to avoid triggering Google CDN DDoS protection
            const visibleFiles = files.slice(0, itemsToShow);

            visibleFiles.forEach((file, index) => {
                const card = document.createElement('div');
                card.className =
                    "relative bg-white rounded-2xl overflow-hidden shadow-sm transition-all duration-200 active:scale-[0.98] cursor-pointer";
                card.onclick = () => openLightbox(index);

                // Use Drive's built-in thumbnail link to save bandwidth, pagination prevents 429 rate limit
                const gridThumbnail = file.thumbnailLink ? file.thumbnailLink.replace(/=s220$/, '=w500') : '';

                // Image Grid Item (Prints are always images, matched to videos and originals inside the lightbox)
                card.innerHTML = `
                    <div class="aspect-[3/4] relative w-full bg-slate-100 flex items-center justify-center overflow-hidden">
                        <img src="${gridThumbnail}" alt="${file.name}" loading="lazy" crossorigin="anonymous" referrerpolicy="no-referrer" class="w-full h-full object-cover">
                    </div>
                    <div class="p-3 md:p-4 border-t border-slate-100 bg-white flex justify-between items-center">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-[11px] md:text-xs text-slate-800 font-semibold truncate">${file.name}</h4>
                            <span class="text-[9px] md:text-[10px] text-slate-500 mt-0.5 block">${formatDate(file.createdTime)}</span>
                        </div>
                        <span class="text-[9px] md:text-[10px] font-bold text-pink-600 uppercase tracking-wider shrink-0 ml-2">Lihat</span>
                    </div>
                `;

                grid.appendChild(card);
            });

            // Show or hide Load More button
            const loadMoreBtn = document.getElementById('load-more-container');
            if (files.length > itemsToShow) {
                loadMoreBtn.classList.remove('hidden');
            } else {
                loadMoreBtn.classList.add('hidden');
            }
        }

        function loadMoreItems() {
            itemsToShow += ITEMS_PER_PAGE;
            renderGrid();
        }

        // =========================================
        // 4. LIGHTBOX & TIME-BASED MATCHING ALGORITHM
        // =========================================
        function openLightbox(index) {
            currentMediaIndex = index;
            document.body.classList.add('overflow-hidden');
            const lightbox = document.getElementById('lightbox');
            // Remove 'hidden' first, then set display grid
            lightbox.classList.remove('hidden');
            lightbox.classList.add('grid');
            renderLightboxContent();
        }

        function closeLightbox() {
            document.body.classList.remove('overflow-hidden');
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.add('hidden');
            document.getElementById('lightbox-media-container').innerHTML = '';
            document.getElementById('originals-box').classList.add('hidden');
        }

        function renderLightboxContent() {
            const file = currentMediaList[currentMediaIndex];
            if (!file) return;

            // Update stats
            document.getElementById('lightbox-index').innerText = currentMediaIndex + 1;
            document.getElementById('lightbox-total').innerText = currentMediaList.length;
            document.getElementById('lightbox-filename').innerText = file.name;

            // Direct download source
            const downloadUrl = `https://docs.google.com/uc?export=download&id=${file.id}`;
            document.getElementById('lightbox-download').href = downloadUrl;

            const mediaContainer = document.getElementById('lightbox-media-container');
            mediaContainer.innerHTML = '';

            // Clean states
            document.getElementById('originals-box').classList.add('hidden');

            if (file.mimeType.includes('video')) {
                // Video Player (Uses Google Drive API alt=media stream with API Key for highly reliable browser streaming)
                mediaContainer.innerHTML = `
                    <video src="https://www.googleapis.com/drive/v3/files/${file.id}?alt=media&key=${API_KEY}" type="video/mp4" controls autoplay loop crossorigin="anonymous" class="h-full w-full max-h-full max-w-full rounded-xl border border-white/10 shadow-2xl object-contain"></video>
                `;
            } else {
                // High-resolution image (official API alt=media endpoint for maximum reliability)
                const highResUrl = `https://www.googleapis.com/drive/v3/files/${file.id}?alt=media&key=${API_KEY}`;
                mediaContainer.innerHTML = `
                    <img src="${highResUrl}" alt="${file.name}" crossorigin="anonymous" referrerpolicy="no-referrer" class="h-full w-full max-h-full max-w-full rounded-xl border border-white/10 shadow-2xl object-contain select-none">
                `;
            }

            // Always run dynamic session files matching
            runTimeBasedMatching(file);
        }

        function runTimeBasedMatching(printFile) {
            const printTime = new Date(printFile.createdTime).getTime();
            const originalsBox = document.getElementById('originals-box');
            const originalsList = document.getElementById('originals-list');

            originalsList.innerHTML = '';
            originalsBox.classList.remove('hidden');

            // Helper function to toggle active borders on session thumbnails
            function setActiveThumb(activeEl) {
                document.querySelectorAll('.session-thumb').forEach(t => {
                    t.classList.remove('border-pink-500');
                    t.classList.add('border-white/10');
                });
                activeEl.classList.remove('border-white/10');
                activeEl.classList.add('border-pink-500');
            }

            // 1. APPEND PRINTS (COLLAGE) - FIRST ITEM
            const printThumb = document.createElement('div');
            printThumb.className =
                "session-thumb flex-none relative aspect-[3/4] h-20 rounded-lg overflow-hidden border border-pink-500 cursor-pointer transition-all duration-300";
            const printThumbUrl = printFile.thumbnailLink ? printFile.thumbnailLink.replace(/=s220$/, '=s150') : '';
            printThumb.innerHTML = `
                <img src="${printThumbUrl}" alt="Prints Collage" crossorigin="anonymous" referrerpolicy="no-referrer" class="w-full h-full object-cover">
                <div class="absolute bottom-0 inset-x-0 bg-black/60 py-0.5 text-center">
                    <span class="text-[9px] font-bold text-white uppercase tracking-wider">Prints</span>
                </div>
            `;
            printThumb.onclick = () => {
                setActiveThumb(printThumb);
                const mediaContainer = document.getElementById('lightbox-media-container');
                const highResUrl = `https://www.googleapis.com/drive/v3/files/${printFile.id}?alt=media&key=${API_KEY}`;
                mediaContainer.innerHTML = `
                    <img src="${highResUrl}" alt="${printFile.name}" crossorigin="anonymous" referrerpolicy="no-referrer" class="h-full w-full max-h-full max-w-full rounded-xl border border-white/10 shadow-2xl object-contain select-none">
                `;
                document.getElementById('lightbox-download').href =
                    `https://docs.google.com/uc?export=download&id=${printFile.id}`;
                document.getElementById('lightbox-filename').innerText = printFile.name;
            };
            originalsList.appendChild(printThumb);

            // 2. FILTER & APPEND ORIGINALS (FOTO SATUAN) - SECOND ITEMS
            let matchedOriginals = [];
            if (originalsFilesCache.length > 0) {
                matchedOriginals = originalsFilesCache.filter(origFile => {
                    const origTime = new Date(origFile.createdTime).getTime();
                    const diffSeconds = (printTime - origTime) / 1000;
                    return diffSeconds >= -5 && diffSeconds <= 60;
                });
            }

            matchedOriginals.forEach(orig => {
                const origThumb = document.createElement('div');
                origThumb.className =
                    "session-thumb flex-none relative aspect-[3/4] h-20 rounded-lg overflow-hidden border border-white/10 cursor-pointer transition-all duration-300";
                const origThumbUrl = orig.thumbnailLink ? orig.thumbnailLink.replace(/=s220$/, '=s150') : '';
                origThumb.innerHTML = `
                    <img src="${origThumbUrl}" alt="${orig.name}" crossorigin="anonymous" referrerpolicy="no-referrer" class="w-full h-full object-cover">
                    <div class="absolute bottom-0 inset-x-0 bg-black/60 py-0.5 text-center">
                        <span class="text-[9px] font-bold text-slate-300 uppercase tracking-wider">Originals</span>
                    </div>
                `;
                origThumb.onclick = () => {
                    setActiveThumb(origThumb);
                    const mediaContainer = document.getElementById('lightbox-media-container');
                    mediaContainer.innerHTML = `
                        <img src="https://www.googleapis.com/drive/v3/files/${orig.id}?alt=media&key=${API_KEY}" alt="${orig.name}" crossorigin="anonymous" referrerpolicy="no-referrer" class="h-full w-full max-h-full max-w-full rounded-xl border border-white/10 shadow-2xl object-contain select-none">
                    `;
                    document.getElementById('lightbox-download').href =
                        `https://docs.google.com/uc?export=download&id=${orig.id}`;
                    document.getElementById('lightbox-filename').innerText = orig.name;
                };
                originalsList.appendChild(origThumb);
            });

            // 3. DETECT & APPEND ANIMATED (BOOMERANG VIDEO) - LAST ITEM
            const cleanName = printFile.name.substring(0, printFile.name.lastIndexOf('.'));
            const matchedAnimated = animatedFilesCache.find(anim => anim.name.startsWith(cleanName));

            if (matchedAnimated) {
                const videoThumb = document.createElement('div');
                videoThumb.className =
                    "session-thumb flex-none relative aspect-[3/4] h-20 rounded-lg overflow-hidden border border-white/10 cursor-pointer transition-all duration-300";
                videoThumb.innerHTML = `
                    <img src="${printThumbUrl}" alt="Boomerang Video" crossorigin="anonymous" referrerpolicy="no-referrer" class="w-full h-full object-cover brightness-50">
                    <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                        <div class="w-7 h-7 rounded-full bg-pink-600/90 flex items-center justify-center text-white shadow-md">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                        </div>
                    </div>
                    <div class="absolute bottom-0 inset-x-0 bg-black/60 py-0.5 text-center">
                        <span class="text-[9px] font-bold text-pink-400 uppercase tracking-wider">Animated</span>
                    </div>
                `;
                videoThumb.onclick = () => {
                    setActiveThumb(videoThumb);
                    const mediaContainer = document.getElementById('lightbox-media-container');
                    mediaContainer.innerHTML = `
                        <video src="https://www.googleapis.com/drive/v3/files/${matchedAnimated.id}?alt=media&key=${API_KEY}" type="video/mp4" controls autoplay loop class="max-w-full max-h-[65vh] rounded-xl border border-white/10 shadow-2xl"></video>
                    `;
                    document.getElementById('lightbox-download').href =
                        `https://docs.google.com/uc?export=download&id=${matchedAnimated.id}`;
                    document.getElementById('lightbox-filename').innerText = matchedAnimated.name;
                };
                originalsList.appendChild(videoThumb);
            }
        }

        // Navigation controls
        function nextMedia() {
            if (currentMediaList.length === 0) return;
            currentMediaIndex = (currentMediaIndex + 1) % currentMediaList.length;
            renderLightboxContent();
        }

        function prevMedia() {
            if (currentMediaList.length === 0) return;
            currentMediaIndex = (currentMediaIndex - 1 + currentMediaList.length) % currentMediaList.length;
            renderLightboxContent();
        }

        // =========================================
        // 5. KEYBOARD COMMAND REGISTRY
        // =========================================
        window.addEventListener('keydown', (e) => {
            const lightbox = document.getElementById('lightbox');
            if (lightbox.classList.contains('hidden')) return;

            if (e.key === 'ArrowRight') nextMedia();
            else if (e.key === 'ArrowLeft') prevMedia();
            else if (e.key === 'Escape') closeLightbox();
        });

        // =========================================
        // 6. UTILITY FUNCTIONS
        // =========================================
        function setLoading(isLoading, message = "") {
            const status = document.getElementById('loading-status');
            if (isLoading) {
                status.classList.remove('hidden');
                status.querySelector('span').innerText = message;
            } else {
                status.classList.add('hidden');
            }
        }

        function showError(message) {
            const box = document.getElementById('error-container');
            document.getElementById('error-message').innerText = message;
            box.classList.remove('hidden');
        }

        function hideError() {
            document.getElementById('error-container').classList.add('hidden');
        }

        function formatDate(isoString) {
            if (!isoString) return '';
            const d = new Date(isoString);
            return d.toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }) + ' WITA';
        }
    </script>
</body>

</html>
