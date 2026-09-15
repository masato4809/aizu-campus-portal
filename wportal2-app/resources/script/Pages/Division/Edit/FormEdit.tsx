import * as React from 'react';
import { Box, FormControl } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { LabelValue } from '@/script/Pages/Common/LabelValue';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { InputField } from '@/script/Component/Form/InputField';
import { IFormEditValidationResult } from '@/script/Pages/Division/Edit/FormEditValidation';
import { InputFieldMultiline } from '@/script/Component/Form/InputFieldMultiline';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

export interface IFormEdit {
  id: number;
  name: string;
  explain: string;
}

interface IProps extends IPropsBase {
  formEdit: IFormEdit;
  validation: IFormEditValidationResult;
  handleUpdate: (newValues: Partial<IFormEdit>) => void;
}
export const FormEdit: React.FC<IProps> = ({
  sx,
  formEdit,
  validation,
  handleUpdate,
}) => {
  return (
    <FormControl
      sx={{
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'start',
        gap: responsiveSpacing(4),
        ...sx,
      }}
    >
      <LabelValue label="id" value={String(formEdit.id)} />
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>名前</TypoText>
        <InputField
          id="form-division-name"
          inputValue={formEdit.name}
          onChange={name => handleUpdate({ name })}
          visibleError
          errors={validation.data.name?.errors}
        />
      </Box>
      <Box
        sx={{
          width: '100%',
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>説明</TypoText>
        <InputFieldMultiline
          sx={{
            width: '100%',
          }}
          id="form-division-explain"
          inputValue={formEdit.explain}
          fieldSx={{
            width: '100%',
          }}
          inputProps={{
            sx: {
              width: '100%',
              resize: 'vertical',
            },
          }}
          onChange={explain => handleUpdate({ explain })}
          visibleError
          errors={validation.data.explain?.errors}
          rows={5}
        />
      </Box>
    </FormControl>
  );
};
