import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { EIcon } from '@/script/Enum/EIcon';
import { E_COLOR } from '@/script/Enum/EColor';
import { Icon } from '@/script/Component/Misc/Icon';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {
  icon: EIcon;
  label: string;
}
export const Status: React.FC<IProps> = ({ sx, icon, label }) => {
  return (
    <Box
      sx={{
        padding: responsiveSpacing(4),
        borderRadius: responsiveSpacing(4),
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        background: E_COLOR.PRIMARY_LIGHT,
        ...sx,
      }}
    >
      <Icon
        sx={{
          marginRight: responsiveSpacing(2),
        }}
        icon={icon}
      />
      <TypoText>{label}</TypoText>
    </Box>
  );
};
