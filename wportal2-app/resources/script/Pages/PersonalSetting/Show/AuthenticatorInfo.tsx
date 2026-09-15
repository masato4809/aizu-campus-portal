import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { LabelValue } from '@/script/Pages/Common/LabelValue';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { Closable } from '@/script/Component/Misc/Closable';
import { AuthenticatorSettingDialog } from '@/script/Pages/PersonalSetting/Show/AuthenticatorSettingDialog/AuthenticatorSettingDialog';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const AuthenticatorInfo: React.FC<IProps> = ({ sx }) => {
  const { authUser } = useCommonIndexContext();
  const [openDialog, setOpenDialog] = React.useState<boolean>(false);
  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <Closable open={openDialog}>
        <AuthenticatorSettingDialog open={openDialog} />
      </Closable>
      <TypoH2 bold>Authenticator情報</TypoH2>
      <Box
        sx={{
          width: '100%',
          marginTop: responsiveSpacing(2),
          display: 'flex',
          flexDirection: 'column',
          gap: responsiveSpacing(2),
        }}
      >
        <Box
          sx={{
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            gap: responsiveSpacing(4),
          }}
        >
          <LabelValue
            sx={{
              width: '100%',
            }}
            label="Google Authenticator"
            value={authUser.eEnableSpLogin ? 'ログイン可' : 'ログイン不可'}
          />
          <ButtonGeneral
            sx={{
              minWidth: responsiveSize(100),
            }}
            label="設定する"
            onClick={() => setOpenDialog(true)}
          />
        </Box>
        <LabelValue
          label="認証失敗回数"
          value={
            authUser.eEnableSpLogin ? String(authUser.spLoginFailedCount) : '-'
          }
        />
      </Box>
    </Box>
  );
};
