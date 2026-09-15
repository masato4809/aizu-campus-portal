// #ENUM_DEFINE_START#
export const E_ARCHIVE_LEVEL = {
  INVALID: 0, // 無効値.
  ALIVE: 1, // 生存.
  ARCHIVE: 2, // アーカイブ.
  DELETE: 3, // 削除設定.
} as const;
export type EArchiveLevel =
  (typeof E_ARCHIVE_LEVEL)[keyof typeof E_ARCHIVE_LEVEL];
// #ENUM_DEFINE_END#
