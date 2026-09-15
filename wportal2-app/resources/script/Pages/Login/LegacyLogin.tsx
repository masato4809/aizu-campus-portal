import * as React from 'react';
import { router } from '@inertiajs/react';
import { Box, Dialog, Paper } from '@mui/material';
import { useGoogleReCaptcha } from 'react-google-recaptcha-v3';
import { IPropsBase } from '@/script/System/System';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { E_BUTTON_TYPE } from '@/script/Component/Button/EButtonType';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import {
  FormLegacyLogin,
  IFormLegacyLogin,
} from '@/script/Pages/Login/FormLegacyLogin';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';

interface IProps extends IPropsBase {}
export const LegacyLogin: React.FC<IProps> = () => {
  const [openDialog, setOpenDialog] = React.useState<boolean>(false);
  const { onStart, onFinish } = useProgressContext();
  const { executeRecaptcha } = useGoogleReCaptcha();
  const [formLegacyLogin, setFormLegacyLogin] =
    React.useState<IFormLegacyLogin>({
      email: '',
      password: '',
    });

  /**
   * 値の更新.
   */
  const handleUpdate = React.useCallback(
    (newValues: Partial<IFormLegacyLogin>) => {
      setFormLegacyLogin(prevState => ({ ...prevState, ...newValues }));
    },
    [setFormLegacyLogin],
  );

  /**
   * 値の送信.
   */
  const handleSubmit = async () => {
    // recaptchaのtoken取得.
    if (!executeRecaptcha) {
      return;
    }
    const recaptchaToken = await executeRecaptcha('guest_login');

    // 更新処理の実装.
    router.visit(getPagesHref(E_PAGES.LEGACY_LOGIN), {
      method: 'post',
      preserveState: true,
      preserveScroll: true,
      data: {
        ...formLegacyLogin,
        recaptchaToken,
      },
      onStart,
      onFinish,
    });
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
          <FormLegacyLogin
            formLegacyLogin={formLegacyLogin}
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
        label="gmail以外のログイン"
        onClick={() => setOpenDialog(true)}
      />
    </Box>
  );
};
