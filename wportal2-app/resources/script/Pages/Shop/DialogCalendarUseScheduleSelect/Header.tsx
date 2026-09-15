import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { getWeekDayShortLabel } from '@/script/Enum/Server/App/EWeekDay';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { E_COLOR } from '@/script/Enum/EColor';
import { DateTime } from '@/script/Common/DateTime';

interface IProps extends IPropsBase {
  dateList: DateTime[];
}
export const Header: React.FC<IProps> = ({ sx, dateList }) => {
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
      {dateList.map(date => {
        return (
          <Box
            key={date.getWeekDay()}
            sx={{
              width: '200px',
              borderTop: `1px solid ${E_COLOR.GREY}`,
              borderLeft: `1px solid ${E_COLOR.GREY}`,
              display: 'flex',
              flexDirection: 'column',
              alignItems: 'center',
            }}
          >
            <TypoText color={E_COLOR.WHITE}>
              {date.toFormatString('MM/dd')}
            </TypoText>
            <TypoText color={E_COLOR.WHITE} bold>
              {getWeekDayShortLabel(date.getWeekDay())}
            </TypoText>
          </Box>
        );
      })}
    </Box>
  );
};
