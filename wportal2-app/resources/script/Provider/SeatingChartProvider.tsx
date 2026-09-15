import * as React from 'react';
import { Vector2D } from '@/script/Common/Canvas/Math/Vector2D';
import { SeatObject } from '@/script/Pages/SeatingChart/SeatObject';
import { AppTrnSeatList, IAppTrnSeat } from '@/script/Models/App/Trn/TrnSeatList';


interface ISeatingChartContext {
  seats: IAppTrnSeat[];
  seatObjects: SeatObject[];
}

const SeatingChartContext = React.createContext<ISeatingChartContext>({
  seats: [],
  seatObjects: [],
});

interface IProps {
  children: React.ReactNode;
  seats: IAppTrnSeat[];
}

export const SeatingChartProvider: React.FC<IProps> = ({ children, seats }) => {
  const seatList = React.useMemo(() => new AppTrnSeatList(seats), [seats]);
  
  const seatObjects = React.useMemo(() => {
    return seatList.list().map((seat: IAppTrnSeat) => {
      return new SeatObject(
        `seat-${seat.id}`,
        seat.label,
        new Vector2D(seat.positionX, seat.positionY),
        new Vector2D(100, 100)
      );
    });
  }, [seatList]);

  const contextValue = React.useMemo(() => {
    return {
      seats: seatList.list(),
      seatObjects,
    };
  }, [seatList, seatObjects]);

  return (
    <SeatingChartContext.Provider value={contextValue}>
      {children}
    </SeatingChartContext.Provider>
  );
};

export const useSeatingChart = (): ISeatingChartContext => {
  return React.useContext(SeatingChartContext);
};
