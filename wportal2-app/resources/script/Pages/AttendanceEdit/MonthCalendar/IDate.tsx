import { DateTime } from '@/script/Common/DateTime';
import { IEvent } from '@/script/Pages/AttendanceEdit/MonthCalendar/IEvent';

export interface IDate {
  dateTime: DateTime;
  inRange: boolean;
  eventList?: IEvent[];
}

export const defaultInterface: IDate = {
  dateTime: DateTime.now(),
  inRange: false,
};
