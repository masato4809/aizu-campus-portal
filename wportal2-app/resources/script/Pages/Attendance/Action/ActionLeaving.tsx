import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import {
  E_ATTENDANCE_STATE,
  EAttendanceState,
  getLabelAttendanceState,
} from '@/script/Enum/Server/App/EAttendanceState';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { responsiveSize } from '@/script/System/Responsive';

interface IProps extends IPropsBase {
  handleClick: (state: EAttendanceState) => void;
}
export const ActionLeaving: React.FC<IProps> = ({ sx, handleClick }) => {
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
        label={getLabelAttendanceState(E_ATTENDANCE_STATE.LEAVING)}
        onClick={() => handleClick(E_ATTENDANCE_STATE.LEAVING)}
      />
    </Box>
  );
};
