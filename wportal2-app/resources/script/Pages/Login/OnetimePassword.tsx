import * as React from 'react';
import { Box, FormControl, Paper } from '@mui/material';
import { router } from '@inertiajs/react';
import { IPropsBase } from '@/script/System/System';
import { E_COLOR } from '@/script/Enum/EColor';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { InputField } from '@/script/Component/Form/InputField';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { useIndexContext } from '@/script/Pages/Login/OnetimePasswordIndex';

interface IProps extends IPropsBase {}
export const OnetimePassword: React.FC<IProps> = () => {
  const { onStart, onFinish } = useProgressContext();
  const { authId, email } = useIndexContext();
  const [onetimePassword, setOnetimePassword] = React.useState<string>('');

  /**
   * ワンタイムパスワードを送信.
   */
  const handleSubmit = () => {
    // TODO: バリデーションが必要であれば.

    // 送信処理.
    router.post(
      getPagesHref(E_PAGES.ONETIME_PASSWORD_LOGIN),
      {
        authId,
        email,
        onetimePassword,
      },
      {
        onStart,
        onFinish,
      },
    );
  };

  return (
    <Box
      sx={{
        backgroundColor: E_COLOR.GREY_LIGHT,
        height: '100vh',
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        justifyContent: 'center',
        gap: '40px',
      }}
    >
      <Paper
        sx={{
          padding: '20px',
          width: {
            xs: '90%',
            sm: '375px',
          },
          display: 'flex',
          flexDirection: 'column',
          alignItems: 'center',
          gap: '20px',
        }}
      >
        <TypoH2>ワンタイムパスワードの入力</TypoH2>
        <FormControl
          sx={{
            width: '100%',
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
            gap: '10px',
          }}
          component="form"
        >
          <InputField
            sx={{
              width: '50%',
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
            id="form-onetime-password"
            inputValue={onetimePassword}
            onChange={onetimePassword => setOnetimePassword(onetimePassword)}
            visibleError
          />
          <ButtonGeneral
            sx={{
              minWidth: '200px',
              padding: '2px 5px',
            }}
            label="ログイン"
            onClick={handleSubmit}
            disabled={!onetimePassword}
          />
        </FormControl>
      </Paper>
    </Box>
  );
};
