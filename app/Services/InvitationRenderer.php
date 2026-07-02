<?php

namespace App\Services;

use App\Models\InvitationPage;

class InvitationRenderer
{
    protected $page;
    protected $sections;

    public function render(InvitationPage $page): string
    {
        $this->page = $page;
        $this->sections = $page->sections()
            ->orderBy('order_index')
            ->where('is_visible', true)
            ->get();

        $byParent = $this->sections->groupBy('parent_id');
        $topLevel = $byParent->get(null, collect());

        return view('templates.section-tree', [
            'sections' => $topLevel,
            'byParent' => $byParent,
            'page' => $page,
        ])->render();
    }

    public function toArray(InvitationPage $page): array
    {
        return [
            'page' => $page,
            'sections' => $page->sections()->orderBy('order_index')->get(),
            'assets' => $page->assets
        ];
    }

    public function themeStyle(InvitationPage $page): string
    {
        $theme = $this->mergeTheme($page);

        return $this->buildStyleBlock($theme) . $this->buildFontLinks($theme);
    }

    protected function mergeTheme(InvitationPage $page): array
    {
        $default = config('invitation.default_theme');
        $templateTheme = $page->template->theme ?? [];
        $overrides = $page->theme_overrides ?? [];

        return [
            'colors' => array_merge($default['colors'], $templateTheme['colors'] ?? [], $overrides['colors'] ?? []),
            'fonts' => array_merge($default['fonts'], $templateTheme['fonts'] ?? [], $overrides['fonts'] ?? []),
        ];
    }

    protected function buildStyleBlock(array $theme): string
    {
        $curatedFontNames = collect(config('invitation.fonts'))->pluck('name')->all();
        $vars = [];
        $safeKey = '/^[a-zA-Z0-9_-]+$/';

        foreach ($theme['colors'] as $key => $value) {
            if (is_string($key) && preg_match($safeKey, $key)
                && is_string($value) && preg_match('/^#[0-9a-fA-F]{3,8}$/', $value)) {
                $vars[] = '--color-'.$key.': '.$value.';';
            }
        }

        foreach ($theme['fonts'] as $key => $value) {
            if (is_string($key) && preg_match($safeKey, $key)
                && in_array($value, $curatedFontNames, true)) {
                $vars[] = '--font-'.$key.": '".$value."';";
            }
        }

        return '<style>:root{'.implode('', $vars).'}</style>';
    }

    protected function buildFontLinks(array $theme): string
    {
        $curated = collect(config('invitation.fonts'));
        $links = '';

        foreach (array_unique($theme['fonts']) as $fontName) {
            $font = $curated->firstWhere('name', $fontName);
            if ($font) {
                $links .= '<link rel="stylesheet" href="'.e($font['url']).'">';
            }
        }

        return $links;
    }
}
