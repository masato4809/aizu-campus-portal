import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import {
  E_ATTENDANCE_STATE,
  EAttendanceState,
  getLabelAttendanceState,
} from '@/script/Enum/Server/App/EAttendanceState';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_ICON } from '@/script/Enum/EIcon';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {
  handleClick: (state: EAttendanceState) => void;
}
export const ActionNone: React.FC<IProps> = ({ sx, handleClick }) => {
  return (
    <Box
      sx={{
        display: 'flex',
        flexFlow: 'column',
        rowGap: responsiveSpacing(4),
        ...sx,
      }}
    >
      <ButtonGeneral
        sx={{
          width: responsiveSize(220),
        }}
        startIcon={<Icon icon={E_ICON.OFFICE} />}
        label={getLabelAttendanceState(E_ATTENDANCE_STATE.ATTENDANCE_OFFICE)}
        onClick={() => handleClick(E_ATTENDANCE_STATE.ATTENDANCE_OFFICE)}
      />
      <ButtonGeneral
        sx={{
          width: responsiveSize(220),
        }}
        startIcon={<Icon icon={E_ICON.HOME} />}
        label={getLabelAttendanceState(E_ATTENDANCE_STATE.ATTENDANCE_TELEWORK)}
        onClick={() => handleClick(E_ATTENDANCE_STATE.ATTENDANCE_TELEWORK)}
      />
    </Box>
  );
};
