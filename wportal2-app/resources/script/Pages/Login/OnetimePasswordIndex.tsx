import * as React from 'react';
import { useContext, useMemo } from 'react';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { OnetimePassword } from '@/script/Pages/Login/OnetimePassword';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  authId: number;
  email: string;
};
export const context = React.createContext<IndexContext>({
  authId: 0,
  email: '',
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  authId: 0;
  email: '';
}
export const OnetimePasswordIndex: React.FC<IProps> = ({
  authId,
  email,
  ...props
}) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(
    () => ({
      authId,
      email,
    }),
    [authId, email],
  );

  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <context.Provider value={providerValue}>
        <OnetimePassword />
      </context.Provider>
    </CommonIndexProvider>
  );
};
export default OnetimePasswordIndex;
