<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotLog extends Model
{
    use HasFactory;

    protected $table = 'chatbot_log';

    public $timestamps = false;

    protected $fillable = [
        'no_hp',
        'pesan_masuk',
        'pesan_keluar',
        'siswa_id',
        'intent',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
