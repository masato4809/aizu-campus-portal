import * as React from 'react';
import { IPropsBase } from '@/script/System/System';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { Box, Divider, Paper } from '@mui/material';
import { useIndexContext } from '@/script/Pages/ShuffleLunch/Index';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { getEventTimeZoneName } from '@/script/Enum/Server/App/ShuffleLunch/EEventTimeZone';
import { LabelValue } from '@/script/Pages/Common/LabelValue';
import { Avatar } from '@/script/Pages/Common/Avatar';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { E_COLOR } from '@/script/Enum/EColor';
import { router } from '@inertiajs/react';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { useProgressContext } from '@/script/Provider/ProgressProvider';

interface IProps extends IPropsBase {}
export const PersonalResult: React.FC<IProps> = ({ sx }) => {
  const { onStart, onFinish } = useProgressContext();
  const { trnShuffleLunchGroupList } = useIndexContext();
  const { authUser } = useCommonIndexContext();
  const groupList = trnShuffleLunchGroupList.findGroupListByTrnUserId(
    authUser.trnUser?.id ?? 0,
  );

  /**
   * rakumoブロックの実施.
   */
  const handleClickRakumo = () => {
    router.post(
      getPagesHref(E_PAGES.SHUFFLE_LUNCH__RAKUMO),
      {
        idList: groupList.map(v => v.id),
      },
      {
        onStart,
        onFinish,
      },
    );
  };

  /**
   * グループが見つからない場合は表示しない.
   */
  if (!groupList.length) {
    return (
      <Box
        sx={{
          ...sx,
        }}
      >
        <TypoH2>本日のマッチング結果 - 個人</TypoH2>
        <Paper
          sx={{
            marginTop: responsiveSpacing(4),
            padding: responsiveSpacing(4),
            display: 'flex',
            justifyContent: 'center',
          }}
        >
          <Box
            sx={{
              padding: responsiveSpacing(4),
              borderRadius: responsiveSpacing(4),
              display: 'flex',
              flexDirection: 'column',
              alignItems: 'center',
              background: E_COLOR.PRIMARY_LIGHT,
            }}
          >
            <TypoText>
              本日のマッチは未成立です、また後日ご参加ください
            </TypoText>
          </Box>
        </Paper>
      </Box>
    );
  }

  // グループのうちの一つ.
  const group = groupList[0];

  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <TypoH2>本日のマッチング結果 - 個人</TypoH2>
      <Paper
        sx={{
          marginTop: responsiveSpacing(2),
          padding: responsiveSpacing(4),
        }}
      >
        <LabelValue
          label={'開催時間'}
          value={getEventTimeZoneName(group.eventTimeZone)}
        />
        <LabelValue
          sx={{ marginTop: responsiveSpacing(2) }}
          label={'グループNo'}
          value={`${group.eventTimeZone}-${group.groupId}`}
        />
        <Box
          sx={{
            marginTop: responsiveSpacing(2),
            display: 'flex',
          }}
        >
          <TypoText
            sx={{
              minWidth: responsiveSize(200),
              width: responsiveSize(200),
            }}
          >
            参加メンバー
          </TypoText>
          <Box
            sx={{
              display: 'flex',
              flexWrap: 'wrap',
              gap: responsiveSpacing(2),
            }}
          >
            {groupList.map(v => {
              return <Avatar key={v.trnUserId} trnUser={v.trnUser} />;
            })}
          </Box>
        </Box>
        <Divider sx={{ marginTop: responsiveSize(6) }} />
        <Box
          sx={{
            display: 'flex',
            alignItems: 'center',
            marginTop: responsiveSize(6),
          }}
        >
          <TypoText>
            ※マッチング時の自動ブロックは未実装です、右のボタンをご利用ください
            <br />
            ※919.jpのアドレス保持者のみ有効です
            <br />
            ※参加者同士で声をかけあって出発してください
          </TypoText>
          <ButtonGeneral
            sx={{
              marginLeft: 'auto',
            }}
            label="rakumoブロック"
            onClick={handleClickRakumo}
            disabled={!!group.rakumoBlocked}
          />
        </Box>
      </Paper>
    </Box>
  );
};
