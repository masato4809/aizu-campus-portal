import * as React from 'react';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { IPropsBase } from '@/script/System/System';
import { SlackLog } from '@/script/Pages/Sample/SlackLog/SlackLog';

interface IProps extends IPropsBase {}
export const Index: React.FC<IProps> = ({ ...props }) => {
  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <SlackLog />
    </CommonIndexProvider>
  );
};
export default Index;
