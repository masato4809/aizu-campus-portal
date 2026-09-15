import { ListBase } from '@/script/Models/ListBase';
import {
  E_EVENT_TIME_ZONE,
  EEventTimeZone,
} from '@/script/Enum/Server/App/ShuffleLunch/EEventTimeZone';

export interface IAppTrnShuffleLunchEntry {
  id: number;
  eventDate: string;
  eventTimeZone: EEventTimeZone;
  trnUserId: number;
}

export const parseAppTrnShuffleLunchEntryPayload = (
  payload: IAppTrnShuffleLunchEntry,
): IAppTrnShuffleLunchEntry => {
  return {
    id: payload.id ? Number(payload.id) : 0,
    eventDate: payload.eventDate ? String(payload.eventDate) : '',
    eventTimeZone: payload.eventTimeZone
      ? (Number(payload.eventTimeZone) as EEventTimeZone)
      : 0,
    trnUserId: payload.trnUserId ? Number(payload.trnUserId) : 0,
  };
};

export class AppTrnShuffleLunchEntryList extends ListBase<IAppTrnShuffleLunchEntry> {
  constructor(data: IAppTrnShuffleLunchEntry[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnShuffleLunchEntry {
    return {
      id: 0,
      eventDate: '',
      eventTimeZone: E_EVENT_TIME_ZONE.INVALID,
      trnUserId: 0,
    };
  }

  default = (): IAppTrnShuffleLunchEntry => {
    return AppTrnShuffleLunchEntryList.defaultInterface();
  };

  getPrimary(value: IAppTrnShuffleLunchEntry): number {
    return value.id;
  }
}
