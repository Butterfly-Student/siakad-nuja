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
        $rawContent = $request->getContent();
        Log::info('[GOWA Webhook] Payload Received: ' . $rawContent);

        // ── 1. Verifikasi HMAC Signature (opsional) ─────────────────────────
        $secret = config('whatsapp.webhook_secret', '');
        if ($secret) {
            $signature = $request->header('x-webhook-signature', '');
            $expected  = hash_hmac('sha256', $rawContent, $secret);

            if (! hash_equals($expected, $signature)) {
                Log::warning('[GOWA Webhook] Signature mismatch — ditolak.');
                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }

        // ── 2. Normalisasi Payload (Dukung Go-WA, Baileys, & Generik) ────────
        $event    = $request->input('event');
        $deviceId = $request->input('device_id');
        $payload  = $request->input('payload') ?? $request->input('data');

        $sender  = null;
        $message = null;

        if ($payload && is_array($payload)) {
            // Abaikan jika event dispesifikasikan dan bukan "message"
            if ($event && $event !== 'message') {
                return response()->json(['status' => 'ignored', 'reason' => "event={$event}"]);
            }

            // Deteksi pesan dari diri sendiri (fromMe)
            $fromMe = $payload['is_from_me'] ?? $payload['fromMe'] ?? $payload['key']['fromMe'] ?? false;
            if ($fromMe) {
                return response()->json(['status' => 'ignored', 'reason' => 'fromMe=true']);
            }

            $sender = $payload['from'] ?? $payload['sender'] ?? $payload['key']['remoteJid'] ?? null;
            if ($sender && $deviceId && $sender === $deviceId) {
                return response()->json(['status' => 'ignored', 'reason' => 'fromMe=true']);
            }

            // Abaikan pesan dari Grup (@g.us)
            $chatId = (string) ($payload['chat_id'] ?? $payload['from'] ?? $payload['key']['remoteJid'] ?? '');
            if (str_ends_with($chatId, '@g.us')) {
                return response()->json(['status' => 'ignored', 'reason' => 'group_chat']);
            }

            // Ekstrak pesan
            $messageObj = $payload['message'] ?? [];
            if (is_string($messageObj)) {
                $message = $messageObj;
            } else {
                $message = $payload['body']
                    ?? $payload['text']
                    ?? $messageObj['conversation']
                    ?? $messageObj['extendedTextMessage']['text']
                    ?? $messageObj['imageMessage']['caption']
                    ?? null;
            }
        } else {
            // Format generik / flat (fallback)
            $sender  = $request->input('sender') ?? $request->input('from') ?? $request->input('key.remoteJid');
            $messageInput = $request->input('message');
            if (is_string($messageInput)) {
                $message = $messageInput;
            } else {
                $message = $request->input('body')
                    ?? $request->input('text')
                    ?? $request->input('message.conversation')
                    ?? $request->input('message.extendedTextMessage.text');
            }
        }

        // ── 3. Validasi ────────────────────────────────────────────────────
        if (! $sender || ! $message) {
            Log::warning('[GOWA Webhook] Missing sender/message: sender=' . json_encode($sender) . ' message=' . json_encode($message));
            return response()->json(['error' => 'Missing sender or message', 'received' => $request->all()], 400);
        }

        // Strip format JID dari nomor Go-WA (@s.whatsapp.net)
        $noHp = preg_replace('/@.*$/', '', (string) $sender);

        // ── 4. Proses FSM chatbot ──────────────────────────────────────────
        try {
            $this->chatbot->process($noHp, (string) $message);
            return response()->json(['status' => 'success']);
        } catch (\Throwable $e) {
            Log::error('[GOWA Webhook] Chatbot error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
