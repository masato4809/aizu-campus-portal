import * as React from 'react';
import { Box, Divider, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IFormInputFormValidationResult } from '@/script/Pages/Sample/InputForm/FormInputFormValidation';
import { IFormInputForm } from '@/script/Pages/Sample/InputForm/FormInputForm';
import { TypoH3 } from '@/script/Component/Typography/TypoH3';
import { Number } from '@/script/Pages/Sample/InputForm/FormInputForm/InputSingle/Number';
import { NotZero } from '@/script/Pages/Sample/InputForm/FormInputForm/InputSingle/NotZero';
import { Range } from '@/script/Pages/Sample/InputForm/FormInputForm/InputSingle/Range';
import { Email } from '@/script/Pages/Sample/InputForm/FormInputForm/InputSingle/Email';

interface IProps extends IPropsBase {
  formInputForm: IFormInputForm;
  validation: IFormInputFormValidationResult;
  handleUpdate: (newValues: Partial<IFormInputForm>) => void;
}
export const InputSingle: React.FC<IProps> = ({
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
      <TypoH3>Input(single)</TypoH3>
      <Paper
        sx={{
          marginTop: '20px',
          padding: '10px',
        }}
      >
        <Number
          formInputForm={formInputForm}
          validation={validation}
          handleUpdate={handleUpdate}
        />
        <Divider
          sx={{
            margin: '20px 0px',
          }}
        />
        <NotZero
          formInputForm={formInputForm}
          validation={validation}
          handleUpdate={handleUpdate}
        />
        <Divider
          sx={{
            margin: '20px 0px',
          }}
        />
        <Range
          formInputForm={formInputForm}
          validation={validation}
          handleUpdate={handleUpdate}
        />
        <Divider
          sx={{
            margin: '20px 0px',
          }}
        />
        <Email
          formInputForm={formInputForm}
          validation={validation}
          handleUpdate={handleUpdate}
        />
      </Paper>
    </Box>
  );
};
