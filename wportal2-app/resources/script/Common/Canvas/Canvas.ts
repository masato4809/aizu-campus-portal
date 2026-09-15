import { Vector2D } from '@/script/Common/Canvas/Math/Vector2D';
import {
  CanvasParameter,
  IParameter,
} from '@/script/Common/Canvas/CanvasParameter';

export class Canvas {
  private canvas: HTMLCanvasElement | null = null;
  private context: CanvasRenderingContext2D | null = null;
  private size: Vector2D = Vector2D.ZERO;
  private canvasParameter: CanvasParameter;
  private initialized = false;

  /**
   * コンストラクタ.
   */
  public constructor() {
    this.canvasParameter = new CanvasParameter();
  }

  /**
   * キャンバスの初期化を実施.
   */
  public init(
    canvas: HTMLCanvasElement | null,
    width: number,
    height: number,
  ): Canvas {
    if (this.initialized || !canvas) {
      return this;
    }

    // エレメントを保持.
    this.canvas = canvas;

    // コンテキストを保持.
    this.context = canvas.getContext('2d');

    // サイズを保持.
    this.size = new Vector2D(width, height);

    // 初期パラメータ―を設定.
    this.canvasParameter = new CanvasParameter();

    this.initialized = true;

    return this;
  }

  /**
   * キャンバスサイズを変更した新規インスタンスを得る.
   */
  public resizeInstance(width: number, height: number): Canvas {
    return this.clone().setSize(width, height);
  }

  /**
   * 新規インスタンスを獲得.
   */
  private clone(): Canvas {
    return new Canvas().init(this.canvas, this.size.x, this.size.y);
  }

  /**
   * コンテキストの取得.
   */
  public getContext(): CanvasRenderingContext2D {
    if (!this.context) {
      throw new Error("CanvasRenderingContext2D is not initialized. Ensure the canvas is properly initialized before accessing the context.");
    }
    return this.context;
  }

  /**
   * サイズの取得.
   */
  public getSize(): Vector2D {
    return this.size ?? Vector2D.ZERO;
  }

  /**
   * キャンバスサイズを変更.
   */
  public setSize(width: number, height: number): Canvas {
    this.size.x = width;
    this.size.y = height;
    return this;
  }

  /**
   * パラメータの取得.
   */
  public getCanvasParameter(): CanvasParameter {
    return this.canvasParameter;
  }

  /**
   * パラメータの直接取得.
   */
  public getCanvasParameterValue(): IParameter {
    return this.canvasParameter.getValue();
  }

  /**
   * パラメータの設定.
   */
  public setCanvasParameterValue(value: Partial<IParameter>): Canvas {
    const ret = this.clone();
    ret.getCanvasParameter().setValue({
      ...this.canvasParameter.getValue(),
      ...value,
    });
    return ret;
  }
}
