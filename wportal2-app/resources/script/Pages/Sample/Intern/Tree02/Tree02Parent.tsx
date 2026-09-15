/* eslint-disable */
import * as React from 'react';
import { Box } from '@mui/material';
import { useMemo } from 'react';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { Tree02Child } from '@/script/Pages/Sample/Intern/Tree02/Tree02Child';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';

interface IPros extends IPropsBase {}
export const Tree02Parent: React.FC<IPros> = () => {
  console.log('<Tree02Parent /> がレンダリングされた');
  const [count, setCount] = React.useState<number>(0);

  /**
   * ボタンクリック時の処理.
   */
  const handleClick = () => {
    setCount(prevState => prevState + 1);
  };

  /**
   * memo化した子コンポーネント.
   */
  const numberValue = 4;
  const stringValue = 'string';
  const arrayValue = [1, 2, 3];
  const objectValue = { key: 'value' };
  const child = useMemo(() => {
    return <Tree02Child />;
  }, [arrayValue]);

  return (
    <Box
      sx={{
        backgroundColor: '#7396cb',
        padding: '10px',
        border: '1px solid #ccc',
        borderRadius: '5px',
        marginTop: '10px',
      }}
    >
      <TypoText>Tree02Parent</TypoText>
      <TypoText>count:{count}</TypoText>
      <ButtonGeneral label="count up" onClick={handleClick} />
      {child}
    </Box>
  );
};
