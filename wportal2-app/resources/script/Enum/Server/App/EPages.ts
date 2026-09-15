// #ENUM_DEFINE_START#
/**
 * ページ種別.
 * routingと1vs1対応
 */
export const E_PAGES = {
  INVALID: 'invalid', // 無効.
  DASHBOARD: 'dashboard', // ダッシュボード.
  ATTENDANCE: 'attendance', // 勤怠共有.
  ATTENDANCE_CREATE: 'attendance_create', // 勤怠共有・追加.
  ATTENDANCE_DELETE: 'attendance_delete', // 勤怠共有・削除.
  ATTENDANCE_DIVISION_PRIORITY: 'attendance_division_priority', // 課優先度変更.
  ATTENDANCE_PROJECT_PRIORITY: 'attendance_project_priority', // プロジェクト優先度変更.
  ATTENDANCE__EDIT: 'attendance__edit', // 勤怠修正.
  ATTENDANCE__EDIT__UPDATE: 'attendance__edit__update', // 勤怠修正・更新.
  ATTENDANCE__TABLE: 'attendance__table', // 勤怠一覧表.
  SHUFFLE_LUNCH: 'shuffle_lunch', // シャッフルランチ.
  SHUFFLE_LUNCH__REGISTER: 'shuffle_lunch__register', // シャッフルランチ登録.
  SHUFFLE_LUNCH__CANCEL: 'shuffle_lunch__cancel', // シャッフルランチキャンセル.
  SHUFFLE_LUNCH__RAKUMO: 'shuffle_lunch__rakumo', // rakumoブロック.
  GOOD_JOB: 'good_job', // GoodJob.
  GOOD_JOB__NEW: 'good_job__new', // GoodJob新規登録ページ.
  GOOD_JOB__CREATE: 'good_job__create', // GoodJob新規登録.
  SHOP: 'shop', // ショップ.
  SHOP__LUNCH_TICKET: 'shop__lunch_ticket', // ショップ・ランチチケット.
  REWARD: 'reward', // 報酬.
  REWARD__LIST: 'reward__list', // 報酬一覧.
  REWARD__ACHIEVE: 'reward__achieve', // 報酬達成.
  DIVISION: 'division', // 課一覧.
  DIVISION__SHOW: 'division__show', // 課詳細.
  DIVISION__EDIT: 'division__edit', // 課編集.
  DIVISION__NEW: 'division__new', // 課新規作成.
  DIVISION__CREATE: 'division__create', // 課新規登録.
  DIVISION__UPDATE: 'division__update', // 課更新.
  DIVISION__UPDATE_USER: 'division__update_user', // 課メンバー更新.
  DIVISION__DELETE: 'division__delete', // 課削除.
  PROJECT: 'project', // プロジェクト一覧.
  PROJECT__SHOW: 'project__show', // プロジェクト詳細.
  PROJECT__EDIT: 'project__edit', // プロジェクト編集.
  PROJECT__NEW: 'project__new', // プロジェクト新規作成.
  PROJECT__CREATE: 'project__create', // プロジェクト新規登録.
  PROJECT__UPDATE: 'project__update', // プロジェクト更新.
  PROJECT__UPDATE_USER: 'project__update_user', // プロジェクトメンバー更新.
  PROJECT__DELETE: 'project__delete', // プロジェクト削除.
  USER: 'user', // メンバー一覧.
  USER__SHOW: 'user__show', // メンバー詳細.
  USER__EDIT: 'user__edit', // メンバー編集.
  USER__NEW: 'user__new', // メンバー新規作成.
  USER__CREATE: 'user__create', // メンバー新規登録.
  USER__UPDATE: 'user__update', // メンバー更新.
  USER__DELETE: 'user__delete', // メンバー削除.
  PERSONAL_SETTING__SHOW: 'personal_setting__show', // 個人設定・閲覧.
  PERSONAL_SETTING__EDIT: 'personal_setting__edit', // 個人設定・編集.
  PERSONAL_SETTING__UPDATE: 'personal_setting__update', // 個人設定・更新.
  LOGIN: 'login', // ログイン.
  LEGACY_LOGIN: 'legacy_login', // ワンタイムパスワードの発行.
  ONETIME_PASSWORD_LOGIN: 'onetime_password_login', // ワンタイムパスワードログイン.
  SP_LOGIN: 'sp_login', // スマートフォン版ログイン.
  NOT_FOUND: 'not_found', // ページが見つからない.
  SAMPLE_DASHBOARD: 'sample_dashboard', // ダッシュボード.
  SAMPLE_MVC_VALUE: 'sample_mvc_value', // MVC値渡し.
  SAMPLE_INPUT_FORM: 'sample_input_form', // 入力フォーム.
  SAMPLE_INPUT_FORM__CREATE: 'sample_input_form__create', // 入力フォームPost更新.
  SAMPLE_REDIS: 'sample_redis', // Redis操作.
  SAMPLE_REDIS_POST_RESULT: 'sample_redis_post_result', // RedisをPostで更新.
  SAMPLE_SLACK_LOG: 'sample_slack_log', // Slackログ通知.
  SAMPLE_PAGINATION: 'sample_pagination', // ページネーション.
  SAMPLE_INTERN: 'sample_intern', // インターン.
  SAMPLE_INERTIA_DATA: 'sample_inertia_data', // Inertiaデータ.
  SEATING_CHART: 'seating_chart', // 座席表.
  QMART: 'qmart', // Qマート.
} as const;
export type EPages = (typeof E_PAGES)[keyof typeof E_PAGES];
// #ENUM_DEFINE_END#

