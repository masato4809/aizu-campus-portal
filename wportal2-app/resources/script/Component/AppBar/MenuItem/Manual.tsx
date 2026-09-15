import * as React from 'react';
import { MenuItem } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { responsiveSize } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const Manual: React.FC<IProps> = () => {
  return (
    <MenuItem
      sx={{
        minHeight: responsiveSize(40),
      }}
    >
      <a
        href="https://docs.google.com/document/d/1lREZOhp2aJOwNavq2go9mXaDuJjsrlwRhroe0te9T2c/edit?usp=sharing"
        target="_blank"
        rel="noreferrer"
      >
        <TypoText>操作の手引き</TypoText>
      </a>
    </MenuItem>
  );
};
