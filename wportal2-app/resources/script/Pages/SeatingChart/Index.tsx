import * as React from 'react';
import { useContext, useMemo } from 'react';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { SeatingChart } from '@/script/Pages/SeatingChart/SeatingChart';
import {
  AppTrnSeatList,
  IAppTrnSeat,
  parseAppTrnSeatPayload,
} from '@/script/Models/App/Trn/TrnSeatList';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  trnSeatList: AppTrnSeatList;
};
export const context = React.createContext<IndexContext>({
  trnSeatList: new AppTrnSeatList(),
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  trnSeatList: IAppTrnSeat[];
}
export const Index: React.FC<IProps> = ({ trnSeatList, ...props }) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue = useMemo(
    () => ({
      trnSeatList: new AppTrnSeatList(
        trnSeatList.map(v => parseAppTrnSeatPayload(v)),
      ),
    }),
    [trnSeatList],
  );

  return (
    <CommonIndexProvider {...props}>
      <context.Provider value={providerValue}>
        <SeatingChart />
      </context.Provider>
    </CommonIndexProvider>
  );
};
export default Index;
