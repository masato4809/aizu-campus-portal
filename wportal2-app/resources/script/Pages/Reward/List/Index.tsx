import * as React from 'react';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { List } from '@/script/Pages/Reward/List/List';
import {
  AppMstRewardList,
  parseAppMstRewardPayload,
} from '@/script/Models/App/Mst/MstRewardList';
import { IPagination } from '@/script/Provider/IPagination';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  mstRewardList: AppMstRewardList;
  mstRewardListCount: number;
};
export const context = React.createContext<IndexContext>({
  mstRewardList: new AppMstRewardList(),
  mstRewardListCount: 0,
});
export const useIndexContext = (): IndexContext => React.useContext(context);

interface IProps extends IPropsBase {
  paginationReward: IPagination;
}
export const Index: React.FC<IProps> = ({ paginationReward, ...props }) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = React.useMemo(
    () => ({
      mstRewardList: new AppMstRewardList(
        paginationReward.list.map(v => parseAppMstRewardPayload(v)),
      ),
      mstRewardListCount: paginationReward.count,
    }),
    [paginationReward],
  );

  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <context.Provider value={providerValue}>
        <List />
      </context.Provider>
    </CommonIndexProvider>
  );
};
export default Index;
