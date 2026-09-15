import * as React from 'react';
import { router } from '@inertiajs/react';
import { Box, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { useIndexContext } from '@/script/Pages/User/Index';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { Paging } from '@/script/Component/Misc/Paging';
import { UserTable } from '@/script/Pages/User/UserTable';
import { IAppTrnUser } from '@/script/Models/App/Trn/TrnUserList';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import {
  FormSearch,
  IFormSearch,
  initialValue,
} from '@/script/Pages/User/FormSearch';
import { Closable } from '@/script/Component/Misc/Closable';
import { SearchFilter } from '@/script/Pages/User/SearchFilter';
import { responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const UserList: React.FC<IProps> = ({ sx }) => {
  const { trnUserListCount } = useIndexContext();
  const { onStart, onFinish } = useProgressContext();
  const pageStep = 20;
  const [pageIndex, setPageIndex] = React.useState<number>(0);
  const [searchFormValue, setSearchFormValue] =
    React.useState<IFormSearch>(initialValue);
  const [searchValue, setSearchValue] =
    React.useState<IFormSearch>(initialValue);

  /**
   * 検索値の更新.
   */
  const handleUpdate = (newValues: Partial<IFormSearch>) => {
    setSearchFormValue({ ...searchFormValue, ...newValues });
  };

  /**
   * 検索の実行.
   */
  const handleSubmitSearch = () => {
    setSearchValue(searchFormValue);
    setPageIndex(0);

    router.reload({
      method: 'post',
      // @ts-expect-error inertia-preserve-state
      preserveState: true,
      replace: true,
      onStart,
      onFinish,
      data: {
        index: 0,
        step: pageStep,
        keyword: searchFormValue.keyword,
        inactiveUserFlag: searchFormValue.inactiveUserFlag,
      },
      only: ['paginationUser'],
    });
  };

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
          keyword: searchValue.keyword,
          inactiveUserFlag: searchValue.inactiveUserFlag,
        },
        only: ['paginationUser'],
      });

      return nextIndex;
    });
  }, [setPageIndex, onStart, onFinish, searchValue]);

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
          keyword: searchValue.keyword,
          inactiveUserFlag: searchValue.inactiveUserFlag,
        },
        only: ['paginationUser'],
      });

      return nextIndex;
    });
  }, [setPageIndex, onStart, onFinish, searchValue]);

  /**
   * ユーザーを選択.
   */
  const handleSelect = (trnUser: IAppTrnUser) => {
    router.visit(
      getPagesHref(E_PAGES.USER__SHOW, [
        {
          target: '$userId',
          value: String(trnUser.id),
        },
      ]),
      {
        onStart,
        onFinish,
      },
    );
  };

  /**
   * 検索をリセットする.
   */
  const handleReset = (): void => {
    setSearchFormValue(initialValue);
    setSearchValue(initialValue);
    setPageIndex(0);

    router.reload({
      method: 'post',
      onStart,
      onFinish,
      data: {
        index: 0,
        step: pageStep,
        keyword: initialValue.keyword,
        inactiveUserFlag: initialValue.inactiveUserFlag,
      },
      only: ['paginationUser'],
    });
  };

  /**
   * 検索状態が有効かどうか.
   */
  const isSearchActive = (): boolean => {
    return searchValue.keyword !== '' || searchValue.inactiveUserFlag;
  };

  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <Paper
        sx={{
          padding: responsiveSpacing(4),
        }}
      >
        <FormSearch
          formSearch={searchFormValue}
          handleUpdate={handleUpdate}
          handleSubmit={handleSubmitSearch}
        />
      </Paper>
      <Closable open={isSearchActive()}>
        <SearchFilter
          sx={{
            marginTop: responsiveSpacing(4),
          }}
          searchValue={searchValue}
          handleReset={handleReset}
        />
      </Closable>
      <Closable open={!!trnUserListCount}>
        <Paging
          sx={{
            marginTop: responsiveSpacing(4),
            justifyContent: 'flex-end',
          }}
          index={pageIndex}
          step={pageStep}
          total={trnUserListCount}
          handlePrev={handlePrev}
          handleNext={handleNext}
        />
      </Closable>
      <UserTable
        sx={{ marginTop: responsiveSpacing(2) }}
        handleSelect={handleSelect}
      />
      <Closable open={!!trnUserListCount}>
        <Paging
          sx={{
            marginTop: responsiveSpacing(2),
            justifyContent: 'flex-end',
          }}
          index={pageIndex}
          step={pageStep}
          total={trnUserListCount}
          handlePrev={handlePrev}
          handleNext={handleNext}
        />
      </Closable>
    </Box>
  );
};
