import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import {
  E_ATTENDANCE_STATE,
  EAttendanceState,
  getLabelAttendanceState,
} from '@/script/Enum/Server/App/EAttendanceState';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { SwitchPlace } from '@/script/Pages/Attendance/Action/SwitchPlace';
import { responsiveSize } from '@/script/System/Responsive';

interface IProps extends IPropsBase {
  handleClick: (state: EAttendanceState) => void;
}
export const ActionRest: React.FC<IProps> = ({ sx, handleClick }) => {
  return (
    <Box
      sx={{
        display: 'flex',
        justifyContent: 'space-between',
        ...sx,
      }}
    >
      <ButtonGeneral
        sx={{
          width: responsiveSize(220),
        }}
        label={getLabelAttendanceState(E_ATTENDANCE_STATE.END_BREAK)}
        onClick={() => handleClick(E_ATTENDANCE_STATE.END_BREAK)}
      />
      <SwitchPlace handleClick={handleClick} />
    </Box>
  );
};
