<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotSession extends Model
{
    use HasFactory;

    protected $table = 'chatbot_session';

    public $timestamps = false;

    protected $fillable = [
        'no_hp',
        'state',
        'data_context',
        'last_activity',
    ];

    protected function casts(): array
    {
        return [
            'data_context' => 'array',
            'last_activity' => 'datetime',
        ];
    }
}
