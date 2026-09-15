import * as React from 'react';
import { Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { useIndexContext } from '@/script/Pages/Attendance/Index';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { E_WORKING_STATE } from '@/script/Enum/Server/App/EWorkingState';
import { MyselfNone } from '@/script/Pages/Attendance/Myself/MyselfNone';
import { MyselfWorking } from '@/script/Pages/Attendance/Myself/MyselfWorking';
import { MyselfRest } from '@/script/Pages/Attendance/Myself/MyselfRest';
import { MyselfLeaving } from '@/script/Pages/Attendance/Myself/MyselfLeaving';
import { AppTrnAttendanceStateList } from '@/script/Models/App/Trn/TrnAttendanceStateList';
import { responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const Myself: React.FC<IProps> = ({ sx }) => {
  const { authUser } = useCommonIndexContext();
  const { trnUserList } = useIndexContext();
  const trnUser = trnUserList.findByPrimary(authUser.trnUser?.id);
  const lastWorkingState = AppTrnAttendanceStateList.lastWorkingState(
    trnUser.trnAttendanceState,
  );

  /**
   * 現在のステータスを勤務状態で切り分け.
   */
  const nodeState = (): React.ReactNode => {
    switch (lastWorkingState) {
      case E_WORKING_STATE.NONE:
        return <MyselfNone />;
      case E_WORKING_STATE.WORKING:
        return <MyselfWorking />;
      case E_WORKING_STATE.REST:
        return <MyselfRest />;
      case E_WORKING_STATE.LEAVING:
        return <MyselfLeaving />;
      default:
        return null;
    }
  };

  return (
    <Paper
      sx={{
        padding: responsiveSpacing(4),
        ...sx,
      }}
    >
      {nodeState()}
    </Paper>
  );
};
