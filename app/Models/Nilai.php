<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilai';

    protected $fillable = [
        'siswa_id',
        'mapel_id',
        'kelas_id',
        'semester',
        'tahun_ajaran',
        'nilai_harian',
        'nilai_uts',
        'nilai_uas',
        'nilai_akhir',
        'predikat',
    ];

    protected function casts(): array
    {
        return [
            'nilai_harian' => 'decimal:2',
            'nilai_uts' => 'decimal:2',
            'nilai_uas' => 'decimal:2',
            'nilai_akhir' => 'decimal:2',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Hitung nilai akhir berbobot: harian 30%, UTS 30%, UAS 40%.
     * Mengembalikan null bila ketiga komponen kosong.
     */
    public static function hitungNilaiAkhir(?float $harian, ?float $uts, ?float $uas): ?float
    {
        $h = (float) ($harian ?? 0);
        $u = (float) ($uts ?? 0);
        $a = (float) ($uas ?? 0);

        if ($h === 0.0 && $u === 0.0 && $a === 0.0) {
            return null;
        }

        return round(($h * 0.3) + ($u * 0.3) + ($a * 0.4), 2);
    }

    /**
     * Tentukan predikat huruf dari nilai akhir.
     */
    public static function hitungPredikat(?float $nilaiAkhir): ?string
    {
        if ($nilaiAkhir === null) {
            return null;
        }

        return match (true) {
            $nilaiAkhir >= 90 => 'A',
            $nilaiAkhir >= 80 => 'B',
            $nilaiAkhir >= 70 => 'C',
            $nilaiAkhir >= 60 => 'D',
            default => 'E',
        };
    }
}
