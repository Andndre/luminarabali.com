<?php

namespace App\Services;

use App\Models\InvitationPage;
use App\Models\InvitationTemplate;

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

        return $this->renderSections($this->sections, $page);
    }

    public function renderTemplate(InvitationTemplate $template): string
    {
        $sections = $template->sections()->where('is_visible', true)->get();

        $placeholderPage = new InvitationPage([
            'groom_name' => 'Romeo',
            'bride_name' => 'Juliet',
            'event_date' => now()->addMonths(6),
        ]);

        return $this->renderSections($sections, $placeholderPage);
    }

    protected function renderSections($sections, InvitationPage $page): string
    {
        $byParent = $sections->groupBy('parent_id');
        $topLevel = $byParent->get(null, collect());

        return view('templates.section-tree', [
            'sections' => $topLevel,
            'byParent' => $byParent,
            'page' => $page,
        ])->render();
    }

    public function coverImage($sections): ?string
    {
        $props = $sections->firstWhere('section_type', 'cover')?->props ?? [];
        $src = $props['background_image'] ?? null;
        if (! $src) {
            return null;
        }

        return \Illuminate\Support\Str::startsWith($src, ['http://', 'https://', '/'])
            ? $src
            : '/storage/'.ltrim($src, '/');
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
        $default = config('invitation.default_theme');
        $templateTheme = is_array($page->template->theme ?? null) ? $page->template->theme : [];
        $overrides = is_array($page->theme_overrides ?? null) ? $page->theme_overrides : [];

        return $this->buildStyleAndFonts($default, $templateTheme, $overrides);
    }

    public function templateThemeStyle(InvitationTemplate $template): string
    {
        $default = config('invitation.default_theme');
        $templateTheme = is_array($template->theme ?? null) ? $template->theme : [];

        return $this->buildStyleAndFonts($default, $templateTheme, []);
    }

    protected function buildStyleAndFonts(array $default, array $templateTheme, array $overrides): string
    {
        $part = fn (array $theme, string $key): array => is_array($theme[$key] ?? null) ? $theme[$key] : [];

        $theme = [
            'colors' => array_merge($default['colors'], $part($templateTheme, 'colors'), $part($overrides, 'colors')),
            'fonts' => array_merge($default['fonts'], $part($templateTheme, 'fonts'), $part($overrides, 'fonts')),
        ];

        return $this->buildStyleBlock($theme) . $this->buildFontLinks($theme);
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

        foreach (array_unique(array_filter($theme['fonts'], 'is_string')) as $fontName) {
            $font = $curated->firstWhere('name', $fontName);
            if ($font) {
                $links .= '<link rel="stylesheet" href="'.e($font['url']).'">';
            }
        }

        return $links;
    }
}
