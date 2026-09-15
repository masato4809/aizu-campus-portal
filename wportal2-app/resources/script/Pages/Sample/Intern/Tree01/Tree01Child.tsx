import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { Tree01Grandchild } from '@/script/Pages/Sample/Intern/Tree01/Tree01Grandchild';

interface IPros extends IPropsBase {}
export const Tree01Child: React.FC<IPros> = () => {
  console.log('<Tree01Child /> がレンダリングされた');
  return (
    <Box
      sx={{
        backgroundColor: '#e09797',
        padding: '10px',
        border: '1px solid #ccc',
        borderRadius: '5px',
        marginTop: '10px',
      }}
    >
      <TypoText>Tree01Child</TypoText>
      <Tree01Grandchild />
    </Box>
  );
};
