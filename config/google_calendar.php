<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Service Account JSON Path
    |--------------------------------------------------------------------------
    |
    | Path relative dari project root ke file JSON service account.
    |
    */
    'service_account_json' => env('GOOGLE_CALENDAR_SERVICE_ACCOUNT_JSON', 'storage/app/google-calendar-service-account.json'),

    /*
    |--------------------------------------------------------------------------
    | Calendar ID
    |--------------------------------------------------------------------------
    |
    | Google Calendar ID. Format: xxx@group.calendar.google.com
    |
    */
    'calendar_id' => env('GOOGLE_CALENDAR_ID'),

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Toggle untuk enable/disable seluruh Google Calendar integration.
    | Set ke false saat development atau jika credential belum disetup.
    |
    */
    'enabled' => (bool) env('GOOGLE_CALENDAR_ENABLED', false),
];
