import * as React from 'react';
import { router } from '@inertiajs/react';
import { Box, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { useIndexContext } from '@/script/Pages/Attendance/Index';
import { E_WORKING_STATE } from '@/script/Enum/Server/App/EWorkingState';
import { ActionNone } from '@/script/Pages/Attendance/Action/ActionNone';
import { EAttendanceState } from '@/script/Enum/Server/App/EAttendanceState';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { ActionWorking } from '@/script/Pages/Attendance/Action/ActionWorking';
import { ActionRest } from '@/script/Pages/Attendance/Action/ActionRest';
import { ActionLeaving } from '@/script/Pages/Attendance/Action/ActionLeaving';
import { AppTrnAttendanceStateList } from '@/script/Models/App/Trn/TrnAttendanceStateList';
import { responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const Action: React.FC<IProps> = ({ sx }) => {
  const { onStart, onFinish } = useProgressContext();
  const { authUser } = useCommonIndexContext();
  const { trnUserList } = useIndexContext();
  const trnUser = trnUserList.findByPrimary(authUser.trnUser?.id);
  const lastWorkingState = AppTrnAttendanceStateList.lastWorkingState(
    trnUser.trnAttendanceState,
  );

  /**
   * いずれかのボタンを押されたとき.
   */
  const handleClick = (attendance: EAttendanceState) => {
    // 更新処理の実施.
    router.visit(getPagesHref(E_PAGES.ATTENDANCE_CREATE), {
      method: 'post',
      onStart,
      onFinish,
      data: {
        eAttendanceState: attendance,
      },
    });
  };

  /**
   * 現在のステータスでボタンを切り分け.
   */
  const nodeButtons = (): React.ReactNode => {
    switch (lastWorkingState) {
      case E_WORKING_STATE.NONE:
        return <ActionNone handleClick={handleClick} />;
      case E_WORKING_STATE.WORKING:
        return <ActionWorking handleClick={handleClick} />;
      case E_WORKING_STATE.REST:
        return <ActionRest handleClick={handleClick} />;
      case E_WORKING_STATE.LEAVING:
        return <ActionLeaving handleClick={handleClick} />;
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
      <Box>{nodeButtons()}</Box>
    </Paper>
  );
};
