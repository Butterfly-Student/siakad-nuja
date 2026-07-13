<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    public $timestamps = false;

    protected $fillable = [
        'judul',
        'konten',
        'target_role',
        'dibuat_oleh',
        'tanggal_publish',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_publish' => 'date',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
