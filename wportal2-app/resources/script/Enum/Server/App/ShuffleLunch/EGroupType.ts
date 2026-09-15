// #ENUM_DEFINE_START#
export const E_GROUP_TYPE = {
  INVALID: 0, // 無効値.
  THREE: 3, // 3人グループ.
  FOUR: 4, // 4人グループ.
  FIVE: 5, // 5人グループ.
} as const;
export type EGroupType = (typeof E_GROUP_TYPE)[keyof typeof E_GROUP_TYPE];
// #ENUM_DEFINE_END#
