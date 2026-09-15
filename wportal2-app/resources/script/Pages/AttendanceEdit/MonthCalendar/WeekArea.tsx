import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import {
  E_WEEK_DAY,
  getWeekDayShortLabel,
} from '@/script/Enum/Server/App/EWeekDay';
import { E_COLOR } from '@/script/Enum/EColor';
import { TypoText } from '@/script/Component/Typography/TypoText';

interface IProps extends IPropsBase {}
export const WeekArea: React.FC<IProps> = ({ sx }) => {
  const weekDayList = Object.values(E_WEEK_DAY);

  return (
    <Box
      sx={{
        backgroundColor: E_COLOR.PRIMARY_MAIN,
        display: 'flex',
        height: '40px',
        borderRight: `1px solid ${E_COLOR.GREY}`,
        ...sx,
      }}
    >
      {weekDayList.map(weekDay => {
        return (
          <Box
            key={weekDay}
            sx={{
              width: 'calc(100% / 7)',
              borderTop: `1px solid ${E_COLOR.GREY}`,
              borderLeft: `1px solid ${E_COLOR.GREY}`,
            }}
            display="flex"
            justifyContent="center"
            alignItems="center"
          >
            <TypoText color={E_COLOR.WHITE} bold>
              {getWeekDayShortLabel(weekDay)}
            </TypoText>
          </Box>
        );
      })}
    </Box>
  );
};
