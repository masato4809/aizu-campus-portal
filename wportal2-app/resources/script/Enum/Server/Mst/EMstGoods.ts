// #ENUM_DEFINE_START#
/**
 * 商品の種別.
 */
export const E_MST_GOODS = {
  INVALID: 0, // 指定なし.
  LUNCH_TICKET_TOBARU_LOW: 1, // ランチチケット・桃原LOW.
  LUNCH_TICKET_YAMADA_LOW: 2, // ランチチケット・山田LOW.
} as const;
export type EMstGoods = (typeof E_MST_GOODS)[keyof typeof E_MST_GOODS];
// #ENUM_DEFINE_END#
