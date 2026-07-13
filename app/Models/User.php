<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'no_hp',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function getNameAttribute(): string
    {
        return $this->attributes['nama'] ?? '';
    }

    public function guru(): HasOne
    {
        return $this->hasOne(Guru::class);
    }

    public function pengumuman(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'dibuat_oleh');
    }
}
