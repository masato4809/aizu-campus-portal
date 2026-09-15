// #ENUM_DEFINE_START#
/**
 * ユーザー権限
 *
 * @note 値の変更禁止.
 */
export const E_USER_AUTHORITY = {
  INVALID: 0, // 無効.
  ROOT_PRIVILEGE: 1, // 完全特権.
  ADMIN_PRIVILEGE: 100, // 管理コマンド特権.
  ADMIN_COMMAND: 101, // 管理コマンド実行権限.
} as const;
export type EUserAuthority =
  (typeof E_USER_AUTHORITY)[keyof typeof E_USER_AUTHORITY];
// #ENUM_DEFINE_END#
