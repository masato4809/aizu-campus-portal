import * as React from 'react';
import { useContext, useMemo } from 'react';
import { IPropsBase } from '@/script/System/System';
import {
  AppTrnUserSlackProfileList,
  IAppTrnUserSlackProfile,
  parseAppTrnUserSlackProfilePayload,
} from '@/script/Models/App/Trn/TrnUserSlackProfileList';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { Edit } from '@/script/Pages/PersonalSetting/Edit/Edit';
import { IMutationResult, parseErrors } from '@/script/Hooks/IMutationResult';
import {
  IFormEditValidationResult,
  initialResult,
  parseServerValidation,
} from '@/script/Pages/PersonalSetting/Edit/FormEditValidation';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  trnUserSlackProfile: IAppTrnUserSlackProfile;
  validation: IFormEditValidationResult;
  setValidation: (validation: IFormEditValidationResult) => void;
};
export const context = React.createContext<IndexContext>({
  trnUserSlackProfile: AppTrnUserSlackProfileList.defaultInterface(),
  validation: initialResult,
  setValidation: () => {},
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  trnUserSlackProfile?: IAppTrnUserSlackProfile;
  serverValidation: IMutationResult;
}
export const Index: React.FC<IProps> = ({
  trnUserSlackProfile,
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
      trnUserSlackProfile:
        parseAppTrnUserSlackProfilePayload(trnUserSlackProfile),
      validation,
      setValidation,
    }),
    [trnUserSlackProfile, validation, setValidation],
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
