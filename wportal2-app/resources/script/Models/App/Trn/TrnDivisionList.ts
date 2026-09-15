import { ListBase } from '@/script/Models/ListBase';
import {
  IAppTrnDivisionUser,
  parseAppTrnDivisionUserPayload,
} from '@/script/Models/App/Trn/TrnDivisionUserList';
import {
  IAppTrnUserDivisionPriority,
  parseAppTrnUserDivisionPriorityPayload,
} from '@/script/Models/App/Trn/TrnUserDivisionPriorityList';

export interface IAppTrnDivision {
  id: number;
  name: string;
  explain: string;
  displayOrder: number;

  trnDivisionUser?: IAppTrnDivisionUser[];

  trnUserDivisionPriority?: IAppTrnUserDivisionPriority[];
}

export const parseAppTrnDivisionPayload = (
  payload: IAppTrnDivision,
): IAppTrnDivision => {
  return {
    id: payload.id ? Number(payload.id) : 0,
    name: payload.name ? String(payload.name) : '',
    explain: payload.explain ? String(payload.explain) : '',
    displayOrder: payload.displayOrder ? Number(payload.displayOrder) : 0,

    trnDivisionUser: payload.trnDivisionUser
      ? payload.trnDivisionUser.map(trnDivisionUser =>
          parseAppTrnDivisionUserPayload(trnDivisionUser),
        )
      : [],

    trnUserDivisionPriority: payload.trnUserDivisionPriority
      ? payload.trnUserDivisionPriority.map(trnUserDivisionPriority =>
          parseAppTrnUserDivisionPriorityPayload(trnUserDivisionPriority),
        )
      : [],
  };
};

export class AppTrnDivisionList extends ListBase<IAppTrnDivision> {
  constructor(data: IAppTrnDivision[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnDivision {
    return {
      id: 0,
      name: '',
      explain: '',
      displayOrder: 0,
    };
  }

  default = (): IAppTrnDivision => {
    return AppTrnDivisionList.defaultInterface();
  };

  getPrimary(value: IAppTrnDivision): number {
    return value.id;
  }
}
