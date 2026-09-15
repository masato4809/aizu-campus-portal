import * as React from 'react';
import { Box, Dialog } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';

interface IActions {
  confirm: () => void;
}

interface ILabels {
  content: string;
  confirm?: string;
}

interface IProps extends IPropsBase {
  open: boolean;
  labels: ILabels;
  actions: IActions;
}
export const DialogConfirm: React.FC<IProps> = ({
  sx,
  open,
  labels,
  actions,
}) => {
  const labelConfirm = labels.confirm ?? '確認';

  return (
    <Dialog
      sx={{
        ...sx,
      }}
      open={open}
      onClose={actions.confirm}
    >
      <Box
        sx={{
          width: '100%',
          padding: '16px',
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          flexDirection: 'column',
        }}
      >
        <TypoText sx={{ whiteSpace: 'pre-wrap' }}>{labels.content}</TypoText>
        <Box
          sx={{
            marginTop: '24px',
            width: '100%',
            display: 'flex',
            justifyContent: 'end',
          }}
        >
          <ButtonGeneral label={labelConfirm} onClick={actions.confirm} />
        </Box>
      </Box>
    </Dialog>
  );
};
