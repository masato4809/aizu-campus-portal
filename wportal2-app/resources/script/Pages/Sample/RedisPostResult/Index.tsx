import * as React from 'react';
import { useContext, useMemo } from 'react';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { IPropsBase } from '@/script/System/System';
import { RedisPostResult } from '@/script/Pages/Sample/RedisPostResult/RedisPostResult';

/**
 * コントローラーから受取保持する値.
 */
type IndexContext = {
  storeValue: string;
};
export const context = React.createContext<IndexContext>({
  storeValue: '',
});
export const useIndexContext = (): IndexContext => useContext(context);

/**
 * コントローラーから受け取るprops.
 */
interface IProps extends IPropsBase {
  storeValue: string;
}
export const Index: React.FC<IProps> = ({
  storeValue,

  ...props
}) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(
    () => ({
      storeValue,
    }),
    [storeValue],
  );

  // noinspection TypeScriptValidateTypes
  return (
    <context.Provider value={providerValue}>
      {}
      <CommonIndexProvider {...props}>
        <RedisPostResult />
      </CommonIndexProvider>
    </context.Provider>
  );
};
export default Index;
