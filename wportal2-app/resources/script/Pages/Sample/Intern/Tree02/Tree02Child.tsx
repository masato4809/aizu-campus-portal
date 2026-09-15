import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { Tree02Grandchild } from '@/script/Pages/Sample/Intern/Tree02/Tree02Grandchild';

interface IPros extends IPropsBase {}
export const Tree02Child: React.FC<IPros> = () => {
  console.log('<Tree02Child /> がレンダリングされた');
  return (
    <Box
      sx={{
        backgroundColor: '#a8c6e8',
        padding: '10px',
        border: '1px solid #ccc',
        borderRadius: '5px',
        marginTop: '10px',
      }}
    >
      <TypoText>Tree02Child</TypoText>
      <Tree02Grandchild />
    </Box>
  );
};
