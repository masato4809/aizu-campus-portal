import * as React from 'react';
import { Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { MonthCalendar } from '@/script/Pages/AttendanceEdit/MonthCalendar/MonthCalendar';

interface IProps extends IPropsBase {}
export const Calendar: React.FC<IProps> = ({ sx }) => {
  return (
    <Paper
      sx={{
        padding: '20px',
        ...sx,
      }}
    >
      <MonthCalendar />
    </Paper>
  );
};
