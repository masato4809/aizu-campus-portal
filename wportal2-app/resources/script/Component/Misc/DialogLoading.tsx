import * as React from 'react';
import { Box, CircularProgress, Dialog } from '@mui/material';
import { IPropsBase } from '@/script/System/System';

interface IProps extends IPropsBase {
  loading?: boolean;
}
export const DialogLoading: React.FC<IProps> = ({ loading }) => {
  return (
    <Dialog
      open={!!loading}
      PaperProps={{
        style: {
          boxShadow: 'none',
          width: '200px',
          height: '200px',
          background: 'none',
        },
      }}
    >
      <Box
        sx={{
          width: '100%',
          height: '100%',
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
        }}
      >
        <CircularProgress
          variant="indeterminate"
          color="secondary"
          disableShrink
          size={80}
          thickness={4}
          value={80}
        />
      </Box>
    </Dialog>
  );
};
