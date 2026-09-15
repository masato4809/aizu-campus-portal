import { IFormCreate } from '@/script/Pages/GoodJob/New/FormCreate';
import {
  EValidationLevel,
  parseErrorMessage,
  validateMulti,
  ValidationResult,
} from '@/script/Common/Validation';
import { IMutationResult } from '@/script/Hooks/IMutationResult';
import { E_VALIDATION_TYPE } from '@/script/Enum/Server/App/EValidationType';
import { E_TARGET_TYPE } from '@/script/Enum/Server/App/GoodJob/ETargetType';

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
    fromTargetType: parseErrorMessage('fromTargetType', result),
    fromTrnUserId: parseErrorMessage('fromTrnUserId', result),
    fromTrnDivisionId: parseErrorMessage('fromTrnDivisionId', result),
    fromTrnProjectId: parseErrorMessage('fromTrnProjectId', result),
    fromOtherLabel: parseErrorMessage('fromOtherLabel', result),
    toTargetType: parseErrorMessage('toTargetType', result),
    toTrnUserId: parseErrorMessage('toTrnUserId', result),
    toTrnDivisionId: parseErrorMessage('toTrnDivisionId', result),
    toTrnProjectId: parseErrorMessage('toTrnProjectId', result),
    toOtherLabel: parseErrorMessage('toOtherLabel', result),
    title: parseErrorMessage('title', result),
    content: parseErrorMessage('content', result),
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
    title: validateMulti(formCreate.title, 'タイトル', [
      {
        type: E_VALIDATION_TYPE.REQUIRED,
        level: EValidationLevel.Error,
      },
      {
        type: E_VALIDATION_TYPE.LENGTH,
        maxLength: 255,
        level: EValidationLevel.Error,
      },
    ]),
    content: validateMulti(formCreate.content, '内容', [
      {
        type: E_VALIDATION_TYPE.REQUIRED,
        level: EValidationLevel.Error,
      },
    ]),
  };

  switch (formCreate.fromTargetType) {
    case E_TARGET_TYPE.USER:
      const fromUserResult = {
        fromTrnUserId: validateMulti(
          String(formCreate.fromTrnUserId),
          '送信元ユーザーID',
          [
            {
              type: E_VALIDATION_TYPE.REQUIRED_NOT_ZERO,
              level: EValidationLevel.Error,
            },
          ],
        ),
      };
      Object.assign(results, fromUserResult);
      break;
    case E_TARGET_TYPE.DIVISION:
      const fromDivisionResult = {
        fromTrnDivisionId: validateMulti(
          String(formCreate.fromTrnDivisionId),
          '送信元部署ID',
          [
            {
              type: E_VALIDATION_TYPE.REQUIRED_NOT_ZERO,
              level: EValidationLevel.Error,
            },
          ],
        ),
      };
      Object.assign(results, fromDivisionResult);
      break;
    case E_TARGET_TYPE.PROJECT:
      const fromProjectResult = {
        fromTrnProjectId: validateMulti(
          String(formCreate.fromTrnProjectId),
          '送信元プロジェクトID',
          [
            {
              type: E_VALIDATION_TYPE.REQUIRED_NOT_ZERO,
              level: EValidationLevel.Error,
            },
          ],
        ),
      };
      Object.assign(results, fromProjectResult);
      break;
    case E_TARGET_TYPE.LABEL:
      const fromOtherResult = {
        fromOtherLabel: validateMulti(
          String(formCreate.fromOtherLabel),
          '送信元ラベルID',
          [
            {
              type: E_VALIDATION_TYPE.REQUIRED,
              level: EValidationLevel.Error,
            },
          ],
        ),
      };
      Object.assign(results, fromOtherResult);
      break;
  }

  switch (formCreate.toTargetType) {
    case E_TARGET_TYPE.USER:
      const toUserResult = {
        toTrnUserId: validateMulti(
          String(formCreate.toTrnUserId),
          '送信先ユーザーID',
          [
            {
              type: E_VALIDATION_TYPE.REQUIRED_NOT_ZERO,
              level: EValidationLevel.Error,
            },
          ],
        ),
      };
      Object.assign(results, toUserResult);
      break;
    case E_TARGET_TYPE.DIVISION:
      const toDivisionResult = {
        toTrnDivisionId: validateMulti(
          String(formCreate.toTrnDivisionId),
          '送信先部署ID',
          [
            {
              type: E_VALIDATION_TYPE.REQUIRED_NOT_ZERO,
              level: EValidationLevel.Error,
            },
          ],
        ),
      };
      Object.assign(results, toDivisionResult);
      break;
    case E_TARGET_TYPE.PROJECT:
      const toProjectResult = {
        toTrnProjectId: validateMulti(
          String(formCreate.toTrnProjectId),
          '送信先プロジェクトID',
          [
            {
              type: E_VALIDATION_TYPE.REQUIRED_NOT_ZERO,
              level: EValidationLevel.Error,
            },
          ],
        ),
      };
      Object.assign(results, toProjectResult);
      break;
    case E_TARGET_TYPE.LABEL:
      const toLabelResult = {
        toOtherLabel: validateMulti(
          String(formCreate.toOtherLabel),
          '送信先ラベルID',
          [
            {
              type: E_VALIDATION_TYPE.REQUIRED,
              level: EValidationLevel.Error,
            },
          ],
        ),
      };
      Object.assign(results, toLabelResult);
      break;
  }

  return {
    hasError: Object.entries(results).some(
      ([, result]) => result && result.hasError,
    ),
    data: results,
  };
};
