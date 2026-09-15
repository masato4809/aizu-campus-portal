import * as React from 'react';
import { Box, TextField } from '@mui/material';
import { Closable } from '@/script/Component/Misc/Closable';
import { InputError } from '@/script/Component/Form/InputError';
import { E_COLOR } from '@/script/Enum/EColor';
import { IInputFieldMultilineProps } from '@/script/Component/Form/IInputFieldMutilineProps';
import { responsiveSize } from '@/script/System/Responsive';

export const InputFieldMultilineMemorized: React.FC<
  IInputFieldMultilineProps
> = ({
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
}) => {
  const [isEdited, setIsEdited] = React.useState<boolean>(false);

  /**
   * ロード時に編集フラグを落とす.
   */
  React.useEffect(() => {
    setIsEdited(false);
  }, [errors]);

  /**
   * 背景色の取得.
   */
  const getBackgroundColor = (): string => {
    if (errors === undefined || !errors.length) {
      return E_COLOR.WHITE;
    }
    return !isEdited ? E_COLOR.RED_LIGHT : E_COLOR.WHITE;
  };

  /**
   * 入力値変更の検知.
   */
  const handleChange = (value: string) => {
    setIsEdited(true);
    onChange(value);
  };

  /**
   * Blur（フォーカスアウト）の検知
   */
  const handleBlur = (value: string) => {
    if (onBlur) {
      onBlur(value);
    }
  };

  return (
    <Box
      sx={{
        '& .MuiOutlinedInput-root': {
          padding: responsiveSize(4),
          lineHeight: 'normal',
        },
        ...sx,
      }}
    >
      <TextField
        id={id}
        sx={{
          backgroundColor: getBackgroundColor(),

          '& .MuiInputBase-input.Mui-disabled': {
            WebkitTextFillColor: E_COLOR.GREY,
          },

          ...fieldSx,
        }}
        inputProps={{
          ...inputProps,
          sx: {
            padding: responsiveSize(6),
            ...inputProps?.sx,
          },
        }}
        variant="outlined"
        value={inputValue}
        onChange={v => {
          handleChange(v.target.value);
        }}
        onBlur={v => {
          handleBlur(v.target.value);
        }}
        placeholder={placeHolder}
        type={type}
        multiline
        rows={rows}
        autoComplete={autoComplete}
        disabled={disabled}
      />
      <Closable open={visibleError}>
        <InputError sx={{ marginLeft: '0' }} errors={errors} />
      </Closable>
    </Box>
  );
};
