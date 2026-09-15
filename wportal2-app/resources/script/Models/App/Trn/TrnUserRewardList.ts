import { ListBase } from '@/script/Models/ListBase';
import {
  IAppMstReward,
  parseAppMstRewardPayload,
} from '@/script/Models/App/Mst/MstRewardList';

export interface IAppTrnUserReward {
  id: number;
  trnUserId: number;
  mstRewardId: number;
  achievementCount: number;
  receivedCount: number;
  achievedAt: string;
  receivedAt: string;

  mstReward?: IAppMstReward;
}

export const parseAppTrnUserRewardPayload = (
  payload?: IAppTrnUserReward,
): IAppTrnUserReward => {
  return {
    id: payload?.id ? Number(payload.id) : 0,
    trnUserId: payload?.trnUserId ? Number(payload.trnUserId) : 0,
    mstRewardId: payload?.mstRewardId ? Number(payload.mstRewardId) : 0,
    achievementCount: payload?.achievementCount
      ? Number(payload.achievementCount)
      : 0,
    receivedCount: payload?.receivedCount ? Number(payload.receivedCount) : 0,
    achievedAt: payload?.achievedAt ? String(payload.achievedAt) : '',
    receivedAt: payload?.receivedAt ? String(payload.receivedAt) : '',

    mstReward: payload?.mstReward
      ? parseAppMstRewardPayload(payload.mstReward)
      : undefined,
  };
};

export class AppTrnUserRewardList extends ListBase<IAppTrnUserReward> {
  constructor(data: IAppTrnUserReward[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnUserReward {
    return {
      id: 0,
      trnUserId: 0,
      mstRewardId: 0,
      achievementCount: 0,
      receivedCount: 0,
      achievedAt: '',
      receivedAt: '',
    };
  }

  default = (): IAppTrnUserReward => {
    return AppTrnUserRewardList.defaultInterface();
  };

  getPrimary(value: IAppTrnUserReward): number {
    return value.id;
  }

  /**
   * リストの中に獲得可能な報酬があるかどうか.
   */
  public hasAchievableReward(): boolean {
    return this.list().some(
      (trnUserReward: IAppTrnUserReward) =>
        trnUserReward.achievementCount > trnUserReward.receivedCount,
    );
  }

  /**
   * 獲得可能なゴールドの合計.
   */
  public totalAchievableGold(): number {
    return this.list().reduce((prev, trnUserReward) => {
      const achievableCount = Math.max(
        trnUserReward.achievementCount - trnUserReward.receivedCount,
        0,
      );
      return (
        prev + Number(trnUserReward.mstReward?.rewardGold) * achievableCount
      );
    }, 0);
  }
}
