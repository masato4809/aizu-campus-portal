import * as React from 'react';
import { Paper } from '@mui/material';
import { Page } from '@/script/Pages/Page';
import { E_PAGES, getPagesName } from '@/script/Enum/Server/App/EPages';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { IPropsBase } from '@/script/System/System';
import { useIndexContext } from '@/script/Pages/Sample/RedisPostResult/Index';

interface IProps extends IPropsBase {}
export const RedisPostResult: React.FC<IProps> = () => {
  const { storeValue } = useIndexContext();

  return (
    <Page>
      <TypoH1>{getPagesName(E_PAGES.SAMPLE_REDIS_POST_RESULT)}</TypoH1>
      <Paper
        sx={{
          marginTop: '20px',
          padding: '10px',
        }}
      >
        <TypoText>{`Redisの値を[${storeValue}]に更新しました`}</TypoText>
      </Paper>
    </Page>
  );
};
