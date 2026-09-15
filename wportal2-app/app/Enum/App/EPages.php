<?php

declare(strict_types=1);

namespace App\Enum\App;

/**
 * ページ種別.
 * routingと1vs1対応
 */
enum EPages: string
{
    case INVALID = 'invalid';   // 無効.
    case DASHBOARD = 'dashboard'; // ダッシュボード.
    case ATTENDANCE = 'attendance'; // 勤怠共有.
    case ATTENDANCE_CREATE = 'attendance_create'; // 勤怠共有・追加.
    case ATTENDANCE_DELETE = 'attendance_delete'; // 勤怠共有・削除.
    case ATTENDANCE_DIVISION_PRIORITY = 'attendance_division_priority'; // 課優先度変更.
    case ATTENDANCE_PROJECT_PRIORITY = 'attendance_project_priority'; // プロジェクト優先度変更.
    case ATTENDANCE__EDIT = 'attendance__edit'; // 勤怠修正.
    case ATTENDANCE__EDIT__UPDATE = 'attendance__edit__update'; // 勤怠修正・更新.
    case ATTENDANCE__TABLE = 'attendance__table'; // 勤怠一覧表.
    case SHUFFLE_LUNCH = 'shuffle_lunch'; // シャッフルランチ.
    case SHUFFLE_LUNCH__REGISTER = 'shuffle_lunch__register'; // シャッフルランチ登録.
    case SHUFFLE_LUNCH__CANCEL = 'shuffle_lunch__cancel'; // シャッフルランチキャンセル.
    case SHUFFLE_LUNCH__RAKUMO = 'shuffle_lunch__rakumo'; // rakumoブロック.
    case GOOD_JOB = 'good_job'; // GoodJob.
    case GOOD_JOB__NEW = 'good_job__new'; // GoodJob新規登録ページ.
    case GOOD_JOB__CREATE = 'good_job__create'; // GoodJob新規登録.
    case SHOP = 'shop'; // ショップ.
    case SHOP__LUNCH_TICKET = 'shop__lunch_ticket'; // ショップ・ランチチケット.
    case REWARD = 'reward'; // 報酬.
    case REWARD__LIST = 'reward__list'; // 報酬一覧.
    case REWARD__ACHIEVE = 'reward__achieve'; // 報酬達成.
    case DIVISION = 'division'; // 課一覧.
    case DIVISION__SHOW = 'division__show'; // 課詳細.
    case DIVISION__EDIT = 'division__edit'; // 課編集.
    case DIVISION__NEW = 'division__new'; // 課新規作成.
    case DIVISION__CREATE = 'division__create'; // 課新規登録.
    case DIVISION__UPDATE = 'division__update'; // 課更新.
    case DIVISION__UPDATE_USER = 'division__update_user'; // 課メンバー更新.
    case DIVISION__DELETE = 'division__delete'; // 課削除.
    case PROJECT = 'project'; // プロジェクト一覧.
    case PROJECT__SHOW = 'project__show'; // プロジェクト詳細.
    case PROJECT__EDIT = 'project__edit'; // プロジェクト編集.
    case PROJECT__NEW = 'project__new'; // プロジェクト新規作成.
    case PROJECT__CREATE = 'project__create'; // プロジェクト新規登録.
    case PROJECT__UPDATE = 'project__update'; // プロジェクト更新.
    case PROJECT__UPDATE_USER = 'project__update_user'; // プロジェクトメンバー更新.
    case PROJECT__DELETE = 'project__delete'; // プロジェクト削除.
    case USER = 'user'; // メンバー一覧.
    case USER__SHOW = 'user__show'; // メンバー詳細.
    case USER__EDIT = 'user__edit'; // メンバー編集.
    case USER__NEW = 'user__new'; // メンバー新規作成.
    case USER__CREATE = 'user__create'; // メンバー新規登録.
    case USER__UPDATE = 'user__update'; // メンバー更新.
    case USER__DELETE = 'user__delete'; // メンバー削除.
    case PERSONAL_SETTING__SHOW = 'personal_setting__show'; // 個人設定・閲覧.
    case PERSONAL_SETTING__EDIT = 'personal_setting__edit'; // 個人設定・編集.
    case PERSONAL_SETTING__UPDATE = 'personal_setting__update'; // 個人設定・更新.
    case LOGIN = 'login'; // ログイン.
    case LEGACY_LOGIN = 'legacy_login'; // ワンタイムパスワードの発行.
    case ONETIME_PASSWORD_LOGIN = 'onetime_password_login'; // ワンタイムパスワードログイン.
    case SP_LOGIN = 'sp_login'; // スマートフォン版ログイン.
    case NOT_FOUND = 'not_found'; // ページが見つからない.

