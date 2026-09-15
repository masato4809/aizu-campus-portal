import {
  attendanceToPlace,
  attendanceToWorking,
  E_ATTENDANCE_STATE,
  EAttendanceState,
} from '@/script/Enum/Server/App/EAttendanceState';
import { ListBase } from '@/script/Models/ListBase';
import {
  E_WORKING_STATE,
  EWorkingState,
} from '@/script/Enum/Server/App/EWorkingState';
import {
  E_WORKING_PLACE,
  EWorkingPlace,
} from '@/script/Enum/Server/App/EWorkingPlace';

export interface IAppTrnAttendanceState {
  id: number;
  trnUserId: number;
  eAttendanceState: EAttendanceState;
  createdAt: string;
  updatedAt: string;
}

export const parseAppTrnAttendanceStatePayload = (
  payload: IAppTrnAttendanceState,
): IAppTrnAttendanceState => {
  return {
    id: payload.id ? Number(payload.id) : 0,
    trnUserId: payload.trnUserId ? Number(payload.trnUserId) : 0,
    eAttendanceState: payload.eAttendanceState
      ? (Number(payload.eAttendanceState) as EAttendanceState)
      : E_ATTENDANCE_STATE.INVALID,
    createdAt: payload.createdAt ? String(payload.createdAt) : '',
    updatedAt: payload.updatedAt ? String(payload.updatedAt) : '',
  };
};

export class AppTrnAttendanceStateList extends ListBase<IAppTrnAttendanceState> {
  constructor(data: IAppTrnAttendanceState[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnAttendanceState {
    return {
      id: 0,
      trnUserId: 0,
      eAttendanceState: E_ATTENDANCE_STATE.INVALID,
      createdAt: '',
      updatedAt: '',
    };
  }

  default = (): IAppTrnAttendanceState => {
    return AppTrnAttendanceStateList.defaultInterface();
  };

  getPrimary(value: IAppTrnAttendanceState): number {
    return value.id;
  }

  /**
   * 指定ユーザーIDの配列を取得.
   */
  public listByTrnUserId = (trnUserId?: number): IAppTrnAttendanceState[] => {
    return this.array.filter(v => v.trnUserId === trnUserId);
  };

  /**
   * 指定ユーザーIDの最新の勤務状態を取得.
   */
  public static lastWorkingState = (
    list?: IAppTrnAttendanceState[],
  ): EWorkingState => {
    if (!list) {
      return E_WORKING_STATE.INVALID;
    }
    const reverse = list.toReversed();
    const attendanceWithWorking = reverse.find(
      it => attendanceToWorking(it.eAttendanceState) !== E_WORKING_STATE.NONE,
    );
    if (attendanceWithWorking === undefined) {
      return E_WORKING_STATE.NONE;
    }
    return attendanceToWorking(attendanceWithWorking.eAttendanceState);
  };

  /**
   * 指定ユーザーIDの最新の勤務環境を取得.
   */
  public static lastWorkingPlace = (
    list?: IAppTrnAttendanceState[],
  ): EWorkingPlace => {
    if (!list) {
      return E_WORKING_PLACE.INVALID;
    }
    const reverse = list.toReversed();
    const attendanceWithPlace = reverse.find(
      it => attendanceToPlace(it.eAttendanceState) !== E_WORKING_PLACE.INVALID,
    );
    if (!attendanceWithPlace) {
      return E_WORKING_PLACE.INVALID;
    }
    return attendanceToPlace(attendanceWithPlace.eAttendanceState);
  };

  /**
   * 指定ユーザーIDが勤務状態かどうか.
   */
  public static isWorking = (list?: IAppTrnAttendanceState[]): boolean => {
    const state = this.lastWorkingState(list);
    switch (state) {
      case E_WORKING_STATE.NONE:
      case E_WORKING_STATE.LEAVING:
        return false;
      default:
        return true;
    }
  };

  /**
   * 指定ユーザーIDが休憩中かどうか.
   */
  public static isRest = (list?: IAppTrnAttendanceState[]): boolean => {
    const state = this.lastWorkingState(list);
    switch (state) {
      case E_WORKING_STATE.REST:
        return true;
      default:
        return false;
    }
  };

  /**
   * 指定ユーザーの最新の出退勤アクションを取得.
   */
  public static lastAttendanceState = (
    list?: IAppTrnAttendanceState[],
  ): IAppTrnAttendanceState => {
    if (!list) {
      return AppTrnAttendanceStateList.defaultInterface();
    }
    const reverse = list.toReversed();
    const attendanceState = reverse.find(_it => true);
    if (attendanceState === undefined) {
      return AppTrnAttendanceStateList.defaultInterface();
    }
    return attendanceState;
  };
}
