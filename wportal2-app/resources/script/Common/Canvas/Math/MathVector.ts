import { Canvas } from '@/script/Common/Canvas/Canvas';
import { Vector2D } from '@/script/Common/Canvas/Math/Vector2D';

export class MathVector {
  /**
   * オブジェクトのX座標をキャンバスX座標に変換する.
   */
  public static object2CanvasX(canvas: Canvas, x: number): number {
    const offset = canvas.getCanvasParameterValue().offset;
    const scale = canvas.getCanvasParameterValue().scale;

    return offset.x * scale + x * scale + canvas.getContext().canvas.width / 2;
  }

  /**
   * オブジェクトのY座標をキャンバスY座標に変換する.
   */
  public static object2CanvasY(canvas: Canvas, y: number): number {
    const offset = canvas.getCanvasParameterValue().offset;
    const scale = canvas.getCanvasParameterValue().scale;

    return (
      offset.y * scale + -y * scale + canvas.getContext().canvas.height / 2
    );
  }

  /**
   * オブジェクトの座標をキャンバス座標に変換する.
   */
  public static object2Canvas(canvas: Canvas, pos: Vector2D): Vector2D {
    return new Vector2D(
      MathVector.object2CanvasX(canvas, pos.x),
      MathVector.object2CanvasY(canvas, pos.y),
    );
  }

  /**
   * キャンバスのX座標をオブジェクトX座標に変換する.
   */
  public static canvas2ObjectX(canvas: Canvas, x: number): number {
    const offset = canvas.getCanvasParameterValue().offset;
    const scale = canvas.getCanvasParameterValue().scale;

    return (
      (x - canvas.getContext().canvas.width / 2 - offset.x * scale) / scale
    );
  }

  /**
   * キャンバスのY座標をオブジェクトY座標に変換する.
   */
  public static canvas2ObjectY(canvas: Canvas, y: number): number {
    const offset = canvas.getCanvasParameterValue().offset;
    const scale = canvas.getCanvasParameterValue().scale;

    return (
      -(y - canvas.getContext().canvas.height / 2 - offset.y * scale) / scale
    );
  }

  /**
   * キャンバスの座標をオブジェクト座標に変換する.
   */
  public static canvas2Object(canvas: Canvas, pos: Vector2D): Vector2D {
    return new Vector2D(
      MathVector.canvas2ObjectX(canvas, pos.x),
      MathVector.canvas2ObjectY(canvas, pos.y),
    );
  }

  /**
   * 2点間の距離を得る.
   */
  public static distance2D(a: Vector2D, b: Vector2D): number {
    return Math.sqrt((a.x - b.x) ** 2 + (a.y - b.y) ** 2);
  }
}
