import * as React from 'react';
import { styled } from '@mui/material';
import AccessTimeIcon from '@mui/icons-material/AccessTime';
import AddIcon from '@mui/icons-material/Add';
import AdminPanelSettingsIcon from '@mui/icons-material/AdminPanelSettings';
import ArrowBackIosNewIcon from '@mui/icons-material/ArrowBackIosNew';
import ArrowForwardIosIcon from '@mui/icons-material/ArrowForwardIos';
import BuildIcon from '@mui/icons-material/Build';
import BusinessIcon from '@mui/icons-material/Business';
import CachedIcon from '@mui/icons-material/Cached';
import CircleIcon from '@mui/icons-material/Circle';
import ClearIcon from '@mui/icons-material/Clear';
import DashboardIcon from '@mui/icons-material/Dashboard';
import DescriptionIcon from '@mui/icons-material/Description';
import Diversity3Icon from '@mui/icons-material/Diversity3';
import ExpandLess from '@mui/icons-material/ExpandLess';
import ExpandMore from '@mui/icons-material/ExpandMore';
import FavoriteIcon from '@mui/icons-material/Favorite';
import GroupsIcon from '@mui/icons-material/Groups';
import HomeIcon from '@mui/icons-material/Home';
import LibraryBooksIcon from '@mui/icons-material/LibraryBooks';
import LocalCafeIcon from '@mui/icons-material/LocalCafe';
import LogoutIcon from '@mui/icons-material/Logout';
import ManageHistoryIcon from '@mui/icons-material/ManageHistory';
import MenuIcon from '@mui/icons-material/Menu';
import MilitaryTechIcon from '@mui/icons-material/MilitaryTech';
import PaidIcon from '@mui/icons-material/Paid';
import PersonIcon from '@mui/icons-material/Person';
import RemoveIcon from '@mui/icons-material/Remove';
import RestaurantIcon from '@mui/icons-material/Restaurant';
import SettingsIcon from '@mui/icons-material/Settings';
import ShoppingBasketIcon from '@mui/icons-material/ShoppingBasket';
import StarIcon from '@mui/icons-material/Star';
import StarOutlineIcon from '@mui/icons-material/StarOutline';
import CalendarMonthIcon from '@mui/icons-material/CalendarMonth';
import { ArrowBackIos } from '@mui/icons-material';
import { E_ICON, EIcon, getIconPath, getIconSize } from '@/script/Enum/EIcon';
import { IPropsBase } from '@/script/System/System';
import { responsiveSize } from '@/script/System/Responsive';

const StyledImg = styled('img')({});

interface IProps extends IPropsBase {
  icon: EIcon;
  size?: number;
  width?: number;
  height?: number;
  onClick?: () => void;
}
export const Icon: React.FC<IProps> = ({
  sx,
  icon,
  size,
  width,
  height,
  onClick,
}) => {
  // 指定がない場合は対応なし.
  if (icon === E_ICON.INVALID) {
    return null;
  }

  // size指定があれば正方利用、いずれの指定もなければデフォルト値を利用.
  const iconWidth = (size || width) ?? getIconSize(icon).w;
  const iconHeight = (size || height) ?? getIconSize(icon).h;
  const iconSx = {
    width: responsiveSize(iconWidth),
    height: responsiveSize(iconHeight),
    ...sx,
  };

  // 組み込みアイコンの場合.
  switch (icon) {
    case E_ICON.ADD:
      return <AddIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.ADMIN_PANEL:
      return <AdminPanelSettingsIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.ATTENDANCE:
      return <AccessTimeIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.ARROW_BACK:
      return <ArrowBackIos sx={iconSx} onClick={onClick} />;
    case E_ICON.ARROW_LEFT:
      return <ArrowBackIosNewIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.ARROW_RIGHT:
      return <ArrowForwardIosIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.BUILD:
      return <BuildIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.CACHED:
      return <CachedIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.CAFE:
      return <LocalCafeIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.CIRCLE:
      return <CircleIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.CLEAR:
      return <ClearIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.DASHBOARD:
      return <DashboardIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.DESCRIPTION:
      return <DescriptionIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.DIVERSITY3:
      return <Diversity3Icon sx={iconSx} onClick={onClick} />;
    case E_ICON.EXPAND_LESS:
      return <ExpandLess sx={iconSx} onClick={onClick} />;
    case E_ICON.EXPAND_MORE:
      return <ExpandMore sx={iconSx} onClick={onClick} />;
    case E_ICON.FAVORITE:
      return <FavoriteIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.GROUPS:
      return <GroupsIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.HOME:
      return <HomeIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.LIBRARY_BOOKS:
      return <LibraryBooksIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.LOGOUT:
      return <LogoutIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.LUNCH:
      return <RestaurantIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.MANAGE_HISTORY:
      return <ManageHistoryIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.MENU:
      return <MenuIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.MILITARY_TECH:
      return <MilitaryTechIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.OFFICE:
      return <BusinessIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.PAID:
      return <PaidIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.PERSON:
      return <PersonIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.REMOVE:
      return <RemoveIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.SETTING:
      return <SettingsIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.SHOP:
      return <ShoppingBasketIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.STAR:
      return <StarIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.STAR_OUTLINE:
      return <StarOutlineIcon sx={iconSx} onClick={onClick} />;
    case E_ICON.CALENDAR_MONTH:
      return <CalendarMonthIcon sx={iconSx} onClick={onClick} />;
    default:
      break;
  }

  return <StyledImg sx={iconSx} src={getIconPath(icon)} onClick={onClick} />;
};
