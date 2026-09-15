import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_ICON } from '@/script/Enum/EIcon';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';
import { E_COLOR } from '@/script/Enum/EColor';

interface IProps extends IPropsBase {}
export const Gold: React.FC<IProps> = ({ sx }) => {
  const { authUser } = useCommonIndexContext();
  return (
    <Box
      sx={{
        width: responsiveSize(100),
        height: '100%',
        display: 'flex',
        alignItems: 'center',
        borderLeft: `1px solid ${E_COLOR.WHITE}`,
        px: responsiveSpacing(2),
        ...sx,
      }}
    >
      <Icon icon={E_ICON.PAID} />
      <TypoText
        sx={{
          marginLeft: 'auto',
        }}
      >
        {authUser.trnUser
          ? authUser.trnUser?.currentGold.toLocaleString()
          : '0'}
      </TypoText>
    </Box>
  );
};
