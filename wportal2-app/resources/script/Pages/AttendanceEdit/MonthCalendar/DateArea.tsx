import * as React from 'react';
import { Box } from '@mui/material';
import { useMemo } from 'react';
import { IPropsBase } from '@/script/System/System';
import { E_COLOR } from '@/script/Enum/EColor';
import { useIndexContext } from '@/script/Pages/AttendanceEdit/Index';
import { DateTime } from '@/script/Common/DateTime';
import {
  IDate as IBaseDate,
  getMonthCalendar,
} from '@/script/Common/getMonthCalendar';
import { getShortLabelAttendanceState } from '@/script/Enum/Server/App/EAttendanceState';
import { IEvent } from '@/script/Pages/AttendanceEdit/MonthCalendar/IEvent';
import { DialogEdit } from '@/script/Pages/AttendanceEdit/DialogEdit';
import { Closable } from '@/script/Component/Misc/Closable';
import { DateCell } from '@/script/Pages/AttendanceEdit/MonthCalendar/DateCell';

export interface IAttendanceDate extends IBaseDate {
  eventList: IEvent[];
}

interface IProps extends IPropsBase {}
export const DateArea: React.FC<IProps> = ({ sx }) => {
  const { trnAttendanceStateList } = useIndexContext();
  const dateTime = DateTime.parseString(useIndexContext().dateTime);
  const [editDate, setEditDate] = React.useState<IAttendanceDate | undefined>(
    undefined,
  );

  // イベントリストを得る.
  const eventList: IEvent[] = useMemo(() => {
    return trnAttendanceStateList.list().map(attendance => {
      const time = DateTime.parseString(attendance.updatedAt).toHHMM();
      const action = getShortLabelAttendanceState(attendance.eAttendanceState);
      return {
        uid: attendance.id,
        start: DateTime.parseString(attendance.updatedAt),
        eAttendanceState: attendance.eAttendanceState,
        label: `${time} ${action}`,
        createdAt: DateTime.parseString(attendance.createdAt),
        updatedAt: DateTime.parseString(attendance.updatedAt),
      };
    });
  }, [trnAttendanceStateList]);

  const baseDateList = useMemo(() => getMonthCalendar(dateTime), [dateTime]);

  /**
   * カレンダーに表示する日付のリスト.
   */
  const dateList: IAttendanceDate[][] = useMemo(() => {
    return baseDateList.map(week => {
      return week.map(day => {
        const targetEventList = eventList.filter(event =>
          event.start.isSameDay(day.dateTime),
        );
        return {
          ...day,
          eventList: targetEventList,
        };
      });
    });
  }, [baseDateList, eventList]);

  /**
   * 日付が選択された.
   */
  const handleSelect = (date: IAttendanceDate) => {
    if (!date.inRange) {
      return;
    }
    setEditDate(date);
  };

  /**
   * ダイアログが閉じられた.
   */
  const handleClose = () => {
    setEditDate(undefined);
  };

  return (
    <Box
      sx={{
        display: 'grid',
        gridTemplateColumns: 'repeat(7, calc(100% / 7))',
        borderRight: `1px solid ${E_COLOR.GREY}`,
        ...sx,
      }}
    >
      <Closable open={!!editDate}>
        <DialogEdit
          open={!!editDate}
          date={editDate}
          handleClose={handleClose}
        />
      </Closable>
      {dateList.map(week => {
        return (
          <Box
            key={week[0].dateTime.toJsTimeStamp()}
            sx={{
              display: 'contents',
            }}
          >
            {week.map(date => {
              return (
                <DateCell
                  key={date.dateTime.toJsTimeStamp()}
                  date={date}
                  handleSelect={handleSelect}
                />
              );
            })}
          </Box>
        );
      })}
    </Box>
  );
};
