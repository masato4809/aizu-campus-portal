import { ListBase } from '@/script/Models/ListBase';

export interface IAppTrnUserSlackProfile {
  id: number;
  trnUserId: number;
  slackUserId: string;
  slackUserName: string;
  slackTeamId: string;
}

export const parseAppTrnUserSlackProfilePayload = (
  payload?: IAppTrnUserSlackProfile,
): IAppTrnUserSlackProfile => {
  return {
    id: payload?.id ? Number(payload.id) : 0,
    trnUserId: payload?.trnUserId ? Number(payload.trnUserId) : 0,
    slackUserId: payload?.slackUserId ? String(payload.slackUserId) : '',
    slackUserName: payload?.slackUserName ? String(payload.slackUserName) : '',
    slackTeamId: payload?.slackTeamId ? String(payload.slackTeamId) : '',
  };
};

export class AppTrnUserSlackProfileList extends ListBase<IAppTrnUserSlackProfile> {
  constructor(data: IAppTrnUserSlackProfile[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnUserSlackProfile {
    return {
      id: 0,
      trnUserId: 0,
      slackUserId: '',
      slackUserName: '',
      slackTeamId: '',
    };
  }

  default = (): IAppTrnUserSlackProfile => {
    return AppTrnUserSlackProfileList.defaultInterface();
  };

  getPrimary(value: IAppTrnUserSlackProfile): number {
    return value.id;
  }
}
