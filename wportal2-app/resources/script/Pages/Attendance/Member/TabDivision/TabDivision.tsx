import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { useIndexContext } from '@/script/Pages/Attendance/Index';
import { AppTrnUserDivisionPriorityList } from '@/script/Models/App/Trn/TrnUserDivisionPriorityList';
import { DivisionBlock } from '@/script/Pages/Attendance/Member/TabDivision/DivisionBlock';

interface IProps extends IPropsBase {}
export const TabDivision: React.FC<IProps> = ({ sx }) => {
  const { trnDivisionList } = useIndexContext();
  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      {trnDivisionList
        .list()
        // 課優先度で並び変える.
        .sort((a, b) => {
          return (
            AppTrnUserDivisionPriorityList.getPriority(
              b.trnUserDivisionPriority,
            ) -
            AppTrnUserDivisionPriorityList.getPriority(
              a.trnUserDivisionPriority,
            )
          );
        })
        .map(trnDivision => {
          return (
            <DivisionBlock key={trnDivision.id} trnDivision={trnDivision} />
          );
        })}
    </Box>
  );
};
