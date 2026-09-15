import * as React from 'react';
import { Dialog, Divider, Paper } from '@mui/material';
import { router } from '@inertiajs/react';
import { IPropsBase } from '@/script/System/System';
import { IDate } from '@/script/Pages/AttendanceEdit/MonthCalendar/IDate';
import {
  FormDialogEdit,
  IFormDialogEdit,
} from '@/script/Pages/AttendanceEdit/FormDialogEdit';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { useIndexContext } from '@/script/Pages/AttendanceEdit/Index';
import { responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {
  open: boolean;
  date?: IDate;
  handleClose: () => void;
}
export const DialogEdit: React.FC<IProps> = ({
  sx,
  open,
  date,
  handleClose,
}) => {
  const { dateTime } = useIndexContext();
  const { onStart, onFinish } = useProgressContext();
  const [formDialogEdit, setFormDialogEdit] = React.useState<IFormDialogEdit>({
    eventList: date?.eventList || [],
  });

  /**
   * 更新処理.
   */
  const handleUpdate = (newValues: Partial<IFormDialogEdit>) => {
    setFormDialogEdit({
      ...formDialogEdit,
      ...newValues,
    });
  };

  /**
   * 送信処理.
   */
  const handleSubmit = () => {
    // 送信データ.
    const formData = new FormData();
    formData.set('dateTime', dateTime);
    formDialogEdit.eventList.forEach(event => {
      formData.append(`eventList[${event.uid}][uid]`, String(event.uid));
      formData.append(
        `eventList[${event.uid}][updatedAt]`,
        event.updatedAt.toDateTime(),
      );
    });

    // 送信処理.
    router.post(getPagesHref(E_PAGES.ATTENDANCE__EDIT__UPDATE), formData, {
      onStart,
      onFinish,
      preserveState: false,
    });
  };

  const nodeContent = () => {
    // 編集対象がない場合.
    if (!date || date.eventList?.length === 0) {
      return (
        <Paper
          sx={{
            padding: responsiveSpacing(4),
            display: 'flex',
            flexDirection: 'column',
            gap: responsiveSpacing(2),
          }}
        >
          <TypoText>登録時刻修正</TypoText>
          <Divider sx={{ width: '100%' }} />
          <TypoText>登録されている勤怠はありません</TypoText>
        </Paper>
      );
    }

    return (
      <Paper
        sx={{
          padding: responsiveSpacing(4),
          display: 'flex',
          flexDirection: 'column',
          gap: responsiveSpacing(2),
        }}
      >
        <TypoText>登録時刻修正</TypoText>
        <Divider sx={{ width: '100%' }} />
        <FormDialogEdit
          formDialogEdit={formDialogEdit}
          handleUpdate={handleUpdate}
          handleSubmit={handleSubmit}
        />
      </Paper>
    );
  };

  return (
    <Dialog sx={sx} open={open} onClose={handleClose}>
      {nodeContent()}
    </Dialog>
  );
};
