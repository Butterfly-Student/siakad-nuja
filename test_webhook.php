<?php

// Test WAHA format webhook
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Reset session first
\App\Models\ChatbotSession::truncate();

// Update test user no_wa
\App\Models\OrangTua::where('no_hp', '087490429290')->update(['no_wa' => '087490429290']);

echo "OrangTua updated.\n";

// Simulate WAHA webhook
$url = 'http://127.0.0.1:8000/api/webhook/whatsapp';

// Test 1: WAHA format - initial greeting
$payload = [
    'event' => 'message',
    'session' => 'default',
    'payload' => [
        'from' => '6287490429290@c.us',
        'body' => 'MENU',
        'fromMe' => false,
    ],
];

$opts = [
    'http' => [
        'header' => "Content-type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($payload),
    ],
];
$context = stream_context_create($opts);
$result = file_get_contents($url, false, $context);
echo "Test 1 (MENU): " . $result . "\n";

sleep(1);

// Test 2: Input "1" (Info Nilai)
$payload['payload']['body'] = '1';
$context = stream_context_create(['http' => ['header' => "Content-type: application/json\r\n", 'method' => 'POST', 'content' => json_encode($payload)]]);
$result = file_get_contents($url, false, $context);
echo "Test 2 (1 = Info Nilai): " . $result . "\n";

sleep(1);

// Test 3: fromMe should be ignored
$payload['payload']['fromMe'] = true;
$context = stream_context_create(['http' => ['header' => "Content-type: application/json\r\n", 'method' => 'POST', 'content' => json_encode($payload)]]);
$result = file_get_contents($url, false, $context);
echo "Test 3 (fromMe=true, should ignore): " . $result . "\n";

echo "\n--- Chatbot Log ---\n";
$logs = \App\Models\ChatbotLog::orderBy('id', 'desc')->take(3)->get();
foreach ($logs as $log) {
    echo "Intent: {$log->intent}\n";
    echo "Masuk : {$log->pesan_masuk}\n";
    echo "Keluar: " . mb_substr($log->pesan_keluar, 0, 100) . "...\n\n";
}

echo "\n--- Session ---\n";
$session = \App\Models\ChatbotSession::first();
echo json_encode($session, JSON_PRETTY_PRINT);
