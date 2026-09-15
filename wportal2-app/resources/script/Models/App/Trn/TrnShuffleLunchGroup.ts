import { ListBase } from '@/script/Models/ListBase';
import {
  E_EVENT_TIME_ZONE,
  EEventTimeZone,
} from '@/script/Enum/Server/App/ShuffleLunch/EEventTimeZone';
import {
  IAppTrnUser,
  parseAppTrnUserPayload,
} from '@/script/Models/App/Trn/TrnUserList';

export interface IAppTrnShuffleLunchGroup {
  id: number;
  eventDate: string;
  eventTimeZone: EEventTimeZone;
  groupId: number;
  trnUserId: number;
  rakumoBlocked: number;

  trnUser?: IAppTrnUser;
}

export const parseAppTrnShuffleLunchGroupPayload = (
  payload: IAppTrnShuffleLunchGroup,
): IAppTrnShuffleLunchGroup => {
  return {
    id: payload.id ? Number(payload.id) : 0,
    eventDate: payload.eventDate ? String(payload.eventDate) : '',
    eventTimeZone: payload.eventTimeZone
      ? (Number(payload.eventTimeZone) as EEventTimeZone)
      : 0,
    groupId: payload.groupId ? Number(payload.groupId) : 0,
    trnUserId: payload.trnUserId ? Number(payload.trnUserId) : 0,
    rakumoBlocked: payload.rakumoBlocked ? Number(payload.rakumoBlocked) : 0,

    trnUser: payload.trnUser
      ? parseAppTrnUserPayload(payload.trnUser)
      : undefined,
  };
};

export class AppTrnShuffleLunchGroupList extends ListBase<IAppTrnShuffleLunchGroup> {
  constructor(data: IAppTrnShuffleLunchGroup[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnShuffleLunchGroup {
    return {
      id: 0,
      eventDate: '',
      eventTimeZone: E_EVENT_TIME_ZONE.INVALID,
      groupId: 0,
      trnUserId: 0,
      rakumoBlocked: 0,
    };
  }

  default = (): IAppTrnShuffleLunchGroup => {
    return AppTrnShuffleLunchGroupList.defaultInterface();
  };

  getPrimary(value: IAppTrnShuffleLunchGroup): number {
    return value.id;
  }

  /**
   * イベントゾーンを指定して、グループIDの配列を取得.
   */
  getGroupIdListByEventTimeZone = (eventTimeZone: EEventTimeZone): number[] => {
    return [
      ...new Set(
        this.list()
          .filter(v => v.eventTimeZone === eventTimeZone)
          .map(v => v.groupId),
      ),
    ];
  };

  /**
   * イベントゾーン・グループIDを指定してグループ情報の配列を取得.
   */
  getGroupListByEventTimeZoneAndGroupId = (
    eventTimeZone: EEventTimeZone,
    groupId: number,
  ): IAppTrnShuffleLunchGroup[] => {
    return this.list().filter(
      v => v.eventTimeZone === eventTimeZone && v.groupId === groupId,
    );
  };

  /**
   * 指定ユーザーIDによるグループの検索.
   */
  findGroupListByTrnUserId = (
    trnUserId: number,
  ): IAppTrnShuffleLunchGroup[] => {
    const targetGroup = this.list().find(v => v.trnUserId === trnUserId);
    if (!targetGroup) {
      return [];
    }

    return this.list().filter(
      v =>
        v.groupId === targetGroup.groupId &&
        v.eventTimeZone === targetGroup.eventTimeZone,
    );
  };
}
