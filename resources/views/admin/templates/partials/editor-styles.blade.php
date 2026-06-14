{{-- Editor Custom Styles --}}
<style>
    /* Custom scrollbar for visual canvas */
    #visual-workspace::-webkit-scrollbar {
        width: 8px;
    }

    #visual-workspace::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #visual-workspace::-webkit-scrollbar-thumb {
        background: #d4af37;
        border-radius: 4px;
    }

    #visual-workspace::-webkit-scrollbar-thumb:hover {
        background: #c9a227;
    }

    /* Global custom css from template */
    {!! $template->global_custom_css !!}
</style>
