import * as React from 'react';
import { Box, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { E_PAGES, getPagesName } from '@/script/Enum/Server/App/EPages';
import { Page } from '@/script/Pages/Page';
import { TypoH3 } from '@/script/Component/Typography/TypoH3';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { IAppTrnUser } from '@/script/Models/App/Trn/TrnUserList';
import { useIndexContext } from '@/script/Pages/Sample/InertiaData/Index';

interface IPros extends IPropsBase {
  fixArray: object[];
  sqlArray: object[];
  eloquentArray: object[];
  payloadArray: IAppTrnUser[];
}
export const InertiaData: React.FC<IPros> = ({
  fixArray,
  sqlArray,
  eloquentArray,
  payloadArray,
}) => {
  /**
   * ⑤データ、providerに保持した値を呼び出す
   * この方式で参照するとpropsのリレーが必要ない.
   */
  const { providerArray } = useIndexContext();
  console.log('[providerArray]での受信をProviderから取得した.');
  console.table(providerArray);

  /**
   * ⑥データ、providerに保持した値を呼び出す
   * この方式で参照するとpropsのリレーが必要ない.
   * parse処理をかけていて、絶対に安全なデータが入っている事を保障している.
   */
  const { trnUserList } = useIndexContext();
  console.log('[parseArray]での受信をProviderから取得した.');
  console.table(trnUserList.list());

  return (
    <Page>
      <TypoH1>{getPagesName(E_PAGES.SAMPLE_INERTIA_DATA)}</TypoH1>
      <Paper
        sx={{
          marginTop: '20px',
          padding: '20px',
        }}
      >
        <TypoH3>fixArray</TypoH3>
        <TypoText sx={{ marginBottom: '20px' }}>
          オブジェクトの配列として受け取り各オブジェクトのnameとageを表示する.
        </TypoText>
        {fixArray.map((v: Partial<{ name: string; age: string }>) => {
          return (
            <Box key={v.name}>
              {v.name}:{v.age}
            </Box>
          );
        })}
      </Paper>
      <Paper
        sx={{
          marginTop: '20px',
          padding: '20px',
        }}
      >
        <TypoH3>sqlArray</TypoH3>
        <TypoText sx={{ marginBottom: '20px' }}>
          オブジェクトの配列として受け取り各オブジェクトのidとnicknameを表示する.
        </TypoText>
        {sqlArray.map((v: Partial<{ id: number; nickname: string }>) => {
          return (
            <Box key={`sql-${v.id}`}>
              {v.id}:{v.nickname}
            </Box>
          );
        })}
      </Paper>
      <Paper
        sx={{
          marginTop: '20px',
          padding: '20px',
        }}
      >
        <TypoH3>eloquentArray</TypoH3>
        <TypoText sx={{ marginBottom: '20px' }}>
          オブジェクトの配列として受け取り各オブジェクトのidとnicknameを表示する.
        </TypoText>
        {eloquentArray.map((v: Partial<{ id: number; nickname: string }>) => {
          return (
            <Box key={`eloquent-${v.id}`}>
              {v.id}:{v.nickname}
            </Box>
          );
        })}
      </Paper>
      <Paper
        sx={{
          marginTop: '20px',
          padding: '20px',
        }}
      >
        <TypoH3>payloadArray</TypoH3>
        <TypoText sx={{ marginBottom: '20px' }}>
          IAppTrnUserの配列として受け取り各インターフェースのidとnicknameを表示する.
        </TypoText>
        {payloadArray.map(v => {
          return (
            <Box key={`payload-${v.id}`}>
              {v.id}:{v.nickname}
            </Box>
          );
        })}
      </Paper>
      <Paper
        sx={{
          marginTop: '20px',
          padding: '20px',
        }}
      >
        <TypoH3>providerArray</TypoH3>
        <TypoText sx={{ marginBottom: '20px' }}>
          ProviderからIAppTrnUserの配列として受け取り各インターフェースのidとnicknameを表示する.
        </TypoText>
        {providerArray.map(v => {
          return (
            <Box key={`provider-${v.id}`}>
              {v.id}:{v.nickname}
            </Box>
          );
        })}
      </Paper>
      <Paper
        sx={{
          marginTop: '20px',
          padding: '20px',
        }}
      >
        <TypoH3>parseArray</TypoH3>
        <TypoText sx={{ marginBottom: '20px' }}>
          ProviderからAppTrnUserListクラスとして受け取り各インターフェースのidとnicknameを表示する.
        </TypoText>
        {trnUserList.list().map(v => {
          return (
            <Box key={`parse-${v.id}`}>
              {v.id}:{v.nickname}
            </Box>
          );
        })}
      </Paper>
    </Page>
  );
};
