import * as React from 'react';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { Dashboard } from '@/script/Pages/Dashboard/Dashboard';

interface IProps extends IPropsBase {}
export const Index: React.FC<IProps> = ({ ...props }) => {
  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <Dashboard />
    </CommonIndexProvider>
  );
};
export default Index;
