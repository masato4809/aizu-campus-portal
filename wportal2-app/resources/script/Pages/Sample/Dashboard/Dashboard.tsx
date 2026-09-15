import * as React from 'react';
import { Box } from '@mui/material';
import { Page } from '@/script/Pages/Page';
import { IPropsBase } from '@/script/System/System';

interface IProps extends IPropsBase {}
export const Dashboard: React.FC<IProps> = () => {
  return (
    <Page>
      <Box
        sx={{
          height: '1200px',
          padding: '10px',
        }}
      >
        長いメッセージ長いメッセージ長いメッセージ長いメッセージ長いメッセージ
      </Box>
    </Page>
  );
};
