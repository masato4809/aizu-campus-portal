import * as React from 'react';
import { Box, Paper, Tab, Tabs } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TabProject } from '@/script/Pages/Attendance/Member/TabProject/TabProject';
import { TabDivision } from '@/script/Pages/Attendance/Member/TabDivision/TabDivision';
import { TabMember } from '@/script/Pages/Attendance/Member/TabMember/TabMember';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

const E_TAB = {
  PROJECT: 0,
  DIVISION: 1,
  MEMBER: 2,
} as const;
type ETab = (typeof E_TAB)[keyof typeof E_TAB];

interface IProps extends IPropsBase {}
export const Member: React.FC<IProps> = ({ sx }) => {
  const [tabIndex, setTabIndex] = React.useState<ETab>(E_TAB.PROJECT);

  /**
   * タブボタン処理.
   */
  const handleChangeTab = (_event: React.SyntheticEvent, newIndex: ETab) => {
    setTabIndex(newIndex);
  };

  /**
   * 各タブページの表示.
   */
  const nodeTab = (): React.ReactNode => {
    switch (tabIndex) {
      case E_TAB.PROJECT:
        return <TabProject />;
      case E_TAB.DIVISION:
        return <TabDivision />;
      case E_TAB.MEMBER:
        return <TabMember />;
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
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'space-between',
          borderBottom: '1px solid',
          borderColor: 'divider',
        }}
      >
        <Tabs value={tabIndex} onChange={handleChangeTab}>
          <Tab
            sx={{
              width: responsiveSize(140),
            }}
            label="プロジェクト"
          />
          <Tab
            sx={{
              width: responsiveSize(140),
            }}
            label="課"
          />
          <Tab
            sx={{
              width: responsiveSize(140),
            }}
            label="メンバー"
          />
        </Tabs>
      </Box>
      {nodeTab()}
    </Paper>
  );
};
