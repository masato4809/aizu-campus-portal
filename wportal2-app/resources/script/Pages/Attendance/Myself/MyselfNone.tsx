import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { E_ICON } from '@/script/Enum/EIcon';
import { Status } from '@/script/Pages/Attendance/Myself/Status';

interface IProps extends IPropsBase {}
export const MyselfNone: React.FC<IProps> = ({ sx }) => {
  return (
    <Box
      sx={{
        display: 'flex',
        justifyContent: 'center',
        ...sx,
      }}
    >
      <Status icon={E_ICON.INVALID} label="現在は出勤していません" />
    </Box>
  );
};
