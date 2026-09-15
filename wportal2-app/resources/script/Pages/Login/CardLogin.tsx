import * as React from 'react';
import { Box, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_ICON } from '@/script/Enum/EIcon';

interface IProps extends IPropsBase {}
export const CardLogin: React.FC<IProps> = () => {
  return (
    <Paper
      sx={{
        padding: '20px',
        width: '100%',
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        overflow: 'hidden',
      }}
    >
      <TypoH2>Web本部ポータル</TypoH2>
      <TypoText>Build:{import.meta.env.VITE_BUILD}</TypoText>
      <ButtonGeneral
        sx={{
          marginTop: '20px',
          width: '100%',
          height: '80px',
        }}
        href="/auth/google"
      >
        <Box
          sx={{
            display: 'flex',
            alignItems: 'center',
            gap: '10px',
          }}
        >
          <Icon icon={E_ICON.GOOGLE} />
          <TypoText>Googleアカウントで認証</TypoText>
        </Box>
      </ButtonGeneral>
    </Paper>
  );
};
