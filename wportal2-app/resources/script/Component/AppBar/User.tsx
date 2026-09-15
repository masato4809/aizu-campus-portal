import * as React from 'react';
import { Box, Divider, Menu } from '@mui/material';
import ExpandMoreTwoToneIcon from '@mui/icons-material/ExpandMoreTwoTone';
import { IPropsBase } from '@/script/System/System';
import { E_COLOR } from '@/script/Enum/EColor';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { Image } from '@/script/Component/Misc/Image';
import { Manual } from '@/script/Component/AppBar/MenuItem/Manual';
import { ReleaseNote } from '@/script/Component/AppBar/MenuItem/ReleaseNote';
import { Logout } from '@/script/Component/AppBar/MenuItem/Logout';
import { PersonalSetting } from '@/script/Component/AppBar/MenuItem/PersonalSetting';
import { responsiveSize } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}

export const User: React.FC<IProps> = ({ sx }) => {
  const { authUser } = useCommonIndexContext();
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
    <>
      <Box
        sx={{
          width: '250px',
          height: '100%',
          borderLeft: `1px solid ${E_COLOR.WHITE}`,
          borderRight: `1px solid ${E_COLOR.WHITE}`,
          display: 'flex',
          alignItems: 'center',
          cursor: 'pointer',
          padding: '0px 10px',
          '&:hover': {
            color: E_COLOR.PRIMARY_DARK,
          },

          ...sx,
        }}
        onClick={handleOpenMenu}
      >
        <Image
          sx={{
            display: 'block',
            borderRadius: '50%',
            width: '40px',
            height: '40px',
            background: E_COLOR.GREY_LIGHT,
          }}
          src={String(authUser.trnUser?.faceImagePath)}
        />
        <TypoText sx={{ marginLeft: 'auto' }}>
          {String(authUser.trnUser?.nickname)}
        </TypoText>
        <ExpandMoreTwoToneIcon sx={{ marginLeft: '10px' }} />
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
        <PersonalSetting />
        <Divider sx={{ margin: '8px' }} />
        <Manual />
        <ReleaseNote />
        <Divider sx={{ margin: '8px' }} />
        <Logout />
      </Menu>
    </>
  );
};
