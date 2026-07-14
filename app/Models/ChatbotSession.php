<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotSession extends Model
{
    use HasFactory;

    protected $table = 'chatbot_session';

    public $timestamps = false;

    protected $fillable = [
        'no_hp',
        'orang_tua_id',
        'anak_terpilih_id',
        'state',
        'data_context',
        'last_activity',
    ];

    protected function casts(): array
    {
        return [
            'data_context'  => 'array',
            'last_activity' => 'datetime',
        ];
    }

    public function orangTua(): BelongsTo
    {
        return $this->belongsTo(OrangTua::class, 'orang_tua_id');
    }

    public function anakTerpilih(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'anak_terpilih_id');
    }
}
