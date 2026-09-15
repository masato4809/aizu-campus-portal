import * as React from 'react';
import { Box, Paper } from '@mui/material';
import { router } from '@inertiajs/react';
import { IPropsBase } from '@/script/System/System';
import { useIndexContext } from '@/script/Pages/Reward/Index';
import { Closable } from '@/script/Component/Misc/Closable';
import { responsiveSpacing } from '@/script/System/Responsive';
import { Paging } from '@/script/Component/Misc/Paging';
import { Achievement } from '@/script/Pages/Reward/Achievement';
import { useProgressContext } from '@/script/Provider/ProgressProvider';

interface IProps extends IPropsBase {}
export const AchievementList: React.FC<IProps> = ({ sx }) => {
  const { trnUserRewardList, trnUserRewardListCount } = useIndexContext();
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
      <Closable open={!!trnUserRewardListCount}>
        <Paging
          sx={{
            marginTop: responsiveSpacing(4),
            justifyContent: 'flex-end',
          }}
          index={pageIndex}
          step={pageStep}
          total={trnUserRewardListCount}
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
        {trnUserRewardList.list().map(trnUserReward => {
          return (
            <Achievement
              sx={{
                '&:not(:first-of-type)': {
                  marginTop: responsiveSpacing(4),
                },
              }}
              key={trnUserReward.id}
              trnUserReward={trnUserReward}
            />
          );
        })}
      </Paper>
      <Closable open={!!trnUserRewardListCount}>
        <Paging
          sx={{
            marginTop: responsiveSpacing(4),
            justifyContent: 'flex-end',
          }}
          index={pageIndex}
          step={pageStep}
          total={trnUserRewardListCount}
          handlePrev={handlePrev}
          handleNext={handleNext}
        />
      </Closable>
    </Box>
  );
};
