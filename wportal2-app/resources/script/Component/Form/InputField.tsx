import * as React from 'react';
import equal from 'fast-deep-equal/react';
import { InputFieldMemorized } from '@/script/Component/Form/InputFieldMemorized';
import {
  IInputFieldProps,
  InputFieldType,
} from '@/script/Component/Form/IInputFieldProps';

/**
 * テキストの更新の度に再作成しないようにメモ化する.
 */
const MemoComponent = React.memo<IInputFieldProps>(
  function MemoComponent({
    sx,
    id,
    inputValue,
    onChange,
    onBlur,
    errors,
    visibleError,
    placeHolder,
    type,
    autoComplete,
    disabled,
    fieldSx,
    inputProps,
  }) {
    return (
      <InputFieldMemorized
        sx={sx}
        id={id}
        inputValue={inputValue}
        onChange={onChange}
        onBlur={onBlur}
        errors={errors}
        visibleError={visibleError}
        placeHolder={placeHolder}
        type={type}
        autoComplete={autoComplete}
        disabled={disabled}
        fieldSx={fieldSx}
        inputProps={inputProps}
      />
    );
  },
  (prev, next) => equal(prev, next),
);

export const InputField: React.FC<IInputFieldProps> = ({
  sx,
  id,
  inputValue,
  onChange,
  onBlur,
  errors,
  visibleError,
  placeHolder = '入力してください',
  type = 'text' as InputFieldType,
  autoComplete = 'off',
  disabled,
  fieldSx,
  inputProps,
}) => {
  return (
    <MemoComponent
      sx={sx}
      id={id}
      inputValue={inputValue}
      onChange={onChange}
      onBlur={onBlur}
      errors={errors}
      visibleError={visibleError}
      placeHolder={placeHolder}
      type={type}
      autoComplete={autoComplete}
      disabled={disabled}
      fieldSx={fieldSx}
      inputProps={inputProps}
    />
  );
};
