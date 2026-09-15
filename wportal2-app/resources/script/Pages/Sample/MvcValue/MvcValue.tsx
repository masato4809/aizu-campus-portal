import * as React from 'react';
import { Box, Button, Paper } from '@mui/material';
import { Page } from '@/script/Pages/Page';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { E_PAGES, getPagesName } from '@/script/Enum/Server/App/EPages';
import { Log } from '@/script/Common/Log';
import { TypoH3 } from '@/script/Component/Typography/TypoH3';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { IPropsBase } from '@/script/System/System';
import { IAppTrnUser } from '@/script/Models/App/Trn/TrnUserList';

interface IPros extends IPropsBase {
  numberValue: number;
  stringValue: string;
  arrayValue: number[];

  trnUserList: IAppTrnUser[];
}
export const MvcValue: React.FC<IPros> = ({
  numberValue,
  stringValue,
  arrayValue,
  trnUserList,
}) => {
  const handleClick = () => {
    Log.info('クリックされた');
  };

  return (
    <Page>
      <TypoH1>{getPagesName(E_PAGES.SAMPLE_MVC_VALUE)}</TypoH1>
      <Paper
        sx={{
          marginTop: '20px',
          padding: '5px',
        }}
      >
        number: {numberValue}
      </Paper>
      <Paper
        sx={{
          marginTop: '10px',
          padding: '5px',
        }}
      >
        string: {stringValue}
      </Paper>
      <Paper
        sx={{
          marginTop: '10px',
          padding: '5px',
        }}
      >
        {arrayValue.map(v => {
          return <Box key={v}>{v}</Box>;
        })}
      </Paper>
      <Button
        sx={{
          marginTop: '10px',
        }}
        style={{ background: '#F00' }}
        onClick={() => handleClick()}
      >
        test
      </Button>
      <TypoH3
        sx={{
          marginTop: '20px',
        }}
      >
        DBから受け取った値を描画する
      </TypoH3>
      <TypoText sx={{ marginTop: '5px' }}>
        ※内容はLaravelのSeederが作成した文字列
      </TypoText>
      <Box
        sx={{
          marginTop: '10px',
          display: 'flex',
          flexWrap: 'wrap',
          gap: '5px',
        }}
      >
        {trnUserList.map(trnUser => (
          <Paper
            sx={{
              padding: '20px',
              width: 'calc(50% - 5px)',
            }}
            key={trnUser.id}
          >
            <TypoText>
              [{trnUser.id}]{trnUser.nickname}
            </TypoText>
            <Box
              sx={{
                border: '1px solid',
                marginTop: '10px',
                padding: '5px',
              }}
            >
              <TypoText sx={{ whiteSpace: 'pre-wrap' }}>
                {trnUser.nickname}
              </TypoText>
            </Box>
          </Paper>
        ))}
      </Box>
    </Page>
  );
};
