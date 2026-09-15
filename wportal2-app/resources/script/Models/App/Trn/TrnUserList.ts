import { ListBase } from '@/script/Models/ListBase';
import {
  IAppTrnUserSlackProfile,
  parseAppTrnUserSlackProfilePayload,
} from '@/script/Models/App/Trn/TrnUserSlackProfileList';
import {
  IAppTrnAttendanceState,
  parseAppTrnAttendanceStatePayload,
} from '@/script/Models/App/Trn/TrnAttendanceStateList';
import {
  IAppAuthUser,
  parseAppAuthUserPayload,
} from '@/script/Models/App/Auth/AuthUserList';

export interface IAppTrnUser {
  id: number;
  authId: number;
  nickname: string;
  currentGold: number;
  accumulationGold: number;
  birthDate: string;
  selfIntroduction: string;
  faceImagePath: string;

  trnAttendanceState?: IAppTrnAttendanceState[];

  trnUserSlackProfile?: IAppTrnUserSlackProfile;

  authUser?: IAppAuthUser;
}

export const parseAppTrnUserPayload = (payload?: IAppTrnUser): IAppTrnUser => {
  return {
    id: payload?.id ? Number(payload.id) : 0,
    authId: payload?.authId ? Number(payload.authId) : 0,
    nickname: payload?.nickname ? String(payload.nickname) : '',
    currentGold: payload?.currentGold ? Number(payload.currentGold) : 0,
    accumulationGold: payload?.accumulationGold
      ? Number(payload.accumulationGold)
      : 0,
    birthDate: payload?.birthDate ? String(payload.birthDate) : '',
    selfIntroduction: payload?.selfIntroduction
      ? String(payload.selfIntroduction)
      : '',
    faceImagePath: payload?.faceImagePath ? String(payload.faceImagePath) : '',

    authUser: payload?.authUser
      ? parseAppAuthUserPayload(payload.authUser)
      : undefined,

    trnAttendanceState: payload?.trnAttendanceState
      ? payload.trnAttendanceState.map(v =>
          parseAppTrnAttendanceStatePayload(v),
        )
      : [],

    trnUserSlackProfile: payload?.trnUserSlackProfile
      ? parseAppTrnUserSlackProfilePayload(payload.trnUserSlackProfile)
      : undefined,
  };
};

export class AppTrnUserList extends ListBase<IAppTrnUser> {
  constructor(data: IAppTrnUser[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnUser {
    return {
      id: 0,
      authId: 0,
      nickname: '',
      currentGold: 0,
      accumulationGold: 0,
      birthDate: '',
      selfIntroduction: '',
      faceImagePath: '',
    };
  }

  default = (): IAppTrnUser => {
    return AppTrnUserList.defaultInterface();
  };

  getPrimary(value: IAppTrnUser): number {
    return value.id;
  }
}
