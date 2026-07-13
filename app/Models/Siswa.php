<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nama_lengkap',
        'kelas_id',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'foto',
        'status',
        'tahun_masuk',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function orangTua(): HasMany
    {
        return $this->hasMany(OrangTua::class, 'siswa_id');
    }

    public function nilai(): HasMany
    {
        return $this->hasMany(Nilai::class, 'siswa_id');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'siswa_id');
    }
}
