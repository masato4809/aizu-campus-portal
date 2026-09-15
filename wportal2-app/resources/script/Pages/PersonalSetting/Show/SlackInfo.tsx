import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { LabelValue } from '@/script/Pages/Common/LabelValue';
import { Str } from '@/script/Common/Str';
import { IAppTrnUserSlackProfile } from '@/script/Models/App/Trn/TrnUserSlackProfileList';
import { E_COLOR } from '@/script/Enum/EColor';
import { responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {
  trnUserSlackProfile: IAppTrnUserSlackProfile;
}
export const SlackInfo: React.FC<IProps> = ({ sx, trnUserSlackProfile }) => {
  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <TypoH2 bold>Slack情報</TypoH2>
      <Box
        sx={{
          width: '100%',
          marginTop: responsiveSpacing(2),
          display: 'flex',
          flexDirection: 'column',
          gap: responsiveSpacing(2),
        }}
      >
        <LabelValue
          label="Slack User ID"
          value={Str.exceptEmpty(trnUserSlackProfile.slackUserId) ?? '設定なし'}
          valueSx={{
            color: Str.exceptEmpty(trnUserSlackProfile.slackUserId)
              ? 'inherit'
              : `${E_COLOR.GREY}`,
          }}
        />
        <LabelValue
          label="Slack User Name"
          value={
            Str.exceptEmpty(trnUserSlackProfile.slackUserName) ?? '設定なし'
          }
          valueSx={{
            color: Str.exceptEmpty(trnUserSlackProfile.slackUserName)
              ? 'inherit'
              : `${E_COLOR.GREY}`,
          }}
        />
        <LabelValue
          label="Slack Team ID"
          value={Str.exceptEmpty(trnUserSlackProfile.slackTeamId) ?? '設定なし'}
          valueSx={{
            color: Str.exceptEmpty(trnUserSlackProfile.slackTeamId)
              ? 'inherit'
              : `${E_COLOR.GREY}`,
          }}
        />
      </Box>
    </Box>
  );
};
