<?php

declare(strict_types=1);

namespace App\Providers\Models\App\Trn;

use Illuminate\Support\ServiceProvider;

class TrnAttendanceStateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        app()->singleton(
            'models\app\trn\trn_attendance_state_service',
            'App\Services\Models\App\Trn\TrnAttendanceStateService'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
