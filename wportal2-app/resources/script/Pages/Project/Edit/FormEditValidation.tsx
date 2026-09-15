import {
  EValidationLevel,
  parseErrorMessage,
  validateMulti,
  ValidationResult,
} from '@/script/Common/Validation';
import { IMutationResult } from '@/script/Hooks/IMutationResult';
import { E_VALIDATION_TYPE } from '@/script/Enum/Server/App/EValidationType';
import { IFormEdit } from '@/script/Pages/Project/Edit/FormEdit';

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
    name: parseErrorMessage('name', result),
    explain: parseErrorMessage('explain', result),
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
    name: validateMulti(formEdit.name, '名前', [
      {
        type: E_VALIDATION_TYPE.REQUIRED,
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
