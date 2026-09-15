/**
 * ログ出力
 */
export class Log {
  /**
   * info出力.
   * @param log
   */
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public static info(log: any): void {
    console.log(log);
  }

  /**
   * error出力.
   * @param log
   */
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public static error(log: any): void {
    console.error(log);
  }
}
