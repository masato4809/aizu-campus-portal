import * as React from 'react';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { Qmart } from '@/script/Pages/Qmart/Qmart';

interface IProps extends IPropsBase {}
export const Index: React.FC<IProps> = ({ ...props }) => {
  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <Qmart />
    </CommonIndexProvider>
  );
};
export default Index;
