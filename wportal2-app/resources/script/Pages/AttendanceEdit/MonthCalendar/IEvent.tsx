import { DateTime } from '@/script/Common/DateTime';
import { EAttendanceState } from '@/script/Enum/Server/App/EAttendanceState';

export interface IEvent {
  uid?: number;
  start: DateTime;
  end?: DateTime;
  eAttendanceState: EAttendanceState;
  label?: string;
  createdAt: DateTime;
  updatedAt: DateTime;
}
