<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\ChatbotService;
use Illuminate\Support\Facades\Log;

class WhatsappMessageListener
{
    public function __construct(private readonly ChatbotService $chatbotService) {}

    public function handle(object $event): void
    {
        try {
            $from  = method_exists($event, 'from') ? $event->from() : ($event->from ?? null);
            $body  = method_exists($event, 'body') ? $event->body() : ($event->body ?? null);

            if (! $from || ! $body) {
                return;
            }

            Log::info("[WhatsappMessageListener] Pesan masuk dari {$from}: {$body}");

            $this->chatbotService->process((string) $from, (string) $body);
        } catch (\Exception $e) {
            Log::error('[WhatsappMessageListener] Error processing message: ' . $e->getMessage());
        }
    }
}
