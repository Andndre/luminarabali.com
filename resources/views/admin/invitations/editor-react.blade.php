@extends('layouts.editor-react')

@section('title', 'Invitation Editor')

@section('content')
    <script>
        window.templateId = {{ $page->id ?? 0 }};
        window.pageId = {{ $page->id ?? 0 }};
        window.editorConfig = {
            mode: 'invitation',
            resourceId: {{ $page->id ?? 0 }},
        };
    </script>

    <div id="template-editor-root" class="h-screen w-screen">
        <div class="flex h-full items-center justify-center">
            <div class="text-center">
                <div class="inline-block h-12 w-12 animate-spin rounded-full border-b-2 border-yellow-500"></div>
                <p class="mt-4 text-gray-600">Loading invitation editor...</p>
            </div>
        </div>
    </div>

    @vite('resources/js/editor/main.jsx')
@endsection
