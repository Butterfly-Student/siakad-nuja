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

                $rawFrom = $event->from() ?? '';
                $body    = $event->body() ?? '';
            } else {
                $rawFrom = (string) (method_exists($event, 'from') ? $event->from() : ($event->from ?? ''));
                $body    = (string) (method_exists($event, 'body') ? $event->body() : ($event->body ?? ''));
            }

            if (empty($rawFrom) || empty($body)) {
                return;
            }

            // Filter out newsletters, status updates, broadcasts, and LID channels
            if (
                str_contains($rawFrom, '@newsletter') ||
                str_contains($rawFrom, '@status') ||
                str_contains($rawFrom, '@broadcast') ||
                str_contains($rawFrom, '@lid')
            ) {
                Log::debug("[WhatsappMessageListener] Ignored non-personal JID: {$rawFrom}");
                return;
            }

            // Clean number from JID or non-digits (e.g. "6287886833160@c.us" -> "6287886833160")
            $noHp = preg_replace('/[^0-9]/', '', $rawFrom);

            // Ignore if invalid phone number length (e.g. channel IDs with 18 digits or empty)
            if (strlen($noHp) < 9 || strlen($noHp) > 15) {
                Log::debug("[WhatsappMessageListener] Ignored invalid phone length ({$noHp}) from {$rawFrom}");
                return;
            }

            Log::info("[WhatsappMessageListener] Processing inbound message from {$noHp}: {$body}");

            $this->chatbotService->process($noHp, (string) $body);
        } catch (\Exception $e) {
            Log::error('[WhatsappMessageListener] Error processing message: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
