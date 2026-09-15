import * as React from 'react';
import {
  Paper,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableRow,
} from '@mui/material';
import { router } from '@inertiajs/react';
import { useIndexContext } from '@/script/Pages/Sample/Pagination/Index';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { Paging } from '@/script/Component/Misc/Paging';
import { IPropsBase } from '@/script/System/System';
import { IAppTrnUser } from '@/script/Models/App/Trn/TrnUserList';

interface IProps extends IPropsBase {}
export const PaginationList: React.FC<IProps> = ({ sx }) => {
  const { trnUserList, trnUserListCount } = useIndexContext();
  const { onStart, onFinish } = useProgressContext();
  const pageStep = 3;
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
        only: ['paginationTrnUser'],
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
        replace: true,
        onStart,
        onFinish,
        data: {
          index: nextIndex,
          step: pageStep,
        },
        only: ['paginationTrnUser'],
      });

      return nextIndex;
    });
  }, [setPageIndex, onStart, onFinish]);

  /**
   * 各レコード.
   * @param trnUser
   */
  const nodeItem = (trnUser: IAppTrnUser): React.ReactNode => {
    return (
      <TableRow key={trnUser.id}>
        <TableCell>{trnUser.id}</TableCell>
        <TableCell>{trnUser.nickname}</TableCell>
        <TableCell />
        <TableCell>{trnUser.nickname}</TableCell>
      </TableRow>
    );
  };

  return (
    <Paper sx={{ padding: '10px', ...sx }}>
      <Paging
        sx={{
          justifyContent: 'flex-end',
        }}
        index={pageIndex}
        step={pageStep}
        total={trnUserListCount}
        handlePrev={handlePrev}
        handleNext={handleNext}
      />
      <Table
        sx={{
          marginTop: '20px',
        }}
      >
        <TableHead>
          <TableRow>
            <TableCell width="10%">ID</TableCell>
            <TableCell width="20%">名前</TableCell>
            <TableCell width="20%">タグ</TableCell>
            <TableCell>説明</TableCell>
          </TableRow>
        </TableHead>
        <TableBody>
          {trnUserList.list().map(v => {
            return nodeItem(v);
          })}
        </TableBody>
      </Table>
    </Paper>
  );
};
