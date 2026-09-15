import { themeCommon } from '@/script/System/Theme';

/**
 * フォントサイズ・レスポンシブ調整:h1~h3
 */
themeCommon.typography.h1 = {
  ...themeCommon.typography.h1,
  fontSize: '28px',
  [themeCommon.breakpoints.down('md')]: {
    fontSize: '14px',
  },
};
themeCommon.typography.h2 = {
  ...themeCommon.typography.h2,
  fontSize: '20px',
  [themeCommon.breakpoints.down('md')]: {
    fontSize: '10px',
  },
};
themeCommon.typography.h3 = {
  ...themeCommon.typography.h3,
  fontSize: '18px',
  [themeCommon.breakpoints.down('md')]: {
    fontSize: '9px',
  },
};

/**
 * フォントサイズ・レスポンシブ調整:subtitle1~2
 */
themeCommon.typography.subtitle1 = {
  ...themeCommon.typography.subtitle1,
  fontSize: '18px',
  [themeCommon.breakpoints.down('md')]: {
    fontSize: '9px',
  },
};
themeCommon.typography.subtitle2 = {
  ...themeCommon.typography.subtitle2,
  fontSize: '16px',
  [themeCommon.breakpoints.down('md')]: {
    fontSize: '8px',
  },
};

/**
 * フォントサイズ・レスポンシブ調整:body1~2
 */
themeCommon.typography.body1 = {
  ...themeCommon.typography.body1,
  fontSize: '16px',
  [themeCommon.breakpoints.down('md')]: {
    fontSize: '10px',
  },
};
themeCommon.typography.body2 = {
  ...themeCommon.typography.body1,
  fontSize: '14px',
  [themeCommon.breakpoints.down('md')]: {
    fontSize: '8px',
  },
};

/**
 * フォントサイズ・レスポンシブ調整:button
 */
themeCommon.typography.button = {
  ...themeCommon.typography.button,
  fontSize: '16px',
  [themeCommon.breakpoints.down('md')]: {
    fontSize: '8px',
  },
};

/**
 * レスポンシブ用spacing設定.
 * @param unit
 */
export const responsiveSpacing = (unit: number) => {
  return {
    xs: unit / 2,
    sm: unit / 2,
    md: unit,
  };
};

/**
 * レスポンシブ用size設定(spacing単位).
 * @param unit
 */
export const responsiveSizeBySpacing = (unit: number) => {
  return {
    xs: themeCommon.spacing(unit / 2),
    sm: themeCommon.spacing(unit / 2),
    md: themeCommon.spacing(unit),
  };
};

/**
 * レスポンシブ用size設定(spacing単位).
 * @param size
 */
export const responsiveSize = (size: number) => {
  return {
    xs: `${size / 2}px`,
    sm: `${size / 2}px`,
    md: `${size}px`,
  };
};
