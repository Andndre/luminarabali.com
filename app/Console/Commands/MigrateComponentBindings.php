<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateComponentBindings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'components:migrate-bindings';

    protected $description = 'Migrate component library raw HTML from Blade syntax to Alpine.js bindings';

    public function handle()
    {
        $components = \App\Models\ComponentLibrary::all();
        $migratedCount = 0;

        foreach ($components as $component) {
            $code = $component->code;
            $originalCode = $code;

            // 1. Process Attributes
            // Match: attribute="... {{ $var }} ..."
            $code = preg_replace_callback('/([a-zA-Z0-9_-]+)="([^"]*\{\{\s*\$([a-zA-Z0-9_]+)\s*\}\}[^"]*)"/', function ($matches) use ($component) {
                $attrName = $matches[1];
                $attrValue = $matches[2];
                $varName = $matches[3];

                // Skip if the attribute is already an Alpine directive
                if (str_starts_with($attrName, ':') || str_starts_with($attrName, 'x-') || str_starts_with($attrName, '@')) {
                    $this->warn("Complex attribute binding found in component ID {$component->id} ({$component->name}): {$attrName}=\"{$attrValue}\". Skipping.");
                    return $matches[0]; // Do not replace
                }

                // If exact match e.g. href="{{ $phone }}"
                if (preg_match('/^\{\{\s*\$([a-zA-Z0-9_]+)\s*\}\}$/', $attrValue, $exactMatch)) {
                    return ":{$attrName}=\"{$exactMatch[1]}\"";
                }

                // If mixed match e.g. href="https://wa.me/{{ $phone }}?text=Hello"
                $jsExpression = preg_replace('/\{\{\s*\$([a-zA-Z0-9_]+)\s*\}\}/', "' + $1 + '", $attrValue);
                $jsExpression = "'" . $jsExpression . "'";
                // Clean up empty string concats: 'foo' + '' -> 'foo'
                $jsExpression = str_replace(["'' + ", " + ''"], "", $jsExpression);

                return ":{$attrName}=\"{$jsExpression}\"";
            }, $code);

            // 2. Process Text Nodes
            // Any remaining {{ $var }} are assumed to be text nodes
            $code = preg_replace('/\{\{\s*\$([a-zA-Z0-9_]+)\s*\}\}/', '<span x-text="$1"></span>', $code);

            if ($code !== $originalCode) {
                $component->update(['code' => $code]);
                $this->info("Migrated component ID {$component->id} ({$component->name})");
                $migratedCount++;
            }
        }

        $this->info("Migration completed. Total migrated: {$migratedCount}");
    }
}
