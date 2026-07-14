<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WhatsappWebhookController;

Route::post('/webhook/whatsapp', [WhatsappWebhookController::class, 'handle']);
