import { CanvasObject } from '@/script/Common/Canvas/CanvasObject';
import { Vector2D } from '@/script/Common/Canvas/Math/Vector2D';

/**
 * 座席オブジェクト.
 */
export class SeatObject extends CanvasObject {
  public name: string;
  public position: Vector2D;
  public size: Vector2D;

  /**
   * コンストラクタ.
   * @param uid
   * @param name
   * @param position
   * @param size
   */
  constructor(uid: string, name: string, position: Vector2D, size: Vector2D) {
    super(uid);
    this.name = name;
    this.position = position;
    this.size = size;
  }
}
