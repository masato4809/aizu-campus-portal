import * as React from 'react';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { Reward } from '@/script/Pages/Reward/Reward';
import { IPagination } from '@/script/Provider/IPagination';
import {
  AppTrnUserRewardList,
  parseAppTrnUserRewardPayload,
} from '@/script/Models/App/Trn/TrnUserRewardList';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  trnUserRewardList: AppTrnUserRewardList;
  trnUserRewardListCount: number;
};
export const context = React.createContext<IndexContext>({
  trnUserRewardList: new AppTrnUserRewardList(),
  trnUserRewardListCount: 0,
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
      trnUserRewardList: new AppTrnUserRewardList(
        paginationReward.list.map(v => parseAppTrnUserRewardPayload(v)),
      ),
      trnUserRewardListCount: paginationReward.count,
    }),
    [paginationReward],
  );
  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <context.Provider value={providerValue}>
        <Reward />
      </context.Provider>
    </CommonIndexProvider>
  );
};
export default Index;
