/**
 * 環境変数.
 */
export class Env {
  /**
   * ローカルバリデーションが有効かどうか.
   */
  public static idEnableLocalValidation(): boolean {
    return !import.meta.env.VITE_DISABLE_LOCAL_VALIDATION;
  }
}
