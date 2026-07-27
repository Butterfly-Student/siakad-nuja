<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$session = \App\Models\ChatbotSession::first();
echo "SESSION: " . json_encode($session) . "\n";

$log = \App\Models\ChatbotLog::orderBy('id', 'desc')->first();
echo "LOG: " . json_encode($log) . "\n";
