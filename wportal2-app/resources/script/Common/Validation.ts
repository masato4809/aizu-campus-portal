import { Env } from '@/script/Common/Env';
import { IMutationResult } from '@/script/Hooks/IMutationResult';
import {
  E_VALIDATION_TYPE,
  EValidationType,
} from '@/script/Enum/Server/App/EValidationType';
import { Log } from '@/script/Common/Log';

/**
 * バリデーション結果.
 */
export type ValidationResult = {
  hasError: boolean;
  errors: string[];
  warnings: string[];
};

/**
 * バリデーション関数の宣言.
 */
export type ValidationFunction = (
  value: string,
  columnName: string,
  ...args: (boolean | undefined)[]
) => ValidationResult;

export enum EValidationLevel {
  Error,
  Warning,
}

type ErrorOnlyValidation = {
  type: EValidationType;
  level: EValidationLevel.Error;
};

type RangeValidation = {
  type: EValidationType;
  level: EValidationLevel.Error;
  min: number;
  max: number;
};

type DigitValidation = {
  type: EValidationType;
  level: EValidationLevel.Error;
  max: number;
};

type LengthValidation = {
  type: EValidationType;
  level: EValidationLevel.Error;
  maxLength: number;
};

type MaxValidation = {
  type: EValidationType;
  level: EValidationLevel.Error;
  max: number;
};

/**
 * バリデーション種別.
 */
export type BaseValidationType = { label?: string } & (
  | ErrorOnlyValidation
  | RangeValidation
  | LengthValidation
);

/**
 * 複数バリデーションの実施.
 * @param value
 * @param columnName
 * @param validations
 */
export const validateMulti = (
  value: string,
  columnName: string,
  validations: BaseValidationType[],
): ValidationResult => {
  // ローカルバリデーションの実施有無のチェック.
  if (!Env.idEnableLocalValidation()) {
    Log.info('ローカルバリデーションが無効のためスキップします');
    return {
      hasError: false,
      errors: [],
      warnings: [],
    };
  }

  let hasError = false;
  const errors: string[] = [];
  const warnings: string[] = [];
  validations.forEach(validation => {
    if (!validate(value, validation)) {
      if (validation.level === EValidationLevel.Error) {
        hasError = true;
        errors.push(errorMessage(columnName, validation));
      } else {
        warnings.push(errorMessage(columnName, validation));
      }
    }
  });
  return {
    hasError,
    errors,
    warnings,
  };
};

const errorMessage = (
  columnName: string,
  validation: BaseValidationType,
): string => {
  switch (validation.type) {
    case E_VALIDATION_TYPE.REQUIRED:
    case E_VALIDATION_TYPE.REQUIRED_NOT_ZERO:
      return `${columnName}は必須項目です`;
    case E_VALIDATION_TYPE.NUMBER:
      return `${columnName}が数値ではありません`;
    case E_VALIDATION_TYPE.NUMBER_HYPHEN:
      return `${columnName}は数値とハイフンのみで入力してください`;
    case E_VALIDATION_TYPE.NUMBER_PLUS:
      return `${columnName}が正の数値ではありません`;
    case E_VALIDATION_TYPE.NUMBER_MAX:
      return `${columnName}は${
        (validation as MaxValidation).max
      }以下の数値を入力してください`;
    case E_VALIDATION_TYPE.NUMBER_RANGE:
      return `${columnName}が範囲外です`;
    case E_VALIDATION_TYPE.MAX_DIGIT:
      return `${columnName}は${
        (validation as DigitValidation).max
      }桁の整数で入力してください`;
    case E_VALIDATION_TYPE.LENGTH:
      return `${columnName}は${
        (validation as LengthValidation).maxLength
      }字以内で入力してください`;
    case E_VALIDATION_TYPE.PHONE:
      return `${columnName}は10〜13字の半角数字及び"-"で入力してください`;
    case E_VALIDATION_TYPE.EMAIL:
      return `${columnName}はメールアドレス形式で入力してください`;
    case E_VALIDATION_TYPE.SIMPLE_PASSWORD:
      return `${columnName}は8文字以上の半角英数字で入力してください`;
    case E_VALIDATION_TYPE.DATE:
      return `${columnName}は日付形式で入力してください`;
    default:
      return '';
  }
};

/**
 * 各バリデーション実施.
 * @param value
 * @param validation
 */
