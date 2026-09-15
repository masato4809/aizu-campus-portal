import {
  EValidationLevel,
  parseErrorMessage,
  validateMulti,
  ValidationFunction,
  ValidationResult,
} from '@/script/Common/Validation';
import { IMutationResult } from '@/script/Hooks/IMutationResult';
import { E_VALIDATION_TYPE } from '@/script/Enum/Server/App/EValidationType';
import { IFormRedis } from '@/script/Pages/Sample/Redis/FormRedis';

type IFormRedisValidation = Partial<{
  [K in keyof IFormRedis]: ValidationResult;
}>;

export interface IFormRedisValidationResult {
  hasError: boolean;
  data: IFormRedisValidation;
}
export const initialResult: IFormRedisValidationResult = {
  hasError: false,
  data: {},
};

/**
 * サーバー側のバリデーションをparseする.
 */
export const parseServerValidation = (
  result: IMutationResult,
): IFormRedisValidationResult => {
  if (!result.errors) {
    return initialResult;
  }
  const ret: IFormRedisValidation = {
    storeValue: parseErrorMessage('store_value', result),
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
  formRedis: IFormRedis,
): IFormRedisValidationResult => {
  const results = {
    storeValue: validateStoreValue(formRedis.storeValue, 'Redis保存値'),
  };
  return {
    hasError: Object.entries(results).some(
      ([, result]) => result && result.hasError,
    ),
    data: results,
  };
};

const validateStoreValue: ValidationFunction = (value, columnName) =>
  validateMulti(value, columnName, [
    {
      type: E_VALIDATION_TYPE.REQUIRED,
      level: EValidationLevel.Error,
    },
  ]);
