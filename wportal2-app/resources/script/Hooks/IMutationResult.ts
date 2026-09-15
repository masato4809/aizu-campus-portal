import { IValidationError } from '@/script/Hooks/IValidationError';

/**
 *  Mutation Result
 */
export interface IMutationResult {
  statusCode: number;
  statusMessage: string;
  errors?: IValidationError;
}

/**
 * JSONで届くバリデーションエラーをparseして返却する.
 * @param errors
 * @constructor
 */
export const parseErrors = (
  errors: string | null | undefined,
): IValidationError => {
  const ret: IValidationError = {};
  const errorsJson = JSON.parse(errors ?? '{}');
  Object.keys(errorsJson).forEach(name => {
    ret[name] = Object.values(errorsJson[name]);
  });

  return ret;
};
