import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { Page } from '@/script/Pages/Page';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { InertiaLink } from '@/script/Component/Misc/InertiaLink';

interface IProps extends IPropsBase {}
export const NotFound: React.FC<IProps> = () => {
  return (
    <Page>
      <Box
        sx={{
          display: 'flex',
          flexDirection: 'column',
          alignItems: 'center',
          paddingTop: '100px',
        }}
      >
        <TypoText>お探しのページは見つかりませんでした</TypoText>
        <InertiaLink href="/">
          <ButtonGeneral
            sx={{
              marginTop: '20px',
            }}
            label="トップページに戻る"
          />
        </InertiaLink>
      </Box>
    </Page>
  );
};
