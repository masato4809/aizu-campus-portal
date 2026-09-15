import * as React from 'react';
import { Box, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IFormInputForm } from '@/script/Pages/Sample/InputForm/FormInputForm';
import { IFormInputFormValidationResult } from '@/script/Pages/Sample/InputForm/FormInputFormValidation';
import { TypoH3 } from '@/script/Component/Typography/TypoH3';
import { ItemString } from '@/script/Pages/Sample/InputForm/FormInputForm/InputSelect/ItemString';

interface IProps extends IPropsBase {
  formInputForm: IFormInputForm;
  validation: IFormInputFormValidationResult;
  handleUpdate: (newValues: Partial<IFormInputForm>) => void;
}
export const InputSelect: React.FC<IProps> = ({
  sx,
  formInputForm,
  validation,
  handleUpdate,
}) => {
  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <TypoH3>Input(select)</TypoH3>
      <Paper
        sx={{
          marginTop: '20px',
          padding: '10px',
        }}
      >
        <ItemString
          formInputForm={formInputForm}
          validation={validation}
          handleUpdate={handleUpdate}
        />
      </Paper>
    </Box>
  );
};
