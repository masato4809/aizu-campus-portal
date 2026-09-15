import {
  EValidationLevel,
  parseErrorMessage,
  validateMulti,
  ValidationResult,
} from '@/script/Common/Validation';
import { IFormPassword } from '@/script/Pages/PersonalSetting/Show/AuthenticatorSettingDialog/ContentPassword';
import { IMutationResult } from '@/script/Hooks/IMutationResult';
import { E_VALIDATION_TYPE } from '@/script/Enum/Server/App/EValidationType';

type IFormPasswordValidation = Partial<{
  [K in keyof IFormPassword]: ValidationResult;
}>;

export interface IFormPasswordValidationResult {
  hasError: boolean;
  data: IFormPasswordValidation;
}

export const initialResult: IFormPasswordValidationResult = {
  hasError: false,
  data: {},
};

/**
 * サーバー側のバリデーションをparseする.
 */
export const parseServerValidation = (
  result: IMutationResult,
): IFormPasswordValidationResult => {
  if (!result.errors) {
    return initialResult;
  }
  const ret: IFormPasswordValidation = {
    password: parseErrorMessage('password', result),
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
  formPassword: IFormPassword,
): IFormPasswordValidationResult => {
  const results = {
    // password.
    password: validateMulti(formPassword.password, 'password', [
      {
        type: E_VALIDATION_TYPE.SIMPLE_PASSWORD,
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
