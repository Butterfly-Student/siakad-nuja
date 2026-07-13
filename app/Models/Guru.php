<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';

    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'jabatan',
        'no_hp',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelasWali(): HasMany
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class, 'guru_id');
    }
}
