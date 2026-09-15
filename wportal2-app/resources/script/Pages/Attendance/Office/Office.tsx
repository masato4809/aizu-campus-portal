import * as React from 'react';
import { IPropsBase } from '@/script/System/System';
import { Box } from '@mui/material';
import { Closable } from '@/script/Component/Misc/Closable';
import { DialogConfirm } from '@/script/Component/Misc/DialogConfirm';
import { E_MOUSE_BUTTON } from '@/script/Common/Canvas/Enum/EMouseButton';
import { Canvas } from '@/script/Common/Canvas/Canvas';
import { CanvasObject } from '@/script/Common/Canvas/CanvasObject';
import { MathVector } from '@/script/Common/Canvas/Math/MathVector';
import { Vector2D } from '@/script/Common/Canvas/Math/Vector2D';

export class PanelObject extends CanvasObject {
  public name: string;
  public position: Vector2D;
  public size: Vector2D;
  public color: string;
  public zIndex: number;

  constructor(
    uid: string,
    name: string,
    position: Vector2D,
    size: Vector2D,
    color: string,
    zIndex: number,
  ) {
    super(uid);
    this.name = name;
    this.position = position;
    this.size = size;
    this.color = color;
    this.zIndex = zIndex;
  }
}

interface IProps extends IPropsBase {}
export const Office: React.FC<IProps> = ({ sx }) => {
  const canvasRef = React.useRef<HTMLCanvasElement | null>(null);
  const [canvas, setCanvas] = React.useState<Canvas>(new Canvas());

  const objectList: PanelObject[] = [
    new PanelObject(
      '1',
      'Object1',
      new Vector2D(0, 0),
      new Vector2D(100, 100),
      'red',
      1,
    ),
    new PanelObject(
      '2',
      'Object2',
      new Vector2D(100, 50),
      new Vector2D(100, 100),
      'green',
      2,
    ),
    new PanelObject(
      '3',
      'Object3',
      new Vector2D(-100, -50),
      new Vector2D(100, 100),
      'blue',
      3,
    ),
  ];

  /**
   * 初期化処理.
   */
  React.useEffect(() => {
    // 画面幅を取得.
    const clientWidth = document?.getElementById('wrapper')?.clientWidth ?? 0;
    const clientHeight = document?.getElementById('wrapper')?.clientHeight ?? 0;

    // 初期化.
    canvas.init(
      canvasRef.current as HTMLCanvasElement,
      clientWidth,
      clientHeight,
    );

    // キャンバスのサイズを画面幅一杯に修正.
    setCanvas(canvas.resizeInstance(clientWidth, canvas.getSize().y));

    // キャンバスにホイールイベント登録.
    document
      ?.getElementById('canvas')
      ?.addEventListener('wheel', handleMouseWheel);

    // 画面がリサイズされたらキャンバスのサイズを修正するイベントを登録.
    window.addEventListener('resize', () => {
      const width = document?.getElementById('wrapper')?.clientWidth ?? 0;
      setCanvas(canvas.resizeInstance(width, canvas.getSize().y));
    });
  }, []);

  /**
   * キャンバスの再描画.
   */
  React.useEffect(() => {
    const context = canvas.getContext();
    const scale = canvas.getCanvasParameter().getValue().scale;

    // キャンバスのクリア.
    context.fillStyle = 'white';
    context.fillRect(0, 0, canvas.getSize().x, canvas.getSize().y);

    // オブジェクト描画.
    objectList.map(object => {
      // オブジェクト自体の描画.
      context.beginPath();
      context.fillStyle = object.color;
      const canvasX =
        MathVector.object2CanvasX(canvas, object.position.x) -
        (object.size.x / 2) * scale;
      const canvasY =
        MathVector.object2CanvasY(canvas, object.position.y) -
        (object.size.y / 2) * scale;
      context.rect(
        canvasX,
        canvasY,
        object.size.x * scale,
        object.size.y * scale,
      );
      context.fill();

      // 選択枠の描画.
      context.beginPath();
      context.strokeStyle = 'black';
      context.rect(
        canvasX,
        canvasY,
        object.size.x * scale,
        object.size.y * scale,
      );
      context.stroke();

      // オーバーレイ枠の描画.
      if (
        canvas
          .getCanvasParameter()
          .getOverlayObject<CanvasObject>()
          ?.getUid() === object.getUid()
      ) {
        context.beginPath();
        context.strokeStyle = 'yellow';
        context.rect(
          canvasX,
          canvasY,
          object.size.x * scale,
          object.size.y * scale,
        );
        context.stroke();
      }
    });

    context.font = '20px serif';
    context.fillStyle = 'black';
    context.fillText(`Scale:${scale}`, 0, 20);
  }, [canvas]);

  /**
   * キャンバス操作.
   */

  /**
   * マウスの移動時.
   */
  const handleMouseMove = (e: React.WheelEvent<HTMLCanvasElement>) => {
    const rect = canvasRef?.current?.getBoundingClientRect() ?? new DOMRect();
    const pos = { x: e.clientX - rect.left, y: e.clientY - rect.top };
    const parameter = canvas.getCanvasParameterValue();

    // 何れかのボタンが押されている時.
    if (parameter.mouseDownL || parameter.mouseDownM || parameter.mouseDownR) {
      const deltaX = pos.x - parameter.currentPosition.x;
      const deltaY = pos.y - parameter.currentPosition.y;
      setCanvas(prevState => {
        return prevState.setCanvasParameterValue({
          prevPosition: prevState.getCanvasParameterValue().currentPosition,
          currentPosition: pos,
          offset: {
            x: prevState.getCanvasParameterValue().offset.x + deltaX,
            y: prevState.getCanvasParameterValue().offset.y + deltaY,
          },
        });
      });
    } else {
      const objectPos = MathVector.canvas2Object(canvas, pos);

      // レイキャストを発生させてオブジェクトを探索する.
      const target = objectList.find(object => {
        return (
          object.position.x - object.size.x / 2 < objectPos.x &&
          object.position.x + object.size.x / 2 > objectPos.x &&
          object.position.y - object.size.y / 2 < objectPos.y &&
          object.position.y + object.size.y / 2 > objectPos.y
        );
      });

      // オーバーレイしているオブジェクトが存在するなら.
      if (target) {
        setCanvas(prevState => {
          return prevState.setCanvasParameterValue({
            overlayObject: target,
          });
        });
      }
    }
  };

  /**
   * マウスホイール操作時.
   */
  const handleMouseWheel = (e: WheelEvent) => {
    if (e.deltaY > 0) {
      setCanvas(prevState => {
        return prevState.setCanvasParameterValue({
          scale: Math.max(
            0.2,
            prevState.getCanvasParameterValue().scale - 0.04,
          ),
        });
      });
    }
    if (e.deltaY < 0) {
      setCanvas(prevState => {
        return prevState.setCanvasParameterValue({
          scale: Math.min(
            2.0,
            prevState.getCanvasParameterValue().scale + 0.04,
          ),
        });
      });
    }
    e.preventDefault();
  };

  /**
   * マウスDown時.
   */
  const handleMouseDown = (e: React.MouseEvent<HTMLCanvasElement>) => {
    const canvas = canvasRef.current;
    if (!canvas) {
      return;
    }
    const rect = canvas.getBoundingClientRect();
    const pos = { x: e.clientX - rect.left, y: e.clientY - rect.top };
    setCanvas(prevState => {
      return prevState.setCanvasParameterValue({
        mouseDownL:
          e.button === E_MOUSE_BUTTON.LEFT
            ? true
            : prevState.getCanvasParameterValue().mouseDownL,
        mouseOriginL:
          e.button === E_MOUSE_BUTTON.LEFT
            ? pos
            : prevState.getCanvasParameterValue().mouseOriginL,

        mouseDownM:
          e.button === E_MOUSE_BUTTON.MIDDLE
            ? true
            : prevState.getCanvasParameterValue().mouseDownM,
        mouseOriginM:
          e.button === E_MOUSE_BUTTON.MIDDLE
            ? pos
            : prevState.getCanvasParameterValue().mouseOriginM,

        mouseDownR:
          e.button === E_MOUSE_BUTTON.RIGHT
            ? true
            : prevState.getCanvasParameterValue().mouseDownR,
        mouseOriginR:
          e.button === E_MOUSE_BUTTON.RIGHT
            ? pos
            : prevState.getCanvasParameterValue().mouseOriginR,

        prevPosition: pos,
        currentPosition: pos,
      });
    });
    e.preventDefault();
  };

  /**
   * マウスUp時.
   */
  const handleMouseUp = (e: React.MouseEvent<HTMLCanvasElement>) => {
    const canvasRefCurrent = canvasRef.current;
    if (!canvasRefCurrent) {
      return;
    }
    const rect = canvasRefCurrent.getBoundingClientRect();
    const pos = { x: e.clientX - rect.left, y: e.clientY - rect.top };

    setCanvas(prevState => {
      return prevState.setCanvasParameterValue({
        mouseDownL:
          e.button === E_MOUSE_BUTTON.LEFT
            ? false
            : prevState.getCanvasParameterValue().mouseDownL,
        mouseDownM:
          e.button === E_MOUSE_BUTTON.MIDDLE
            ? false
            : prevState.getCanvasParameterValue().mouseDownM,
        mouseDownR:
          e.button === E_MOUSE_BUTTON.RIGHT
            ? false
            : prevState.getCanvasParameterValue().mouseDownR,
        prevPosition: pos,
        currentPosition: pos,
      });
    });

    // Lの時にOriginと距離が近い場合はクリックと判断する.
    if (
      e.button === E_MOUSE_BUTTON.LEFT &&
      MathVector.distance2D(
        pos,
        canvas.getCanvasParameterValue().mouseOriginL,
      ) < 5
    ) {
      handleMouseClick(e);
    }

    e.preventDefault();
  };

  /**
   * マウスクリック時.
   */
  const handleMouseClick = (e: React.MouseEvent<HTMLCanvasElement>) => {
    const rect = canvasRef?.current?.getBoundingClientRect() ?? new DOMRect();
    const pos = {
      x: MathVector.canvas2ObjectX(canvas, e.clientX - rect.left),
      y: MathVector.canvas2ObjectY(canvas, e.clientY - rect.top),
    };

    // レイキャストを発生させてオブジェクトを探索する.
    const target = objectList.find(object => {
      return (
        object.position.x - object.size.x / 2 < pos.x &&
        object.position.x + object.size.x / 2 > pos.x &&
        object.position.y - object.size.y / 2 < pos.y &&
        object.position.y + object.size.y / 2 > pos.y
      );
    });

    if (!!target) {
      // 対象が見つかる場合.
      setCanvas(prevState => {
        return prevState.setCanvasParameterValue({
          captureObject: target,
        });
      });
    } else {
      // 対象が見つからない場合.
      setCanvas(prevState => {
        return prevState.setCanvasParameterValue({
          captureObject: null,
        });
      });
    }
  };

  /**
   * マウスが離れた時.
   */
  const handleMouseLeave = (e: React.MouseEvent<HTMLCanvasElement>) => {
    setCanvas(prevState => {
      return prevState.setCanvasParameterValue({
        mouseDownL: false,
      });
    });
    e.preventDefault();
  };

  return (
    <Box
      sx={{
        ...sx,
      }}
      id={'wrapper'}
    >
      <Closable
        open={!!canvas.getCanvasParameter()?.getCaptureObject<PanelObject>()}
      >
        <DialogConfirm
          open={!!canvas.getCanvasParameter()?.getCaptureObject<PanelObject>()}
          labels={{
            content: `オブジェクト${canvas.getCanvasParameter()?.getCaptureObject<PanelObject>()?.name}を選択しました。`,
            confirm: '閉じる',
          }}
          actions={{
            confirm: () =>
              setCanvas(prevState => {
                return prevState.setCanvasParameterValue({
                  captureObject: null,
                });
              }),
          }}
        />
      </Closable>
      <canvas
        id={'canvas'}
        ref={canvasRef}
        width={canvas.getSize().x}
        height={400}
        onMouseMove={handleMouseMove}
        onMouseDown={handleMouseDown}
        onMouseUp={handleMouseUp}
        onMouseLeave={handleMouseLeave}
      />
    </Box>
  );
};
