import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { DateTime } from '@/script/Common/DateTime';
import { IUsecasesCalendarUserSchedule } from '@/script/Models/Usecases/UsecasesCalendarUserScheduleList';
import { E_COLOR } from '@/script/Enum/EColor';
import { Closable } from '@/script/Component/Misc/Closable';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { E_WEEK_DAY } from '@/script/Enum/Server/App/EWeekDay';

// 各IDの表示色.
export type ColorTable = {
  [key: string]: string;
};

export interface IScheduleBlock {
  index: number;
  start: DateTime;
  end: DateTime;
}

interface IProps extends IPropsBase {
  date: DateTime;
  scheduleList: IUsecasesCalendarUserSchedule[];
  selected?: IScheduleBlock;
  colorTable: ColorTable;
  handleSelect: (block: IScheduleBlock) => void;
}
export const ScheduleDateArea: React.FC<IProps> = ({
  sx,
  date,
  scheduleList,
  selected,
  colorTable,
  handleSelect,
}) => {
  const factorNum = 5;
  const heightUnit = 50;
  const areaHeight = (factorNum + 1) * heightUnit;
  const height = areaHeight + 100;
  const offsetHeight = 50;
  const [displayFrame, setDisplayFrame] = React.useState<boolean>(false);
  const [selectIndex, setSelectIndex] = React.useState<number>(0);

  /**
   * 11:00～14:00の間のみのリスト.
   */
  const filteredScheduleList = React.useMemo(() => {
    // 重複を削除.
    const uniqueList = Array.from(
      new Map(scheduleList.map(item => [item.identify, item])).values(),
    );

    return (
      uniqueList
        // 11:00-14:00の間のスケジュールを抽出.
        .filter(schedule => {
          // 範囲の開始と終了を取得.
          const startLimitJsTimeStamp = DateTime.parseString(schedule.start)
            .setHour(11)
            .setMinute(0)
            .toJsTimeStamp();
          const endLimitJsTimeStamp = DateTime.parseString(schedule.start)
            .setHour(14)
            .setMinute(0)
            .toJsTimeStamp();

          // スケジュールの開始と終了を取得.
          const startJsTimeStamp = DateTime.parseString(
            schedule.start,
          ).toJsTimeStamp();
          const endJsTimeStamp = DateTime.parseString(
            schedule.end,
          ).toJsTimeStamp();

          // 範囲内か判定.
          return (
            endJsTimeStamp > startLimitJsTimeStamp &&
            startJsTimeStamp < endLimitJsTimeStamp
          );
        })
    );
  }, [scheduleList]);

  /**
   * 11:00～14:00の間を30分間隔
   */
  const scheduleBlockList: IScheduleBlock[] = React.useMemo(() => {
    const ret = [];

    // 5個のブロックを作成.
    for (let i = 0; i < factorNum + 1; ++i) {
      const offset = date.setHour(11).addMinute(i * 30);
      ret.push({
        index: i,
        start: offset,
        end: offset.addMinute(30),
      });
    }

    return ret;
  }, [date]);

  /**
   * マウスイベント情報をインデックスに変換.
   * @param e
   */
  const eventToIndex = (e: React.MouseEvent): number => {
    const rect = (e.target as HTMLElement).getBoundingClientRect();
    const y = e.clientY - (rect.top + offsetHeight);
    if (y < 0 || y > areaHeight) {
      return -1;
    }

    return Math.min(factorNum - 1, Math.max(0, Math.floor(y / heightUnit)));
  };

  /**
   * 営業日かどうか
   * @note 祝日判定はない
   */
  const isWorkingDay = (): boolean => {
    switch (date.getWeekDay()) {
      case E_WEEK_DAY.SUNDAY:
      case E_WEEK_DAY.SATURDAY:
        return false;
      default:
        return true;
    }
  };

  /**
   * ブロックされているインデックスかどうか.
   * @param index
   * @param hour
   * @param minute
   */
  const isBlockedIndex = (
    index: number,
    hour: number = 0,
    minute: number = 0,
  ): boolean => {
    const block = scheduleBlockList.find(block => block.index === index);
    if (!block) {
      return false;
    }

    const blockStart = block.start;
    const blockEnd = block.start.addHour(hour).addMinute(minute);

    return filteredScheduleList.some(
      schedule =>
        DateTime.parseString(schedule.start).toJsTimeStamp() <
          blockEnd.toJsTimeStamp() &&
        DateTime.parseString(schedule.end).toJsTimeStamp() >
          blockStart.toJsTimeStamp(),
    );
  };

  /**
   * マウス移動時.
   * @param e
   */
  const handleMouseMove = (e: React.MouseEvent) => {
    // 営業日は選択できない.
    if (!isWorkingDay()) {
      setDisplayFrame(false);
      return;
    }

    const index = eventToIndex(e);
    setDisplayFrame(index >= 0);
    setSelectIndex(eventToIndex(e));
  };

  /**
   * スケジュールが選択された.
   * @param e
   */
  const handleMouseDown = (e: React.MouseEvent) => {
    // 営業日は選択できない.
    if (!isWorkingDay()) {
      return;
    }

    // 既にブロックされている時間帯かどうかチェック.
    const index = eventToIndex(e);
    if (isBlockedIndex(index, 1, 0)) {
      return;
    }

    const block = scheduleBlockList.find(block => block.index === index);
    if (block) {
      handleSelect(block);
    }
  };

  /**
   * 各1hのブロック.
   * @param block
   */
  const nodeHourBlock = (block: IScheduleBlock): React.ReactNode => {
    // 既にブロックされている時間帯かどうか.
    const blocked = isBlockedIndex(block.index, 0, 30);
    return (
      <Box
        key={block.index}
        sx={{
          borderBottom: '1px dashed #000',
          height: `${heightUnit}px`,
          position: 'absolute',
          width: '100%',
          top: `${heightUnit * block.index + offsetHeight}px`,
          backgroundColor: blocked ? E_COLOR.GREY : 'none',
          pointerEvents: 'none',
        }}
      >
        <Closable open={date.getWeekDay() === E_WEEK_DAY.SUNDAY}>
          <TypoText>
            {block.start.toHHMM()}-{block.end.toHHMM()}
          </TypoText>
        </Closable>
      </Box>
    );
  };

  /**
   * 出社か在宅か.
   */
  const nodeOfficeOrHome = () => {
    // 在宅テーブル.
    const homeFlagTable: { [key: string]: boolean } = {};
    Object.keys(colorTable).forEach(calendarId => {
      homeFlagTable[calendarId] = scheduleList
        .filter(v => v.calendarId === calendarId)
        .some(v => v.summary.indexOf('在宅') > -1);
    });

    // 全て出社か.
    const isAllOffice = !Object.values(homeFlagTable).some(v => v);

    // 背景色.
    const getBackgroundColor = () => {
      if (!isWorkingDay()) {
        return 'none';
      }
      return isAllOffice ? 'none' : E_COLOR.YELLOW_LIGHT;
    };
    const backgroundColor = getBackgroundColor();

    return (
      <Box
        sx={{
          backgroundColor,
          borderBottom: '1px dashed #000',
          height: `${heightUnit}px`,
          position: 'absolute',
          width: '100%',
          top: `0px`,
          pointerEvents: 'none',
          display: 'flex',
          alignItems: 'center',
          gap: '2px',
          padding: '2px',
        }}
      >
        <Closable open={isWorkingDay()}>
          {Object.keys(homeFlagTable).map(calendarId => {
            return (
              <Box
                key={calendarId}
                sx={{
                  width: '50%',
                  backgroundColor: homeFlagTable[calendarId]
                    ? E_COLOR.RED_LIGHT
                    : `${colorTable[calendarId]}`,
                  border: '1px solid #000',
                  borderRadius: '8px',
                  display: 'flex',
                  justifyContent: 'center',
                }}
              >
                <TypoText size="10px" bold>
                  {homeFlagTable[calendarId] ? '在宅' : '出社'}
                </TypoText>
              </Box>
            );
          })}
        </Closable>
      </Box>
    );
  };

  /**
   * 各ユーザーの予定.
   * @param schedule
   */
  const nodeScheduleBlock = (
    schedule: IUsecasesCalendarUserSchedule,
  ): React.ReactNode => {
    const offset = date.setHour(11);

    // topをstartから求める
    const startJsTimeStamp = Math.max(
      0,
      DateTime.parseString(schedule.start).toJsTimeStamp() -
        offset.toJsTimeStamp(),
    );
    const top = (startJsTimeStamp / (1000 * 60 * 30)) * heightUnit;

    // heightをend-startから求める
    const endJsTimeStamp =
      DateTime.parseString(schedule.end).toJsTimeStamp() -
      offset.toJsTimeStamp();
    const height = Math.min(
      areaHeight - top,
      ((endJsTimeStamp - startJsTimeStamp) / (1000 * 60 * 30)) * heightUnit,
    );

    // leftをカラーテーブルの順番から求める
    const left =
      Object.keys(colorTable).findIndex(key => key === schedule.calendarId) *
      20;
    const right = 20 - left;

    return (
      <Box
        key={`${schedule.identify}`}
        sx={{
          padding: '2px',
          position: 'absolute',
          top: `${top + 2 + offsetHeight}px`,
          left: `${left + 2}px`,
          right: `${right + 2}px`,
          height: `${height - 4}px`,
          pointerEvents: 'none',
          backgroundColor: colorTable[schedule.calendarId],
          border: '1px solid #000',
          borderRadius: '8px',
          overflow: 'hidden',
        }}
      >
        <TypoText
          sx={{ wordBreak: 'break-all' }}
          noWrap={false}
          size="10px"
          bold
        >
          {schedule.summary}
        </TypoText>
      </Box>
    );
  };

  return (
    <Box
      sx={{
        borderTop: `1px solid ${E_COLOR.GREY}`,
        borderLeft: `1px solid ${E_COLOR.GREY}`,
        height: `${height}px`,
        position: 'relative',
        ...sx,
      }}
      onMouseEnter={() => {
        setDisplayFrame(true);
      }}
      onMouseLeave={() => {
        setDisplayFrame(false);
      }}
      onMouseMove={event => handleMouseMove(event)}
      onMouseDown={event => handleMouseDown(event)}
    >
      {/* 出社・在宅判定ブロック */}
      {nodeOfficeOrHome()}
      {/* 30分づつのブロック */}
      {scheduleBlockList.map(block => {
        return nodeHourBlock(block);
      })}
      {/* 各ユーザーの予定 */}
      {filteredScheduleList.map(schedule => {
        return nodeScheduleBlock(schedule);
      })}
      {/* マウス選択中の枠 */}
      <Closable open={displayFrame}>
        <Box
          sx={{
            width: '100%',
            height: '100px',
            position: 'absolute',
            top: `${selectIndex * heightUnit + offsetHeight}px`,
            pointerEvents: 'none',
            outline: '3px solid #000',
            outlineOffset: '-3px',
            outlineColor: isBlockedIndex(selectIndex, 1, 0)
              ? E_COLOR.RED
              : E_COLOR.BLUE,
          }}
        />
      </Closable>
      {/* 選択中の枠 */}
      <Closable open={!!selected}>
        <Box
          sx={{
            width: '100%',
            height: '100px',
            position: 'absolute',
            top: `${(selected ? selected.index : 0) * heightUnit + offsetHeight}px`,
            pointerEvents: 'none',
            outline: '3px solid #000',
            outlineOffset: '-3px',
            outlineColor: E_COLOR.SECONDARY_MAIN,
          }}
        />
      </Closable>
    </Box>
  );
};
