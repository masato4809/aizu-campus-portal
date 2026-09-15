export interface IBase {}
export abstract class ListBase<I extends IBase> {
  // 不要なデータコピーが行われないようにするためのローカルキャッシュ.
  private static cache: ListBase<IBase> | null = null;

  // データ本体.
  protected array: I[] = [];

  // 主キー:配列内インデックスのハッシュマップ.
  protected hash: { [key: number | string]: number } = {};

  protected constructor(data: I[] = []) {
    this.array = data;

    // ハッシュマップも初期化.
    data.forEach((v, index) => {
      this.hash[this.getPrimary(v)] = index;
    });
  }

  abstract default(): I;

  abstract getPrimary(value: IBase): number | string;

  /**
   * 全件取得(shallow).
   */
  public list(): I[] {
    return this.array;
  }

  /**
   * 全件取得：逆順.
   * 保持している配列を変更しない.
   */
  public reverse(): I[] {
    return this.array.toReversed();
  }

  /**
   * 全件取得(deep)
   */
  public slice(): I[] {
    return this.array.slice();
  }

  /**
   * 最初の要素を取得.
   */
  public first(): I {
    if (this.array.length === 0) {
      return this.default();
    }
    return this.array[0];
  }

  /**
   * IDによる検索.
   * @param primaryValue
   */
  public findByPrimary(primaryValue?: number | string): I {
    if (!primaryValue) {
      return this.default();
    }
    // ハッシュマップからインデックスを参照し、直接取得する.
    return this.array[this.hash[primaryValue]] ?? this.default();
  }

  /**
   * データが存在するかどうか
   */
  public isExistByPrimary(primaryValue: number | string): boolean {
    // ハッシュマップからインデックスを参照し、直接取得する.
    return !!this.array[this.hash[primaryValue]];
  }

  /**
   * 件数.
   */
  public count(): number {
    return this.array.length;
  }

  /**
   * キャッシュデータがあるかどうか.
   */
  public static isExistCache(): boolean {
    return this.cache !== null;
  }

  /**
   * キャッシュデータの保存.
   */
  public static setCache(data: ListBase<IBase>) {
    this.cache = data;
  }

  /**
   * キャッシュデータの取得.
   */
  public static getCache<I>() {
    return this.cache as I;
  }
}
