import * as React from 'react';
import { Box, Divider, TextField } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { IEvent } from '@/script/Pages/AttendanceEdit/MonthCalendar/IEvent';
import { Icon } from '@/script/Component/Misc/Icon';
import { getIconWorkingPlace } from '@/script/Enum/Server/App/EWorkingPlace';
import {
  attendanceToPlace,
  getLabelAttendanceState,
} from '@/script/Enum/Server/App/EAttendanceState';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { DateTime } from '@/script/Common/DateTime';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

export interface IFormDialogEdit {
  eventList: IEvent[];
}

interface IProps extends IPropsBase {
  formDialogEdit: IFormDialogEdit;
  handleUpdate: (newValues: Partial<IFormDialogEdit>) => void;
  handleSubmit: () => void;
}
export const FormDialogEdit: React.FC<IProps> = ({
  sx,
  formDialogEdit,
  handleUpdate,
  handleSubmit,
}) => {
  /**
   * 時刻の変更時の処理.
   */
  const handleChangeTime = (event: IEvent, newTime: string) => {
    // 入力された時刻を取得.
    const newTimeString = `${event.updatedAt.toFormatString('yyyy/MM/dd')} ${newTime}:00`;
    const newDateTime = DateTime.parseString(newTimeString);

    // 変更されたデータを入れ替える.
    const index = formDialogEdit.eventList.findIndex(v => v.uid === event.uid);
    const newValues = formDialogEdit;
    newValues.eventList.splice(index, 1, {
      uid: event.uid,
      start: event.start,
      eAttendanceState: event.eAttendanceState,
      label: event.label,
      createdAt: event.createdAt,
      updatedAt: newDateTime,
    });

    // 更新を通知.
    handleUpdate(newValues);
  };

  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      {formDialogEdit.eventList.map(event => {
        const icon = getIconWorkingPlace(
          attendanceToPlace(event.eAttendanceState),
        );
        return (
          <Box
            key={event.uid}
            sx={{
              marginBottom: responsiveSpacing(2),
              display: 'flex',
              justifyContent: 'space-between',
              alignItems: 'center',
              gap: responsiveSpacing(4),
            }}
          >
            <Icon icon={icon} />
            <TypoText sx={{ width: responsiveSize(160) }}>
              {getLabelAttendanceState(event.eAttendanceState)}
            </TypoText>
            <TextField
              sx={{
                '& .MuiOutlinedInput-input': {
                  py: responsiveSize(16),
                  px: responsiveSize(14),
                },
              }}
              type="time"
              value={event.updatedAt.toHHMM()}
              onChange={v => handleChangeTime(event, v.target.value)}
            />
          </Box>
        );
      })}
      <Divider />
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'flex-end',
          alignItems: 'center',
          gap: responsiveSpacing(2),
        }}
      >
        <ButtonGeneral
          sx={{ marginTop: responsiveSpacing(2) }}
          onClick={handleSubmit}
        >
          更新する
        </ButtonGeneral>
      </Box>
    </Box>
  );
};
