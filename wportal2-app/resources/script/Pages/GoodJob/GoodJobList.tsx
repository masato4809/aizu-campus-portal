import * as React from 'react';
import { router } from '@inertiajs/react';
import { IPropsBase } from '@/script/System/System';
import { Box } from '@mui/material';
import { useIndexContext } from '@/script/Pages/GoodJob/Index';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { Paging } from '@/script/Component/Misc/Paging';
import { responsiveSpacing } from '@/script/System/Responsive';
import { GoodJobItem } from '@/script/Pages/GoodJob/GoodJobItem';

interface IProps extends IPropsBase {}
export const GoodJobList: React.FC<IProps> = ({ sx }) => {
  const { trnGoodJobList, trnGoodJobListCount } = useIndexContext();
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
        replace: true,
        onStart,
        onFinish,
        data: {
          index: nextIndex,
          step: pageStep,
        },
        only: ['paginationGoodJob'],
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
        replace: true,
        onStart,
        onFinish,
        data: {
          index: nextIndex,
          step: pageStep,
        },
        only: ['paginationGoodJob'],
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
      <Paging
        sx={{
          justifyContent: 'flex-end',
        }}
        index={pageIndex}
        step={pageStep}
        total={trnGoodJobListCount}
        handlePrev={handlePrev}
        handleNext={handleNext}
      />
      {trnGoodJobList.list().map(goodJob => {
        return <GoodJobItem key={goodJob.id} trnGoodJob={goodJob} />;
      })}
      <Paging
        sx={{
          marginTop: responsiveSpacing(2),
          justifyContent: 'flex-end',
        }}
        index={pageIndex}
        step={pageStep}
        total={trnGoodJobListCount}
        handlePrev={handlePrev}
        handleNext={handleNext}
      />
    </Box>
  );
};
