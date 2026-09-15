import * as React from 'react';
import { AppBar as MuiAppBar, Box, Menu, Toolbar } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { Logo } from '@/script/Component/AppBar/Logo';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_ICON } from '@/script/Enum/EIcon';
import { responsiveSize } from '@/script/System/Responsive';
import { MobileMenu } from '@/script/Component/AppBar/MobileMenu';

interface IProps extends IPropsBase {}
export const AppBarMobile: React.FC<IProps> = () => {
  const [anchor, setAnchor] = React.useState<HTMLElement | null>(null);

  /**
   * メニューを開く.
   */
  const handleOpenMenu = (event: React.MouseEvent<HTMLDivElement>) => {
    setAnchor(event.currentTarget);
  };

  /**
   * メニューを閉じる.
   */
  const handleClose = () => {
    setAnchor(null);
  };

  return (
    <MuiAppBar
      sx={{
        zIndex: theme => theme.zIndex.drawer + 1,
        height: theme => theme.mixins.toolbar.height,
      }}
      position="fixed"
    >
      <Toolbar
        sx={{
          width: '100%',
          height: '100%',
          padding: '0px 20px',
          display: 'flex',
        }}
        disableGutters
      >
        <Logo
          sx={{
            marginRight: 'auto',
          }}
        />
        <Box onClick={handleOpenMenu}>
          <Icon icon={E_ICON.MENU} size={48} />
        </Box>
        <Menu
          sx={{
            '& .MuiMenuItem-root': {
              width: responsiveSize(250),
            },
          }}
          autoFocus={false}
          anchorEl={anchor}
          open={!!anchor}
          anchorOrigin={{ vertical: 'bottom', horizontal: 'right' }}
          transformOrigin={{ vertical: 'top', horizontal: 'right' }}
          onClose={handleClose}
        >
          <MobileMenu />
        </Menu>
      </Toolbar>
    </MuiAppBar>
  );
};
