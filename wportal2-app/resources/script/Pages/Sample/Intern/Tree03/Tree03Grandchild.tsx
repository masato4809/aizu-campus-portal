import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';

interface IPros extends IPropsBase {
  handleClick: () => void;
}
export const Tree03Grandchild: React.FC<IPros> = ({ handleClick }) => {
  console.log('<Tree03Grandchild /> がレンダリングされた');
  return (
    <Box
      sx={{
        backgroundColor: '#cbe1ca',
        padding: '10px',
        border: '1px solid #ccc',
        borderRadius: '5px',
        marginTop: '10px',
      }}
    >
      <TypoText>Tree03Grandchild</TypoText>
      <ButtonGeneral label="count up" onClick={handleClick} />
    </Box>
  );
};
