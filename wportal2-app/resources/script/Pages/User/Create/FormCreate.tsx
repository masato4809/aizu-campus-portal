import * as React from 'react';
import { Box, FormControl } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { InputField } from '@/script/Component/Form/InputField';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { IFormCreateValidationResult } from '@/script/Pages/User/Create/FormCreateValidation';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

export interface IFormCreate {
  name: string;
  nickname: string;
  email: string;
}

interface IProps extends IPropsBase {
  formCreate: IFormCreate;
  validation: IFormCreateValidationResult;
  handleUpdate: (newValues: Partial<IFormCreate>) => void;
  handleSubmit: () => void;
}
export const FormCreate: React.FC<IProps> = ({
  sx,
  formCreate,
  validation,
  handleUpdate,
  handleSubmit,
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
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>名前</TypoText>
        <InputField
          id="form-user-create-name"
          inputValue={formCreate.name}
          onChange={name => handleUpdate({ name })}
          visibleError
          errors={validation.data.name?.errors}
        />
      </Box>
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>表示名</TypoText>
        <InputField
          id="form-user-create-nickname"
          inputValue={formCreate.nickname}
          onChange={nickname => handleUpdate({ nickname })}
          visibleError
          errors={validation.data.nickname?.errors}
        />
      </Box>
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>E-Mail</TypoText>
        <InputField
          id="form-user-create-email"
          inputValue={formCreate.email}
          onChange={email => handleUpdate({ email })}
          visibleError
          errors={validation.data.email?.errors}
        />
      </Box>
      <ButtonGeneral
        sx={{
          minWidth: responsiveSize(200),
        }}
        label="登録"
        onClick={handleSubmit}
      />
    </FormControl>
  );
};
