import { IFormCreate } from '@/script/Pages/User/Create/FormCreate';
import {
  EValidationLevel,
  parseErrorMessage,
  validateMulti,
  ValidationResult,
} from '@/script/Common/Validation';
import { IMutationResult } from '@/script/Hooks/IMutationResult';
import { E_VALIDATION_TYPE } from '@/script/Enum/Server/App/EValidationType';

type IFormCreateValidation = Partial<{
  [K in keyof IFormCreate]: ValidationResult;
}>;

export interface IFormCreateValidationResult {
  hasError: boolean;
  data: IFormCreateValidation;
}
export const initialResult: IFormCreateValidationResult = {
  hasError: false,
  data: {},
};

/**
 * サーバー側のバリデーションをparseする.
 */
export const parseServerValidation = (
  result: IMutationResult,
): IFormCreateValidationResult => {
  if (!result.errors) {
    return initialResult;
  }
  const ret: IFormCreateValidation = {
    name: parseErrorMessage('name', result),
    nickname: parseErrorMessage('nickname', result),
    email: parseErrorMessage('email', result),
  };
  return {
    hasError: Object.entries(ret).some(([, res]) => res && res.hasError),
    data: ret,
  };
};

/**
 * バリデーション実行.
 */
export const validateAll = (
  formCreate: IFormCreate,
): IFormCreateValidationResult => {
  const results = {
    name: validateMulti(formCreate.name, '名前', [
      {
        type: E_VALIDATION_TYPE.REQUIRED,
        level: EValidationLevel.Error,
      },
    ]),
    nickname: validateMulti(formCreate.nickname, '表示名', [
      {
        type: E_VALIDATION_TYPE.REQUIRED,
        level: EValidationLevel.Error,
      },
    ]),
    email: validateMulti(formCreate.email, 'E-Mail', [
      {
        type: E_VALIDATION_TYPE.REQUIRED,
        level: EValidationLevel.Error,
      },
      {
        type: E_VALIDATION_TYPE.EMAIL,
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
