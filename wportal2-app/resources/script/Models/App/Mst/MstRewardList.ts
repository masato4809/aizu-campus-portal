import {
  E_ENABLE_FLAG,
  EEnableFlag,
} from '@/script/Enum/Server/App/EEnableFlag';
import { ListBase } from '@/script/Models/ListBase';
import { IAppAuthUser } from '@/script/Models/App/Auth/AuthUserList';

export interface IAppMstReward {
  id: number;
  rewardKind: number;
  rewardRank: number;
  rewardName: string;
  rewardExplain: string;
  rewardGold: number;
  eEnableRepeat: EEnableFlag;
}

export const parseAppMstRewardPayload = (
  payload: IAppMstReward,
): IAppMstReward => {
  return {
    id: payload?.id ? Number(payload.id) : 0,
    rewardKind: payload?.rewardKind ? Number(payload.rewardKind) : 0,
    rewardRank: payload?.rewardRank ? Number(payload.rewardRank) : 0,
    rewardName: payload?.rewardName ? String(payload.rewardName) : '',
    rewardExplain: payload?.rewardExplain ? String(payload.rewardExplain) : '',
    rewardGold: payload?.rewardGold ? Number(payload.rewardGold) : 0,
    eEnableRepeat: payload?.eEnableRepeat
      ? (Number(payload.eEnableRepeat) as EEnableFlag)
      : E_ENABLE_FLAG.INVALID,
  };
};

export class AppMstRewardList extends ListBase<IAppMstReward> {
  constructor(data: IAppMstReward[] = []) {
    super(data);
  }

  static defaultInterface(): IAppMstReward {
    return {
      id: 0,
      rewardKind: 0,
      rewardRank: 0,
      rewardName: '',
      rewardExplain: '',
      rewardGold: 0,
      eEnableRepeat: E_ENABLE_FLAG.INVALID,
    };
  }

  default = (): IAppMstReward => {
    return AppMstRewardList.defaultInterface();
  };

  getPrimary(value: IAppAuthUser): number {
    return value.id;
  }
}
