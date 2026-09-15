import * as React from 'react';
import { useContext, useMemo } from 'react';
import {
  AppTrnProjectList,
  IAppTrnProject,
  parseAppTrnProjectPayload,
} from '@/script/Models/App/Trn/TrnProjectList';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { Show } from '@/script/Pages/Project/Show/Show';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  trnProjectList: AppTrnProjectList;
};
export const context = React.createContext<IndexContext>({
  trnProjectList: new AppTrnProjectList(),
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  trnProject: IAppTrnProject;
}
export const Index: React.FC<IProps> = ({ trnProject, ...props }) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(
    () => ({
      trnProjectList: new AppTrnProjectList([
        parseAppTrnProjectPayload(trnProject),
      ]),
    }),
    [trnProject],
  );

  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <context.Provider value={providerValue}>
        <Show />
      </context.Provider>
    </CommonIndexProvider>
  );
};
export default Index;
