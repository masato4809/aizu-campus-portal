import * as React from 'react';
import { useContext, useMemo } from 'react';
import { IPropsBase } from '@/script/System/System';
import { New } from '@/script/Pages/GoodJob/New/New';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import {
  IFormCreateValidationResult,
  initialResult,
  parseServerValidation,
} from '@/script/Pages/GoodJob/New/FormCreateValidation';
import { IMutationResult, parseErrors } from '@/script/Hooks/IMutationResult';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  validation: IFormCreateValidationResult;
  setValidation: (validation: IFormCreateValidationResult) => void;
};
export const context = React.createContext<IndexContext>({
  validation: initialResult,
  setValidation: () => {},
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  serverValidation: IMutationResult;
}
export const Index: React.FC<IProps> = ({ serverValidation, ...props }) => {
  const [validation, setValidation] =
    React.useState<IFormCreateValidationResult>(initialResult);

  /**
   * 受信したserverValidationが変更されたら駆動する.
   */
  React.useEffect(() => {
    if (!serverValidation?.errors) {
      return;
    }
    setValidation(
      parseServerValidation({
        statusCode: Number(serverValidation.statusCode),
        statusMessage: String(serverValidation.statusMessage),
        errors: parseErrors(String(serverValidation.errors)),
      }),
    );
  }, [serverValidation]);

  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(
    () => ({
      validation,
      setValidation,
    }),
    [validation, setValidation],
  );

  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <context.Provider value={providerValue}>
        <New />
      </context.Provider>
    </CommonIndexProvider>
  );
};
export default Index;
