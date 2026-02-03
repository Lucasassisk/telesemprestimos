<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    use HasFactory;

    protected $table = 'configuracoes';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    public static function getValue(string $key, $default = null)
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('configuracoes')) {
            return $default;
        }

        $value = static::where('key', $key)->value('value');
        return $value !== null ? $value : $default;
    }
}
