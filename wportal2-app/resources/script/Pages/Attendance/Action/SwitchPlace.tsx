import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { E_WORKING_PLACE } from '@/script/Enum/Server/App/EWorkingPlace';
import {
  E_ATTENDANCE_STATE,
  EAttendanceState,
} from '@/script/Enum/Server/App/EAttendanceState';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_ICON } from '@/script/Enum/EIcon';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { AppTrnAttendanceStateList } from '@/script/Models/App/Trn/TrnAttendanceStateList';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { useIndexContext } from '@/script/Pages/Attendance/Index';
import { responsiveSize } from '@/script/System/Responsive';

interface IProps extends IPropsBase {
  handleClick: (state: EAttendanceState) => void;
}
export const SwitchPlace: React.FC<IProps> = ({ handleClick }) => {
  const { authUser } = useCommonIndexContext();
  const { trnUserList } = useIndexContext();
  const trnUser = trnUserList.findByPrimary(authUser.trnUser?.id);
  const workingPlace = AppTrnAttendanceStateList.lastWorkingPlace(
    trnUser.trnAttendanceState,
  );

  return (
    <Box>
      {/* オフィス勤務の場合 */}
      {workingPlace === E_WORKING_PLACE.HOME && (
        <ButtonGeneral
          sx={{ width: responsiveSize(220) }}
          startIcon={<Icon icon={E_ICON.OFFICE} />}
          label="オフィス勤務へ切替"
          onClick={() => handleClick(E_ATTENDANCE_STATE.SWITCH_TO_OFFICE)}
        />
      )}
      {/* 在宅勤務の場合 */}
      {workingPlace === E_WORKING_PLACE.OFFICE && (
        <ButtonGeneral
          sx={{ width: responsiveSize(220) }}
          startIcon={<Icon icon={E_ICON.HOME} />}
          label="在宅勤務へ切替"
          onClick={() => handleClick(E_ATTENDANCE_STATE.SWITCH_TO_TELEWORK)}
        />
      )}
    </Box>
  );
};
