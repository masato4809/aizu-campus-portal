import * as React from 'react';
import { IPropsBase } from '@/script/System/System';
import { Box } from '@mui/material';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { responsiveSpacing } from '@/script/System/Responsive';
import {
  E_EVENT_TIME_ZONE,
  getEventTimeZoneName,
} from '@/script/Enum/Server/App/ShuffleLunch/EEventTimeZone';
import { TypoH3 } from '@/script/Component/Typography/TypoH3';
import { EventTimezoneResult } from '@/script/Pages/ShuffleLunch/Established/EventTimezoneResult';

interface IProps extends IPropsBase {}
export const EstablishedResult: React.FC<IProps> = ({ sx }) => {
  // 表示するタイムゾーン.
  const timeZoneList = Object.values(E_EVENT_TIME_ZONE).filter(
    v => v !== E_EVENT_TIME_ZONE.INVALID,
  );

  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <TypoH2>本日のマッチング結果 - 全体</TypoH2>
      {timeZoneList.map(zone => {
        return (
          <Box
            key={zone}
            sx={{
              marginTop: responsiveSpacing(4),
            }}
          >
            <TypoH3>{getEventTimeZoneName(zone)}</TypoH3>
            <EventTimezoneResult
              sx={{ marginTop: responsiveSpacing(2) }}
              eventTimeZone={zone}
            />
          </Box>
        );
      })}
    </Box>
  );
};
