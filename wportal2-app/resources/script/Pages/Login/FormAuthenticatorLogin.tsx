import * as React from 'react';
import { Box, FormControl } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { E_COLOR } from '@/script/Enum/EColor';
import { InputField } from '@/script/Component/Form/InputField';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';

export interface IFormAuthenticatorLogin {
  email: string;
  password: string;
  code: string;
}
interface IProps extends IPropsBase {
  formAuthenticatorLogin: IFormAuthenticatorLogin;
  handleUpdate: (newValues: Partial<IFormAuthenticatorLogin>) => void;
  handleSubmit: () => void;
}
export const FormAuthenticatorLogin: React.FC<IProps> = ({
  sx,
  formAuthenticatorLogin,
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
        <TypoText>認証コードでログイン</TypoText>
        <TypoText bold color={E_COLOR.TEXT_RED}>
          ※事前設定が必要
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
          id="form-authenticator-login-email"
          inputValue={formAuthenticatorLogin.email}
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
          id="form-authenticator-login-password"
          type="password"
          inputValue={formAuthenticatorLogin.password}
          onChange={password => handleUpdate({ password })}
          visibleError
        />
      </Box>
      <Box sx={{ width: '100%' }}>
        <TypoText size="12px">6桁のコード</TypoText>
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
          id="form-authenticator-login-token"
          inputValue={formAuthenticatorLogin.code}
          onChange={code => handleUpdate({ code })}
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
        disabled={!formAuthenticatorLogin.email || !formAuthenticatorLogin.code}
      />
    </FormControl>
  );
};
