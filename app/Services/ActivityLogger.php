<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

final class ActivityLogger
{
    public static function log(string $module, string $objectId, string $action): void
    {
        try {
            ActivityLog::create([
                'module' => $module,
                'object_id' => $objectId,
                'action' => $action,
                'user_id' => Auth::id(),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            Log::error('ActivityLog erro: '.$e->getMessage());
        }
    }
}
