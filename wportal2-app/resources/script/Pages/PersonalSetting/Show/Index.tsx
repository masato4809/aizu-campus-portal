import * as React from 'react';
import { useContext, useMemo } from 'react';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { Show } from '@/script/Pages/PersonalSetting/Show/Show';
import {
  AppTrnUserSlackProfileList,
  IAppTrnUserSlackProfile,
  parseAppTrnUserSlackProfilePayload,
} from '@/script/Models/App/Trn/TrnUserSlackProfileList';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  trnUserSlackProfile: IAppTrnUserSlackProfile;
};
export const context = React.createContext<IndexContext>({
  trnUserSlackProfile: AppTrnUserSlackProfileList.defaultInterface(),
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  trnUserSlackProfile?: IAppTrnUserSlackProfile;
}
export const Index: React.FC<IProps> = ({ trnUserSlackProfile, ...props }) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(
    () => ({
      trnUserSlackProfile:
        parseAppTrnUserSlackProfilePayload(trnUserSlackProfile),
    }),
    [trnUserSlackProfile],
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
