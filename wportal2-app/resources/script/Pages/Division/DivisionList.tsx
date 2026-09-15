import * as React from 'react';
import { router } from '@inertiajs/react';
import { Box, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { useIndexContext } from '@/script/Pages/Division/Index';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { TypoSubTitle1 } from '@/script/Component/Typography/TypoSubTitle1';
import { E_COLOR } from '@/script/Enum/EColor';
import { Paging } from '@/script/Component/Misc/Paging';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { Avatar } from '@/script/Pages/Common/Avatar';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { InertiaLink } from '@/script/Component/Misc/InertiaLink';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const DivisionList: React.FC<IProps> = ({ sx }) => {
  const { trnDivisionList, trnDivisionListCount } = useIndexContext();
  const { onStart, onFinish } = useProgressContext();
  const pageStep = 5;
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
        replace: true,
        onStart,
        onFinish,
        data: {
          index: nextIndex,
          step: pageStep,
        },
        only: ['paginationDivision'],
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
        only: ['paginationDivision'],
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
        total={trnDivisionListCount}
        handlePrev={handlePrev}
        handleNext={handleNext}
      />
      {trnDivisionList.list().map(division => {
        return (
          <Paper
            sx={{
              padding: responsiveSpacing(4),
              '&:not(:first-of-type)': {
                marginTop: responsiveSpacing(2),
              },
            }}
            key={division.id}
          >
            <Box
              sx={{
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
              }}
            >
              <TypoSubTitle1>
                [{division.id}]{division.name}
              </TypoSubTitle1>
              <InertiaLink
                href={getPagesHref(E_PAGES.DIVISION__SHOW, [
                  {
                    target: '$divisionId',
                    value: String(division.id),
                  },
                ])}
              >
                <ButtonGeneral label="詳細" />
              </InertiaLink>
            </Box>
            <Box
              sx={{
                marginTop: responsiveSpacing(2),
                backgroundColor: E_COLOR.GREY_LIGHT,
              }}
            >
              <TypoText sx={{ whiteSpace: 'pre-wrap' }}>
                {division.explain}
              </TypoText>
            </Box>
            <Box
              sx={{
                marginTop: responsiveSpacing(2),
                display: 'flex',
                flexWrap: 'wrap',
                gap: responsiveSpacing(2),
              }}
            >
              {division.trnDivisionUser?.map(trnDivisionUser => {
                return (
                  <Avatar
                    key={trnDivisionUser.id}
                    trnUser={trnDivisionUser.trnUser}
                  />
                );
              })}
            </Box>
          </Paper>
        );
      })}
      <Paging
        sx={{
          marginTop: responsiveSpacing(2),
          justifyContent: 'flex-end',
        }}
        index={pageIndex}
        step={pageStep}
        total={trnDivisionListCount}
        handlePrev={handlePrev}
        handleNext={handleNext}
      />
    </Box>
  );
};
