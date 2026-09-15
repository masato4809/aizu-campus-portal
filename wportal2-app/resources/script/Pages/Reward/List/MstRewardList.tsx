import * as React from 'react';
import { Box, Paper } from '@mui/material';
import { router } from '@inertiajs/react';
import { IPropsBase } from '@/script/System/System';
import { useIndexContext } from '@/script/Pages/Reward/List/Index';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { Paging } from '@/script/Component/Misc/Paging';
import { responsiveSpacing } from '@/script/System/Responsive';
import { Closable } from '@/script/Component/Misc/Closable';
import { MstReward } from '@/script/Pages/Reward/List/MstReward';

interface IProps extends IPropsBase {}
export const MstRewardList: React.FC<IProps> = ({ sx }) => {
  const { mstRewardList, mstRewardListCount } = useIndexContext();
  const { onStart, onFinish } = useProgressContext();
  const pageStep = 20;
  const [pageIndex, setPageIndex] = React.useState<number>(0);

  /**
   * 前を選択.
   */
  const handlePrev = React.useCallback(() => {
    setPageIndex(prevState => {
      const nextIndex = prevState - 1;

      router.reload({
        method: 'post',
        // @ts-expect-error inertia-preserve-state
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onStart,
        onFinish,
        data: {
          index: nextIndex,
          step: pageStep,
        },
        only: ['paginationReward'],
      });

      return nextIndex;
    });
  }, [setPageIndex, onStart, onFinish]);

  /**
   * 次を選択.
   */
  const handleNext = React.useCallback(() => {
    setPageIndex(prevState => {
      const nextIndex = prevState + 1;

      router.reload({
        method: 'post',
        // @ts-expect-error inertia-preserve-state
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onStart,
        onFinish,
        data: {
          index: nextIndex,
          step: pageStep,
        },
        only: ['paginationReward'],
      });

      return nextIndex;
    });
  }, [setPageIndex, onStart, onFinish]);

  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <Closable open={!!mstRewardListCount}>
        <Paging
          sx={{
            marginTop: responsiveSpacing(4),
            justifyContent: 'flex-end',
          }}
          index={pageIndex}
          step={pageStep}
          total={mstRewardListCount}
          handlePrev={handlePrev}
          handleNext={handleNext}
        />
      </Closable>
      <Paper
        sx={{
          marginTop: responsiveSpacing(4),
          padding: responsiveSpacing(4),
        }}
      >
        {mstRewardList.list().map(mstReward => {
          return (
            <MstReward
              sx={{
                '&:not(:first-of-type)': {
                  marginTop: responsiveSpacing(4),
                },
              }}
              key={mstReward.id}
              mstReward={mstReward}
            />
          );
        })}
      </Paper>
      <Closable open={!!mstRewardListCount}>
        <Paging
          sx={{
            marginTop: responsiveSpacing(4),
            justifyContent: 'flex-end',
          }}
          index={pageIndex}
          step={pageStep}
          total={mstRewardListCount}
          handlePrev={handlePrev}
          handleNext={handleNext}
        />
      </Closable>
    </Box>
  );
};
