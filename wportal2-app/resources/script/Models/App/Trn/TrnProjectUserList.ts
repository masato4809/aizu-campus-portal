import { ListBase } from '@/script/Models/ListBase';
import {
  IAppTrnUser,
  parseAppTrnUserPayload,
} from '@/script/Models/App/Trn/TrnUserList';

export interface IAppTrnProjectUser {
  id: number;
  trnProjectId: number;
  trnUserId: number;

  trnUser?: IAppTrnUser;
}

export const parseAppTrnProjectUserPayload = (
  payload: IAppTrnProjectUser,
): IAppTrnProjectUser => {
  return {
    id: payload.id ? Number(payload.id) : 0,
    trnProjectId: payload.trnProjectId ? Number(payload.trnProjectId) : 0,
    trnUserId: payload.trnUserId ? Number(payload.trnUserId) : 0,

    trnUser: payload.trnUser
      ? parseAppTrnUserPayload(payload.trnUser)
      : undefined,
  };
};

export class AppTrnProjectUserList extends ListBase<IAppTrnProjectUser> {
  constructor(data: IAppTrnProjectUser[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnProjectUser {
    return {
      id: 0,
      trnProjectId: 0,
      trnUserId: 0,
    };
  }

  default = (): IAppTrnProjectUser => {
    return AppTrnProjectUserList.defaultInterface();
  };

  getPrimary(value: IAppTrnProjectUser): number {
    return value.id;
  }
}
