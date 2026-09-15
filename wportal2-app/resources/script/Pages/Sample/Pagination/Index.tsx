import * as React from 'react';
import { useContext, useMemo } from 'react';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import {
  AppTrnUserList,
  IAppTrnUser,
  parseAppTrnUserPayload,
} from '@/script/Models/App/Trn/TrnUserList';
import { IPropsBase } from '@/script/System/System';
import { Pagination } from '@/script/Pages/Sample/Pagination/Pagination';

/**
 * コントローラーから受取保持する値.
 */
type IndexContext = {
  trnUserList: AppTrnUserList;
  trnUserListCount: number;
  fixList: AppTrnUserList;
};
export const context = React.createContext<IndexContext>({
  trnUserList: new AppTrnUserList(),
  trnUserListCount: 0,
  fixList: new AppTrnUserList(),
});
export const useIndexContext = (): IndexContext => useContext(context);

/**
 * コントローラーから受け取るprops.
 */
interface IProps extends IPropsBase {
  paginationTrnUser: { list: []; count: number };
  fixList: IAppTrnUser[];
}
export const Index: React.FC<IProps> = ({
  paginationTrnUser,
  fixList,

  ...props
}) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(
    () => ({
      trnUserList: new AppTrnUserList(
        paginationTrnUser.list.map(v => parseAppTrnUserPayload(v)),
      ),
      trnUserListCount: paginationTrnUser.count,
      fixList: new AppTrnUserList(fixList.map(v => parseAppTrnUserPayload(v))),
    }),
    [paginationTrnUser, fixList],
  );

  // noinspection TypeScriptValidateTypes
  return (
    <context.Provider value={providerValue}>
      {}
      <CommonIndexProvider {...props}>
        <Pagination />
      </CommonIndexProvider>
    </context.Provider>
  );
};
export default Index;
