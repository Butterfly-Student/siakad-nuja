<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konfigurasi extends Model
{
    protected $table = 'konfigurasi';
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Helper to get a configuration value easily.
     */
    public static function get(string $key, $default = null)
    {
        $config = self::find($key);
        return $config ? $config->value : $default;
    }
}
