import * as React from 'react';
import { Box, CircularProgress } from '@mui/material';
import { IPropsBase } from '@/script/System/System';

interface IProps extends IPropsBase {
  loading?: boolean;
}
export const Loading: React.FC<IProps> = ({ sx, loading, children }) => {
  if (loading) {
    return (
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'center',
          ...sx,
        }}
      >
        <CircularProgress
          variant="indeterminate"
          disableShrink
          size={40}
          color="secondary"
          thickness={4}
          value={80}
        />
      </Box>
    );
  }
  return <>{children}</>;
};
