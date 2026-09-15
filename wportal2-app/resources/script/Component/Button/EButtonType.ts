import { E_COLOR, EColor } from '@/script/Enum/EColor';

/**
 * ボタン種別.
 */
export const E_BUTTON_TYPE = {
  INVALID: 0,
  CONTAINED_PRIMARY: 1,
  CONTAINED_SECONDARY: 2,
  OUTLINE_PRIMARY: 3,
} as const;
export type EButtonType = (typeof E_BUTTON_TYPE)[keyof typeof E_BUTTON_TYPE];

/**
 * 通常カラーの取得.
 * @param type
 */
export const getButtonColorNormal = (type: EButtonType): EColor => {
  switch (type) {
    case E_BUTTON_TYPE.INVALID:
      return E_COLOR.WHITE;
    case E_BUTTON_TYPE.CONTAINED_PRIMARY:
      return E_COLOR.PRIMARY_MAIN;
    case E_BUTTON_TYPE.CONTAINED_SECONDARY:
      return E_COLOR.SECONDARY_MAIN;
    case E_BUTTON_TYPE.OUTLINE_PRIMARY:
      return E_COLOR.WHITE;
    default:
      throw new Error();
  }
};

/**
 * Hoverカラーの取得.
 * @param type
 */
export const getButtonColorHover = (type: EButtonType): EColor => {
  switch (type) {
    case E_BUTTON_TYPE.INVALID:
      return E_COLOR.WHITE;
    case E_BUTTON_TYPE.CONTAINED_PRIMARY:
      return E_COLOR.PRIMARY_DARK;
    case E_BUTTON_TYPE.CONTAINED_SECONDARY:
      return E_COLOR.SECONDARY_DARK;
    case E_BUTTON_TYPE.OUTLINE_PRIMARY:
      return E_COLOR.GREY_LIGHT;
    default:
      throw new Error();
  }
};

/**
 * 非活性カラーの取得.
 * @param type
 */
export const getButtonColorDisabled = (type: EButtonType): EColor => {
  switch (type) {
    case E_BUTTON_TYPE.INVALID:
    case E_BUTTON_TYPE.CONTAINED_PRIMARY:
    case E_BUTTON_TYPE.OUTLINE_PRIMARY:
    case E_BUTTON_TYPE.CONTAINED_SECONDARY:
      return E_COLOR.GREY;
    default:
      throw new Error();
  }
};

/**
 * ラベルカラーの取得.
 * @param type
 */
export const getButtonColorText = (type: EButtonType): EColor => {
  switch (type) {
    case E_BUTTON_TYPE.INVALID:
      return E_COLOR.TEXT_PRIMARY;
    case E_BUTTON_TYPE.CONTAINED_PRIMARY:
      return E_COLOR.WHITE;
    case E_BUTTON_TYPE.CONTAINED_SECONDARY:
      return E_COLOR.TEXT_PRIMARY;
    case E_BUTTON_TYPE.OUTLINE_PRIMARY:
      return E_COLOR.PRIMARY_MAIN;
    default:
      throw new Error();
  }
};

export const getButtonVariant = (
  type: EButtonType,
): 'text' | 'outlined' | 'contained' | undefined => {
  switch (type) {
    case E_BUTTON_TYPE.INVALID:
      return 'text';
    case E_BUTTON_TYPE.CONTAINED_PRIMARY:
    case E_BUTTON_TYPE.CONTAINED_SECONDARY:
      return 'contained';
    case E_BUTTON_TYPE.OUTLINE_PRIMARY:
      return 'outlined';
    default:
      throw new Error();
  }
};
