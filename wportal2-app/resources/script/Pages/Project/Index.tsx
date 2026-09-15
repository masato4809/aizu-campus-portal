import * as React from 'react';
import { useContext, useMemo } from 'react';
import {
  AppTrnProjectList,
  parseAppTrnProjectPayload,
} from '@/script/Models/App/Trn/TrnProjectList';
import { IPropsBase } from '@/script/System/System';
import { IPagination } from '@/script/Provider/IPagination';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { Project } from '@/script/Pages/Project/Project';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  trnProjectList: AppTrnProjectList;
  trnProjectListCount: number;
};
export const context = React.createContext<IndexContext>({
  trnProjectList: new AppTrnProjectList(),
  trnProjectListCount: 0,
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  paginationProject: IPagination;
}
export const Index: React.FC<IProps> = ({ paginationProject, ...props }) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(
    () => ({
      trnProjectList: new AppTrnProjectList(
        paginationProject.list.map(v => parseAppTrnProjectPayload(v)),
      ),
      trnProjectListCount: paginationProject.count,
    }),
    [paginationProject],
  );

  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <context.Provider value={providerValue}>
        <Project />
      </context.Provider>
    </CommonIndexProvider>
  );
};
export default Index;