const validate = (value: string, validation: BaseValidationType): boolean => {
  switch (validation.type) {
    case E_VALIDATION_TYPE.REQUIRED:
      return validateRequired(value);
    case E_VALIDATION_TYPE.REQUIRED_NOT_ZERO:
      return validateRequiredNotZero(value);
    case E_VALIDATION_TYPE.NUMBER:
      return validateNumber(value);
    case E_VALIDATION_TYPE.NUMBER_HYPHEN:
      return validateNumberHyphen(value);
    case E_VALIDATION_TYPE.NUMBER_PLUS:
      return validateNumberPlus(value);
    case E_VALIDATION_TYPE.NUMBER_MAX:
      return validateNumberMax(value, (validation as MaxValidation).max, false);
    case E_VALIDATION_TYPE.NUMBER_RANGE:
      return validateNumberRange(
        value,
        (validation as RangeValidation).min,
        (validation as RangeValidation).max,
        false,
      );
    case E_VALIDATION_TYPE.MAX_DIGIT:
      return validateMaxDigit(
        value,
        (validation as DigitValidation).max,
        false,
      );
    case E_VALIDATION_TYPE.LENGTH:
      return validateLength(value, (validation as LengthValidation).maxLength);
    case E_VALIDATION_TYPE.PHONE:
      return validatePhoneRegex(value, false);
    case E_VALIDATION_TYPE.EMAIL:
      return validateMailRegex(value, false);
    case E_VALIDATION_TYPE.SIMPLE_PASSWORD:
      return validateSimplePassword(value);
    case E_VALIDATION_TYPE.DATE:
      return validateDate(value);
    default:
      throw new Error(`invalid validation.`);
  }
};

const validateRequired = (value: string) => {
  return value.trim() !== '';
};
const validateRequiredNotZero = (value: string) =>
  value.trim() !== '' && value.trim() !== '0';
const validateNumber = (value: string) => /^[-0-9]+$/.test(value);
const validateNumberHyphen = (value: string) =>
  value.trim() === '' || /^[0-9-]+$/.test(value);
const validateNumberPlus = (value: string) =>
  value.trim() === '' || /^[0-9]+$/.test(value);
const validateNumberMax = (value: string, max: number, isRequired: boolean) => {
  if (!isRequired && value === '') {
    return true;
  }
  return /^[+-]?\d+(?:\.\d+)?$/.test(value) && Number(value) <= max;
};
const validateNumberRange = (
  value: string,
  min: number,
  max: number,
  isRequired: boolean,
) => {
  if (!isRequired && value === '') {
    return true;
  }
  const number = Number(value);
  return number >= min && number <= max;
};
const validateMaxDigit = (value: string, max: number, isRequired: boolean) => {
  if (!isRequired && value === '') {
    return true;
  }
  return /^[-0-9]+$/.test(value) && value.length <= max;
};
const validateLength = (value: string, maxLength: number) =>
  count(value) <= maxLength;

const validatePhoneRegex = (value: string, isRequired: boolean) => {
  if (!isRequired && value === '') {
    return true;
  }

  return /^[0-9-]{10,13}$/.test(value);
};

const validateMailRegex = (value: string, isRequired: boolean) => {
  if (!isRequired && value === '') {
    return true;
  }

  return /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9.-]+\.[a-z]{2,13}$/.test(
    value,
  );
};

/**
 * SIMPLE_PASSWORD
 * 8文字以上の英数字(英文字・数字必ず1つ含む)
 * Password::min(8)->letters()->numbers()
 */
const validateSimplePassword = (value: string) => {
  // min(8)->letters()->numbers()
  return /^(?=.*?[a-zA-Z])(?=.*?\d)[a-zA-Z\d]{8,}$/.test(value);
};

/**
 * DATE
 * 有効な日付かどうか.
 */
const validateDate = (value: string) => {
  if (value === '') {
    return true;
  }
  const date = new Date(value);
  return !Number.isNaN(date.getDate());
};

/**
 * 改行コードを揃えた上で文字数カウントを行う.
 * @param value
 */
const count = (value: string): number => {
  return value.replaceAll('\r\n', '\n').length;
};

/**
 * サーバーのバリデーション結果をparseする.
 * @param key
 * @param result
 */
export const parseErrorMessage = (
  key: string,
  result: IMutationResult,
): ValidationResult => {
  if (result.errors === undefined) {
    return {
      hasError: false,
      errors: [],
      warnings: [],
    };
  }
  return {
    hasError: key in result.errors,
    errors: key in result.errors ? result.errors[key] : [],
    warnings: [],
  };
};
