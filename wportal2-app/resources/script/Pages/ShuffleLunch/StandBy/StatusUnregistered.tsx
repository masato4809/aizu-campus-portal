import * as React from 'react';
import { Box, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { responsiveSpacing } from '@/script/System/Responsive';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import {
  E_EVENT_TIME_ZONE,
  EEventTimeZone,
  getEventTimeZoneName,
} from '@/script/Enum/Server/App/ShuffleLunch/EEventTimeZone';
import { router } from '@inertiajs/react';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { useProgressContext } from '@/script/Provider/ProgressProvider';

interface IProps extends IPropsBase {}
export const StatusUnregistered: React.FC<IProps> = () => {
  const { onStart, onFinish } = useProgressContext();

  // 表示するタイムゾーン.
  const timeZoneList = Object.values(E_EVENT_TIME_ZONE).filter(
    v => v !== E_EVENT_TIME_ZONE.INVALID,
  );

  /**
   * 参加表明ボタンを押した.
   * @param zone
   */
  const handleClick = (zone: EEventTimeZone): void => {
    router.post(
      getPagesHref(E_PAGES.SHUFFLE_LUNCH__REGISTER),
      {
        zone,
      },
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
        <TypoText>参加表明を行っていません</TypoText>
        {timeZoneList.map(zone => {
          return (
            <ButtonGeneral
              key={zone}
              label={`${getEventTimeZoneName(zone)}に参加表明する`}
              onClick={() => handleClick(zone)}
            />
          );
        })}
      </Box>
    </Paper>
  );
};
