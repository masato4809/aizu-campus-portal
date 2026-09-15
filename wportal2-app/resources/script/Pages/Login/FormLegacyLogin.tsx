import * as React from 'react';
import { Box, FormControl } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { InputField } from '@/script/Component/Form/InputField';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { E_COLOR } from '@/script/Enum/EColor';

export interface IFormLegacyLogin {
  email: string;
  password: string;
}
interface IProps extends IPropsBase {
  formLegacyLogin: IFormLegacyLogin;
  handleUpdate: (newValues: Partial<IFormLegacyLogin>) => void;
  handleSubmit: () => void;
}
export const FormLegacyLogin: React.FC<IProps> = ({
  sx,
  formLegacyLogin,
  handleUpdate,
  handleSubmit,
}) => {
  return (
    <FormControl
      sx={{
        width: '100%',
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        gap: '20px',
        ...sx,
      }}
      component="form"
    >
      <Box>
        <TypoText>ワンタイムパスワードでログイン</TypoText>
        <TypoText bold color={E_COLOR.TEXT_RED}>
          ※gmail以外のアドレス
        </TypoText>
      </Box>
      <Box sx={{ width: '100%' }}>
        <TypoText size="12px">メールアドレス</TypoText>
        <InputField
          sx={{
            width: '100%',
          }}
          fieldSx={{
            width: '100%',
          }}
          inputProps={{
            sx: {
              width: '100%',
              padding: '2px',
            },
          }}
          id="form-legacy-login-email"
          inputValue={formLegacyLogin.email}
          onChange={email => handleUpdate({ email })}
          visibleError
        />
      </Box>
      <Box sx={{ width: '100%' }}>
        <TypoText size="12px">パスワード</TypoText>
        <InputField
          sx={{
            width: '100%',
          }}
          fieldSx={{
            width: '100%',
          }}
          inputProps={{
            sx: {
              width: '100%',
              padding: '2px',
            },
          }}
          id="form-legacy-login-password"
          type="password"
          inputValue={formLegacyLogin.password}
          onChange={password => handleUpdate({ password })}
          visibleError
        />
      </Box>
      <ButtonGeneral
        sx={{
          width: '80%',
          height: '60px',
          padding: '2px 5px',
        }}
        label="ログイン"
        onClick={handleSubmit}
        disabled={!formLegacyLogin.email || !formLegacyLogin.password}
      />
    </FormControl>
  );
};
