import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';

interface IPros extends IPropsBase {}
export const Tree02Grandchild: React.FC<IPros> = () => {
  console.log('<Tree02Grandchild /> がレンダリングされた');
  return (
    <Box
      sx={{
        backgroundColor: '#c8d5e1',
        padding: '10px',
        border: '1px solid #ccc',
        borderRadius: '5px',
        marginTop: '10px',
      }}
    >
      <TypoText>Tree02Grandchild</TypoText>
    </Box>
  );
};
