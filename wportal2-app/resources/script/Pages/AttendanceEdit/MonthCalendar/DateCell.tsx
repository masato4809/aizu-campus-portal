import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IAttendanceDate } from '@/script/Pages/AttendanceEdit/MonthCalendar/DateArea';
import { E_COLOR } from '@/script/Enum/EColor';
import { TypoText } from '@/script/Component/Typography/TypoText';

interface IProps extends IPropsBase {
  date: IAttendanceDate;
  handleSelect: (date: IAttendanceDate) => void;
}
export const DateCell: React.FC<IProps> = ({ sx, date, handleSelect }) => {
  const dateColor = date.inRange ? E_COLOR.BLACK : E_COLOR.GREY;

  return (
    <Box
      sx={{
        padding: '6px',
        minHeight: '100px',
        borderTop: `1px solid ${E_COLOR.GREY}`,
        borderLeft: `1px solid ${E_COLOR.GREY}`,
        '&:hover': {
          backgroundColor: date.inRange ? E_COLOR.PRIMARY_LIGHT : E_COLOR.WHITE,
        },
        ...sx,
      }}
      onClick={() => handleSelect(date)}
    >
      <TypoText color={dateColor} bold={date.inRange}>
        {date.dateTime.toFormatString('dd日')}
      </TypoText>
      {date.eventList?.map(event => {
        return (
          <TypoText key={event.uid} size="10px">
            {event.label}
          </TypoText>
        );
      })}
    </Box>
  );
};
