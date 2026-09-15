import { DateTime } from '@/script/Common/DateTime';
import { E_WEEK_DAY } from '@/script/Enum/Server/App/EWeekDay';

export interface IDate {
  dateTime: DateTime;
  inRange: boolean;
}

export const getMonthCalendar = (currentDate: DateTime): IDate[][] => {
  const ret: IDate[][] = [];
  const targetDateList: IDate[] = [];
  const DAYS_IN_WEEK = Object.values(E_WEEK_DAY).length;
  const startOfMonth = currentDate.startOfMonth();
  const endOfMonth = currentDate.endOfMonth();
  const daysInMonth = currentDate.daysInMonth();
  const daysInBefore = startOfMonth.getWeekDay();
  const mod = DAYS_IN_WEEK - ((daysInBefore + daysInMonth) % DAYS_IN_WEEK);
  const daysInAfter = mod === DAYS_IN_WEEK ? 0 : mod;
  const dayNum = daysInBefore + daysInMonth + daysInAfter;
  const firstDate = startOfMonth.subDay(daysInBefore);

  for (let i = 0; i < dayNum; ++i) {
    const targetDate = firstDate.addDay(i);
    targetDateList.push({
      dateTime: targetDate,
      inRange: targetDate.inRange(startOfMonth, endOfMonth),
    });
  }

  while (targetDateList.length > 0) {
    ret.push(targetDateList.splice(0, DAYS_IN_WEEK));
  }

  return ret;
};
