/**
 * 文字列処理.
 */
export class Str {
  /**
   * 空文字をundefinedとする.
   * @param val
   */
  public static exceptEmpty(val: string): string | undefined {
    if (!val || !val.length) {
      return undefined;
    }
    return val;
  }
}
