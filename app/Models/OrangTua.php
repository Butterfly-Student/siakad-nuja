<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrangTua extends Model
{
    use HasFactory;

    protected $table = 'orang_tua';

    protected $fillable = [
        'siswa_id',
        'nama',
        'hubungan',
        'no_hp',
        'no_wa',
        'alamat',
        'pekerjaan',
        'is_kontak_utama',
    ];

    protected function casts(): array
    {
        return [
            'is_kontak_utama' => 'boolean',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Ambil semua anak (siswa) yang dimiliki wali ini berdasarkan nomor WA.
     * Berguna untuk fitur multi-anak di chatbot.
     */
    public function semuaAnak(): HasMany
    {
        // Semua orang_tua dengan no_wa yang sama berarti 1 wali punya banyak anak
        return $this->hasMany(OrangTua::class, 'no_wa', 'no_wa')
                    ->with('siswa');
    }
}
