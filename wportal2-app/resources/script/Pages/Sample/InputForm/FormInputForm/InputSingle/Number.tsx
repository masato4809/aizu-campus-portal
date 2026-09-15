import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IFormInputFormValidationResult } from '@/script/Pages/Sample/InputForm/FormInputFormValidation';
import { IFormInputForm } from '@/script/Pages/Sample/InputForm/FormInputForm';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { InputField } from '@/script/Component/Form/InputField';
import { E_COLOR } from '@/script/Enum/EColor';

interface IProps extends IPropsBase {
  formInputForm: IFormInputForm;
  validation: IFormInputFormValidationResult;
  handleUpdate: (newValues: Partial<IFormInputForm>) => void;
}
export const Number: React.FC<IProps> = ({
  sx,
  formInputForm,
  validation,
  handleUpdate,
}) => {
  // 適用バリデーション.
  const validationList: string[] = ['required', 'number'];

  return (
    <Box
      sx={{
        ...sx,
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
      <InputField
        sx={{
          marginTop: '10px',
        }}
        inputProps={{
          sx: {
            width: '400px',
          },
        }}
        inputValue={formInputForm.inputSingleNumber}
        onChange={inputSingleNumber => handleUpdate({ inputSingleNumber })}
        placeHolder="入力してください"
        errors={validation.data.inputSingleNumber?.errors}
        visibleError
      />
    </Box>
  );
};
