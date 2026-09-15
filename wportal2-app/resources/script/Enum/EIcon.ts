/**
 * アイコン定義.
 */
export const E_ICON = {
  INVALID: 'invalid',

  ADD: 'add',
  ADMIN_PANEL: 'admin_panel',
  ARROW_BACK: 'arrow_back',
  ARROW_LEFT: 'arrow_left',
  ARROW_RIGHT: 'arrow_right',
  ATTENDANCE: 'attendance',
  BUILD: 'build',
  CACHED: 'cached',
  CAFE: 'cafe',
  CIRCLE: 'circle',
  CLEAR: 'clear',
  DASHBOARD: 'dashboard',
  DESCRIPTION: 'description',
  DIVERSITY3: 'diversity3',
  EXPAND_LESS: 'expand_less',
  EXPAND_MORE: 'expand_more',
  FAVICON: 'favicon.png',
  FAVORITE: 'favorite',
  GOOGLE: 'google_icon.svg',
  GROUPS: 'groups',
  HELP: 'help',
  HOME: 'home',
  LIBRARY_BOOKS: 'library_books',
  LOGOUT: 'logout',
  LUNCH: 'lunch',
  MANAGE_HISTORY: 'manage_history',
  MENU: 'menu',
  MILITARY_TECH: 'military_tech',
  OFFICE: 'office',
  PAID: 'paid',
  PERSON: 'person',
  REMOVE: 'remove',
  SETTING: 'setting',
  SHOP: 'shop',
  STAR: 'star',
  STAR_OUTLINE: 'star_outline',
  CALENDAR_MONTH: 'calendar_month',
} as const;
export type EIcon = (typeof E_ICON)[keyof typeof E_ICON];

/**
 * アイコンファイルのパス取得.
 * @param icon
 */
export const getIconPath = (icon: EIcon): string => {
  return `/image/icon/${icon}`;
};

export const getIconSize = (icon: EIcon): { w: number; h: number } => {
  switch (icon) {
    case E_ICON.ADD:
    case E_ICON.ADMIN_PANEL:
    case E_ICON.ARROW_BACK:
    case E_ICON.ATTENDANCE:
    case E_ICON.BUILD:
    case E_ICON.CAFE:
    case E_ICON.CIRCLE:
    case E_ICON.CLEAR:
    case E_ICON.DASHBOARD:
    case E_ICON.DESCRIPTION:
    case E_ICON.DIVERSITY3:
    case E_ICON.EXPAND_LESS:
    case E_ICON.EXPAND_MORE:
    case E_ICON.FAVORITE:
    case E_ICON.GROUPS:
    case E_ICON.HELP:
    case E_ICON.HOME:
    case E_ICON.LIBRARY_BOOKS:
    case E_ICON.LOGOUT:
    case E_ICON.LUNCH:
    case E_ICON.MANAGE_HISTORY:
    case E_ICON.MENU:
    case E_ICON.MILITARY_TECH:
    case E_ICON.OFFICE:
    case E_ICON.PAID:
    case E_ICON.PERSON:
    case E_ICON.REMOVE:
    case E_ICON.SETTING:
    case E_ICON.SHOP:
    case E_ICON.STAR:
    case E_ICON.STAR_OUTLINE:
    case E_ICON.CALENDAR_MONTH:
      return { w: 20, h: 20 };

    case E_ICON.ARROW_LEFT:
    case E_ICON.ARROW_RIGHT:
    case E_ICON.CACHED:
      return { w: 32, h: 32 };

    case E_ICON.FAVICON:
      return { w: 64, h: 64 };

    case E_ICON.GOOGLE:
      return { w: 18, h: 18 };

    default:
      return { w: 0, h: 0 };
  }
};
