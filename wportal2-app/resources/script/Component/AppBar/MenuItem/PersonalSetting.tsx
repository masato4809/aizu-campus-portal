import * as React from 'react';
import { MenuItem } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { InertiaLink } from '@/script/Component/Misc/InertiaLink';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { responsiveSize } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const PersonalSetting: React.FC<IProps> = () => {
  return (
    <MenuItem
      sx={{
        minHeight: responsiveSize(40),
      }}
    >
      <InertiaLink href={getPagesHref(E_PAGES.PERSONAL_SETTING__SHOW)}>
        <TypoText>個人設定</TypoText>
      </InertiaLink>
    </MenuItem>
  );
};
