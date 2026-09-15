import * as React from 'react';
import { MenuItem } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { responsiveSize } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const ReleaseNote: React.FC<IProps> = () => {
  return (
    <MenuItem
      sx={{
        minHeight: responsiveSize(40),
      }}
    >
      <a
        href="https://docs.google.com/spreadsheets/d/1wYJkLvmAX-sCdvwzTAAu8vMQ9Gp6SiLFqKRzDIDwHAY/edit?usp=sharing"
        target="_blank"
        rel="noreferrer"
      >
        <TypoText>リリースノート</TypoText>
      </a>
    </MenuItem>
  );
};