/**
 * 各ページのURL.
 * @param page
 * @param val1
 */
export interface IPagesHrefReplace {
  target: string;
  value: string;
}

const getHref = (page: EPages): string => {
  switch (page) {
    case E_PAGES.DASHBOARD:
      return '/dashboard';
    case E_PAGES.ATTENDANCE:
      return '/attendance';
    case E_PAGES.ATTENDANCE_CREATE:
      return '/attendance/create';
    case E_PAGES.ATTENDANCE_DELETE:
      return '/attendance/delete';
    case E_PAGES.ATTENDANCE_DIVISION_PRIORITY:
      return '/attendance/division_priority';
    case E_PAGES.ATTENDANCE_PROJECT_PRIORITY:
      return '/attendance/project_priority';
    case E_PAGES.SHUFFLE_LUNCH:
      return '/shuffle_lunch';
    case E_PAGES.SHUFFLE_LUNCH__REGISTER:
      return '/shuffle_lunch/register';
    case E_PAGES.SHUFFLE_LUNCH__CANCEL:
      return '/shuffle_lunch/cancel';
    case E_PAGES.SHUFFLE_LUNCH__RAKUMO:
      return '/shuffle_lunch/rakumo';
    case E_PAGES.GOOD_JOB:
      return '/good_job';
    case E_PAGES.GOOD_JOB__NEW:
      return '/good_job/new';
    case E_PAGES.GOOD_JOB__CREATE:
      return '/good_job/create';
    case E_PAGES.SHOP:
      return '/shop';
    case E_PAGES.SHOP__LUNCH_TICKET:
      return '/shop/lunch_ticket';
    case E_PAGES.REWARD:
      return '/reward';
    case E_PAGES.REWARD__LIST:
      return '/reward/list';
    case E_PAGES.REWARD__ACHIEVE:
      return '/reward/achieve';
    case E_PAGES.ATTENDANCE__EDIT:
      return '/attendance/edit';
    case E_PAGES.ATTENDANCE__EDIT__UPDATE:
      return '/attendance/edit/update';
    case E_PAGES.DIVISION:
      return '/division';
    case E_PAGES.DIVISION__SHOW:
      return `/division/$divisionId/show`;
    case E_PAGES.DIVISION__EDIT:
      return `/division/$divisionId/edit`;
    case E_PAGES.DIVISION__NEW:
      return `/division/new`;
    case E_PAGES.DIVISION__CREATE:
      return `/division/create`;
    case E_PAGES.DIVISION__UPDATE:
      return '/division/update';
    case E_PAGES.DIVISION__UPDATE_USER:
      return '/division/update_user';
    case E_PAGES.DIVISION__DELETE:
      return '/division/delete';
    case E_PAGES.PROJECT:
      return '/project';
    case E_PAGES.PROJECT__SHOW:
      return '/project/$projectId/show';
    case E_PAGES.PROJECT__EDIT:
      return '/project/$projectId/edit';
    case E_PAGES.PROJECT__NEW:
      return '/project/new';
    case E_PAGES.PROJECT__CREATE:
      return '/project/create';
    case E_PAGES.PROJECT__UPDATE:
      return '/project/update';
    case E_PAGES.PROJECT__UPDATE_USER:
      return '/project/update_user';
    case E_PAGES.PROJECT__DELETE:
      return '/project/delete';
    case E_PAGES.USER:
      return '/user';
    case E_PAGES.USER__SHOW:
      return '/user/$userId/show';
    case E_PAGES.USER__EDIT:
      return '/user/$userId/edit';
    case E_PAGES.USER__NEW:
      return '/user/new';
    case E_PAGES.USER__CREATE:
      return '/user/create';
    case E_PAGES.USER__UPDATE:
      return '/user/update';
    case E_PAGES.USER__DELETE:
      return '/user/delete';
    case E_PAGES.PERSONAL_SETTING__SHOW:
      return '/personal_setting/show';
    case E_PAGES.PERSONAL_SETTING__EDIT:
      return '/personal_setting/edit';
    case E_PAGES.PERSONAL_SETTING__UPDATE:
      return '/personal_setting/update';
    case E_PAGES.LEGACY_LOGIN:
      return '/legacy_login';
    case E_PAGES.SP_LOGIN:
      return '/sp_login';
    case E_PAGES.ONETIME_PASSWORD_LOGIN:
      return '/onetime_password_login';

    /**
     * サンプル用.
     */
    case E_PAGES.SAMPLE_DASHBOARD:
      return '/sample/dashboard';
    case E_PAGES.SAMPLE_MVC_VALUE:
      return '/sample/mvc_value';
    case E_PAGES.SAMPLE_INPUT_FORM:
      return '/sample/input_form';
    case E_PAGES.SAMPLE_INPUT_FORM__CREATE:
      return '/sample/input_form/create';
    case E_PAGES.SAMPLE_REDIS:
      return '/sample/redis';
    case E_PAGES.SAMPLE_REDIS_POST_RESULT:
      return '/sample/redis_post_result';
    case E_PAGES.SAMPLE_SLACK_LOG:
      return '/sample/slack_log';
    case E_PAGES.SAMPLE_PAGINATION:
      return '/sample/pagination';
    case E_PAGES.SAMPLE_INTERN:
      return '/sample/intern';
    case E_PAGES.SAMPLE_INERTIA_DATA:
      return '/sample/inertia_data';
    case E_PAGES.SEATING_CHART:
      return '/seating_chart';
    case E_PAGES.QMART:
      return '/qmart';
    default:
      return '/';
  }
};
export const getPagesHref = (
  page: EPages,
  replace: IPagesHrefReplace[] = [],
): string => {
  let href = getHref(page);
  replace.forEach(v => {
    href = href.replace(v.target, v.value);
  });
  return href;
};

