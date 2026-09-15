<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Facades\Internal\ActivityLogService;
use Closure;
use Illuminate\Http\Request;

class ActivityLog
{
    /**
     * フラグが有効の場合に計測を実施する.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $request = $next($request);

        // ログ出力の実施.
        ActivityLogService::outputLog();

        return $request;
    }
}
