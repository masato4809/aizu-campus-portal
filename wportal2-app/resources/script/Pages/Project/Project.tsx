import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { Page } from '@/script/Pages/Page';
import {
  E_PAGES,
  getPagesHref,
  getPagesName,
} from '@/script/Enum/Server/App/EPages';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { ProjectList } from '@/script/Pages/Project/ProjectList';
import { InertiaLink } from '@/script/Component/Misc/InertiaLink';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const Project: React.FC<IProps> = () => {
  return (
    <Page>
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'space-between',
        }}
      >
        <TypoH1>{getPagesName(E_PAGES.PROJECT)}</TypoH1>
        <InertiaLink href={getPagesHref(E_PAGES.PROJECT__NEW)}>
          <ButtonGeneral
            sx={{
              width: responsiveSize(140),
            }}
            label="新規作成"
          />
        </InertiaLink>
      </Box>
      <ProjectList sx={{ marginTop: responsiveSpacing(4) }} />
    </Page>
  );
};
