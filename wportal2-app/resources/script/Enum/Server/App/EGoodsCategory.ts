// #ENUM_DEFINE_START#
/**
 * 商品カテゴリ.
 */
export const E_GOODS_CATEGORY = {
  INVALID: 0, // 指定なし.
  LUNCH_TICKET: 1, // ランチチケット.
} as const;
export type EGoodsCategory =
  (typeof E_GOODS_CATEGORY)[keyof typeof E_GOODS_CATEGORY];
// #ENUM_DEFINE_END#
