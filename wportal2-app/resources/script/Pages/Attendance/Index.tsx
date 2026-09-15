import * as React from 'react';
import { useContext, useMemo } from 'react';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { Attendance } from '@/script/Pages/Attendance/Attendance';
import { AppTrnAttendanceStateList } from '@/script/Models/App/Trn/TrnAttendanceStateList';
import {
  AppTrnProjectList,
  IAppTrnProject,
  parseAppTrnProjectPayload,
} from '@/script/Models/App/Trn/TrnProjectList';
import {
  AppTrnUserList,
  IAppTrnUser,
  parseAppTrnUserPayload,
} from '@/script/Models/App/Trn/TrnUserList';
import { IAttendanceState } from '@/script/Pages/Attendance/IAttendanceState';
import {
  AppTrnDivisionList,
  IAppTrnDivision,
  parseAppTrnDivisionPayload,
} from '@/script/Models/App/Trn/TrnDivisionList';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  trnProjectList: AppTrnProjectList;
  trnDivisionList: AppTrnDivisionList;
  trnUserList: AppTrnUserList;
  stateList: IAttendanceState[];
  message: string;
};
export const context = React.createContext<IndexContext>({
  trnProjectList: new AppTrnProjectList(),
  trnDivisionList: new AppTrnDivisionList(),
  trnUserList: new AppTrnUserList(),
  stateList: [],
  message: '',
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  trnProjectList: IAppTrnProject[];
  trnDivisionList: IAppTrnDivision[];
  trnUserList: IAppTrnUser[];
  message: string;
}
export const Index: React.FC<IProps> = ({
  trnProjectList,
  trnDivisionList,
  trnUserList,
  message,
  ...props
}) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(() => {
    return {
      trnProjectList: new AppTrnProjectList(
        trnProjectList.map(v => parseAppTrnProjectPayload(v)),
      ),
      trnDivisionList: new AppTrnDivisionList(
        trnDivisionList.map(v => parseAppTrnDivisionPayload(v)),
      ),
      trnUserList: new AppTrnUserList(
        trnUserList.map(v => parseAppTrnUserPayload(v)),
      ),

      // 初回取得時に各ユーザーの出勤状態を一括で取得しておく.
      // TODO: 一括で変換する関数を作成する.
      stateList: trnUserList.map(trnUser => {
        return {
          trnUser,
          isWorking: AppTrnAttendanceStateList.isWorking(
            trnUser.trnAttendanceState,
          ),
          isRest: AppTrnAttendanceStateList.isRest(trnUser.trnAttendanceState),
          workingPlace: AppTrnAttendanceStateList.lastWorkingPlace(
            trnUser.trnAttendanceState,
          ),
          workingState: AppTrnAttendanceStateList.lastWorkingState(
            trnUser.trnAttendanceState,
          ),
          lastAttendanceState: AppTrnAttendanceStateList.lastAttendanceState(
            trnUser.trnAttendanceState,
          ),
        };
      }),

      // メッセージ.
      message,
    };
  }, [trnDivisionList, trnProjectList, trnUserList, message]);

  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <context.Provider value={providerValue}>
        <Attendance />
      </context.Provider>
    </CommonIndexProvider>
  );
};
export default Index;
