import * as React from 'react';
import { Box, Dialog } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';

interface IActions {
  submit: () => void;
  close: () => void;
}

interface ILabels {
  content: string;
  decide?: string;
  cancel?: string;
}

interface IProps extends IPropsBase {
  open: boolean;
  labels: ILabels;
  actions: IActions;
}
export const DialogYesNo: React.FC<IProps> = ({
  sx,
  open,
  labels,
  actions,
}) => {
  const labelDecide = labels.decide ?? '決定';
  const labelCancel = labels.cancel ?? 'キャンセル';

  return (
    <Dialog
      sx={{
        ...sx,
      }}
      open={open}
      onClose={actions.close}
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
        <TypoText>{labels.content}</TypoText>
        <Box
          sx={{
            marginTop: '24px',
            width: '100%',
            display: 'flex',
            justifyContent: 'space-between',
          }}
        >
          <ButtonGeneral label={labelCancel} onClick={actions.close} />
          <ButtonGeneral label={labelDecide} onClick={actions.submit} />
        </Box>
      </Box>
    </Dialog>
  );
};
