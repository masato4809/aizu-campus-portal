// #ENUM_DEFINE_START#
/**
 * 報酬の種別.
 */
export const E_MST_REWARD = {
  INVALID: 0, // 指定なし.
  CREATE_ACCOUNT: 1, // 華麗なる登場.
  UPDATE_PROFILE: 2, // 前略プロフィール.
  CREATE_PERFECT_ATTENDANCE: 3, // 紳士の勤怠.
  GOOD_JOB: 4, // ささやかながらの花束を.
} as const;
export type EMstReward = (typeof E_MST_REWARD)[keyof typeof E_MST_REWARD];
// #ENUM_DEFINE_END#
