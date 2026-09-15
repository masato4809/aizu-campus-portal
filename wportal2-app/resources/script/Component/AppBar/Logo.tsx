import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { InertiaLink } from '@/script/Component/Misc/InertiaLink';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';

interface IProps extends IPropsBase {}
export const Logo: React.FC<IProps> = ({ sx }) => {
  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <InertiaLink href={getPagesHref(E_PAGES.DASHBOARD)}>
        <TypoH1>Web本部ポータル3.0</TypoH1>
      </InertiaLink>
    </Box>
  );
};
