// #ENUM_DEFINE_START#
/**
 * 待機中(マッチング登録可能)かどうか.
 */
export const E_CALCULATE = {
  INVALID: 0, // 無効値.
  READY: 1, // 計算未処理.
  CALCULATING: 2, // 計算中.
  ESTABLISHED: 3, // 計算済.
} as const;
export type ECalculate = (typeof E_CALCULATE)[keyof typeof E_CALCULATE];
// #ENUM_DEFINE_END#