/**
 * ページ名取得.
 * @param page
 */
export const getPagesName = (page: EPages): string => {
  switch (page) {
    case E_PAGES.DASHBOARD:
      return 'ダッシュボード';
    case E_PAGES.ATTENDANCE:
      return '勤怠共有';
    case E_PAGES.SHUFFLE_LUNCH:
      return 'シャッフルランチ';
    case E_PAGES.GOOD_JOB:
      return '感謝を記録する';
    case E_PAGES.GOOD_JOB__NEW:
      return '感謝の新規登録';
    case E_PAGES.SHOP:
      return 'ショップ';
    case E_PAGES.REWARD:
      return '獲得報酬';
    case E_PAGES.REWARD__LIST:
      return '報酬一覧';
    case E_PAGES.ATTENDANCE__EDIT:
      return '勤怠修正';
    case E_PAGES.DIVISION:
      return '課一覧';
    case E_PAGES.DIVISION__SHOW:
      return '課詳細';
    case E_PAGES.DIVISION__EDIT:
      return '課編集';
    case E_PAGES.DIVISION__NEW:
      return '課追加';
    case E_PAGES.DIVISION__DELETE:
      return '課削除';
    case E_PAGES.PROJECT:
      return 'プロジェクト一覧';
    case E_PAGES.PROJECT__SHOW:
      return 'プロジェクト詳細';
    case E_PAGES.PROJECT__EDIT:
      return 'プロジェクト編集';
    case E_PAGES.PROJECT__NEW:
      return 'プロジェクト追加';
    case E_PAGES.USER:
      return 'メンバー一覧';
    case E_PAGES.USER__SHOW:
      return 'メンバー詳細';
    case E_PAGES.USER__EDIT:
      return 'メンバー編集';
    case E_PAGES.USER__NEW:
      return 'メンバー追加';
    case E_PAGES.PERSONAL_SETTING__SHOW:
      return '個人設定';
    case E_PAGES.PERSONAL_SETTING__EDIT:
      return '個人設定編集';

    /**
     * サンプル用.
     */
    case E_PAGES.SAMPLE_DASHBOARD:
      return 'ダッシュボード';
    case E_PAGES.SAMPLE_MVC_VALUE:
      return 'MVC値渡し';
    case E_PAGES.SAMPLE_INPUT_FORM:
      return '入力フォーム';
    case E_PAGES.SAMPLE_REDIS:
      return 'Redis/GraphQL';
    case E_PAGES.SAMPLE_REDIS_POST_RESULT:
      return 'Redis - Postによる更新結果';
    case E_PAGES.SAMPLE_SLACK_LOG:
      return 'Slack通知・Log出力';
    case E_PAGES.SAMPLE_PAGINATION:
      return 'ページネーション';
    case E_PAGES.SAMPLE_INTERN:
      return 'コンポーネント';
    case E_PAGES.SAMPLE_INERTIA_DATA:
      return 'データ受信';
    case E_PAGES.SEATING_CHART:
      return '座席表';
    case E_PAGES.QMART:
      return 'Qマート';
    default:
      return '';
  }
};

