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
            'scales' => array_merge($default['scales'], $part($templateTheme, 'scales'), $part($overrides, 'scales')),
            'ornaments' => array_merge($default['ornaments'] ?? [], $part($templateTheme, 'ornaments'), $part($overrides, 'ornaments')),
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

        $vars = array_merge($vars, $this->scaleVars($theme['scales'] ?? []));

        $vars = array_merge($vars, $this->headingRuleVars(
            is_array($theme['ornaments'] ?? null) ? $theme['ornaments'] : []
        ));

        return '<style>:root{'.implode('', $vars).'}</style>';
    }

    /**
     * Ornamen judul (atas dan/atau bawah). Var hanya dikeluarkan untuk sisi yang punya
     * ornamen: kalau tidak, aturan CSS jatuh ke batang lurus lama (bawah) atau tidak
     * dirender sama sekali (atas).
     */
    protected function headingRuleVars(array $ornaments): array
    {
        // Path masuk ke url() di dalam <style>: kutip, kurung, dan backslash harus
        // DITOLAK, bukan di-escape — hanya path aset lokal yang lolos.
        $safe = fn ($v) => is_string($v) && preg_match('#^[A-Za-z0-9/_.-]+$#', $v) ? $v : null;
        // URL absolut, bukan '/storage/…': url() di dalam custom property diselesaikan
        // relatif ke stylesheet tempat var itu DIPAKAI (invitation.css), bukan tempat ia
        // dideklarasikan. Di dev Vite menyajikan file itu dari port lain, jadi path
        // berawalan slash menembak host yang salah dan berakhir 404.
        $url = fn (string $p) => "url('".asset('storage/'.ltrim($p, '/'))."')";
        $num = fn ($v, float $min, float $max, float $fallback) => rtrim(rtrim(number_format(
            is_numeric($v) ? max($min, min($max, (float) $v)) : $fallback, 2, '.', ''
        ), '0'), '.');

        $top = $safe($ornaments['heading_rule_top'] ?? null);
        $bottom = $safe($ornaments['heading_rule'] ?? null);

        $vars = ['--heading-rule-gap: '.$num($ornaments['heading_rule_gap'] ?? null, 0, 80, 14).'px;'];

        if ($top) {
            $vars[] = '--heading-rule-top: '.$url($top).';';
            // ::before tidak dirender sama sekali tanpa ornamen atas.
            $vars[] = '--heading-rule-top-d: block;';
            $vars[] = '--heading-rule-top-w: '.$num($ornaments['heading_rule_top_width'] ?? null, 10, 100, 80).'%;';
        }

        if ($bottom) {
            $vars[] = '--heading-rule: '.$url($bottom).';';
            $vars[] = '--heading-rule-w: '.$num($ornaments['heading_rule_width'] ?? null, 10, 100, 80).'%;';
            // Tinggi diturunkan dari lebar lewat aspect-ratio: rasio SVG-nya tidak diketahui
            // server, dan mask-size:contain memuat bentuk apa pun di dalam kotak ini.
            $vars[] = '--heading-rule-h: auto;';
            $vars[] = '--heading-rule-ar: 7 / 1;';
        }

        return $vars;
    }

    protected function scaleVars(array $scales): array
    {
        $defaults = config('invitation.default_theme.scales');
        $num = function ($v, $fallback, bool $mustBePositive = false) {
            $isNumeric = is_int($v) || is_float($v) || (is_string($v) && is_numeric($v));
            if (! $isNumeric) {
                return $fallback;
            }
            $n = (float) $v;
            if ($mustBePositive && $n <= 0) {
                return $fallback;
            }
            if (! $mustBePositive && $n < 0) {
                return $fallback;
            }

            return $n;
        };

        $base = $num($scales['type_base'] ?? null, $defaults['type_base'], true);
        $ratio = $num($scales['type_ratio'] ?? null, $defaults['type_ratio'], true);
        $radius = $num($scales['radius'] ?? null, $defaults['radius']);
        $sectionY = $num($scales['section_spacing'] ?? null, $defaults['section_spacing']);

        $round = fn (float $n) => rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
        $shadowMap = [
            'none' => 'none',
            'sm' => '0 1px 3px rgba(0,0,0,.08)',
            'md' => '0 8px 24px rgba(0,0,0,.12)',
            'lg' => '0 16px 40px rgba(0,0,0,.16)',
        ];
        $shadowLevel = is_string($scales['shadow_level'] ?? null) ? $scales['shadow_level'] : 'sm';
        $shadow = $shadowMap[$shadowLevel] ?? $shadowMap['sm'];

        return [
            '--step-sm: '.$round($base / $ratio).'px;',
            '--step-base: '.$round($base).'px;',
            '--step-lg: '.$round($base * $ratio).'px;',
            '--step-xl: '.$round($base * $ratio ** 2).'px;',
            '--step-2xl: '.$round($base * $ratio ** 3).'px;',
            '--step-3xl: '.$round($base * $ratio ** 4).'px;',
            '--radius: '.$round($radius).'px;',
            '--section-y: '.$round($sectionY).'px;',
            '--shadow: '.$shadow.';',
        ];
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
