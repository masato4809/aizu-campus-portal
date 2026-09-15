import * as React from 'react';
import { Box, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { Page } from '@/script/Pages/Page';
import { BackLink } from '@/script/Pages/Common/BackLink';
import {
  E_PAGES,
  getPagesHref,
  getPagesName,
} from '@/script/Enum/Server/App/EPages';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { useIndexContext } from '@/script/Pages/User/Show/Index';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { InertiaLink } from '@/script/Component/Misc/InertiaLink';
import { Image } from '@/script/Component/Misc/Image';
import { LabelValue } from '@/script/Pages/Common/LabelValue';
import { E_ICON } from '@/script/Enum/EIcon';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { Icon } from '@/script/Component/Misc/Icon';
import { Closable } from '@/script/Component/Misc/Closable';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { E_USER_AUTHORITY } from '@/script/Enum/Server/App/EUserAuthority';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';
import { DateTime } from '@/script/Common/DateTime';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { E_COLOR } from '@/script/Enum/EColor';

interface IProps extends IPropsBase {}
export const Show: React.FC<IProps> = () => {
  const { trnUserAuthorityList } = useCommonIndexContext();
  const { trnUser } = useIndexContext();

  // 生年月日の表示文字列.
  const birthDate = trnUser.birthDate
    ? DateTime.parseString(String(trnUser.birthDate)).toDateJP()
    : '';

  return (
    <Page>
      <BackLink current={E_PAGES.USER__SHOW} />
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'space-between',
        }}
      >
        <TypoH1>
          {getPagesName(E_PAGES.USER__SHOW)}[{trnUser.id}]
        </TypoH1>
        <Closable
          open={trnUserAuthorityList.hasAuthority([
            E_USER_AUTHORITY.ADMIN_PRIVILEGE,
          ])}
        >
          <InertiaLink
            href={getPagesHref(E_PAGES.USER__EDIT, [
              {
                target: '$userId',
                value: String(trnUser.id),
              },
            ])}
          >
            <ButtonGeneral
              sx={{
                minWidth: responsiveSize(140),
              }}
              label="編集"
            />
          </InertiaLink>
        </Closable>
      </Box>
      <Paper
        sx={{ marginTop: responsiveSpacing(4), padding: responsiveSpacing(4) }}
      >
        <Box
          sx={{
            display: 'flex',
            alignItems: 'center',
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
            <LabelValue
              sx={{ marginTop: responsiveSpacing(2) }}
              labelSx={{
                width: responsiveSize(100),
                minWidth: responsiveSize(100),
              }}
              label="生年月日"
              value={birthDate}
            />
          </Box>
        </Box>
        <Box
          sx={{
            marginTop: responsiveSpacing(4),
          }}
        >
          <TypoH2 bold>自己紹介</TypoH2>
          <Box
            sx={{
              marginTop: responsiveSpacing(2),
              borderRadius: responsiveSize(8),
              padding: responsiveSpacing(2),
              minHeight: responsiveSize(100),
              border: '1px solid #ddd',
            }}
          >
            <TypoText
              sx={{
                whiteSpace: 'pre-wrap',
                color: trnUser.selfIntroduction ? 'inherit' : `${E_COLOR.GREY}`,
              }}
            >
              {String(trnUser.selfIntroduction) || '何も入力されていません'}
            </TypoText>
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
        <Closable open>
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
          <LabelValue
            sx={{
              marginTop: responsiveSpacing(2),
            }}
            label="レガシーログインの許可"
            value={trnUser.authUser?.eEnableLegacyLogin ? 'Yes' : 'No'}
          />
        </Closable>
      </Paper>
    </Page>
  );
};
