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
export const ActionWorking: React.FC<IProps> = ({ sx, handleClick }) => {
  return (
    <Box sx={sx}>
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'space-between',
        }}
      >
        <ButtonGeneral
          sx={{ width: responsiveSize(220) }}
          label={getLabelAttendanceState(E_ATTENDANCE_STATE.BEGIN_BREAK)}
          onClick={() => handleClick(E_ATTENDANCE_STATE.BEGIN_BREAK)}
        />
        <SwitchPlace handleClick={handleClick} />
      </Box>
      <Box>
        <Box
          sx={{
            marginTop: '40px',
            display: 'flex',
            justifyContent: 'flex-end',
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
      </Box>
    </Box>
  );
};