/**
 * ページの戻り先を取得.
 * @param pages
 */
export const getPagesBack = (pages: EPages): EPages[] => {
  switch (pages) {
    case E_PAGES.GOOD_JOB__NEW:
      return [E_PAGES.GOOD_JOB];
    case E_PAGES.REWARD__LIST:
      return [E_PAGES.REWARD];
    case E_PAGES.DIVISION__SHOW:
      return [E_PAGES.DIVISION];
    case E_PAGES.DIVISION__EDIT:
      return [E_PAGES.DIVISION, E_PAGES.DIVISION__SHOW];
    case E_PAGES.DIVISION__NEW:
      return [E_PAGES.DIVISION];
    case E_PAGES.PROJECT__SHOW:
      return [E_PAGES.PROJECT];
    case E_PAGES.PROJECT__EDIT:
      return [E_PAGES.PROJECT, E_PAGES.PROJECT__SHOW];
    case E_PAGES.PROJECT__NEW:
      return [E_PAGES.PROJECT];
    case E_PAGES.USER__SHOW:
      return [E_PAGES.USER];
    case E_PAGES.USER__EDIT:
      return [E_PAGES.USER, E_PAGES.USER__SHOW];
    case E_PAGES.USER__NEW:
      return [E_PAGES.USER];
    case E_PAGES.PERSONAL_SETTING__EDIT:
      return [E_PAGES.PERSONAL_SETTING__SHOW];
    default:
      return [E_PAGES.DASHBOARD];
  }
};
