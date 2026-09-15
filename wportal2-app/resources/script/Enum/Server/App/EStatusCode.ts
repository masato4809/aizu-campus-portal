// #ENUM_DEFINE_START#
/**
 * APIステータスコード.
 * vendor/symfony/http-foundation/Response.phpを利用すればよいが
 * その場合はフロントとステータス定義を同期しづらいため.
 */
export const E_STATUS_CODE = {
  INVALID: 0, // 無効.
  OK: 200, // リクエストが正常に処理できた
  BAD_REQUEST: 400, // サーバーがそのリクエストを処理しない (できない).
  UNAUTHORIZED: 401, // アクセス権が無い、または認証に失敗.
  NOT_FOUND: 404, // Webページが見つからない.
  GONE: 410, // 利用不可のリソースへのアクセス.
  UNPROCESSABLE_ENTITY: 422, // リクエストは適正だが意味が異なるためサーバが返すことが出来ない
} as const;
export type EStatusCode = (typeof E_STATUS_CODE)[keyof typeof E_STATUS_CODE];
// #ENUM_DEFINE_END#
