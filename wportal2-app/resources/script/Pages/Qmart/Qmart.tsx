import * as React from 'react';
import { Box, Divider } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { Page } from '@/script/Pages/Page';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { E_PAGES, getPagesName } from '@/script/Enum/Server/App/EPages';
import { responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const Qmart: React.FC<IProps> = () => {
  return (
    <Page>
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'space-between',
        }}
      >
        <TypoH1>{getPagesName(E_PAGES.QMART)}</TypoH1>
      </Box>
      <Divider
        sx={{
          my: responsiveSpacing(4),
        }}
      />
    </Page>
  );
};
