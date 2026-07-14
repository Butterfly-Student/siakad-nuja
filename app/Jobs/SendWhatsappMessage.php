<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\WhatsappGatewayService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsappMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int Maximum retries before giving up */
    public int $tries = 3;

    /** @var int Backoff in seconds before retry */
    public int $backoff = 10;

    public function __construct(
        public readonly string $noHp,
        public readonly string $pesan,
        public readonly string $jenis = 'umum',
        public readonly ?int $orangTuaId = null,
        public readonly ?int $siswaId = null,
    ) {}

    public function handle(WhatsappGatewayService $gateway): void
    {
        $success = $gateway->sendNotification(
            noHp: $this->noHp,
            pesan: $this->pesan,
            jenis: $this->jenis,
            orangTuaId: $this->orangTuaId,
            siswaId: $this->siswaId,
        );

        if (!$success) {
            Log::warning("[SendWhatsappMessage] Gagal kirim ke {$this->noHp}, attempt {$this->attempts()}");

            // Release back to queue jika masih ada retry tersisa
            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff * $this->attempts());
            } else {
                $this->fail(new \RuntimeException("Gagal mengirim WA ke {$this->noHp} setelah {$this->tries} percobaan."));
            }
        }
    }
}
