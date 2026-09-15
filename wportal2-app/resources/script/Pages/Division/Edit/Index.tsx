import * as React from 'react';
import { useContext, useMemo } from 'react';
import {
  AppTrnDivisionList,
  IAppTrnDivision,
  parseAppTrnDivisionPayload,
} from '@/script/Models/App/Trn/TrnDivisionList';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { Edit } from '@/script/Pages/Division/Edit/Edit';
import {
  IFormEditValidationResult,
  initialResult,
  parseServerValidation,
} from '@/script/Pages/Division/Edit/FormEditValidation';
import { IMutationResult, parseErrors } from '@/script/Hooks/IMutationResult';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  trnDivisionList: AppTrnDivisionList;
  validation: IFormEditValidationResult;
  setValidation: (validation: IFormEditValidationResult) => void;
};
export const context = React.createContext<IndexContext>({
  trnDivisionList: new AppTrnDivisionList(),
  validation: initialResult,
  setValidation: () => {},
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  trnDivision: IAppTrnDivision;
  serverValidation: IMutationResult;
}
export const Index: React.FC<IProps> = ({
  trnDivision,
  serverValidation,
  ...props
}) => {
  const [validation, setValidation] =
    React.useState<IFormEditValidationResult>(initialResult);

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
      trnDivisionList: new AppTrnDivisionList([
        parseAppTrnDivisionPayload(trnDivision),
      ]),
      validation,
      setValidation,
    }),
    [trnDivision, validation, setValidation],
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
