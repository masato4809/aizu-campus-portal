import * as React from 'react';
import { useContext, useMemo } from 'react';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { GoodJob } from '@/script/Pages/GoodJob/GoodJob';
import { IPagination } from '@/script/Provider/IPagination';
import {
  AppTrnGoodJobList,
  parseAppTrnGoodJobPayload,
} from '@/script/Models/App/Trn/TrnGoodJobList';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  trnGoodJobList: AppTrnGoodJobList;
  trnGoodJobListCount: number;
};
export const context = React.createContext<IndexContext>({
  trnGoodJobList: new AppTrnGoodJobList(),
  trnGoodJobListCount: 0,
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  paginationGoodJob: IPagination;
}
export const Index: React.FC<IProps> = ({ paginationGoodJob, ...props }) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(
    () => ({
      trnGoodJobList: new AppTrnGoodJobList(
        paginationGoodJob.list.map(v => parseAppTrnGoodJobPayload(v)),
      ),
      trnGoodJobListCount: paginationGoodJob.count,
    }),
    [paginationGoodJob],
  );

  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <context.Provider value={providerValue}>
        <GoodJob />
      </context.Provider>
    </CommonIndexProvider>
  );
};
export default Index;
