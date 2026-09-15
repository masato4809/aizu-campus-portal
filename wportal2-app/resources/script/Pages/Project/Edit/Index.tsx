import * as React from 'react';
import { useContext, useMemo } from 'react';
import {
  AppTrnProjectList,
  IAppTrnProject,
  parseAppTrnProjectPayload,
} from '@/script/Models/App/Trn/TrnProjectList';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { Edit } from '@/script/Pages/Project/Edit/Edit';
import {
  IFormEditValidationResult,
  initialResult,
  parseServerValidation,
} from '@/script/Pages/Project/Edit/FormEditValidation';
import { IMutationResult, parseErrors } from '@/script/Hooks/IMutationResult';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  trnProjectList: AppTrnProjectList;
  validation: IFormEditValidationResult;
  setValidation: (validation: IFormEditValidationResult) => void;
};
export const context = React.createContext<IndexContext>({
  trnProjectList: new AppTrnProjectList(),
  validation: initialResult,
  setValidation: () => {},
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  trnProject: IAppTrnProject;
  serverValidation: IMutationResult;
}
export const Index: React.FC<IProps> = ({
  trnProject,
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
      trnProjectList: new AppTrnProjectList([
        parseAppTrnProjectPayload(trnProject),
      ]),
      validation,
      setValidation,
    }),
    [trnProject, validation, setValidation],
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
