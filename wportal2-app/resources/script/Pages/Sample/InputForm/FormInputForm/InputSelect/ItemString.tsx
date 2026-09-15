import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IFormInputForm } from '@/script/Pages/Sample/InputForm/FormInputForm';
import { IFormInputFormValidationResult } from '@/script/Pages/Sample/InputForm/FormInputFormValidation';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { E_COLOR } from '@/script/Enum/EColor';
import {
  IItemNumber,
  SelectItemNumber,
} from '@/script/Component/Form/SelectItemNumber';

interface IProps extends IPropsBase {
  formInputForm: IFormInputForm;
  validation: IFormInputFormValidationResult;
  handleUpdate: (newValues: Partial<IFormInputForm>) => void;
}
export const ItemString: React.FC<IProps> = ({
  sx,
  formInputForm,
  validation,
  handleUpdate,
}) => {
  // 適用バリデーション.
  const validationList: string[] = ['required_not_zero'];

  // リストに表示するアイテム.
  const list: IItemNumber[] = [
    {
      id: 0,
      label: '-----',
    },
    {
      id: 1,
      label: '文字列1',
    },
    {
      id: 2,
      label: '文字列2',
    },
  ];

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
      <SelectItemNumber
        sx={{
          marginTop: '10px',
        }}
        sxSelect={{
          width: '300px',
        }}
        value={formInputForm.inputSelectStringId}
        list={list}
        onChange={inputSelectStringId => handleUpdate({ inputSelectStringId })}
        errors={validation.data.inputSelectStringId?.errors}
        visibleError
      />
    </Box>
  );
};
