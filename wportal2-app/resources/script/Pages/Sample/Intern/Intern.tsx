import * as React from 'react';
import { useEffect } from 'react';
import { Page } from '@/script/Pages/Page';
import { E_PAGES, getPagesName } from '@/script/Enum/Server/App/EPages';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { Tree01Parent } from '@/script/Pages/Sample/Intern/Tree01/Tree01Parent';
import { Tree02Parent } from '@/script/Pages/Sample/Intern/Tree02/Tree02Parent';
import { Tree03Parent } from '@/script/Pages/Sample/Intern/Tree03/Tree03Parent';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { IPropsBase } from '@/script/System/System';

interface IPros extends IPropsBase {}
export const Intern: React.FC<IPros> = () => {
  console.log('<Intern /> がレンダリングされた');

  /**
   * useEffectの例
   */
  useEffect(() => {
    console.log(
      'マウント以降、再レンダリング時にdepsの変更検知の度にここが動作する',
    );
  }, []);

  return (
    <Page>
      <TypoH1>{getPagesName(E_PAGES.SAMPLE_INTERN)}</TypoH1>
      <Tree01Parent />
      <Tree02Parent />
      <Tree03Parent>
        <TypoText>
          Tree03Parentの子コンポーネントをCompositionとして渡す
        </TypoText>
      </Tree03Parent>
    </Page>
  );
};
