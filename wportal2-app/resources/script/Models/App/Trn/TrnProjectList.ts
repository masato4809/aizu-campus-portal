import { ListBase } from '@/script/Models/ListBase';
import {
  IAppTrnProjectUser,
  parseAppTrnProjectUserPayload,
} from '@/script/Models/App/Trn/TrnProjectUserList';
import {
  IAppTrnUserProjectPriority,
  parseAppTrnUserProjectPriorityPayload,
} from '@/script/Models/App/Trn/TrnUserProjectPriorityList';
import {
  IAppTrnProjectNotification,
  parseAppTrnProjectNotification,
} from '@/script/Models/App/Trn/TrnProjectNotification';

export interface IAppTrnProject {
  id: number;
  name: string;
  explain: string;
  displayOrder: number;

  trnProjectUser?: IAppTrnProjectUser[];

  trnUserProjectPriority?: IAppTrnUserProjectPriority[];

  trnProjectNotification?: IAppTrnProjectNotification[];
}

export const parseAppTrnProjectPayload = (
  payload: IAppTrnProject,
): IAppTrnProject => {
  return {
    id: payload.id ? Number(payload.id) : 0,
    name: payload.name ? String(payload.name) : '',
    explain: payload.explain ? String(payload.explain) : '',
    displayOrder: payload.displayOrder ? Number(payload.displayOrder) : 0,

    trnProjectUser: payload.trnProjectUser
      ? payload.trnProjectUser.map(trnProjectUser =>
          parseAppTrnProjectUserPayload(trnProjectUser),
        )
      : [],

    trnUserProjectPriority: payload.trnUserProjectPriority
      ? payload.trnUserProjectPriority.map(trnUserProjectPriority =>
          parseAppTrnUserProjectPriorityPayload(trnUserProjectPriority),
        )
      : [],

    trnProjectNotification: payload.trnProjectNotification
      ? payload.trnProjectNotification.map(trnProjectNotification =>
          parseAppTrnProjectNotification(trnProjectNotification),
        )
      : [],
  };
};

export class AppTrnProjectList extends ListBase<IAppTrnProject> {
  constructor(data: IAppTrnProject[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnProject {
    return {
      id: 0,
      name: '',
      explain: '',
      displayOrder: 0,
    };
  }

  default = (): IAppTrnProject => {
    return AppTrnProjectList.defaultInterface();
  };

  getPrimary(value: IAppTrnProject): number {
    return value.id;
  }
}
