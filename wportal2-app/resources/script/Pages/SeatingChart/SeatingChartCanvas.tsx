import * as React from 'react';
import { IPropsBase } from '@/script/System/System';
import { Canvas } from '@/script/Common/Canvas/Canvas';
import { Box } from '@mui/material';
import { MathVector } from '@/script/Common/Canvas/Math/MathVector';
import { E_MOUSE_BUTTON } from '@/script/Common/Canvas/Enum/EMouseButton';
import { AppTrnSeatList } from '@/script/Models/App/Trn/TrnSeatList';

interface IProps extends IPropsBase {
  trnSeatList: AppTrnSeatList;
}
export const SeatingChartCanvas: React.FC<IProps> = ({ sx, trnSeatList }) => {
  const canvasRef = React.useRef<HTMLCanvasElement | null>(null);
  const [canvas, setCanvas] = React.useState<Canvas>(new Canvas());

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
    const canvasSize = canvas.getSize();
    const scale = canvas.getCanvasParameter().getValue().scale;
    const offset = canvas.getCanvasParameter().getValue().offset;
    const blockSize = 100;

    // キャンバスのクリア.
    context.fillStyle = 'white';
    context.fillRect(0, 0, canvas.getSize().x, canvas.getSize().y);

    // 背景枠.
    context.beginPath();
    context.strokeStyle = 'grey';
    const lineDash = context.getLineDash();
    const dashScale = 10 * scale;

    // 縦線.
    const numX = Math.floor(canvasSize.x / 2 / (blockSize * scale));
    const minX = numX + Math.floor(offset.x / blockSize);
    const maxX = numX - Math.floor(offset.x / blockSize);
    for (let x = -minX - 1; x <= maxX; x++) {
      context.setLineDash([dashScale, dashScale]);
      context.moveTo(MathVector.object2CanvasX(canvas, x * blockSize), 0);
      context.lineTo(
        MathVector.object2CanvasX(canvas, x * blockSize),
        canvasSize.y,
      );
    }

    // 横線.
    const numY = Math.floor(canvasSize.y / 2 / (blockSize * scale));
    const minY = numY - Math.floor(offset.y / blockSize);
    const maxY = numY + Math.floor(offset.y / blockSize);
    for (let y = -minY; y <= maxY + 1; y++) {
      context.setLineDash([dashScale, dashScale]);
      context.moveTo(0, MathVector.object2CanvasY(canvas, y * blockSize));
      context.lineTo(
        canvasSize.x,
        MathVector.object2CanvasY(canvas, y * blockSize),
      );
    }

    // 描画処理.
    context.stroke();

    // 設定を元に戻す.
    context.setLineDash(lineDash);

    // 座席の描画.
    context.beginPath();
    context.strokeStyle = 'black';
    context.textAlign = 'center';
    context.textBaseline = 'middle';
    const fontSize = 30 * scale;
    context.font = `${fontSize}px sans-serif`;
    trnSeatList.list().map(seat => {
      const seatX = MathVector.object2CanvasX(
        canvas,
        seat.positionX - blockSize / 2,
      );
      const seatY = MathVector.object2CanvasY(
        canvas,
        seat.positionY + blockSize / 2,
      );
      context.fillStyle = '#8feb34';
      context.fillRect(seatX, seatY, blockSize * scale, blockSize * scale);
      context.rect(seatX, seatY, blockSize * scale, blockSize * scale);

      const fontX = MathVector.object2CanvasX(canvas, seat.positionX);
      const fontY = MathVector.object2CanvasY(canvas, seat.positionY);
      context.fillStyle = 'black';
      context.fillText(seat.label, fontX, fontY, 100 * scale);
    });
    context.stroke();
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
  const handleMouseClick = (_e: React.MouseEvent<HTMLCanvasElement>) => {};

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
      <canvas
        id={'canvas'}
        ref={canvasRef}
        width={canvas.getSize().x}
        height={700}
        onMouseMove={handleMouseMove}
        onMouseDown={handleMouseDown}
        onMouseUp={handleMouseUp}
        onMouseLeave={handleMouseLeave}
      />
    </Box>
  );
};
