import * as React from 'react';
import { Box, Drawer as MuiDrawer, List } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { DrawerItem } from '@/script/Component/Drawer/DrawerItem';
import { E_MENU, isActiveMenu, MenuList } from '@/script/Enum/EMenu';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';

interface IProps extends IPropsBase {}
export const DrawerDesktop: React.FC<IProps> = () => {
  const { trnUserAuthorityList } = useCommonIndexContext();
  return (
    <MuiDrawer
      sx={{
        position: 'fixed',
        height: '100vh',
        overflow: 'hidden',
        width: theme => theme.mixins.drawer?.width,

        flexShrink: 0,
        [`& .MuiDrawer-paper`]: {
          width: theme => theme.mixins.drawer?.width,
          boxSizing: 'border-box',
        },
      }}
      variant="permanent"
    >
      <Box
        sx={{
          marginTop: theme => theme.mixins.toolbar.height,
        }}
      >
        <List>
          {MenuList.filter(v => v.parent === E_MENU.INVALID)
            .filter(v => isActiveMenu(v.menu, trnUserAuthorityList))
            .map(menu => (
              <DrawerItem key={menu.menu} menu={menu} depth={0} />
            ))}
        </List>
      </Box>
    </MuiDrawer>
  );
};
