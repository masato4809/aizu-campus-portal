import {
  E_WORKING_STATE,
  EWorkingState,
} from '@/script/Enum/Server/App/EWorkingState';
import {
  E_WORKING_PLACE,
  EWorkingPlace,
} from '@/script/Enum/Server/App/EWorkingPlace';

// #ENUM_DEFINE_START#
/**
 * 出退勤ステータス
 *
 * @note 値の変更禁止.
 */
export const E_ATTENDANCE_STATE = {
  INVALID: 0, // 無効
  ATTENDANCE_OFFICE: 10001, // 出勤:オフィス
  ATTENDANCE_TELEWORK: 10002, // 出勤:テレワーク
  BEGIN_BREAK: 20001, // 休憩:開始
  END_BREAK: 20002, // 休憩:終了
  SWITCH_TO_OFFICE: 30001, // 切替:オフィスへ
  SWITCH_TO_TELEWORK: 30002, // 切替:テレワークへ
  LEAVING: 40001, // 退勤
} as const;
export type EAttendanceState =
  (typeof E_ATTENDANCE_STATE)[keyof typeof E_ATTENDANCE_STATE];
// #ENUM_DEFINE_END#

/**
 * 出退勤アクションのラベルを取得.
 */
export const getLabelAttendanceState = (
  attendance: EAttendanceState,
): string => {
  switch (attendance) {
    case E_ATTENDANCE_STATE.INVALID:
      return '';
    case E_ATTENDANCE_STATE.ATTENDANCE_OFFICE:
      return 'オフィスへ出勤';
    case E_ATTENDANCE_STATE.ATTENDANCE_TELEWORK:
      return 'テレワーク出勤';
    case E_ATTENDANCE_STATE.BEGIN_BREAK:
      return '休憩開始';
    case E_ATTENDANCE_STATE.END_BREAK:
      return '勤務再開';
    case E_ATTENDANCE_STATE.SWITCH_TO_OFFICE:
      return 'オフィス勤務へ切替';
    case E_ATTENDANCE_STATE.SWITCH_TO_TELEWORK:
      return '在宅勤務へ切替';
    case E_ATTENDANCE_STATE.LEAVING:
      return '退勤';
    default:
      return '';
  }
};

/**
 * 出退勤アクションのショートラベルを取得.
 */
export const getShortLabelAttendanceState = (
  attendance: EAttendanceState,
): string => {
  switch (attendance) {
    case E_ATTENDANCE_STATE.INVALID:
      return '';
    case E_ATTENDANCE_STATE.ATTENDANCE_OFFICE:
    case E_ATTENDANCE_STATE.ATTENDANCE_TELEWORK:
      return '出勤';
    case E_ATTENDANCE_STATE.BEGIN_BREAK:
      return '休憩in';
    case E_ATTENDANCE_STATE.END_BREAK:
      return '休憩out';
    case E_ATTENDANCE_STATE.SWITCH_TO_OFFICE:
    case E_ATTENDANCE_STATE.SWITCH_TO_TELEWORK:
      return '切替';
    case E_ATTENDANCE_STATE.LEAVING:
      return '退勤';
    default:
      return '';
  }
};

/**
 * アクションを勤務状態に変換.
 * @param attendance
 */
export const attendanceToWorking = (
  attendance: EAttendanceState,
): EWorkingState => {
  switch (attendance) {
    case E_ATTENDANCE_STATE.ATTENDANCE_OFFICE:
    case E_ATTENDANCE_STATE.ATTENDANCE_TELEWORK:
    case E_ATTENDANCE_STATE.END_BREAK:
      return E_WORKING_STATE.WORKING;
    case E_ATTENDANCE_STATE.BEGIN_BREAK:
      return E_WORKING_STATE.REST;
    case E_ATTENDANCE_STATE.LEAVING:
      return E_WORKING_STATE.LEAVING;
    case E_ATTENDANCE_STATE.SWITCH_TO_OFFICE:
    case E_ATTENDANCE_STATE.SWITCH_TO_TELEWORK:
    case E_ATTENDANCE_STATE.INVALID:
      return E_WORKING_STATE.NONE;
    default:
      return E_WORKING_STATE.NONE;
  }
};

/**
 * アクションを勤務場所に変換.
 * @param attendance
 */
export const attendanceToPlace = (
  attendance: EAttendanceState,
): EWorkingPlace => {
  switch (attendance) {
    case E_ATTENDANCE_STATE.ATTENDANCE_OFFICE:
    case E_ATTENDANCE_STATE.SWITCH_TO_OFFICE:
      return E_WORKING_PLACE.OFFICE;
    case E_ATTENDANCE_STATE.ATTENDANCE_TELEWORK:
    case E_ATTENDANCE_STATE.SWITCH_TO_TELEWORK:
      return E_WORKING_PLACE.HOME;
    default:
      return E_WORKING_PLACE.INVALID;
  }
};
