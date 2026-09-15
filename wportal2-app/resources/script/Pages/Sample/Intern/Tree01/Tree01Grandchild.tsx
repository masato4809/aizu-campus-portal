import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';

interface IPros extends IPropsBase {}
export const Tree01Grandchild: React.FC<IPros> = () => {
  console.log('<Tree01Grandchild /> がレンダリングされた');
  return (
    <Box
      sx={{
        backgroundColor: '#ecdbdb',
        padding: '10px',
        border: '1px solid #ccc',
        borderRadius: '5px',
        marginTop: '10px',
      }}
    >
      <TypoText>Tree01Grandchild</TypoText>
    </Box>
  );
};
