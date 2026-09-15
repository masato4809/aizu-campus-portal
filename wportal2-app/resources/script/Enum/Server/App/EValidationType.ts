// #ENUM_DEFINE_START#
/**
 * バリデーション種別.
 * Class EValidationType
 */
export const E_VALIDATION_TYPE = {
  INVALID: 'invalid', // 未設定.
  REQUIRED: 'required', // 空白を許可しない.
  REQUIRED_NOT_ZERO: 'required_not_zero', // 空白・0を許可しない.
  NUMBER: 'number', // 数値のみ.
  NUMBER_HYPHEN: 'number_hyphen', // 数値とハイフンのみ.
  NUMBER_PLUS: 'number_plus', // 0以上の数値のみ.
  NUMBER_MAX: 'max', // 数値の最大値.
  NUMBER_RANGE: 'number_range', // min〜maxの範囲内のみ.
  MAX_DIGIT: 'max_digit', // max桁以下の整数のみ.
  LENGTH: 'length', // 文字数がmax以下.
  PHONE: 'phone', // 電話番号.
  EMAIL: 'email', // Eメール.
  UNIQUE_AUTH_EMAIL: 'unique_auth_email', // 認証メールユニーク確認.
  SIMPLE_PASSWORD: 'simple_password', // 英数字8文字以上16文字以内.
  DATE: 'date', // 日付形式.
} as const;
export type EValidationType =
  (typeof E_VALIDATION_TYPE)[keyof typeof E_VALIDATION_TYPE];
// #ENUM_DEFINE_END#
