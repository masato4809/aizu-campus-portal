<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name'                => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env'                 => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug'               => (bool) env('APP_DEBUG', false),

    'performance_measure' => (bool) env('APP_PERFORMANCE_MEASURE', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url'                 => env('APP_URL', 'http://localhost'),

    'asset_url'           => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone'            => 'Asia/Tokyo',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale'              => 'ja',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale'     => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    //'faker_locale'    => 'en_US',
    'faker_locale'        => 'ja_JP',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key'                 => env('APP_KEY'),

    'cipher'              => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance'         => [
        'driver' => 'file',
        // 'store' => 'redis',
    ],

    /**
     * 以下はide-helperに認識させるために記載、実際のロードは
     * wportal2-app/bootstrap/providers.phpで行う
     */

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers'           => ServiceProvider::defaultProviders()->merge([
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

        // Usecases.
        App\Providers\Usecases\UsecasesShuffleLunchServiceProvider::class,

    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases'             => Facade::defaultAliases()->merge([

        // External.
        'external\google_calendar_service'                   => App\Facades\External\GoogleCalendarService::class,
        'external\recaptcha_service'                         => App\Facades\External\RecaptchaService::class,
        'external\ses_service'                               => App\Facades\External\SesService::class,
        'external\slack_service'                             => App\Facades\External\SlackService::class,

        // Internal.
        'internal\activity_log_service'                      => App\Facades\Internal\ActivityLogService::class,
        'internal\pipe_service'                              => App\Facades\Internal\PipeService::class,

        // Models.App.Auth.
        'models\app\trn\auth_user_service'                   => App\Facades\Models\App\Auth\AuthUserService::class,

        // Models.App.Mst.
        'models\app\mst\mst_goods_service'                   => App\Facades\Models\App\Mst\MstGoodsService::class,
        'models\app\mst\mst_reward_service'                  => App\Facades\Models\App\Mst\MstRewardService::class,

        // Models.App.Trn.
        'models\app\trn\trn_attendance_state_service'        => App\Facades\Models\App\Trn\TrnAttendanceStateService::class,
        'models\app\trn\trn_batch_release_history_service'   => App\Facades\Models\App\Trn\TrnBatchReleaseHistoryService::class,
        'models\app\trn\trn_division_service'                => App\Facades\Models\App\Trn\TrnDivisionService::class,
        'models\app\trn\trn_division_user_service'           => App\Facades\Models\App\Trn\TrnDivisionUserService::class,
        'models\app\trn\trn_good_job_service'                => App\Facades\Models\App\Trn\TrnGoodJobService::class,
        'models\app\trn\trn_project_service'                 => App\Facades\Models\App\Trn\TrnProjectService::class,
        'models\app\trn\trn_project_notification_service'    => App\Facades\Models\App\Trn\TrnProjectNotificationService::class,
        'models\app\trn\trn_project_user_service'            => App\Facades\Models\App\Trn\TrnProjectUserService::class,
        'models\app\trn\trn_shop_aggregate_service'          => App\Facades\Models\App\Trn\TrnShopAggregateService::class,
        'models\app\trn\trn_shuffle_lunch_entry_service'     => App\Facades\Models\App\Trn\TrnShuffleLunchEntryService::class,
        'models\app\trn\trn_shuffle_lunch_group_service'     => App\Facades\Models\App\Trn\TrnShuffleLunchGroupService::class,
        'models\app\trn\trn_seat_service'                    => App\Facades\Models\App\Trn\TrnSeatService::class,
        'models\app\trn\trn_user_authority_service'          => App\Facades\Models\App\Trn\TrnUserAuthorityService::class,
        'models\app\trn\trn_user_division_priority_service'  => App\Facades\Models\App\Trn\TrnUserDivisionPriorityService::class,
        'models\app\trn\trn_user_project_priority_service'   => App\Facades\Models\App\Trn\TrnUserProjectPriorityService::class,
        'models\app\trn\trn_user_service'                    => App\Facades\Models\App\Trn\TrnUserService::class,
        'models\app\trn\trn_user_reward_service'             => App\Facades\Models\App\Trn\TrnUserRewardService::class,
        'models\app\trn\trn_user_slack_profile_service'      => App\Facades\Models\App\Trn\TrnUserSlackProfileService::class,
        'models\app\trn\trn_seat_service'                    => App\Facades\Models\App\Trn\TrnSeatService::class,

        // Usecases.
        'usecases\usecases_shuffle_lunch_service'            => App\Facades\Usecases\UsecasesShuffleLunchService::class,

    ])->toArray(),
];
