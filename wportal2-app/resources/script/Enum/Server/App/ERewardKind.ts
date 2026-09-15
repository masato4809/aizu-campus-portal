// #ENUM_DEFINE_START#
/**
 * 報酬種別.
 */
export const E_REWARD_KIND = {
  INVALID: 0, // 指定なし.
  DAILY: 1, // デイリー報酬.
  ACHIEVE: 2, // 達成報酬.
} as const;
export type ERewardKind = (typeof E_REWARD_KIND)[keyof typeof E_REWARD_KIND];
// #ENUM_DEFINE_END#
