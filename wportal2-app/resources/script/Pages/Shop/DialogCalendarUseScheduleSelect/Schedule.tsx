import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { E_COLOR } from '@/script/Enum/EColor';
import { DateTime } from '@/script/Common/DateTime';
import { IUsecasesCalendarUserSchedule } from '@/script/Models/Usecases/UsecasesCalendarUserScheduleList';
import {
  ColorTable,
  IScheduleBlock,
  ScheduleDateArea,
} from '@/script/Pages/Shop/DialogCalendarUseScheduleSelect/ScheduleDateArea';

interface IProps extends IPropsBase {
  dateList: DateTime[];
  scheduleList: IUsecasesCalendarUserSchedule[];
  selected?: IScheduleBlock;
  colorTable: ColorTable;
  handleSelect: (block: IScheduleBlock) => void;
}
export const Schedule: React.FC<IProps> = ({
  sx,
  dateList,
  scheduleList,
  selected,
  colorTable,
  handleSelect,
}) => {
  /**
   * 指定日付における選択済み情報.
   * @param date
   */
  const selectedInDate = (date: DateTime): IScheduleBlock | undefined => {
    if (!selected) {
      return undefined;
    }

    return date.isSameDay(selected.start) ? selected : undefined;
  };

  return (
    <Box
      sx={{
        backgroundColor: E_COLOR.WHITE,
        display: 'flex',
        borderRight: `1px solid ${E_COLOR.GREY}`,
        borderBottom: `1px solid ${E_COLOR.GREY}`,
        ...sx,
      }}
    >
      {dateList.map(date => {
        return (
          <ScheduleDateArea
            key={date.getWeekDay()}
            sx={{
              width: '200px',
            }}
            date={date}
            scheduleList={scheduleList.filter(schedule =>
              date.isSameDay(DateTime.parseString(schedule.start)),
            )}
            selected={selectedInDate(date)}
            colorTable={colorTable}
            handleSelect={handleSelect}
          />
        );
      })}
    </Box>
  );
};
