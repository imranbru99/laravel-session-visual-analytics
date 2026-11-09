<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    */
    'name' => env('SESSION_VISTA_NAME', 'SessionVista'),
    'path' => env('SESSION_VISTA_PATH', 'session-vista'),

    /*
    |--------------------------------------------------------------------------
    | Access control
    |--------------------------------------------------------------------------
    | Middleware stack applied to the dashboard routes.
    */
    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Tracking
    |--------------------------------------------------------------------------
    */
    'track' => [
        'enabled' => env('SESSION_VISTA_TRACK', true),
        'exclude_paths' => [
            'session-vista*',
            'telescope*',
            'horizon*',
            'livewire*',
            '_debugbar*',
        ],
        'heartbeat_seconds' => 60,
        'idle_after_minutes' => 15,
        'expire_after_minutes' => 120,
        'store_payload' => false,
        'store_ip' => true,
        'store_user_agent' => true,
        'store_geo' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Retention
    |--------------------------------------------------------------------------
    */
    'retention_days' => (int) env('SESSION_VISTA_RETENTION_DAYS', 90),

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    'dashboard' => [
        'refresh_seconds' => 30,
        'chart_hours' => 24,
        'top_pages_limit' => 10,
        'active_users_limit' => 12,
    ],

    /*
    |--------------------------------------------------------------------------
    | Geo lookup (optional HTTP provider)
    |--------------------------------------------------------------------------
    | Leave null to use a lightweight offline inference from Accept-Language /
    | timezone hints when available. You may plug any GeoIP resolver.
    */
    'geo' => [
        'driver' => env('SESSION_VISTA_GEO_DRIVER', 'none'), // none|http
        'http_url' => env('SESSION_VISTA_GEO_URL'), // e.g. https://ipapi.co/{ip}/json/
    ],
];
