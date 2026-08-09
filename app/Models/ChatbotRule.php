<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotRule extends Model
{
    use HasFactory;

    protected $table = 'chatbot_rules';

    protected $fillable = [
        'keyword',
        'judul_menu',
        'tipe_action',
        'action_key',
        'isi_balasan',
        'urutan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'urutan'    => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
