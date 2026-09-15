import * as React from 'react';
import { Box, Container, useTheme } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { AppBarDesktop } from '@/script/Component/AppBar/AppBarDesktop';
import { DrawerDesktop } from '@/script/Component/Drawer/DrawerDesktop';
import { DisplayDesktop } from '@/script/Component/Misc/DisplayDesktop';
import { DisplayMobile } from '@/script/Component/Misc/DisplayMobile';
import { AppBarMobile } from '@/script/Component/AppBar/AppBarMobile';
import { responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const Page: React.FC<IProps> = ({ sx, children }) => {
  const theme = useTheme();
  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <DisplayDesktop>
        <AppBarDesktop />
        <DrawerDesktop />
      </DisplayDesktop>
      <DisplayMobile>
        <AppBarMobile />
      </DisplayMobile>
      <Container
        sx={{
          marginTop: theme => theme.mixins.toolbar.height,
          paddingLeft: {
            sm: '0px',
            md: theme.mixins.drawer?.width,
          },
        }}
        maxWidth={false}
        disableGutters
      >
        <Box
          sx={{
            padding: responsiveSpacing(4),
          }}
        >
          {children}
        </Box>
      </Container>
    </Box>
  );
};