    /**
     * 以下、標準環境と同等のサンプルページ.
     */
    case SAMPLE_DASHBOARD = 'sample_dashboard'; // ダッシュボード.
    case SAMPLE_MVC_VALUE = 'sample_mvc_value'; // MVC値渡し.
    case SAMPLE_INPUT_FORM = 'sample_input_form'; // 入力フォーム.
    case SAMPLE_INPUT_FORM__CREATE = 'sample_input_form__create'; // 入力フォームPost更新.
    case SAMPLE_REDIS = 'sample_redis'; // Redis操作.
    case SAMPLE_REDIS_POST_RESULT = 'sample_redis_post_result'; // RedisをPostで更新.
    case SAMPLE_SLACK_LOG = 'sample_slack_log'; // Slackログ通知.
    case SAMPLE_PAGINATION = 'sample_pagination'; // ページネーション.
    case SAMPLE_INTERN = 'sample_intern'; // インターン.
    case SAMPLE_INERTIA_DATA = 'sample_inertia_data'; // Inertiaデータ.
    case SEATING_CHART = 'seating_chart'; // 座席表.
    case QMART = 'qmart'; // Qマート.

    /**
     * routing時のinvokeパスの取得.
     */
    public function getInvokePath(): string
    {
        return match ($this) {
            self::DASHBOARD => '\App\Http\Controllers\Pages\DashboardController@invoke',
            self::ATTENDANCE => '\App\Http\Controllers\Pages\AttendanceController@invoke',
            self::ATTENDANCE_CREATE => '\App\Http\Controllers\Pages\AttendanceController@create',
            self::ATTENDANCE_DELETE => '\App\Http\Controllers\Pages\AttendanceController@delete',
            self::ATTENDANCE_DIVISION_PRIORITY => '\App\Http\Controllers\Pages\AttendanceController@division_priority',
            self::ATTENDANCE_PROJECT_PRIORITY => '\App\Http\Controllers\Pages\AttendanceController@project_priority',
            self::SHUFFLE_LUNCH => '\App\Http\Controllers\Pages\ShuffleLunchController@invoke',
            self::SHUFFLE_LUNCH__REGISTER => '\App\Http\Controllers\Pages\ShuffleLunchController@register',
            self::SHUFFLE_LUNCH__CANCEL => '\App\Http\Controllers\Pages\ShuffleLunchController@cancel',
            self::SHUFFLE_LUNCH__RAKUMO => '\App\Http\Controllers\Pages\ShuffleLunchController@rakumo',
            self::GOOD_JOB => '\App\Http\Controllers\Pages\GoodJobController@invoke',
            self::GOOD_JOB__NEW => '\App\Http\Controllers\Pages\GoodJobController@new',
            self::GOOD_JOB__CREATE => '\App\Http\Controllers\Pages\GoodJobController@create',
            self::SHOP => '\App\Http\Controllers\Pages\ShopController@invoke',
            self::SHOP__LUNCH_TICKET => '\App\Http\Controllers\Pages\ShopController@lunch_ticket',
            self::REWARD => '\App\Http\Controllers\Pages\RewardController@invoke',
            self::REWARD__LIST => '\App\Http\Controllers\Pages\RewardController@list',
            self::REWARD__ACHIEVE => '\App\Http\Controllers\Pages\RewardController@achieve',
            self::ATTENDANCE__EDIT => '\App\Http\Controllers\Pages\AttendanceEditController@invoke',
            self::ATTENDANCE__EDIT__UPDATE => '\App\Http\Controllers\Pages\AttendanceEditController@update',
            self::DIVISION => '\App\Http\Controllers\Pages\DivisionController@invoke',
            self::DIVISION__SHOW => '\App\Http\Controllers\Pages\DivisionController@show',
            self::DIVISION__EDIT => '\App\Http\Controllers\Pages\DivisionController@edit',
            self::DIVISION__NEW => '\App\Http\Controllers\Pages\DivisionController@new',
            self::DIVISION__CREATE => '\App\Http\Controllers\Pages\DivisionController@create',
            self::DIVISION__UPDATE => '\App\Http\Controllers\Pages\DivisionController@update',
            self::DIVISION__DELETE => '\App\Http\Controllers\Pages\DivisionController@delete',
            self::DIVISION__UPDATE_USER => '\App\Http\Controllers\Pages\DivisionController@update_user',
            self::PROJECT => '\App\Http\Controllers\Pages\ProjectController@invoke',
            self::PROJECT__SHOW => '\App\Http\Controllers\Pages\ProjectController@show',
            self::PROJECT__EDIT => '\App\Http\Controllers\Pages\ProjectController@edit',
            self::PROJECT__NEW => '\App\Http\Controllers\Pages\ProjectController@new',
            self::PROJECT__CREATE => '\App\Http\Controllers\Pages\ProjectController@create',
            self::PROJECT__UPDATE => '\App\Http\Controllers\Pages\ProjectController@update',
            self::PROJECT__UPDATE_USER => '\App\Http\Controllers\Pages\ProjectController@update_user',
            self::PROJECT__DELETE => '\App\Http\Controllers\Pages\ProjectController@delete',
            self::USER => '\App\Http\Controllers\Pages\UserController@invoke',
            self::USER__SHOW => '\App\Http\Controllers\Pages\UserController@show',
            self::USER__EDIT => '\App\Http\Controllers\Pages\UserController@edit',
            self::USER__NEW => '\App\Http\Controllers\Pages\UserController@new',
            self::USER__CREATE => '\App\Http\Controllers\Pages\UserController@create',
            self::USER__UPDATE => '\App\Http\Controllers\Pages\UserController@update',
            self::USER__DELETE => '\App\Http\Controllers\Pages\UserController@delete',
            self::PERSONAL_SETTING__SHOW => '\App\Http\Controllers\Pages\PersonalSettingController@show',
            self::PERSONAL_SETTING__EDIT => '\App\Http\Controllers\Pages\PersonalSettingController@edit',
            self::PERSONAL_SETTING__UPDATE => '\App\Http\Controllers\Pages\PersonalSettingController@update',
            self::LOGIN => '\App\Http\Controllers\Pages\LoginController@invoke',
            self::LEGACY_LOGIN => '\App\Http\Controllers\Pages\LoginController@legacy_login',
            self::ONETIME_PASSWORD_LOGIN => '\App\Http\Controllers\Pages\LoginController@onetime_password_login',
            self::SP_LOGIN => '\App\Http\Controllers\Pages\LoginController@sp_login',
            self::NOT_FOUND => '\App\Http\Controllers\Pages\NotFoundController@invoke',

            /**
             * 以下、標準環境と同等のサンプルページ.
             */
            self::SAMPLE_DASHBOARD => '\App\Http\Controllers\Pages\Sample\DashboardController@invoke',
            self::SAMPLE_MVC_VALUE => '\App\Http\Controllers\Pages\Sample\MvcValueController@invoke',
            self::SAMPLE_INPUT_FORM => '\App\Http\Controllers\Pages\Sample\InputFormController@invoke',
            self::SAMPLE_INPUT_FORM__CREATE => '\App\Http\Controllers\Pages\Sample\InputFormController@create',
            self::SAMPLE_REDIS => '\App\Http\Controllers\Pages\Sample\RedisController@invoke',
            self::SAMPLE_REDIS_POST_RESULT => '\App\Http\Controllers\Pages\Sample\RedisPostResultController@invoke',
            self::SAMPLE_SLACK_LOG => '\App\Http\Controllers\Pages\Sample\SlackLogController@invoke',
            self::SAMPLE_PAGINATION => '\App\Http\Controllers\Pages\Sample\PaginationController@invoke',
            self::SAMPLE_INTERN => '\App\Http\Controllers\Pages\Sample\InternController@invoke',
            self::SAMPLE_INERTIA_DATA => '\App\Http\Controllers\Pages\Sample\InertiaDataController@invoke',
            self::SEATING_CHART => '\App\Http\Controllers\Pages\SeatingChartController@invoke',
            self::QMART => '\App\Http\Controllers\Pages\QmartController@invoke',

            default => '',
        };
    }
}
