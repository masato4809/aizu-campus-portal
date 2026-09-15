import { E_ICON, EIcon } from '@/script/Enum/EIcon';

// #ENUM_DEFINE_START#
/**
 * 勤務場所
 */
export const E_WORKING_PLACE = {
  INVALID: 'invalid', // 無効.
  OFFICE: 'office', // オフィス.
  HOME: 'home', // 在宅.
} as const;
export type EWorkingPlace =
  (typeof E_WORKING_PLACE)[keyof typeof E_WORKING_PLACE];
// #ENUM_DEFINE_END#

/**
 * 勤務場所のラベルを取得.
 * @param place
 */
export const getLabelWorkingPlace = (place: EWorkingPlace): string => {
  switch (place) {
    case E_WORKING_PLACE.OFFICE:
      return 'オフィス';
    case E_WORKING_PLACE.HOME:
      return '在宅';
    default:
      return '';
  }
};

/**
 * 勤務場所のアイコンを取得.
 * @param place
 */
export const getIconWorkingPlace = (place: EWorkingPlace): EIcon => {
  switch (place) {
    case E_WORKING_PLACE.OFFICE:
      return E_ICON.OFFICE;
    case E_WORKING_PLACE.HOME:
      return E_ICON.HOME;
    default:
      return E_ICON.CIRCLE;
  }
};
