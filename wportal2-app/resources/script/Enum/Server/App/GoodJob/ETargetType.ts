// #ENUM_DEFINE_START#
export const E_TARGET_TYPE = {
  INVALID: 0, // 無効値.
  USER: 1, // ユーザー.
  DIVISION: 2, // 部署.
  PROJECT: 3, // プロジェクト.
  LABEL: 4, // ラベル直指定.
} as const;
export type ETargetType = (typeof E_TARGET_TYPE)[keyof typeof E_TARGET_TYPE];
// #ENUM_DEFINE_END#

/**
 * 種別名称取得.
 */
export const getTargetTypeName = (targetType: ETargetType): string => {
  switch (targetType) {
    case E_TARGET_TYPE.USER:
      return 'ユーザー';
    case E_TARGET_TYPE.DIVISION:
      return '部署';
    case E_TARGET_TYPE.PROJECT:
      return 'プロジェクト';
    case E_TARGET_TYPE.LABEL:
      return 'ラベル';
    default:
      return '';
  }
};
