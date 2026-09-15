import * as React from 'react';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { IAppTrnUser } from '@/script/Models/App/Trn/TrnUserList';
import { IPropsBase } from '@/script/System/System';
import { MvcValue } from '@/script/Pages/Sample/MvcValue/MvcValue';

/**
 * コントローラーから受け取るprops.
 */
interface IPros extends IPropsBase {
  numberValue: number;
  stringValue: string;
  arrayValue: number[];

  trnUserList: IAppTrnUser[];
}
export const Index: React.FC<IPros> = ({
  numberValue,
  stringValue,
  arrayValue,

  trnUserList,

  ...props
}) => {
  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <MvcValue
        numberValue={numberValue}
        stringValue={stringValue}
        arrayValue={arrayValue}
        trnUserList={trnUserList}
      />
    </CommonIndexProvider>
  );
};
export default Index;
