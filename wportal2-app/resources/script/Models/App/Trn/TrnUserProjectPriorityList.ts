import { ListBase } from '@/script/Models/ListBase';

export interface IAppTrnUserProjectPriority {
  id: number;
  trnUserId: number;
  trnProjectId: number;
  projectPriority: number;
}

export const parseAppTrnUserProjectPriorityPayload = (
  payload: IAppTrnUserProjectPriority,
): IAppTrnUserProjectPriority => {
  return {
    id: payload.id ? Number(payload.id) : 0,
    trnUserId: payload.trnUserId ? Number(payload.trnUserId) : 0,
    trnProjectId: payload.trnProjectId ? Number(payload.trnProjectId) : 0,
    projectPriority: payload.projectPriority
      ? Number(payload.projectPriority)
      : 0,
  };
};

export class AppTrnUserProjectPriorityList extends ListBase<IAppTrnUserProjectPriority> {
  constructor(data: IAppTrnUserProjectPriority[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnUserProjectPriority {
    return {
      id: 0,
      trnProjectId: 0,
      trnUserId: 0,
      projectPriority: 0,
    };
  }

  default = (): IAppTrnUserProjectPriority => {
    return AppTrnUserProjectPriorityList.defaultInterface();
  };

  getPrimary(value: IAppTrnUserProjectPriority): number {
    return value.id;
  }

  /**
   * 最初に見つかった優先度を取得.
   */
  public static getPriority(list?: IAppTrnUserProjectPriority[]): number {
    if (!list?.length) {
      return 1;
    }
    return list[0].projectPriority;
  }
}
