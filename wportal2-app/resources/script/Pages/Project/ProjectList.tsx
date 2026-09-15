import * as React from 'react';
import { router } from '@inertiajs/react';
import { Box, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { useIndexContext } from '@/script/Pages/Project/Index';
import { Paging } from '@/script/Component/Misc/Paging';
import { TypoSubTitle1 } from '@/script/Component/Typography/TypoSubTitle1';
import { InertiaLink } from '@/script/Component/Misc/InertiaLink';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { E_COLOR } from '@/script/Enum/EColor';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { Avatar } from '@/script/Pages/Common/Avatar';
import { responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const ProjectList: React.FC<IProps> = ({ sx }) => {
  const { trnProjectList, trnProjectListCount } = useIndexContext();
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
        only: ['paginationProject'],
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
        only: ['paginationProject'],
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
        total={trnProjectListCount}
        handlePrev={handlePrev}
        handleNext={handleNext}
      />
      {trnProjectList.list().map(project => {
        return (
          <Paper
            sx={{
              padding: responsiveSpacing(4),
              '&:not(:first-of-type)': {
                marginTop: responsiveSpacing(2),
              },
            }}
            key={project.id}
          >
            <Box
              sx={{
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
              }}
            >
              <TypoSubTitle1>
                [{project.id}]{project.name}
              </TypoSubTitle1>
              <InertiaLink
                href={getPagesHref(E_PAGES.PROJECT__SHOW, [
                  {
                    target: '$projectId',
                    value: String(project.id),
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
                {project.explain}
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
              {project.trnProjectUser?.map(trnProjectUser => {
                return (
                  <Avatar
                    key={trnProjectUser.id}
                    trnUser={trnProjectUser.trnUser}
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
        total={trnProjectListCount}
        handlePrev={handlePrev}
        handleNext={handleNext}
      />
    </Box>
  );
};
