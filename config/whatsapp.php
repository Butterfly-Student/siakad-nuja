<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Go-WA (go-whatsapp-web-multidevice) — Self-Hosted Gateway
    |--------------------------------------------------------------------------
    |
    | URL ke server Go-WA yang sedang berjalan di VPS.
    | Default http://localhost:3000, ganti ke IP/domain server Go-WA Anda.
    |
    */
    'url' => env('GOWA_URL', 'http://localhost:3000'),

    /*
    | Device ID untuk Go-WA. Digunakan sebagai header X-Device-Id.
    | Biarkan kosong jika hanya ada satu device (single-device mode).
    */
    'device_id' => env('GOWA_DEVICE_ID', ''),

    /*
    | Basic Auth credentials untuk Go-WA (jika diaktifkan via flag -b).
    | Biarkan kosong jika tidak menggunakan Basic Auth.
    */
    'username' => env('GOWA_USERNAME', ''),
    'password' => env('GOWA_PASSWORD', ''),

    /*
    | Suffix JID WhatsApp. Go-WA menggunakan format @s.whatsapp.net
    | untuk personal chat. Jangan ubah kecuali perlu.
    */
    'phone_suffix' => '@s.whatsapp.net',

    /*
    | HTTP timeout dalam detik untuk setiap request ke gateway.
    */
    'timeout' => (int) env('GOWA_TIMEOUT', 30),

    /*
    | Timeout sesi chatbot dalam menit. Jika wali diam selama ini, sesi akan di-reset.
    */
    'session_timeout_minutes' => (int) env('GOWA_SESSION_TIMEOUT', 30),

    /*
    | Webhook secret untuk verifikasi HMAC signature (opsional).
    | Set ini sama dengan --webhook-secret di Go-WA server.
    */
    'webhook_secret' => env('GOWA_WEBHOOK_SECRET', ''),
];
