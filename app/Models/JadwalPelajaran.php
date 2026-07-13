<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalPelajaran extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'kelas_id',
        'mapel_id',
        'guru_id',
        'hari',
        'jam_ke',
        'jam_mulai',
        'jam_selesai',
        'ruangan',
        'tahun_ajaran',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'jadwal_id');
    }
}
