import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import {
  E_PAGES,
  getPagesHref,
  getPagesName,
} from '@/script/Enum/Server/App/EPages';
import { Page } from '@/script/Pages/Page';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';
import { InertiaLink } from '@/script/Component/Misc/InertiaLink';
import { GoodJobList } from '@/script/Pages/GoodJob/GoodJobList';

interface IProps extends IPropsBase {}
export const GoodJob: React.FC<IProps> = () => {
  return (
    <Page>
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'space-between',
        }}
      >
        <TypoH1>{getPagesName(E_PAGES.GOOD_JOB)}</TypoH1>
        <InertiaLink href={getPagesHref(E_PAGES.GOOD_JOB__NEW)}>
          <ButtonGeneral
            sx={{
              width: responsiveSize(140),
            }}
            label="新規投稿"
          />
        </InertiaLink>
      </Box>
      <GoodJobList sx={{ marginTop: responsiveSpacing(4) }} />
    </Page>
  );
};
