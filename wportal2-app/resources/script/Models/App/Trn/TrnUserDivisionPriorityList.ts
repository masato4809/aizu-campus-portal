import { ListBase } from '@/script/Models/ListBase';

export interface IAppTrnUserDivisionPriority {
  id: number;
  trnUserId: number;
  trnDivisionId: number;
  divisionPriority: number;
}

export const parseAppTrnUserDivisionPriorityPayload = (
  payload: IAppTrnUserDivisionPriority,
): IAppTrnUserDivisionPriority => {
  return {
    id: payload.id ? Number(payload.id) : 0,
    trnUserId: payload.trnUserId ? Number(payload.trnUserId) : 0,
    trnDivisionId: payload.trnDivisionId ? Number(payload.trnDivisionId) : 0,
    divisionPriority: payload.divisionPriority
      ? Number(payload.divisionPriority)
      : 0,
  };
};

export class AppTrnUserDivisionPriorityList extends ListBase<IAppTrnUserDivisionPriority> {
  constructor(data: IAppTrnUserDivisionPriority[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnUserDivisionPriority {
    return {
      id: 0,
      trnDivisionId: 0,
      trnUserId: 0,
      divisionPriority: 0,
    };
  }

  default = (): IAppTrnUserDivisionPriority => {
    return AppTrnUserDivisionPriorityList.defaultInterface();
  };

  getPrimary(value: IAppTrnUserDivisionPriority): number {
    return value.id;
  }

  /**
   * 最初に見つかった優先度を取得.
   */
  public static getPriority(list?: IAppTrnUserDivisionPriority[]): number {
    if (!list?.length) {
      return 1;
    }
    return list[0].divisionPriority;
  }
}
