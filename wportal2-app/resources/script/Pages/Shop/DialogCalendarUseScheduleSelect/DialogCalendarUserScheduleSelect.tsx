import * as React from 'react';
import { Box, Dialog, Divider } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { useFetchAppCalendarUserScheduleList } from '@/script/Hooks/Usecases/Queries/useFetchAppCalendarUserScheduleList';
import { Loading } from '@/script/Component/Misc/Loading';
import { responsiveSpacing } from '@/script/System/Responsive';
import { DateTime } from '@/script/Common/DateTime';
import { Header } from '@/script/Pages/Shop/DialogCalendarUseScheduleSelect/Header';
import { Schedule } from '@/script/Pages/Shop/DialogCalendarUseScheduleSelect/Schedule';
import {
  ColorTable,
  IScheduleBlock,
} from '@/script/Pages/Shop/DialogCalendarUseScheduleSelect/ScheduleDateArea';
import { E_COLOR, EColor } from '@/script/Enum/EColor';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { TypoH3 } from '@/script/Component/Typography/TypoH3';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { E_BUTTON_TYPE } from '@/script/Component/Button/EButtonType';

/**
 * 複数ユーザーのスケジュールから、空いている時間を選択するダイアログ.
 * ランチ向けに11:00～14:00の間のスケジュールを選択する.
 */
interface IProps extends IPropsBase {
  open: boolean;
  title: string;
  comment: string;
  emailList: string[];
  handleSubmit: (target: string) => void;
  handleClose: () => void;
}
export const DialogCalendarUserScheduleSelect: React.FC<IProps> = ({
  sx,
  open,
  title,
  comment,
  emailList,
  handleSubmit,
  handleClose,
}) => {
  const [startDate, setStartDate] = React.useState<DateTime>(
    DateTime.now().startOfWeek(),
  );
  const [endDate, setEndDate] = React.useState<DateTime>(
    DateTime.now().endOfWeek(),
  );
  const [loading, list] = useFetchAppCalendarUserScheduleList({
    emailList,
    startDate,
    endDate,
  });
  const [selected, setSelected] = React.useState<IScheduleBlock | undefined>(
    undefined,
  );

  /**
   * emailListからカラーリストを作成する.
   */
  const colorTable: ColorTable = React.useMemo(() => {
    const ret: ColorTable = {};
    const colorTable: EColor[] = [E_COLOR.GREEN_LIGHT, E_COLOR.BLUE_LIGHT];

    emailList.forEach((email, index) => {
      ret[email] = colorTable[index % colorTable.length];
    });

    return ret;
  }, [emailList]);

  /**
   * 表示日付の成形.
   */
  const dateList: DateTime[] = React.useMemo(() => {
    const ret = [];
    const daySpan = startDate.getDateDiff(endDate) + 1;
    for (let i = 0; i < daySpan; ++i) {
      const date = startDate.addDay(i);
      ret.push(date);
    }
    return ret;
  }, [startDate, endDate]);

  /**
   * 前の週を選択.
   */
  const handleClickPrevWeek = () => {
    setStartDate(prevState => {
      return prevState.subDay(7);
    });
    setEndDate(prevState => {
      return prevState.subDay(7);
    });
    setSelected(undefined);
  };

  /**
   * 次の週を選択.
   */
  const handleClickNextWeek = () => {
    setStartDate(prevState => {
      return prevState.addDay(7);
    });
    setEndDate(prevState => {
      return prevState.addDay(7);
    });
    setSelected(undefined);
  };

  /**
   * スケジュールを選択した.
   * @param block
   */
  const handleSelect = (block: IScheduleBlock) => {
    setSelected(block);
  };

  /**
   * 予約ボタンを押した.
   */
  const handleClickSubmit = () => {
    if (!selected) {
      return;
    }
    handleSubmit(selected.start.toDateTime());
  };

  /**
   * 選択中スケジュールのテキスト.
   */
  const nodeSelectedText = (): string => {
    if (!selected) {
      return 'スケジュールを選択してください';
    }

    const start = `${selected.start.toDateJP()} ${selected.start.toHHMM()}`;
    const end = selected.start.addHour().toHHMM();
    return `${start}～${end}`;
  };

  return (
    <Dialog
      sx={{
        '& .MuiDialog-container': {
          '& .MuiPaper-root': {
            width: '100%',
            maxWidth: '90%',
          },
        },
        ...sx,
      }}
      open={open}
      onClose={handleClose}
    >
      <Box
        sx={{
          padding: responsiveSpacing(4),
        }}
      >
        <Box
          sx={{
            display: 'flex',
            justifyContent: 'center',
          }}
        >
          <TypoH2>{title}</TypoH2>
        </Box>
        <TypoText
          sx={{
            marginTop: responsiveSpacing(2),
          }}
        >
          {comment}
        </TypoText>
        <Divider sx={{ width: '100%', marginTop: responsiveSpacing(2) }} />
        <Loading
          sx={{
            height: '500px',
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
          }}
          loading={loading}
        >
          <Box
            sx={{
              display: 'flex',
              justifyContent: 'space-between',
              alignItems: 'center',
              padding: responsiveSpacing(2),
            }}
          >
            <ButtonGeneral label="前の週" onClick={handleClickPrevWeek} />
            <TypoH3>{startDate.toDateJP()}の週</TypoH3>
            <ButtonGeneral label="次の週" onClick={handleClickNextWeek} />
          </Box>
          <Header dateList={dateList} />
          <Schedule
            dateList={dateList}
            scheduleList={list.list()}
            selected={selected}
            colorTable={colorTable}
            handleSelect={handleSelect}
          />
          <Box
            sx={{
              marginTop: responsiveSpacing(2),
              display: 'flex',
              justifyContent: 'end',
              alignItems: 'center',
              gap: responsiveSpacing(4),
            }}
          >
            <TypoText>{nodeSelectedText()}</TypoText>
            <ButtonGeneral
              disabled={!selected}
              buttonType={E_BUTTON_TYPE.CONTAINED_SECONDARY}
              label="選択したスケジュールを予約する"
              onClick={handleClickSubmit}
            />
          </Box>
        </Loading>
      </Box>
    </Dialog>
  );
};
