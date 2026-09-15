import * as React from 'react';
import { Box, Checkbox, FormControl, FormControlLabel } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { Image } from '@/script/Component/Misc/Image';
import { LabelValue } from '@/script/Pages/Common/LabelValue';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_ICON } from '@/script/Enum/EIcon';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { useIndexContext } from '@/script/Pages/User/Edit/Index';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { Closable } from '@/script/Component/Misc/Closable';
import { InputField } from '@/script/Component/Form/InputField';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

export interface IFormEdit {
  enableLegacyLogin: boolean;
  overwritePassword: string;
}

interface IProps extends IPropsBase {
  formEdit: IFormEdit;
  handleUpdate: (newValues: Partial<IFormEdit>) => void;
  handleSubmit: () => void;
}
export const FormEdit: React.FC<IProps> = ({
  sx,
  formEdit,
  handleUpdate,
  handleSubmit,
}) => {
  const { trnUser } = useIndexContext();

  return (
    <FormControl sx={sx}>
      <Box
        sx={{
          display: 'flex',
          gap: responsiveSpacing(4),
        }}
      >
        <Image
          sx={{
            width: responsiveSize(120),
            height: responsiveSize(120),
            borderRadius: responsiveSize(24),
          }}
          src={trnUser.faceImagePath}
        />
        <Box sx={{ width: '100%' }}>
          <LabelValue
            sx={{ marginTop: responsiveSpacing(2) }}
            labelSx={{
              width: responsiveSize(100),
              minWidth: responsiveSize(100),
            }}
            label="名前"
            value={String(trnUser?.authUser?.name)}
          />
          <LabelValue
            sx={{ marginTop: responsiveSpacing(2) }}
            labelSx={{
              width: responsiveSize(100),
              minWidth: responsiveSize(100),
            }}
            label="表示名"
            value={String(trnUser?.nickname)}
          />
          <LabelValue
            sx={{ marginTop: responsiveSpacing(2) }}
            labelSx={{
              width: responsiveSize(100),
              minWidth: responsiveSize(100),
            }}
            label="E-Mail"
            value={String(trnUser?.authUser?.email)}
          />
        </Box>
      </Box>
      <Box
        sx={{
          marginTop: responsiveSpacing(4),
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <Icon sx={{ marginRight: responsiveSize(5) }} icon={E_ICON.BUILD} />
        <TypoH2 bold>Slack情報</TypoH2>
      </Box>
      <LabelValue
        sx={{
          marginTop: responsiveSpacing(2),
        }}
        label="Slack User ID"
        value={trnUser.trnUserSlackProfile?.slackUserId ?? '設定なし'}
      />
      <LabelValue
        sx={{
          marginTop: responsiveSpacing(2),
        }}
        label="Slack User Name"
        value={trnUser.trnUserSlackProfile?.slackUserName ?? '設定なし'}
      />
      <LabelValue
        sx={{
          marginTop: responsiveSpacing(2),
        }}
        label="Slack Team ID"
        value={trnUser.trnUserSlackProfile?.slackTeamId ?? '設定なし'}
      />
      <Box
        sx={{
          marginTop: responsiveSpacing(4),
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <Icon sx={{ marginRight: responsiveSize(5) }} icon={E_ICON.BUILD} />
        <TypoH2 bold>管理者向け情報</TypoH2>
      </Box>
      <LabelValue
        sx={{
          marginTop: responsiveSpacing(2),
        }}
        label="Auth User ID"
        value={String(trnUser.authUser?.id ?? '')}
      />
      <Box
        sx={{
          marginTop: responsiveSpacing(2),
          display: 'flex',
        }}
      >
        <TypoText
          sx={{
            marginTop: responsiveSpacing(2),
            minWidth: responsiveSize(200),
            width: responsiveSize(200),
          }}
        >
          ログイン許可
        </TypoText>
        <Box>
          <Box sx={{ display: 'flex' }}>
            <FormControlLabel
              control={
                <Checkbox
                  checked={formEdit.enableLegacyLogin}
                  onChange={v =>
                    handleUpdate({ enableLegacyLogin: v.target.checked })
                  }
                />
              }
              label={<TypoText>レガシーログインを許可する</TypoText>}
            />
          </Box>
        </Box>
      </Box>
      <Closable open={formEdit.enableLegacyLogin}>
        <Box
          sx={{
            marginTop: responsiveSpacing(2),
            display: 'flex',
          }}
        >
          <TypoText
            sx={{
              marginTop: responsiveSpacing(2),
              minWidth: responsiveSize(200),
              width: responsiveSize(200),
            }}
          >
            上書きパスワード
          </TypoText>
          <InputField
            id="form-user-overwrite-password"
            inputValue={formEdit.overwritePassword}
            onChange={overwritePassword => handleUpdate({ overwritePassword })}
          />
        </Box>
      </Closable>
      <ButtonGeneral
        sx={{
          marginTop: responsiveSpacing(2),
          minWidth: responsiveSize(200),
          width: responsiveSize(200),
        }}
        label="更新"
        onClick={handleSubmit}
      />
    </FormControl>
  );
};
