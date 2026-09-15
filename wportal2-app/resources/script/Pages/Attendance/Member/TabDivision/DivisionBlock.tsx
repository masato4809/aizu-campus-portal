import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IAppTrnDivision } from '@/script/Models/App/Trn/TrnDivisionList';
import { E_COLOR } from '@/script/Enum/EColor';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { AttendanceState } from '@/script/Pages/Attendance/AttendanceState';
import { E_WORKING_PLACE } from '@/script/Enum/Server/App/EWorkingPlace';
import { useIndexContext } from '@/script/Pages/Attendance/Index';
import { DivisionPriority } from '@/script/Pages/Attendance/Member/TabDivision/DivisionPriority';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {
  trnDivision: IAppTrnDivision;
}
export const DivisionBlock: React.FC<IProps> = ({ sx, trnDivision }) => {
  const { stateList } = useIndexContext();
  const divisionUserIdList = trnDivision.trnDivisionUser?.map(v => v.trnUserId);
  const divisionStateList = stateList.filter(state =>
    divisionUserIdList?.includes(state.trnUser.id),
  );
  return (
    <Box
      sx={{
        marginTop: responsiveSpacing(2),
        padding: responsiveSpacing(2),
        ...sx,
      }}
    >
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
          gap: responsiveSpacing(4),
        }}
      >
        <Box
          sx={{
            backgroundColor: E_COLOR.PRIMARY_LIGHT,
            width: 'fit-content',
            py: responsiveSpacing(2),
            px: responsiveSpacing(8),
            borderRadius: responsiveSize(8),
          }}
        >
          <TypoH2>{trnDivision.name}</TypoH2>
        </Box>
        <DivisionPriority trnDivision={trnDivision} />
      </Box>
      <AttendanceState
        sx={{ marginTop: responsiveSpacing(2) }}
        label="オフィス"
        stateList={divisionStateList
          .filter(state => state.isWorking)
          .filter(state => state.workingPlace === E_WORKING_PLACE.OFFICE)}
      />
      <AttendanceState
        label="在宅"
        stateList={divisionStateList
          .filter(state => state.isWorking)
          .filter(state => state.workingPlace === E_WORKING_PLACE.HOME)}
      />
      <AttendanceState
        label="不在"
        stateList={divisionStateList.filter(state => !state.isWorking)}
      />
    </Box>
  );
};
