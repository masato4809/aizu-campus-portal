import * as React from 'react';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { IPropsBase } from '@/script/System/System';
import { Intern } from '@/script/Pages/Sample/Intern/Intern';

interface IProps extends IPropsBase {}
export const Index: React.FC<IProps> = ({ ...props }) => {
  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <Intern />
    </CommonIndexProvider>
  );
};
export default Index;
