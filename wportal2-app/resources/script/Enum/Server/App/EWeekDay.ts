// #ENUM_DEFINE_START#
/**
 * 曜日の定義.
 */
export const E_WEEK_DAY = {
  SUNDAY: 0, // 日曜日.
  MONDAY: 1, // 月曜日.
  TUESDAY: 2, // 火曜日.
  WEDNESDAY: 3, // 水曜日.
  THURSDAY: 4, // 木曜日.
  FRIDAY: 5, // 金曜日.
  SATURDAY: 6, // 土曜日.
} as const;
export type EWeekDay = (typeof E_WEEK_DAY)[keyof typeof E_WEEK_DAY];
// #ENUM_DEFINE_END#

/**
 * 曜日に対応するラベルを取得.
 */
export const getWeekDayShortLabel = (weekDay: EWeekDay): string => {
  switch (weekDay) {
    case E_WEEK_DAY.SUNDAY:
      return '日';
    case E_WEEK_DAY.MONDAY:
      return '月';
    case E_WEEK_DAY.TUESDAY:
      return '火';
    case E_WEEK_DAY.WEDNESDAY:
      return '水';
    case E_WEEK_DAY.THURSDAY:
      return '木';
    case E_WEEK_DAY.FRIDAY:
      return '金';
    case E_WEEK_DAY.SATURDAY:
      return '土';
    default:
      return '';
  }
};
