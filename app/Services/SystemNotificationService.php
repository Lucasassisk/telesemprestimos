<?php

namespace App\Services;

use App\Models\SystemNotification;
use Illuminate\Support\Facades\Schema;

class SystemNotificationService
{
    public static function createOnce(string $code, string $title, ?string $body = null, string $level = 'info', ?string $link = null): void
    {
        if (! Schema::hasTable('system_notifications')) {
            return;
        }

        SystemNotification::firstOrCreate(
            ['code' => $code],
            [
                'title' => $title,
                'body' => $body,
                'level' => $level,
                'link' => $link,
            ]
        );
    }
}
