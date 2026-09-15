// #ENUM_DEFINE_START#
export const E_REGISTER = {
  INVALID: 0, // 無効値.
  REGISTERED: 1, // 登録済み.
  UNREGISTERED: 2, // 未登録.
} as const;
export type ERegister = (typeof E_REGISTER)[keyof typeof E_REGISTER];
// #ENUM_DEFINE_END#
