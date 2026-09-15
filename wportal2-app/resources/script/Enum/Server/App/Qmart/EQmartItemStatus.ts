// #ENUM_DEFINE_START#
/**
 * Qマート出品ステータス.
 */
export const E_QMART_ITEM_STATUS = {
  SELLING: 1, // 出品中.
  NEGOTIATING: 2, // 交渉中.
  SOLD: 3, // 売却済.
  ARCHIVED: 4, // アーカイブ.
} as const;
export type EQmartItemStatus =
  (typeof E_QMART_ITEM_STATUS)[keyof typeof E_QMART_ITEM_STATUS];
// #ENUM_DEFINE_END#

/**
 * ステータス名称取得.
 */
export const getQmartItemStatusName = (status: EQmartItemStatus): string => {
  switch (status) {
    case E_QMART_ITEM_STATUS.SELLING:
      return '出品中';
    case E_QMART_ITEM_STATUS.NEGOTIATING:
      return '交渉中';
    case E_QMART_ITEM_STATUS.SOLD:
      return '売却済';
    case E_QMART_ITEM_STATUS.ARCHIVED:
      return 'アーカイブ';
    default:
      return '';
  }
};
