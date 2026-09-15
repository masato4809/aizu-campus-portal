// #ENUM_DEFINE_START#
/**
 * 勤務状態
 */
export const E_WORKING_STATE = {
  INVALID: 'invalid', // 無効.
  NONE: 'none', // 出勤前 or 休日.
  WORKING: 'working', // 勤務中.
  REST: 'rest', // 休憩中.
  LEAVING: 'leaving', // 退勤後.
} as const;
export type EWorkingState =
  (typeof E_WORKING_STATE)[keyof typeof E_WORKING_STATE];
// #ENUM_DEFINE_END#

/**
 * 勤務状態のラベルを取得.
 * @param state
 */
export const getLabelWorkingState = (state: EWorkingState) => {
  switch (state) {
    case E_WORKING_STATE.NONE:
      return '不在';
    case E_WORKING_STATE.WORKING:
      return '勤務中';
    case E_WORKING_STATE.REST:
      return '休憩中';
    case E_WORKING_STATE.LEAVING:
      return '退勤後';
    default:
      return '';
  }
};
