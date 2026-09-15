import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { Page } from '@/script/Pages/Page';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { E_PAGES, getPagesName } from '@/script/Enum/Server/App/EPages';
import { BackLink } from '@/script/Pages/Common/BackLink';
import { MstRewardList } from '@/script/Pages/Reward/List/MstRewardList';

interface IProps extends IPropsBase {}
export const List: React.FC<IProps> = () => {
  return (
    <Page>
      <BackLink current={E_PAGES.REWARD__LIST} />
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'space-between',
        }}
      >
        <TypoH1>{getPagesName(E_PAGES.REWARD__LIST)}</TypoH1>
      </Box>
      <MstRewardList />
    </Page>
  );
};
