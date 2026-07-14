<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsappWebhookController extends Controller
{
    public function __construct(private readonly ChatbotService $chatbot) {}

    /**
     * Endpoint webhook untuk menerima pesan masuk dari WAHA.
     *
     * WAHA Payload Format:
     * {
     *   "event": "message",
     *   "session": "default",
     *   "payload": {
     *     "id": "xxx",
     *     "from": "628xxx@c.us",
     *     "body": "1",
     *     "timestamp": 1720900000,
     *     "fromMe": false
     *   }
     * }
     */
    public function handle(Request $request): JsonResponse
    {
        Log::info('[WAHA Webhook] Payload: ' . json_encode($request->all()));

        // ── 1. Normalisasi: Dukung format WAHA dan format generik ──────────
        $event   = $request->input('event');
        $payload = $request->input('payload');

        // Jika format WAHA (nested payload)
        if ($event && $payload) {
            // Abaikan bukan event "message"
            if ($event !== 'message') {
                return response()->json(['status' => 'ignored', 'reason' => "event={$event}"]);
            }

            $fromMe = $payload['fromMe'] ?? false;
            if ($fromMe) {
                return response()->json(['status' => 'ignored', 'reason' => 'fromMe=true']);
            }

            $sender  = $payload['from'] ?? null;
            $message = $payload['body'] ?? null;
        } else {
            // Format generik (fallback untuk testing atau gateway lain)
            $sender  = $request->input('sender');
            $message = $request->input('message');
        }

        // ── 2. Validasi ────────────────────────────────────────────────────
        if (!$sender || !$message) {
            return response()->json(['error' => 'Missing sender or message'], 400);
        }

        // Strip format @c.us dari nomor WAHA
        $noHp = preg_replace('/@.*$/', '', (string) $sender);

        // ── 3. Proses FSM chatbot ──────────────────────────────────────────
        try {
            $this->chatbot->process($noHp, (string) $message);
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('[WAHA Webhook] Chatbot error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
