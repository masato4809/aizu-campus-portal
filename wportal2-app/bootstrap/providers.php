<?php

declare(strict_types=1);

return [
    /*
     * Package Service Providers...
     */

    /*
     * Application Service Providers...
     */
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    // App\Providers\BroadcastServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,

    /**
     * Custom providers...
     */

    // External.
    App\Providers\External\GoogleCalendarServiceProvider::class,
    App\Providers\External\RecaptchaServiceProvider::class,
    App\Providers\External\SesServiceProvider::class,
    App\Providers\External\SlackServiceProvider::class,

    // Internal.
    App\Providers\Internal\ActivityLogServiceProvider::class,
    App\Providers\Internal\PipeServiceProvider::class,

    // Models.App.Auth.
    App\Providers\Models\App\Auth\AuthUserServiceProvider::class,

    // Models.App.Mst.
    App\Providers\Models\App\Mst\MstGoodsServiceProvider::class,
    App\Providers\Models\App\Mst\MstRewardServiceProvider::class,

    // Models.App.Trn.
    App\Providers\Models\App\Trn\TrnAttendanceStateServiceProvider::class,
    App\Providers\Models\App\Trn\TrnBatchReleaseHistoryServiceProvider::class,
    App\Providers\Models\App\Trn\TrnDivisionServiceProvider::class,
    App\Providers\Models\App\Trn\TrnDivisionUserServiceProvider::class,
    App\Providers\Models\App\Trn\TrnGoodJobServiceProvider::class,
    App\Providers\Models\App\Trn\TrnProjectServiceProvider::class,
    App\Providers\Models\App\Trn\TrnProjectNotificationServiceProvider::class,
    App\Providers\Models\App\Trn\TrnProjectUserServiceProvider::class,
    App\Providers\Models\App\Trn\TrnShopAggregateServiceProvider::class,
    App\Providers\Models\App\Trn\TrnShuffleLunchEntryServiceProvider::class,
    App\Providers\Models\App\Trn\TrnShuffleLunchGroupServiceProvider::class,
    App\Providers\Models\App\Trn\TrnSeatServiceProvider::class,
    App\Providers\Models\App\Trn\TrnUserAuthorityServiceProvider::class,
    App\Providers\Models\App\Trn\TrnUserDivisionPriorityServiceProvider::class,
    App\Providers\Models\App\Trn\TrnUserProjectPriorityServiceProvider::class,
    App\Providers\Models\App\Trn\TrnUserServiceProvider::class,
    App\Providers\Models\App\Trn\TrnUserRewardServiceProvider::class,
    App\Providers\Models\App\Trn\TrnUserSlackProfileServiceProvider::class,

    // Models.App.Trn.Qmart.
    App\Providers\Models\App\Trn\TrnQmartItemServiceProvider::class,
    App\Providers\Models\App\Trn\TrnQmartItemImageServiceProvider::class,
    App\Providers\Models\App\Trn\TrnQmartCommentServiceProvider::class,

    // Usecases.
    App\Providers\Usecases\UsecasesShuffleLunchServiceProvider::class,
];
