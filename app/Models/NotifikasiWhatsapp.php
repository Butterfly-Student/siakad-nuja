<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotifikasiWhatsapp extends Model
{
    protected $table = 'notifikasi_whatsapp';

    protected $fillable = [
        'orang_tua_id',
        'siswa_id',
        'no_tujuan',
        'jenis',
        'isi_pesan',
        'status',
        'dikirim_pada',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'dikirim_pada' => 'datetime',
        ];
    }

    public function orangTua(): BelongsTo
    {
        return $this->belongsTo(OrangTua::class, 'orang_tua_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
