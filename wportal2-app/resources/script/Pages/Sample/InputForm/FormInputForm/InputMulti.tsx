import * as React from 'react';
import { Box, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IFormInputFormValidationResult } from '@/script/Pages/Sample/InputForm/FormInputFormValidation';
import { IFormInputForm } from '@/script/Pages/Sample/InputForm/FormInputForm';
import { TypoH3 } from '@/script/Component/Typography/TypoH3';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { InputFieldMultiline } from '@/script/Component/Form/InputFieldMultiline';
import { E_COLOR } from '@/script/Enum/EColor';

interface IProps extends IPropsBase {
  formInputForm: IFormInputForm;
  validation: IFormInputFormValidationResult;
  handleUpdate: (newValues: Partial<IFormInputForm>) => void;
}
export const InputMulti: React.FC<IProps> = ({
  sx,
  formInputForm,
  validation,
  handleUpdate,
}) => {
  // 適用バリデーション.
  const validationList: string[] = ['required', 'length:100'];

  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <TypoH3>Input(multi)</TypoH3>
      <Paper
        sx={{
          marginTop: '20px',
          padding: '10px',
        }}
      >
        <Box
          sx={{
            display: 'flex',
            gap: '5px',
            alignItems: 'center',
          }}
        >
          <TypoText>適用バリデーション</TypoText>
          {validationList.map(type => {
            return (
              <Box
                key={type}
                sx={{
                  backgroundColor: E_COLOR.RED_LIGHT,
                  padding: '5px',
                }}
              >
                {type}
              </Box>
            );
          })}
        </Box>
        <InputFieldMultiline
          sx={{
            marginTop: '10px',
          }}
          fieldSx={{
            width: '100%',
          }}
          inputProps={{
            sx: {
              width: '100%',
              height: '100%',
              resize: 'vertical',
            },
          }}
          rows={5}
          inputValue={formInputForm.inputMulti}
          onChange={inputMulti => handleUpdate({ inputMulti })}
          placeHolder="入力してください"
          errors={validation.data.inputMulti?.errors}
          visibleError
        />
      </Paper>
    </Box>
  );
};
