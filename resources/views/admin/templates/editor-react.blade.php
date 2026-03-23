@extends('layouts.editor-react')

@section('title', 'Template Editor')

@section('content')
    <script>
        window.templateId = {{ $template->id ?? 0 }};
        window.editorConfig = {
            mode: 'template',
            resourceId: {{ $template->id ?? 0 }},
        };
    </script>

    <!-- React App Root -->
    <div id="template-editor-root" class="h-screen w-screen">
        <!-- Loading state -->
        <div class="flex h-full items-center justify-center">
            <div class="text-center">
                <div class="inline-block h-12 w-12 animate-spin rounded-full border-b-2 border-yellow-500"></div>
                <p class="mt-4 text-gray-600">Loading editor...</p>
            </div>
        </div>
    </div>

    @vite('resources/js/editor/main.jsx')
@endsection
