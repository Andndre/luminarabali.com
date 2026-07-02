{{-- Editor JavaScript: Sync Logic, Alpine Components, Monaco, Tabs, Media Library --}}
<script>
    window.isSyncing = false;
    window.typingTimer = null;
    window.activeTab = 'html';

    window.syncToMonaco = function() {
        if (window.isSyncing) return;
        window.isSyncing = true;
        
        const canvas = document.getElementById('visual-canvas');
        if (!canvas || typeof window.htmlCodeModel === 'undefined') {
            window.isSyncing = false;
            return;
        }

        const clone = canvas.cloneNode(true);
        const textElements = clone.querySelectorAll('[contenteditable]');
        textElements.forEach(el => {
            el.removeAttribute('contenteditable');
            el.classList.remove('hover:outline', 'hover:outline-1', 'hover:outline-blue-400',
                'focus:outline-2', 'focus:outline-blue-500', 'transition-all');
            if (el.getAttribute('class') === '') el.removeAttribute('class');
        });

        // Remove visual indicators for dynamic variables
        const dynamicElements = clone.querySelectorAll('[x-text]');
        dynamicElements.forEach(el => {
            el.classList.remove('border-b', 'border-dashed', 'border-blue-400', 'cursor-not-allowed');
            el.removeAttribute('title');
            if (el.getAttribute('class') === '') el.removeAttribute('class');
        });

        // Convert container queries back to standard Tailwind breakpoints (@md: -> md:)
        // and clean up Sortable/Alpine temporary attributes
        const allEls = clone.querySelectorAll('*');
        allEls.forEach(el => {
            el.removeAttribute('draggable');
            if (el.getAttribute('style') === '') {
                el.removeAttribute('style');
            }

            if (el.className && typeof el.className === 'string') {
                el.className = el.className.replace(/@(sm|md|lg|xl|2xl):/g, '$1:');
                el.className = el.className.replace(
                    /\bring-2\b|\bring-blue-500\b|\bring-inset\b|\boutline-none\b/g, '').replace(/\s+/g,
                    ' ').trim();

                if (el.className.trim() === '') {
                    el.removeAttribute('class');
                }
            }
        });

        // Strip any Sortable classes if they leaked
        const sortables = clone.querySelectorAll('.sortable-ghost, .sortable-chosen, .sortable-drag');
        sortables.forEach(el => {
            el.classList.remove('sortable-ghost', 'sortable-chosen', 'sortable-drag');
            if (el.getAttribute('class') === '') el.removeAttribute('class');
        });

        // Update Monaco Model
        let cleanHTML = clone.innerHTML.trim();
        const activeModel = window.activeTab === 'cover' ? window.coverCodeModel : window.htmlCodeModel;
        
        if (window.globalEditor) {
            const fullRange = activeModel.getFullModelRange();
            window.globalEditor.executeEdits('visual-canvas', [{
                range: fullRange,
                text: cleanHTML
            }]);
        } else {
            activeModel.setValue(cleanHTML);
        }
        
        setTimeout(() => { window.isSyncing = false; }, 50);
    };

        // Editor Config injected by Blade
    window.EditorConfig = {
        assetsRoute: "{{ route('admin.assets.index') }}?modal=1"
    };
</script>

@vite('resources/js/editor/app.js')
