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
     * Endpoint webhook untuk menerima pesan masuk dari Go-WA.
     */
    public function handle(Request $request): JsonResponse
    {
        Log::info('[GOWA Webhook] Payload: ' . json_encode($request->all()));

        // ── 1. Verifikasi HMAC Signature (opsional) ─────────────────────────
        $secret = config('whatsapp.webhook_secret', '');
        if ($secret) {
            $signature = $request->header('x-webhook-signature', '');
            $expected  = hash_hmac('sha256', $request->getContent(), $secret);

            if (!hash_equals($expected, $signature)) {
                Log::warning('[GOWA Webhook] Signature mismatch — ditolak.');
                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }

        // ── 2. Normalisasi: Dukung format Go-WA dan format generik ──────────
        $event    = $request->input('event');
        $deviceId = $request->input('device_id');
        $payload  = $request->input('payload');

        // Jika format Go-WA (nested payload)
        if ($event && $payload) {
            // Abaikan bukan event "message"
            if ($event !== 'message') {
                return response()->json(['status' => 'ignored', 'reason' => "event={$event}"]);
            }

            // Deteksi pesan dari diri sendiri (fromMe)
            $fromMe = $payload['is_from_me'] ?? false;
            if ($fromMe) {
                return response()->json(['status' => 'ignored', 'reason' => 'fromMe=true']);
            }

            $sender = $payload['from'] ?? null;
            if ($sender && $deviceId && $sender === $deviceId) {
                return response()->json(['status' => 'ignored', 'reason' => 'fromMe=true']);
            }

            // Abaikan pesan dari Grup (@g.us)
            $chatId = $payload['chat_id'] ?? '';
            if (str_ends_with($chatId, '@g.us')) {
                return response()->json(['status' => 'ignored', 'reason' => 'group_chat']);
            }

            // Ekstrak isi pesan (Go-WA bisa di payload.body atau payload.message.conversation)
            $messageObj = $payload['message'] ?? [];
            $message    = $payload['body']
                ?? $messageObj['conversation']
                ?? $messageObj['extendedTextMessage']['text']
                ?? null;
        } else {
            // Format generik (fallback untuk testing atau gateway lain)
            $sender  = $request->input('sender');
            $message = $request->input('message');
        }

        // ── 3. Validasi ────────────────────────────────────────────────────
        if (!$sender || !$message) {
            return response()->json(['error' => 'Missing sender or message'], 400);
        }

        // Strip format JID dari nomor Go-WA (@s.whatsapp.net)
        $noHp = preg_replace('/@.*$/', '', (string) $sender);

        // ── 4. Proses FSM chatbot ──────────────────────────────────────────
        try {
            $this->chatbot->process($noHp, (string) $message);
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('[GOWA Webhook] Chatbot error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
