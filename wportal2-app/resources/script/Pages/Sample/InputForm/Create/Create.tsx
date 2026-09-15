import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { Page } from '@/script/Pages/Page';

interface IProps extends IPropsBase {}
export const Create: React.FC<IProps> = () => {
  return (
    <Page>
      <Box
        sx={{
          display: 'flex',
          flexDirection: 'column',
          alignItems: 'center',
          paddingTop: '100px',
        }}
      >
        <TypoText>POSTでの登録が完了しました</TypoText>
        <ButtonGeneral
          sx={{
            marginTop: '20px',
          }}
          label="入力フォームに戻る"
          href={getPagesHref(E_PAGES.SAMPLE_INPUT_FORM)}
        />
      </Box>
    </Page>
  );
};
