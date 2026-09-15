import { ListBase } from '@/script/Models/ListBase';

export interface IUsecasesCalendarUserSchedule {
  identify: string;
  eventId: string;
  calendarId: string;
  kind: string;
  summary: string;
  start: string;
  end: string;
}

export const parseUsecasesCalendarUserSchedulePayload = (
  payload?: IUsecasesCalendarUserSchedule,
): IUsecasesCalendarUserSchedule => {
  return {
    identify: payload?.identify ? String(payload.identify) : '',
    eventId: payload?.eventId ? String(payload.eventId) : '',
    calendarId: payload?.calendarId ? String(payload.calendarId) : '',
    kind: payload?.kind ? String(payload.kind) : '',
    summary: payload?.summary ? String(payload.summary) : '',
    start: payload?.start ? String(payload.start) : '',
    end: payload?.end ? String(payload.end) : '',
  };
};

export class UseCasesCalendarUserScheduleList extends ListBase<IUsecasesCalendarUserSchedule> {
  constructor(data: IUsecasesCalendarUserSchedule[] = []) {
    super(data);
  }

  static defaultInterface(): IUsecasesCalendarUserSchedule {
    return {
      identify: '',
      eventId: '',
      calendarId: '',
      kind: '',
      summary: '',
      start: '',
      end: '',
    };
  }

  default = (): IUsecasesCalendarUserSchedule => {
    return UseCasesCalendarUserScheduleList.defaultInterface();
  };

  getPrimary(value: IUsecasesCalendarUserSchedule): string {
    return value.identify;
  }
}
