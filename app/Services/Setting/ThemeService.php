<?php

namespace App\Services\Setting;

use App\Models\ThemeSetting;

class ThemeService
{
    public const THEMES = [
        'default' => [
            'label' => 'Default',
            'description' => 'Standard company colors',
        ],
        'kwibuka' => [
            'label' => 'Kwibuka',
            'description' => 'Kwibuka — April 7th',
        ],
        'umuganura' => [
            'label' => 'Umuganura',
            'description' => 'Harvest Festival — First Friday of August',
        ],
        'christmas' => [
            'label' => 'Christmas',
            'description' => 'December 25th',
        ],
        'new_year' => [
            'label' => 'New Year',
            'description' => 'January 1st',
        ],
    ];

    public function getActiveTheme(): string
    {
        return ThemeSetting::get('theme', 'default') ?? 'default';
    }

    public function setTheme(string $theme): void
    {
        if (! $this->isValidTheme($theme)) {
            throw new \InvalidArgumentException('Invalid theme selected.');
        }

        ThemeSetting::set('theme', $theme);
    }

    public function getAllThemes(): array
    {
        return self::THEMES;
    }

    public function isValidTheme(string $theme): bool
    {
        return array_key_exists($theme, self::THEMES);
    }
}