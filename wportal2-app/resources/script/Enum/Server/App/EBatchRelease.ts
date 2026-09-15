// #ENUM_DEFINE_START#
/**
 * バッチ種別
 */
export const E_BATCH_RELEASE = {
  MST_INIT_REWARD: 'mst_init_reward', // 報酬初期化.
  MST_INIT_GOODS: 'mst_init_goods', // ショップ商品初期化.
} as const;
export type EBatchRelease =
  (typeof E_BATCH_RELEASE)[keyof typeof E_BATCH_RELEASE];
// #ENUM_DEFINE_END#
