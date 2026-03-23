<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: {{ $template->name }} - Luminara</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Lato', sans-serif;
            overflow-x: hidden;
            background-color: white;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #d4af37;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #c9a227;
        }
    </style>
</head>

<body class="bg-white">
    <!-- Preview Banner -->
    <div class="sticky top-0 z-50 bg-yellow-500 px-4 py-2 text-center text-sm font-medium text-black">
        Preview Mode - Template: {{ $template->name }}
    </div>

    <!-- Preview Content -->
    @php
        $sortedSections = $template->sections->sortBy('order_index')->values();
        $sectionsByParent = [];

        foreach ($sortedSections as $item) {
            $key = $item->parent_id ? (string) $item->parent_id : 'root';
            $sectionsByParent[$key] = $sectionsByParent[$key] ?? [];
            $sectionsByParent[$key][] = $item;
        }

        $renderSection = function ($section) use (&$renderSection, $sectionsByParent, $template) {
            $children = collect($sectionsByParent[(string) $section->id] ?? [])
                ->sortBy('order_index')
                ->values();
            $viewPath = "templates.components.{$section->section_type}";
            $filePath = str_replace('.', '/', $viewPath); // Convert dots to slashes for filesystem path

            if (!file_exists(resource_path("views/{$filePath}.blade.php"))) {
                return '<div class="bg-red-100 p-4 text-center text-red-700">Component not found: ' .
                    e($section->section_type) .
                    '</div>';
            }

            return view($viewPath, [
                'props' => $section->props ?? [],
                'section' => $section,
                'page' => $template,
                'elements' => $children,
            ])->render();
        };

        $rootSections = collect($sectionsByParent['root'] ?? [])
            ->sortBy('order_index')
            ->values();
    @endphp

    <div>
        @foreach ($rootSections as $section)
            {!! $renderSection($section) !!}
        @endforeach
    </div>
</body>

</html>
