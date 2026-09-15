// #ENUM_DEFINE_START#
/**
 * 許可フラグ.
 */
export const E_ENABLE_FLAG = {
  INVALID: 0, // 指定なし・不許可.
  ENABLE: 1, // 許可.
} as const;
export type EEnableFlag = (typeof E_ENABLE_FLAG)[keyof typeof E_ENABLE_FLAG];
// #ENUM_DEFINE_END#
