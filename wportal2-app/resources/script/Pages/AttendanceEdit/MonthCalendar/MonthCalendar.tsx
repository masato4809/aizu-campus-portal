import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { Header } from '@/script/Pages/AttendanceEdit/MonthCalendar/Header';
import { E_COLOR } from '@/script/Enum/EColor';
import { WeekArea } from '@/script/Pages/AttendanceEdit/MonthCalendar/WeekArea';
import { DateArea } from '@/script/Pages/AttendanceEdit/MonthCalendar/DateArea';

interface IProps extends IPropsBase {}
export const MonthCalendar: React.FC<IProps> = ({ sx }) => {
  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <Header />
      <Box
        sx={{
          borderBottom: `1px solid ${E_COLOR.GREY}`,
        }}
      >
        <WeekArea />
        <DateArea />
      </Box>
    </Box>
  );
};
