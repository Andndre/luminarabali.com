<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Curated fonts
    |--------------------------------------------------------------------------
    | The only fonts a template's theme (or a buyer's override) may reference.
    | "name" must exactly match a Google Fonts family name.
    */
    'fonts' => [
        ['name' => 'Playfair Display', 'url' => 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap'],
        ['name' => 'Lora', 'url' => 'https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&display=swap'],
        ['name' => 'Lato', 'url' => 'https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap'],
        ['name' => 'Montserrat', 'url' => 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap'],
        ['name' => 'Great Vibes', 'url' => 'https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap'],
        ['name' => 'Open Sans', 'url' => 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default theme
    |--------------------------------------------------------------------------
    | Used when a template has no theme set, or a key is missing from it.
    */
    'default_theme' => [
        'colors' => [
            'primary' => '#3b2f2f',
            'accent' => '#b5654d',
            'surface' => '#fffaf3',
            'text' => '#2b2b2b',
        ],
        'fonts' => [
            'heading' => 'Playfair Display',
            'body' => 'Lato',
        ],
    ],
];
