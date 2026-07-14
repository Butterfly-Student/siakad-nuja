<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | WAHA (WhatsApp HTTP API) — Self-Hosted Gateway
    |--------------------------------------------------------------------------
    |
    | URL ke server WAHA yang sedang berjalan. Dalam development, ini biasanya
    | http://localhost:3000 (via Docker). Di produksi, ganti ke IP/domain server.
    |
    */
    'url'     => env('WAHA_URL', 'http://localhost:3000'),

    /*
    | Nama sesi WAHA. Biarkan 'default' kecuali Anda menjalankan multi-sesi.
    */
    'session' => env('WAHA_SESSION', 'default'),

    /*
    | API Key untuk WAHA (hanya tersedia di WAHA Pro / versi Plus).
    | Biarkan kosong jika menggunakan versi Core gratis.
    */
    'api_key' => env('WAHA_API_KEY', ''),

    /*
    | HTTP timeout dalam detik untuk setiap request ke gateway.
    */
    'timeout' => (int) env('WAHA_TIMEOUT', 30),

    /*
    | Timeout sesi chatbot dalam menit. Jika wali diam selama ini, sesi akan di-reset.
    */
    'session_timeout_minutes' => (int) env('WAHA_SESSION_TIMEOUT', 30),
];
