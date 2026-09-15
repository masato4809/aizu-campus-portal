import * as React from 'react';
import {
  Paper,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableRow,
} from '@mui/material';
import { useIndexContext } from '@/script/Pages/Sample/Pagination/Index';
import { IPropsBase } from '@/script/System/System';
import { IAppTrnUser } from '@/script/Models/App/Trn/TrnUserList';

interface IProps extends IPropsBase {}
export const FixList: React.FC<IProps> = ({ sx }) => {
  const { fixList } = useIndexContext();

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
      <Table>
        <TableHead>
          <TableRow>
            <TableCell width="10%">ID</TableCell>
            <TableCell width="20%">名前</TableCell>
            <TableCell width="20%">タグ</TableCell>
            <TableCell>説明</TableCell>
          </TableRow>
        </TableHead>
        <TableBody>
          {fixList.list().map(v => {
            return nodeItem(v);
          })}
        </TableBody>
      </Table>
    </Paper>
  );
};
