import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_ICON } from '@/script/Enum/EIcon';
import { responsiveSize } from '@/script/System/Responsive';

interface IProps extends IPropsBase {
  gold: number;
}
export const RewardGold: React.FC<IProps> = ({ sx, gold }) => {
  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <Box
        sx={{
          border: '1px solid',
          width: 'fit-content',
          padding: responsiveSize(4),
          borderRadius: responsiveSize(12),
          display: 'flex',
          alignItems: 'center',
          gap: responsiveSize(6),
        }}
      >
        <Icon icon={E_ICON.PAID} />
        <TypoText bold>{gold}</TypoText>
      </Box>
    </Box>
  );
};
