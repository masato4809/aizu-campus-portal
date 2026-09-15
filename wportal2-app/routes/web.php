<?php

declare(strict_types=1);

use App\Enum\App\EPages;
use Illuminate\Support\Facades\Route;

require __DIR__.'/campus.php';

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['auth:web']], function () {
    Route::get('/', EPages::DASHBOARD->getInvokePath());
    Route::get('/dashboard', EPages::DASHBOARD->getInvokePath());

    /**
     * 勤怠共有
     */
    Route::prefix('/attendance')->group(function () {
        Route::match(['get', 'post'], '/', EPages::ATTENDANCE->getInvokePath());
        Route::post('/create', EPages::ATTENDANCE_CREATE->getInvokePath());
        Route::post('/delete', EPages::ATTENDANCE_DELETE->getInvokePath());
        Route::post('/project_priority', EPages::ATTENDANCE_PROJECT_PRIORITY->getInvokePath());
        Route::post('/division_priority', EPages::ATTENDANCE_DIVISION_PRIORITY->getInvokePath());
    });

    /**
     * シャッフルランチ.
     */
    Route::prefix('/shuffle_lunch')->group(function () {
        Route::get('/', EPages::SHUFFLE_LUNCH->getInvokePath());
        Route::post('/register', EPages::SHUFFLE_LUNCH__REGISTER->getInvokePath());
        Route::post('/cancel', EPages::SHUFFLE_LUNCH__CANCEL->getInvokePath());
        Route::post('/rakumo', EPages::SHUFFLE_LUNCH__RAKUMO->getInvokePath());
    });

    /**
     * 感謝を記録する.
     */
    Route::prefix('/good_job')->group(function () {
        Route::match(['get', 'post'], '/', EPages::GOOD_JOB->getInvokePath());
        Route::get('/new', EPages::GOOD_JOB__NEW->getInvokePath());
        Route::post('/create', EPages::GOOD_JOB__CREATE->getInvokePath());
    });

    /**
     * ショップ
     */
    Route::prefix('/shop')->group(function () {
        Route::get('/', EPages::SHOP->getInvokePath());
        Route::post('/lunch_ticket', EPages::SHOP__LUNCH_TICKET->getInvokePath());
    });

    /**
     * 報酬.
     */
    Route::prefix('/reward')->group(function () {
        Route::match(['get', 'post'], '/', EPages::REWARD->getInvokePath());
        Route::get('/list', EPages::REWARD__LIST->getInvokePath());
        Route::post('/achieve', EPages::REWARD__ACHIEVE->getInvokePath());
    });

    /**
     * 勤怠確認・修正.
     */
    Route::prefix('/attendance/edit')->group(function () {
        Route::match(['get', 'post'], '/', EPages::ATTENDANCE__EDIT->getInvokePath());
        Route::post('/update', EPages::ATTENDANCE__EDIT__UPDATE->getInvokePath());
    });

    /**
     * 課
     */
    Route::prefix('/division')->group(function () {
        Route::match(['get', 'post'], '/', EPages::DIVISION->getInvokePath());
        Route::get('/{divisionId}/show', EPages::DIVISION__SHOW->getInvokePath())
            ->where('divisionId', '[0-9]+');
        Route::get('/{divisionId}/edit', EPages::DIVISION__EDIT->getInvokePath())
            ->where('divisionId', '[0-9]+');
        Route::post('/update', EPages::DIVISION__UPDATE->getInvokePath());
        Route::post('/update_user', EPages::DIVISION__UPDATE_USER->getInvokePath());
        Route::get('/new', EPages::DIVISION__NEW->getInvokePath());
        Route::post('/create', EPages::DIVISION__CREATE->getInvokePath());
        Route::post('/delete', EPages::DIVISION__DELETE->getInvokePath());
    });

    /**
     * プロジェクト.
     */
    Route::prefix('/project')->group(function () {
        Route::match(['get', 'post'], '/', EPages::PROJECT->getInvokePath());
        Route::get('/{projectId}/show', EPages::PROJECT__SHOW->getInvokePath())
            ->where('projectId', '[0-9]+');
        Route::get('/{projectId}/edit', EPages::PROJECT__EDIT->getInvokePath())
            ->where('projectId', '[0-9]+');
        Route::post('/update', EPages::PROJECT__UPDATE->getInvokePath());
        Route::post('/update_user', EPages::PROJECT__UPDATE_USER->getInvokePath());
        Route::get('/new', EPages::PROJECT__NEW->getInvokePath());
        Route::post('/create', EPages::PROJECT__CREATE->getInvokePath());
        Route::post('/delete', EPages::PROJECT__DELETE->getInvokePath());
    });

    /**
     * メンバー.
     */
    Route::prefix('/user')->group(function () {
        Route::match(['get', 'post'], '/', EPages::USER->getInvokePath());
        Route::get('/{userId}/show', EPages::USER__SHOW->getInvokePath())
            ->where('userId', '[0-9]+');
        Route::get('/{userId}/edit', EPages::USER__EDIT->getInvokePath())
            ->where('userId', '[0-9]+');
        Route::post('/update', EPages::USER__UPDATE->getInvokePath());
        Route::get('/new', EPages::USER__NEW->getInvokePath());
        Route::post('/create', EPages::USER__CREATE->getInvokePath());
        Route::post('/delete', EPages::USER__DELETE->getInvokePath());
    });

    /**
     * 個人設定.
     */
    Route::prefix('/personal_setting')->group(function () {
        Route::get('/show', EPages::PERSONAL_SETTING__SHOW->getInvokePath());
        Route::get('/edit', EPages::PERSONAL_SETTING__EDIT->getInvokePath());
        Route::post('/update', EPages::PERSONAL_SETTING__UPDATE->getInvokePath());
    });

    /**
     * サンプルページ用.
     */
    Route::prefix('/sample')->group(function () {
        Route::get('/dashboard', '\App\Http\Controllers\Pages\Sample\DashboardController@invoke');
        Route::get('/mvc_value', '\App\Http\Controllers\Pages\Sample\MvcValueController@invoke');
        Route::get('/input_form', '\App\Http\Controllers\Pages\Sample\InputFormController@invoke');
        Route::post('/input_form/create', '\App\Http\Controllers\Pages\Sample\InputFormController@create');
        Route::get('/redis', '\App\Http\Controllers\Pages\Sample\RedisController@invoke');
        Route::post('/redis_post_result', '\App\Http\Controllers\Pages\Sample\RedisPostResultController@invoke');
        Route::get('/slack_log', '\App\Http\Controllers\Pages\Sample\SlackLogController@invoke');
        Route::match(['get', 'post'], '/pagination', '\App\Http\Controllers\Pages\Sample\PaginationController@invoke');
        Route::get('/intern', '\App\Http\Controllers\Pages\Sample\InternController@invoke');
        Route::get('/inertia_data', '\App\Http\Controllers\Pages\Sample\InertiaDataController@invoke');
    });

    /**
     * Qマート
     */
    Route::get('/qmart', EPages::QMART->getInvokePath());

    /**
     * 座席表
     */
    Route::get('/seating_chart', EPages::SEATING_CHART->getInvokePath());
});

Route::get('/image/{path?}', '\App\Http\Controllers\ImageController@invoke')
    ->where('path', '.*');

Route::get('/login', EPages::LOGIN->getInvokePath())
    ->name('login');
Route::post('/legacy_login', EPages::LEGACY_LOGIN->getInvokePath());
Route::post('/onetime_password_login', EPages::ONETIME_PASSWORD_LOGIN->getInvokePath());
Route::post('/sp_login', EPages::SP_LOGIN->getInvokePath());
Route::get('/auth/google', '\App\Http\Controllers\Pages\LoginController@getGoogleAuth');
Route::get('/login/googleCallback', '\App\Http\Controllers\Pages\LoginController@authGoogleCallback');
Route::get('/logout', '\App\Http\Controllers\Pages\LoginController@logout');

// ローカル用ゲストログイン
if (isLocal()) {
    Route::get('/login/guest', '\App\Http\Controllers\Pages\LoginController@guestLogin');
}

// 認証前はログイン、認証後は404にフォールバックする
Route::fallback(EPages::LOGIN->getInvokePath());
Route::group(['middleware' => ['auth:web']], function () {
    Route::fallback(EPages::NOT_FOUND->getInvokePath());
});
