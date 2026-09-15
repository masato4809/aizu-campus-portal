import * as React from 'react';
import { useContext, useMemo } from 'react';
import { InputForm } from '@/script/Pages/Sample/InputForm/InputForm';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { IMutationResult, parseErrors } from '@/script/Hooks/IMutationResult';
import {
  IFormInputFormValidationResult,
  initialResult,
  parseServerValidation,
} from '@/script/Pages/Sample/InputForm/FormInputFormValidation';
import { IPropsBase } from '@/script/System/System';

/**
 * コントローラーから受取保持する値.
 */
type IndexContext = {
  validation: IFormInputFormValidationResult;
  setValidation: (validation: IFormInputFormValidationResult) => void;
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
    React.useState<IFormInputFormValidationResult>(initialResult);

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
    <context.Provider value={providerValue}>
      {}
      <CommonIndexProvider {...props}>
        <InputForm />
      </CommonIndexProvider>
    </context.Provider>
  );
};
export default Index;
