import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { Tree01Child } from '@/script/Pages/Sample/Intern/Tree01/Tree01Child';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';

interface IPros extends IPropsBase {}
export const Tree01Parent: React.FC<IPros> = () => {
  console.log('<Tree01Parent /> がレンダリングされた');
  const [count, setCount] = React.useState<number>(0);

  /**
   * ボタンクリック時の処理.
   */
  const handleClick = () => {
    setCount(prevState => prevState + 1);
  };

  return (
    <Box
      sx={{
        backgroundColor: '#e06c6c',
        padding: '10px',
        border: '1px solid #ccc',
        borderRadius: '5px',
        marginTop: '10px',
      }}
    >
      <TypoText>Tree01Parent</TypoText>
      <TypoText>count:{count}</TypoText>
      <ButtonGeneral label="count up" onClick={handleClick} />
      <Tree01Child />
    </Box>
  );
};
