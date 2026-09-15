import * as React from 'react';
import { Box, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { responsiveSpacing } from '@/script/System/Responsive';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { useIndexContext } from '@/script/Pages/ShuffleLunch/Index';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import {
  E_EVENT_TIME_ZONE,
  getEventTimeZoneName,
} from '@/script/Enum/Server/App/ShuffleLunch/EEventTimeZone';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { E_COLOR } from '@/script/Enum/EColor';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { router } from '@inertiajs/react';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';

interface IProps extends IPropsBase {}
export const StatusRegistered: React.FC<IProps> = () => {
  const { onStart, onFinish } = useProgressContext();
  const { trnShuffleLunchEntryList } = useIndexContext();
  const { authUser } = useCommonIndexContext();

  // 登録済みのゾーン.
  const registeredZone =
    trnShuffleLunchEntryList
      .list()
      .find(v => v.trnUserId === authUser.trnUser?.id)?.eventTimeZone ??
    E_EVENT_TIME_ZONE.INVALID;

  /**
   * キャンセル処理.
   */
  const handleClickCancel = () => {
    router.post(
      getPagesHref(E_PAGES.SHUFFLE_LUNCH__CANCEL),
      {},
      {
        onStart,
        onFinish,
      },
    );
  };

  return (
    <Paper
      sx={{
        marginTop: responsiveSpacing(4),
        padding: responsiveSpacing(4),
      }}
    >
      <TypoH2>あなたの状態</TypoH2>
      <Box
        sx={{
          marginTop: responsiveSpacing(4),
          display: 'flex',
          flexDirection: 'column',
          alignItems: 'center',
          gap: responsiveSpacing(4),
        }}
      >
        <Box
          sx={{
            padding: responsiveSpacing(2),
            borderRadius: responsiveSpacing(2),
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
            background: E_COLOR.PRIMARY_LIGHT,
          }}
        >
          <TypoText>
            [{getEventTimeZoneName(registeredZone)}
            ]に参加表明済みです、マッチングをお待ちください
          </TypoText>
        </Box>
        <ButtonGeneral label="キャンセルする" onClick={handleClickCancel} />
      </Box>
    </Paper>
  );
};
