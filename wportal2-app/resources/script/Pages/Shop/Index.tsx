import * as React from 'react';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { Shop } from '@/script/Pages/Shop/Shop';
import {
  AppMstGoodsList,
  IAppMstGoods,
  parseAppMstGoodsPayload,
} from '@/script/Models/App/Mst/MstGoodsList';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  mstGoodsList: AppMstGoodsList;
};
export const context = React.createContext<IndexContext>({
  mstGoodsList: new AppMstGoodsList(),
});
export const useIndexContext = (): IndexContext => React.useContext(context);

interface IProps extends IPropsBase {
  mstGoodsList: IAppMstGoods[];
}
export const Index: React.FC<IProps> = ({ mstGoodsList, ...props }) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = React.useMemo(
    () => ({
      mstGoodsList: new AppMstGoodsList(
        mstGoodsList.map(v => parseAppMstGoodsPayload(v)),
      ),
    }),
    [mstGoodsList],
  );

  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <context.Provider value={providerValue}>
        <Shop />
      </context.Provider>
    </CommonIndexProvider>
  );
};
export default Index;
