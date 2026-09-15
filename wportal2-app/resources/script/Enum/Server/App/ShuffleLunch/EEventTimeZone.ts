// #ENUM_DEFINE_START#
/**
 * 開始時間を取得.
 *
 * @throws Exception
 */
export const E_EVENT_TIME_ZONE = {
  INVALID: 0, // 無効値.
  EARLY: 1, // 11:30-12:30.
  NORMAL: 2, // 12:00-13:00.
} as const;
export type EEventTimeZone =
  (typeof E_EVENT_TIME_ZONE)[keyof typeof E_EVENT_TIME_ZONE];
// #ENUM_DEFINE_END#

/**
 * イベント時間範囲の名称を取得.
 * @param zone
 */
export const getEventTimeZoneName = (zone: EEventTimeZone): string => {
  switch (zone) {
    case E_EVENT_TIME_ZONE.EARLY:
      return '11:30 - 12:30';
    case E_EVENT_TIME_ZONE.NORMAL:
      return '12:00 - 13:00';
    default:
      return '';
  }
};
