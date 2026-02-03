<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    use HasFactory;

    protected $table = 'system_notifications';

    protected $fillable = [
        'title',
        'body',
        'level',
        'link',
        'code',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
