import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { useIndexContext } from '@/script/Pages/Attendance/Index';
import { ProjectBlock } from '@/script/Pages/Attendance/Member/TabProject/ProjectBlock';
import { AppTrnUserProjectPriorityList } from '@/script/Models/App/Trn/TrnUserProjectPriorityList';

interface IProps extends IPropsBase {}
export const TabProject: React.FC<IProps> = ({ sx }) => {
  const { trnProjectList } = useIndexContext();
  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      {trnProjectList
        .list()
        // プロジェクト優先度で並び変える.
        .sort((a, b) => {
          return (
            AppTrnUserProjectPriorityList.getPriority(
              b.trnUserProjectPriority,
            ) -
            AppTrnUserProjectPriorityList.getPriority(a.trnUserProjectPriority)
          );
        })
        .map(trnProject => {
          return <ProjectBlock key={trnProject.id} trnProject={trnProject} />;
        })}
    </Box>
  );
};
