import * as React from 'react';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { IPropsBase } from '@/script/System/System';
import { Redis } from '@/script/Pages/Sample/Redis/Redis';

interface IProps extends IPropsBase {}
export const Index: React.FC<IProps> = ({ ...props }) => {
  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <Redis />
    </CommonIndexProvider>
  );
};
export default Index;
