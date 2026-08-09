<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel WhatsApp Integration (kstmostofa/laravel-whatsapp)
    |--------------------------------------------------------------------------
    */
    'enabled' => env('WHATSAPP_WEB_ENABLED', true),

    'host' => env('WHATSAPP_WEB_HOST', '127.0.0.1'),

    'port' => (int) env('WHATSAPP_WEB_PORT', 3000),

    'token' => env('WHATSAPP_WEB_TOKEN', 'siakad-nuja-secret-token'),

    'phone_suffix' => '@s.whatsapp.net',

    'timeout' => (int) env('WHATSAPP_TIMEOUT', 30),

    'session_timeout_minutes' => (int) env('WHATSAPP_SESSION_TIMEOUT', 30),
];
