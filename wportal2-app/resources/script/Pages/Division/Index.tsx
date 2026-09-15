import * as React from 'react';
import { useContext, useMemo } from 'react';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { Division } from '@/script/Pages/Division/Division';
import { IPagination } from '@/script/Provider/IPagination';
import {
  AppTrnDivisionList,
  parseAppTrnDivisionPayload,
} from '@/script/Models/App/Trn/TrnDivisionList';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  trnDivisionList: AppTrnDivisionList;
  trnDivisionListCount: number;
};
export const context = React.createContext<IndexContext>({
  trnDivisionList: new AppTrnDivisionList(),
  trnDivisionListCount: 0,
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  paginationDivision: IPagination;
}
export const Index: React.FC<IProps> = ({ paginationDivision, ...props }) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(
    () => ({
      trnDivisionList: new AppTrnDivisionList(
        paginationDivision.list.map(v => parseAppTrnDivisionPayload(v)),
      ),
      trnDivisionListCount: paginationDivision.count,
    }),
    [paginationDivision],
  );

  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <context.Provider value={providerValue}>
        <Division />
      </context.Provider>
    </CommonIndexProvider>
  );
};
export default Index;
