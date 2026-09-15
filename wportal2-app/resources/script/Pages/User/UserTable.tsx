import * as React from 'react';
import {
  Box,
  styled,
  Table,
  TableBody,
  TableCell,
  tableCellClasses,
  TableHead,
  TableRow,
} from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { useIndexContext } from '@/script/Pages/User/Index';
import { E_COLOR } from '@/script/Enum/EColor';
import { Image } from '@/script/Component/Misc/Image';
import { IAppTrnUser } from '@/script/Models/App/Trn/TrnUserList';
import { AppTrnAttendanceStateList } from '@/script/Models/App/Trn/TrnAttendanceStateList';
import { DateTime } from '@/script/Common/DateTime';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { responsiveSize } from '@/script/System/Responsive';

const StyledTableCell = styled(TableCell)(({ theme }) => ({
  [`&.${tableCellClasses.head}`]: {
    backgroundColor: theme.palette.primary.main,
    color: theme.palette.primary.contrastText,
    fontSize: '14px',
    height: '48px',
    padding: '5px 10px',
    [theme.breakpoints.down('md')]: {
      fontSize: '8px',
      height: '24px',
      padding: '0px 5px',
    },
  },
  [`&.${tableCellClasses.body}`]: {
    fontSize: '14px',
    height: '80px',
    padding: '5px 10px',
    [theme.breakpoints.down('md')]: {
      fontSize: '8px',
      height: '40px',
      padding: '0px 5px',
    },
  },
}));

interface IProps extends IPropsBase {
  handleSelect: (trnUser: IAppTrnUser) => void;
}
export const UserTable: React.FC<IProps> = ({ sx, handleSelect }) => {
  const { trnUserList } = useIndexContext();

  const tableBody = (): React.ReactNode => {
    return trnUserList.list().map(trnUser => {
      // 最終出退勤アクションを取得.
      const latestActionState = trnUser.trnAttendanceState?.length
        ? trnUser.trnAttendanceState[0]
        : AppTrnAttendanceStateList.defaultInterface();

      // 最終出退勤アクションの日時を取得.
      const latestDate = latestActionState.createdAt
        ? DateTime.parseString(latestActionState.createdAt).toDateTime()
        : '----';

      return (
        <TableRow
          sx={{
            backgroundColor: E_COLOR.WHITE,
            '&:hover': {
              backgroundColor: theme => theme.palette.primary.light,
              cursor: 'pointer',
            },
          }}
          key={trnUser.id}
          onClick={() => handleSelect(trnUser)}
        >
          <StyledTableCell>{trnUser.id}</StyledTableCell>
          <StyledTableCell sx={{ display: 'flex', alignItems: 'center' }}>
            <Image
              sx={{
                width: responsiveSize(60),
                height: responsiveSize(60),
                borderRadius: responsiveSize(30),
              }}
              src={trnUser.faceImagePath}
            />
          </StyledTableCell>
          <StyledTableCell>{trnUser.nickname}</StyledTableCell>
          <StyledTableCell>{latestDate}</StyledTableCell>
        </TableRow>
      );
    });
  };

  /**
   * データが見つからない場合.
   */
  if (!trnUserList.count()) {
    return (
      <Box
        sx={{
          marginTop: responsiveSize(60),
          width: '100%',
          display: 'flex',
          justifyContent: 'center',
        }}
      >
        <TypoText>検索条件に一致するユーザーが見つかりませんでした。</TypoText>
      </Box>
    );
  }

  return (
    <Table sx={sx}>
      <TableHead>
        <TableRow>
          <StyledTableCell sx={{ width: responsiveSize(60) }}>
            id
          </StyledTableCell>
          <StyledTableCell sx={{ width: responsiveSize(60) }}>
            icon
          </StyledTableCell>
          <StyledTableCell>nickname</StyledTableCell>
          <StyledTableCell>最終アクション</StyledTableCell>
        </TableRow>
      </TableHead>
      <TableBody>{tableBody()}</TableBody>
    </Table>
  );
};
