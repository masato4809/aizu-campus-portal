// #ENUM_DEFINE_START#
/**
 * 通知タイプ.
 *
 * @note 値の変更禁止.
 */
export const E_NOTIFICATION_TYPE = {
  INVALID: 0, // 無効.
  SLACK_CHANNEL: 1, // Slack通知.
} as const;
export type ENotificationType =
  (typeof E_NOTIFICATION_TYPE)[keyof typeof E_NOTIFICATION_TYPE];
// #ENUM_DEFINE_END#
