import * as React from 'react';
import { Box, Divider, Grid } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { responsiveSize } from '@/script/System/Responsive';
import {
  E_MENU,
  EMenu,
  isActiveMenu,
  MenuList,
  mobileIgnoreList,
} from '@/script/Enum/EMenu';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { MobileIcon } from '@/script/Component/AppBar/MobileIcon';
import { E_ICON } from '@/script/Enum/EIcon';
import { Icon } from '@/script/Component/Misc/Icon';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';

interface IProps extends IPropsBase {}
export const MobileMenu: React.FC<IProps> = () => {
  const { authUser } = useCommonIndexContext();
  const { trnUserAuthorityList, achievementCount } = useCommonIndexContext();
  const itemList = MenuList.filter(v => !mobileIgnoreList.includes(v.menu))
    .filter(v => v.pages !== E_PAGES.INVALID)
    .filter(v => isActiveMenu(v.menu, trnUserAuthorityList));

  /**
   * バッジの表示数.
   */
  const calcBadgeCount = (menu: EMenu): number => {
    switch (menu) {
      case E_MENU.REWARD:
        return achievementCount;
      default:
        return 0;
    }
  };

  return (
    <>
      <Box
        sx={{
          padding: responsiveSize(16),
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <Icon icon={E_ICON.PAID} size={32} />
        <TypoH1
          sx={{
            marginLeft: responsiveSize(10),
          }}
        >
          {authUser.trnUser?.currentGold.toLocaleString()}
        </TypoH1>
      </Box>
      <Divider sx={{ margin: responsiveSize(8) }} />
      <Grid
        sx={{
          width: '306px',
        }}
        container
      >
        {itemList.map(menu => {
          return (
            <Grid item xs={4} key={menu.menu}>
              <MobileIcon
                key={menu.menu}
                href={getPagesHref(menu.pages)}
                icon={menu.icon}
                label={menu.name}
                badgeCount={calcBadgeCount(menu.menu)}
              />
            </Grid>
          );
        })}
      </Grid>
      <Divider sx={{ margin: responsiveSize(8) }} />
      <MobileIcon
        href={getPagesHref(E_PAGES.PERSONAL_SETTING__SHOW)}
        icon={E_ICON.SETTING}
        label="個人設定"
      />
      <Divider sx={{ margin: '8px' }} />
      <MobileIcon href="/logout" icon={E_ICON.LOGOUT} label="ログアウト" />
    </>
  );
};
