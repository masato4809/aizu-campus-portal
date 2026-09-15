import { IAppTrnUser } from '@/script/Models/App/Trn/TrnUserList';
import { EWorkingPlace } from '@/script/Enum/Server/App/EWorkingPlace';
import { EWorkingState } from '@/script/Enum/Server/App/EWorkingState';
import { IAppTrnAttendanceState } from '@/script/Models/App/Trn/TrnAttendanceStateList';

export interface IAttendanceState {
  trnUser: IAppTrnUser;
  isWorking: boolean;
  isRest: boolean;
  workingState: EWorkingState;
  workingPlace: EWorkingPlace;
  lastAttendanceState: IAppTrnAttendanceState;
}
