import { ListBase } from '@/script/Models/ListBase';
import {
  IAppTrnUser,
  parseAppTrnUserPayload,
} from '@/script/Models/App/Trn/TrnUserList';

export interface IAppTrnDivisionUser {
  id: number;
  trnDivisionId: number;
  trnUserId: number;

  trnUser?: IAppTrnUser;
}

export const parseAppTrnDivisionUserPayload = (
  payload: IAppTrnDivisionUser,
): IAppTrnDivisionUser => {
  return {
    id: payload.id ? Number(payload.id) : 0,
    trnDivisionId: payload.trnDivisionId ? Number(payload.trnDivisionId) : 0,
    trnUserId: payload.trnUserId ? Number(payload.trnUserId) : 0,

    trnUser: payload.trnUser
      ? parseAppTrnUserPayload(payload.trnUser)
      : undefined,
  };
};

export class AppTrnDivisionUserList extends ListBase<IAppTrnDivisionUser> {
  constructor(data: IAppTrnDivisionUser[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnDivisionUser {
    return {
      id: 0,
      trnDivisionId: 0,
      trnUserId: 0,
    };
  }

  default = (): IAppTrnDivisionUser => {
    return AppTrnDivisionUserList.defaultInterface();
  };

  getPrimary(value: IAppTrnDivisionUser): number {
    return value.id;
  }
}
