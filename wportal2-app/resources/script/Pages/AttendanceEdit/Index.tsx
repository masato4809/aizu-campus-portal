import * as React from 'react';
import { useContext, useMemo } from 'react';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { AttendanceEdit } from '@/script/Pages/AttendanceEdit/AttendanceEdit';
import {
  AppTrnAttendanceStateList,
  IAppTrnAttendanceState,
  parseAppTrnAttendanceStatePayload,
} from '@/script/Models/App/Trn/TrnAttendanceStateList';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  dateTime: string;
  trnAttendanceStateList: AppTrnAttendanceStateList;
};
export const context = React.createContext<IndexContext>({
  dateTime: '',
  trnAttendanceStateList: new AppTrnAttendanceStateList(),
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  dateTime: string;
  trnAttendanceStateList: IAppTrnAttendanceState[];
}
export const Index: React.FC<IProps> = ({
  dateTime,
  trnAttendanceStateList,
  ...props
}) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(
    () => ({
      dateTime,
      trnAttendanceStateList: new AppTrnAttendanceStateList(
        trnAttendanceStateList.map(v => parseAppTrnAttendanceStatePayload(v)),
      ),
    }),
    [dateTime, trnAttendanceStateList],
  );

  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <context.Provider value={providerValue}>
        <AttendanceEdit />
      </context.Provider>
    </CommonIndexProvider>
  );
};
export default Index;
