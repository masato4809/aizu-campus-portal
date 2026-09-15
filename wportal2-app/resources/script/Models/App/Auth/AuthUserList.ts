import { ListBase } from '@/script/Models/ListBase';
import {
  IAppTrnUser,
  parseAppTrnUserPayload,
} from '@/script/Models/App/Trn/TrnUserList';

export interface IAppAuthUser {
  id: number;
  name: string;
  email: string;
  eEnableLegacyLogin: number;
  eEnableSpLogin: number;
  spLoginFailedCount: number;

  trnUser?: IAppTrnUser;
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const parseAppAuthUserPayload = (payload: any): IAppAuthUser => {
  return {
    id: payload?.id ? Number(payload.id) : 0,
    name: payload?.name ? String(payload.name) : '',
    email: payload?.email ? String(payload.email) : '',
    eEnableLegacyLogin: payload?.eEnableLegacyLogin
      ? Number(payload.eEnableLegacyLogin)
      : 0,
    eEnableSpLogin: payload?.eEnableSpLogin
      ? Number(payload.eEnableSpLogin)
      : 0,
    spLoginFailedCount: payload?.spLoginFailedCount
      ? Number(payload.spLoginFailedCount)
      : 0,

    trnUser: payload?.trnUser
      ? parseAppTrnUserPayload(payload.trnUser)
      : undefined,
  };
};

export class AppAuthUserList extends ListBase<IAppAuthUser> {
  constructor(data: IAppAuthUser[] = []) {
    super(data);
  }

  static defaultInterface(): IAppAuthUser {
    return {
      id: 0,
      name: '',
      email: '',
      eEnableLegacyLogin: 0,
      eEnableSpLogin: 0,
      spLoginFailedCount: 0,
    };
  }

  default = (): IAppAuthUser => {
    return AppAuthUserList.defaultInterface();
  };

  getPrimary(value: IAppAuthUser): number {
    return value.id;
  }
}
