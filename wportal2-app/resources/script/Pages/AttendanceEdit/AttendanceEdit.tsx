import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { Page } from '@/script/Pages/Page';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { E_PAGES, getPagesName } from '@/script/Enum/Server/App/EPages';
import { Calendar } from '@/script/Pages/AttendanceEdit/Calendar';

interface IProps extends IPropsBase {}
export const AttendanceEdit: React.FC<IProps> = () => {
  return (
    <Page>
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'space-between',
        }}
      >
        <TypoH1>{getPagesName(E_PAGES.ATTENDANCE__EDIT)}</TypoH1>
      </Box>
      <Calendar sx={{ marginTop: '20px' }} />
    </Page>
  );
};
