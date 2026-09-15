import { IFormEdit } from '@/script/Pages/PersonalSetting/Edit/FormEdit';
import {
  EValidationLevel,
  parseErrorMessage,
  validateMulti,
  ValidationResult,
} from '@/script/Common/Validation';
import { IMutationResult } from '@/script/Hooks/IMutationResult';
import { E_VALIDATION_TYPE } from '@/script/Enum/Server/App/EValidationType';

type IFormEditValidation = Partial<{
  [K in keyof IFormEdit]: ValidationResult;
}>;

export interface IFormEditValidationResult {
  hasError: boolean;
  data: IFormEditValidation;
}
export const initialResult: IFormEditValidationResult = {
  hasError: false,
  data: {},
};

/**
 * サーバー側のバリデーションをparseする.
 */
export const parseServerValidation = (
  result: IMutationResult,
): IFormEditValidationResult => {
  if (!result.errors) {
    return initialResult;
  }
  const ret: IFormEditValidation = {
    nickname: parseErrorMessage('nickname', result),
    birthDate: parseErrorMessage('birthDate', result),
  };
  return {
    hasError: Object.entries(ret).some(([, res]) => res && res.hasError),
    data: ret,
  };
};

/**
 * バリデーション実行.
 */
export const validateAll = (formEdit: IFormEdit): IFormEditValidationResult => {
  const results = {
    nickname: validateMulti(formEdit.nickname, '表示名', [
      {
        type: E_VALIDATION_TYPE.REQUIRED,
        level: EValidationLevel.Error,
      },
    ]),
    birthDate: validateMulti(String(formEdit.birthDate), '生年月日', [
      {
        type: E_VALIDATION_TYPE.DATE,
        level: EValidationLevel.Error,
      },
    ]),
  };
  return {
    hasError: Object.entries(results).some(
      ([, result]) => result && result.hasError,
    ),
    data: results,
  };
};
