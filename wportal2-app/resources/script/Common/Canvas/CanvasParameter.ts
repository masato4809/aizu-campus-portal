import { Vector2D } from '@/script/Common/Canvas/Math/Vector2D';
import { CanvasObject } from '@/script/Common/Canvas/CanvasObject';

export class CanvasParameter {
  private value: IParameter = defaultParameter;

  constructor() {}

  public getValue(): IParameter {
    return this.value;
  }

  public setValue(value: IParameter): void {
    this.value = value;
  }

  public getOverlayObject<T>(): T | null {
    return this.value.overlayObject as T;
  }

  public getCaptureObject<T>(): T | null {
    return this.value.captureObject as T;
  }
}

export interface IParameter {
  scale: number;
  offset: Vector2D;
  mouseDownL: boolean;
  mouseOriginL: Vector2D;
  mouseDownM: boolean;
  mouseOriginM: Vector2D;
  mouseDownR: boolean;
  mouseOriginR: Vector2D;
  prevPosition: Vector2D;
  currentPosition: Vector2D;
  overlayObject: CanvasObject | null;
  captureObject: CanvasObject | null;
}

export const defaultParameter: IParameter = {
  scale: 1,
  offset: Vector2D.ZERO,
  mouseDownL: false,
  mouseOriginL: Vector2D.ZERO,
  mouseDownM: false,
  mouseOriginM: Vector2D.ZERO,
  mouseDownR: false,
  mouseOriginR: Vector2D.ZERO,
  prevPosition: Vector2D.ZERO,
  currentPosition: Vector2D.ZERO,
  overlayObject: null,
  captureObject: null,
};
