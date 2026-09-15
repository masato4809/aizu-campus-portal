import * as React from 'react';
import { useContext, useMemo } from 'react';
import {
  AppTrnUserList,
  parseAppTrnUserPayload,
} from '@/script/Models/App/Trn/TrnUserList';
import { IPropsBase } from '@/script/System/System';
import { IPagination } from '@/script/Provider/IPagination';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { User } from '@/script/Pages/User/User';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  trnUserList: AppTrnUserList;
  trnUserListCount: number;
};
export const context = React.createContext<IndexContext>({
  trnUserList: new AppTrnUserList(),
  trnUserListCount: 0,
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  paginationUser: IPagination;
}
export const Index: React.FC<IProps> = ({ paginationUser, ...props }) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(
    () => ({
      trnUserList: new AppTrnUserList(
        paginationUser.list.map(v => parseAppTrnUserPayload(v)),
      ),
      trnUserListCount: paginationUser.count,
    }),
    [paginationUser],
  );

  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <context.Provider value={providerValue}>
        <User />
      </context.Provider>
    </CommonIndexProvider>
  );
};
export default Index;
