import * as React from 'react';
import { Box, Dialog, Divider } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoSubTitle1 } from '@/script/Component/Typography/TypoSubTitle1';
import { SelectTrnUserMultiple } from '@/script/Pages/Common/Select/SelectTrnUserMultiple';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';

interface IProps extends IPropsBase {
  open: boolean;
  defaultUserIdList: number[];
  handleSubmit: (newValues: number[]) => void;
  handleClose: () => void;
}
export const DialogReplaceTrnUser: React.FC<IProps> = ({
  sx,
  open,
  defaultUserIdList,
  handleSubmit,
  handleClose,
}) => {
  const [userIdList, setUserIdList] =
    React.useState<number[]>(defaultUserIdList);

  // 選択した値が初期と比較して変更されているかどうか.
  const isChanged =
    userIdList.some(v => !defaultUserIdList.includes(v)) ||
    userIdList.length !== defaultUserIdList.length;

  /**
   * 値を更新.
   */
  const handleChange = (newValues: number[]) => {
    setUserIdList(newValues);
  };

  return (
    <Dialog
      sx={{
        '& .MuiDialog-container': {
          '& .MuiPaper-root': {
            width: '80vw',
            minWidth: '600px',
            maxWidth: '80vw',
          },
        },
        ...sx,
      }}
      open={open}
      onClose={handleClose}
    >
      <Box
        sx={{
          padding: '20px',
          display: 'flex',
          flexDirection: 'column',
          alignItems: 'center',
          gap: '10px',
        }}
      >
        <TypoSubTitle1>メンバー編集</TypoSubTitle1>
        <Divider sx={{ width: '100%' }} />
        <SelectTrnUserMultiple
          sx={{ width: '100%' }}
          value={userIdList}
          handleChange={handleChange}
        />
        <Box
          sx={{
            width: '100%',
            display: 'flex',
            justifyContent: 'space-between',
          }}
        >
          <ButtonGeneral label="キャンセル" onClick={handleClose} />
          <ButtonGeneral
            label="更新"
            onClick={() => handleSubmit(userIdList)}
            disabled={!isChanged}
          />
        </Box>
      </Box>
    </Dialog>
  );
};
