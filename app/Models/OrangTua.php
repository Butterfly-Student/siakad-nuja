<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrangTua extends Model
{
    use HasFactory;

    protected $table = 'orang_tua';

    protected $fillable = [
        'siswa_id',
        'nama',
        'hubungan',
        'no_hp',
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
}
