import * as React from 'react';
import { useContext, useMemo } from 'react';
import {
  AppTrnUserList,
  IAppTrnUser,
  parseAppTrnUserPayload,
} from '@/script/Models/App/Trn/TrnUserList';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { Edit } from '@/script/Pages/User/Edit/Edit';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  trnUser: IAppTrnUser;
};
export const context = React.createContext<IndexContext>({
  trnUser: AppTrnUserList.defaultInterface(),
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  trnUser: IAppTrnUser;
}
export const Index: React.FC<IProps> = ({ trnUser, ...props }) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(
    () => ({
      trnUser: parseAppTrnUserPayload(trnUser),
    }),
    [trnUser],
  );

  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <context.Provider value={providerValue}>
        <Edit />
      </context.Provider>
    </CommonIndexProvider>
  );
};
export default Index;
