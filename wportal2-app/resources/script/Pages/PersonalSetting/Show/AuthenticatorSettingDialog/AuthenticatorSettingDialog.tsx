import * as React from 'react';
import { Dialog, Paper } from '@mui/material';
import { router } from '@inertiajs/react';
import { IPropsBase } from '@/script/System/System';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { ContentPassword } from '@/script/Pages/PersonalSetting/Show/AuthenticatorSettingDialog/ContentPassword';
import { ContentConfirmCode } from '@/script/Pages/PersonalSetting/Show/AuthenticatorSettingDialog/ContentConfirmCode';
import { ContentComplete } from '@/script/Pages/PersonalSetting/Show/AuthenticatorSettingDialog/ContentComplete';
import { responsiveSpacing } from '@/script/System/Responsive';

/**
 * ダイアログの状態管理.
 */
export const E_DIALOG_STATE = {
  SETTING_PASSWORD: 0,
  SETTING_QR: 1,
  SETTING_COMPLETE: 2,
} as const;
export type E_DIALOG_STATE =
  (typeof E_DIALOG_STATE)[keyof typeof E_DIALOG_STATE];

interface IProps extends IPropsBase {
  open: boolean;
}
export const AuthenticatorSettingDialog: React.FC<IProps> = ({ sx, open }) => {
  const { onStart, onFinish } = useProgressContext();
  const [dialogState, setDialogState] = React.useState<E_DIALOG_STATE>(
    E_DIALOG_STATE.SETTING_PASSWORD,
  );

  const [url, setUrl] = React.useState<string>('');

  /**
   * 処理を終了.
   */
  const handleClose = () => {
    router.visit(getPagesHref(E_PAGES.PERSONAL_SETTING__SHOW), {
      onStart,
      onFinish,
      preserveScroll: true,
      preserveState: false,
    });
  };

  /**
   * パスワードの送信の完了.
   * @param url
   */
  const handleCompletePassword = (url: string) => {
    setUrl(url);
    setDialogState(E_DIALOG_STATE.SETTING_QR);
  };

  /**
   * 2FAコードの送信完了.
   */
  const handleCompleteConfirmCode = () => {
    setDialogState(E_DIALOG_STATE.SETTING_COMPLETE);
  };

  /**
   * 表示コンテンツ.
   */
  const nodeContent = (): React.ReactNode => {
    switch (dialogState) {
      case E_DIALOG_STATE.SETTING_PASSWORD:
        return (
          <ContentPassword
            handleClose={handleClose}
            handleComplete={handleCompletePassword}
          />
        );
      case E_DIALOG_STATE.SETTING_QR:
        return (
          <ContentConfirmCode
            url={url}
            handleClose={handleClose}
            handleComplete={handleCompleteConfirmCode}
          />
        );
      case E_DIALOG_STATE.SETTING_COMPLETE:
        return <ContentComplete handleComplete={handleClose} />;
      default:
        return null;
    }
  };

  return (
    <Dialog
      sx={{
        ...sx,
      }}
      PaperProps={{
        style: {
          width: '100%',
          maxWidth: '600px',
        },
      }}
      open={open}
    >
      <Paper
        sx={{
          padding: responsiveSpacing(4),
        }}
      >
        {nodeContent()}
      </Paper>
    </Dialog>
  );
};
