import * as React from 'react';
import {
  Box,
  Dialog,
  DialogActions,
  DialogContent,
  DialogTitle,
} from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IAppTrnUser } from '@/script/Models/App/Trn/TrnUserList';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { DateTime } from '@/script/Common/DateTime';
import { getLabelAttendanceState } from '@/script/Enum/Server/App/EAttendanceState';

interface IProps extends IPropsBase {
  trnUser?: IAppTrnUser;
  handleClose: () => void;
}
export const DialogUser: React.FC<IProps> = ({ sx, trnUser, handleClose }) => {
  /**
   * 表示内容.
   */
  const nodeContent = (): React.ReactNode => {
    if (!trnUser?.trnAttendanceState?.length) {
      return <TypoText>出退勤履歴はありません</TypoText>;
    }
    return trnUser.trnAttendanceState.map(attendance => (
      <Box
        key={attendance.id}
        sx={{
          display: 'flex',
        }}
      >
        <TypoText sx={{ marginRight: '20px' }}>
          {DateTime.parseString(attendance.updatedAt).toHHMM()}
        </TypoText>
        <TypoText>
          {getLabelAttendanceState(attendance.eAttendanceState)}
        </TypoText>
      </Box>
    ));
  };

  return (
    <Dialog
      sx={{
        ...sx,
      }}
      open={!!trnUser}
      onClose={handleClose}
    >
      <DialogTitle>{`${trnUser?.nickname}さんの出退勤履歴`}</DialogTitle>
      <DialogContent dividers>{nodeContent()}</DialogContent>
      <DialogActions>
        <ButtonGeneral label="閉じる" onClick={handleClose} />
      </DialogActions>
    </Dialog>
  );
};
