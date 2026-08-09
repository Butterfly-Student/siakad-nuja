<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\ChatbotService;
use Illuminate\Support\Facades\Log;
use Kstmostofa\LaravelWhatsApp\Events\Web\MessageReceived;

class WhatsappMessageListener
{
    public function __construct(private readonly ChatbotService $chatbotService) {}

    public function handle(object $event): void
    {
        try {
            if ($event instanceof MessageReceived) {
                if ($event->fromMe()) {
                    Log::debug("[WhatsappMessageListener] Skipped message from self (fromMe=true)");
                    return;
                }

                if ($event->isGroup()) {
                    Log::debug("[WhatsappMessageListener] Skipped message from group");
                    return;
                }

                $from = $event->from();
                $body = $event->body();
            } else {
                $from = method_exists($event, 'from') ? $event->from() : ($event->from ?? null);
                $body = method_exists($event, 'body') ? $event->body() : ($event->body ?? null);
            }

            if (! $from || ! $body) {
                return;
            }

            // Clean number from JID or non-digits
            $noHp = preg_replace('/[^0-9]/', '', (string) $from);

            Log::info("[WhatsappMessageListener] Processing inbound message from {$noHp}: {$body}");

            $this->chatbotService->process($noHp, (string) $body);
        } catch (\Exception $e) {
            Log::error('[WhatsappMessageListener] Error processing message: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
