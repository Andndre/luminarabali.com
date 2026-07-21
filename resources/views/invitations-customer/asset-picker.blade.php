@extends('layouts.dashboard')

@section('title', 'Pilih Foto')

@section('content')
<div id="picker" class="dash-content" style="padding: 0">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem">
        <h1 class="dash-title" style="font-size: 1.1rem">Pilih Foto</h1>
        <label class="dash-btn dash-btn--solid" style="cursor: pointer">
            <span id="upload-label">Upload Foto</span>
            <input type="file" id="upload-input" accept="image/*" style="display: none">
        </label>
    </div>
    <div id="grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(7rem, 1fr)); gap: .75rem"></div>
    <p id="empty" class="dash-muted" style="display: none">Belum ada foto. Upload dulu di atas.</p>
</div>

<script>
(() => {
    const pageId = {{ $page->id }};
    const dataUrl = '{{ route('invitations.assets.data', $page->id) }}';
    const uploadUrl = '{{ route('invitations.assets.upload', $page->id) }}';
    const grid = document.getElementById('grid');
    const empty = document.getElementById('empty');
    const input = document.getElementById('upload-input');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    function render(assets) {
        grid.innerHTML = '';
        empty.style.display = assets.length ? 'none' : 'block';
        assets.forEach((asset) => {
            const img = document.createElement('img');
            img.src = '/storage/' + asset.file_path;
            img.style.cssText = 'width:100%;aspect-ratio:1;object-fit:cover;border-radius:.5rem;cursor:pointer;border:1px solid var(--dash-hair)';
            img.addEventListener('click', () => {
                window.parent.postMessage({ type: 'assetSelected', asset }, window.location.origin);
            });
            grid.appendChild(img);
        });
    }

    async function load() {
        const res = await fetch(dataUrl);
        render(await res.json());
    }

    input.addEventListener('change', async () => {
        const file = input.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('file', file);
        document.getElementById('upload-label').textContent = 'Mengunggah…';
        try {
            const res = await fetch(uploadUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }, body: formData });
            if (res.ok) await load();
        } finally {
            document.getElementById('upload-label').textContent = 'Upload Foto';
            input.value = '';
        }
    });

    load();
})();
</script>
@endsection
