import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { E_COLOR } from '@/script/Enum/EColor';
import { Image } from '@/script/Component/Misc/Image';
import { IAppMstGoods } from '@/script/Models/App/Mst/MstGoodsList';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_ICON } from '@/script/Enum/EIcon';
import { TypoText2 } from '@/script/Component/Typography/TypoText2';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';

/**
 * CSSで色々デザインしてみようという実験含む.
 */
interface IProps extends IPropsBase {
  mstGoods: IAppMstGoods;
}
export const LunchTicket: React.FC<IProps> = ({ mstGoods }) => {
  const { authUser } = useCommonIndexContext();
  const currentGold = authUser.trnUser?.currentGold ?? 0;
  const isEnablePurchase = currentGold >= mstGoods.price;
  const color = isEnablePurchase ? E_COLOR.TEXT_PRIMARY : E_COLOR.TEXT_RED;
  return (
    <Box
      sx={{
        width: '300px',
        height: '100px',
        position: 'relative',
        backgroundColor: E_COLOR.WHITE,
        border: `3px solid ${E_COLOR.BLACK}`,
        borderRadius: '10px',

        '&:before': {
          content: '""',
          display: 'block',
          position: 'absolute',
          top: '30px',
          left: '-20px',
          width: '40px',
          height: '40px',
          borderRadius: '50%',
          background: E_COLOR.BLACK,
          zIndex: 5,
        },

        '&:after': {
          content: '""',
          display: 'block',
          position: 'absolute',
          top: '30px',
          right: '-20px',
          width: '40px',
          height: '40px',
          borderRadius: '50%',
          background: E_COLOR.BLACK,
          zIndex: 5,
        },
      }}
    >
      <Box
        sx={{
          position: 'absolute',
          top: '30px',
          left: '-33px',
          width: '30px',
          height: '40px',
          background: E_COLOR.BACKGROUND,
          zIndex: 6,
        }}
      />
      <Box
        sx={{
          position: 'absolute',
          top: '30px',
          right: '-33px',
          width: '30px',
          height: '40px',
          background: E_COLOR.BACKGROUND,
          zIndex: 6,
        }}
      />
      <Box
        sx={{
          position: 'absolute',
          top: '0px',
          right: '0px',
          width: '80px',
          height: '94px',
          background: E_COLOR.WHITE,
          borderRadius: '0 7px 7px 0',
          borderLeft: '2px dotted #000',
          zIndex: 3,

          display: 'flex',
          justifyContent: 'end',
          alignItems: 'end',
          paddingRight: '5px',
        }}
      >
        <Icon
          sx={{
            width: '20px',
            height: '20px',
          }}
          icon={E_ICON.PAID}
        />
        <TypoText2
          sx={{
            fontSize: {
              sm: '14px',
              md: '14px',
            },
          }}
          color={color}
          bold
        >
          {mstGoods.price.toLocaleString()}
        </TypoText2>
      </Box>
      <Box
        sx={{
          width: '100%',
          height: '100%',

          '&:before': {
            content: '""',
            display: 'block',
            position: 'absolute',
            top: '33px',
            left: '-17px',
            width: '34px',
            height: '34px',
            borderRadius: '50%',
            background: E_COLOR.BACKGROUND,
            zIndex: 6,
          },

          '&:after': {
            content: '""',
            display: 'block',
            position: 'absolute',
            top: '33px',
            right: '-17px',
            width: '34px',
            height: '34px',
            borderRadius: '50%',
            background: E_COLOR.BACKGROUND,
            zIndex: 6,
          },
        }}
      >
        <Image
          sx={{
            borderRadius: '7px',
            width: '294px',
            height: '94px',
          }}
          src={mstGoods.imagePath}
          alt="ランチチケット"
        />
      </Box>
    </Box>
  );
};
