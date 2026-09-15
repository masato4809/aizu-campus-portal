import * as React from 'react';
import { Box, IconButton } from '@mui/material';
import { router } from '@inertiajs/react';
import { IPropsBase } from '@/script/System/System';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_ICON } from '@/script/Enum/EIcon';
import { E_COLOR } from '@/script/Enum/EColor';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { DateTime } from '@/script/Common/DateTime';
import { useIndexContext } from '@/script/Pages/AttendanceEdit/Index';
import { useProgressContext } from '@/script/Provider/ProgressProvider';

interface IProps extends IPropsBase {}
export const Header: React.FC<IProps> = ({ sx }) => {
  const { onStart, onFinish } = useProgressContext();
  const dateTime = DateTime.parseString(useIndexContext().dateTime);

  /**
   * 前月を選択.
   */
  const handlePrevMonth = () => {
    router.reload({
      method: 'post',
      // @ts-expect-error inertia-preserve-state
      preserveState: true,
      preserveScroll: true,
      onStart,
      onFinish,
      data: {
        dateTime: dateTime.subMonth().startOfMonth().toDateTime(),
      },
    });
  };

  /**
   * 次月を選択.
   */
  const handleNextMonth = () => {
    router.reload({
      method: 'post',
      // @ts-expect-error inertia-preserve-state
      preserveState: true,
      preserveScroll: true,
      onStart,
      onFinish,
      data: {
        dateTime: dateTime.addMonth().startOfMonth().toDateTime(),
      },
    });
  };

  return (
    <Box
      sx={{
        paddingTop: '10px',
        paddingBottom: '20px',
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        gap: '20px',
        ...sx,
      }}
    >
      <IconButton
        sx={{
          color: E_COLOR.WHITE,
          background: E_COLOR.PRIMARY_MAIN,
        }}
        onClick={handlePrevMonth}
      >
        <Icon icon={E_ICON.ARROW_LEFT} />
      </IconButton>
      <TypoH2>{dateTime.toFormatString('yyyy年MM月')}</TypoH2>
      <IconButton
        sx={{
          color: E_COLOR.WHITE,
          background: E_COLOR.PRIMARY_MAIN,
        }}
        onClick={handleNextMonth}
      >
        <Icon icon={E_ICON.ARROW_RIGHT} />
      </IconButton>
    </Box>
  );
};
