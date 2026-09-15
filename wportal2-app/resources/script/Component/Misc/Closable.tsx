import * as React from 'react';
import { IPropsBase } from '@/script/System/System';

interface IProps extends IPropsBase {
  open?: boolean;
}
export const Closable: React.FC<IProps> = ({ open, children }) => {
  if (!open) {
    return null;
  }
  return <>{children}</>;
};
