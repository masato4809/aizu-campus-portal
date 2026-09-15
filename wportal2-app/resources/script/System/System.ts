import * as React from 'react';
import { SxProps, Theme } from '@mui/material';

export interface IPropsBase {
  sx?: SxProps<Theme>;
  id?: string;
  className?: string;
  children?: React.ReactNode;
}
