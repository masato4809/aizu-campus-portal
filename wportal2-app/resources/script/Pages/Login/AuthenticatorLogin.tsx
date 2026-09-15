import * as React from 'react';
import { Box, Dialog, Paper } from '@mui/material';
import { router } from '@inertiajs/react';
import { useGoogleReCaptcha } from 'react-google-recaptcha-v3';
import { IPropsBase } from '@/script/System/System';
import { E_BUTTON_TYPE } from '@/script/Component/Button/EButtonType';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import {
  FormAuthenticatorLogin,
  IFormAuthenticatorLogin,
} from '@/script/Pages/Login/FormAuthenticatorLogin';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { useProgressContext } from '@/script/Provider/ProgressProvider';

interface IProps extends IPropsBase {}
export const AuthenticatorLogin: React.FC<IProps> = () => {
  const { onStart, onFinish } = useProgressContext();
  const { executeRecaptcha } = useGoogleReCaptcha();
  const [openDialog, setOpenDialog] = React.useState<boolean>(false);
  const [formAuthenticatorLogin, setFormAuthenticatorLogin] =
    React.useState<IFormAuthenticatorLogin>({
      email: '',
      password: '',
      code: '',
    });

  /**
   * 値の更新.
   */
  const handleUpdate = React.useCallback(
    (newValues: Partial<IFormAuthenticatorLogin>) => {
      setFormAuthenticatorLogin(prevState => ({ ...prevState, ...newValues }));
    },
    [setFormAuthenticatorLogin],
  );

  /**
   * 値の送信.
   */
  const handleSubmit = async () => {
    // recaptchaのtoken取得.
    if (!executeRecaptcha) {
      return;
    }
    const recaptchaToken = await executeRecaptcha('sp_login');

    // SPログイン処理.
    router.post(
      getPagesHref(E_PAGES.SP_LOGIN),
      {
        email: formAuthenticatorLogin.email,
        password: formAuthenticatorLogin.password,
        code: formAuthenticatorLogin.code,
        recaptchaToken,
      },
      {
        preserveState: false,
        onStart,
        onFinish,
      },
    );
  };

  return (
    <Box
      sx={{
        width: '100%',
        display: 'flex',
        justifyContent: 'center',
      }}
    >
      <Dialog
        open={openDialog}
        PaperProps={{
          style: {
            width: '100%',
          },
        }}
        onClose={() => setOpenDialog(false)}
      >
        <Paper
          sx={{
            padding: '20px',
          }}
        >
          <FormAuthenticatorLogin
            formAuthenticatorLogin={formAuthenticatorLogin}
            handleUpdate={handleUpdate}
            handleSubmit={handleSubmit}
          />
        </Paper>
      </Dialog>
      <ButtonGeneral
        sx={{
          width: '60%',
          height: '60px',
        }}
        buttonType={E_BUTTON_TYPE.OUTLINE_PRIMARY}
        label="スマホ版ログイン"
        onClick={() => setOpenDialog(true)}
      />
    </Box>
  );
};
