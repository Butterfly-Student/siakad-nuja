<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';

    public const STATUS_BELUM = 'belum_dibayar';
    public const STATUS_MENUNGGU = 'menunggu_verifikasi';
    public const STATUS_LUNAS = 'lunas';

    protected $fillable = [
        'siswa_id',
        'judul',
        'jenis',
        'periode',
        'nominal',
        'jatuh_tempo',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'jatuh_tempo' => 'date',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'tagihan_id');
    }

    /**
     * Pembayaran terbaru (untuk menampilkan status verifikasi di tabel).
     */
    public function pembayaranTerakhir(): HasMany
    {
        return $this->pembayaran()->latest();
    }

    /**
     * Label status yang ramah dibaca.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_LUNAS => 'Lunas',
            self::STATUS_MENUNGGU => 'Menunggu Verifikasi',
            default => 'Belum Dibayar',
        };
    }

    /**
     * Apakah tagihan sudah melewati jatuh tempo dan belum lunas.
     */
    public function isTunggakan(): bool
    {
        return $this->status !== self::STATUS_LUNAS
            && $this->jatuh_tempo !== null
            && $this->jatuh_tempo->isPast();
    }
}
