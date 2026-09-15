import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { Page } from '@/script/Pages/Page';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import {
  E_PAGES,
  getPagesHref,
  getPagesName,
} from '@/script/Enum/Server/App/EPages';
import { DivisionList } from '@/script/Pages/Division/DivisionList';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { InertiaLink } from '@/script/Component/Misc/InertiaLink';

interface IProps extends IPropsBase {}
export const Division: React.FC<IProps> = () => {
  return (
    <Page>
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'space-between',
        }}
      >
        <TypoH1>{getPagesName(E_PAGES.DIVISION)}</TypoH1>
        <InertiaLink href={getPagesHref(E_PAGES.DIVISION__NEW)}>
          <ButtonGeneral
            sx={{
              width: responsiveSize(140),
            }}
            label="新規作成"
          />
        </InertiaLink>
      </Box>
      <DivisionList sx={{ marginTop: responsiveSpacing(4) }} />
    </Page>
  );
};
