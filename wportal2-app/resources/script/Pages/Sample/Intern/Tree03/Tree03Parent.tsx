import * as React from 'react';
import { Box } from '@mui/material';
import { useCallback, useMemo } from 'react';
import { IPropsBase } from '@/script/System/System';
import { Tree03Child } from '@/script/Pages/Sample/Intern/Tree03/Tree03Child';
import { TypoText } from '@/script/Component/Typography/TypoText';

interface IPros extends IPropsBase {}
export const Tree03Parent: React.FC<IPros> = ({ children }) => {
  console.log('<Tree03Parent /> がレンダリングされた');
  const [count, setCount] = React.useState<number>(0);

  /**
   * ボタンクリック時の処理.
   */
  // 関数が再定義されないため、[handleClick]は変更なしとみなされる
  const handleClick = useCallback(() => {
    setCount(prevState => prevState + 1);
  }, []);

  // 関数が再定義されるため、[handleClick]は変更されたとみなされる
  /*
  const handleClick = () => {
    setCount(prevState => prevState + 1);
  }
   */

  /**
   * memo化した子コンポーネント.
   */
  const child = useMemo(() => {
    return <Tree03Child handleClick={handleClick} />;
  }, [handleClick]);

  return (
    <Box
      sx={{
        backgroundColor: '#82ce7f',
        padding: '10px',
        border: '1px solid #ccc',
        borderRadius: '5px',
        marginTop: '10px',
      }}
    >
      {children}
      <TypoText>count:{count}</TypoText>
      {child}
    </Box>
  );
};
