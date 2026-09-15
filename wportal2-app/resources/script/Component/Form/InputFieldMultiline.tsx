import * as React from 'react';
import equal from 'fast-deep-equal/react';
import { InputFieldMultilineMemorized } from '@/script/Component/Form/InputFieldMultilineMemorized';
import {
  IInputFieldMultilineProps,
  InputFieldMultilineType,
} from '@/script/Component/Form/IInputFieldMutilineProps';

/**
 * テキストの更新の度に再作成しないようにメモ化する.
 */
const MemoComponent = React.memo<IInputFieldMultilineProps>(
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
    rows,
    autoComplete,
    disabled,
    fieldSx,
    inputProps,
  }) {
    return (
      <InputFieldMultilineMemorized
        sx={sx}
        id={id}
        inputValue={inputValue}
        onChange={onChange}
        onBlur={onBlur}
        errors={errors}
        visibleError={visibleError}
        placeHolder={placeHolder}
        type={type}
        rows={rows}
        autoComplete={autoComplete}
        disabled={disabled}
        fieldSx={fieldSx}
        inputProps={inputProps}
      />
    );
  },
  (prev, next) => equal(prev, next),
);

export const InputFieldMultiline: React.FC<IInputFieldMultilineProps> = ({
  sx,
  id,
  inputValue,
  onChange,
  onBlur,
  errors,
  visibleError,
  placeHolder,
  type = 'text' as InputFieldMultilineType,
  rows = 1,
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
      rows={rows}
      autoComplete={autoComplete}
      disabled={disabled}
      fieldSx={fieldSx}
      inputProps={inputProps}
    />
  );
};
