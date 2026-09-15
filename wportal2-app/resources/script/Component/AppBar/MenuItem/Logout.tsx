import * as React from 'react';
import { MenuItem } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { InertiaLink } from '@/script/Component/Misc/InertiaLink';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { responsiveSize } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const Logout: React.FC<IProps> = () => {
  return (
    <MenuItem
      sx={{
        minHeight: responsiveSize(40),
      }}
    >
      <InertiaLink href="/logout">
        <TypoText>ログアウト</TypoText>
      </InertiaLink>
    </MenuItem>
  );
};
