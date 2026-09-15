import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { Tree03Grandchild } from '@/script/Pages/Sample/Intern/Tree03/Tree03Grandchild';

interface IPros extends IPropsBase {
  handleClick: () => void;
}
export const Tree03Child: React.FC<IPros> = ({ handleClick }) => {
  console.log('<Tree03Child /> がレンダリングされた');
  return (
    <Box
      sx={{
        backgroundColor: '#bae5b8',
        padding: '10px',
        border: '1px solid #ccc',
        borderRadius: '5px',
        marginTop: '10px',
      }}
    >
      <TypoText>Tree03Child</TypoText>
      <Tree03Grandchild handleClick={handleClick} />
    </Box>
  );
};
