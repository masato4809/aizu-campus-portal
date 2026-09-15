# レスポンシブ対応（media query）について

## SSR時に考慮すべきこと

レスポンシブ対応（PC画面・モバイル画面などで表示を制御する）を行う場合
JavaScriptでどちらの画面サイズかを判定するので問題がなければMUI提供のuseMediaQueryを利用すれば良い

- wportal2-app/resources/script/Hooks/Common/useMediaSize.tsx

```
export const useMediaSize = () => {
  const isMobileSize = useMediaQuery((theme: Theme) => {
    return theme.breakpoints.down('md');
  });

  return { isMobileSize };
};
```

上記カスタムフックを下記のように利用すれば、該当のコンポーネントで画面サイズに応じた処理を実施できる

```
const { isMobileSize } = useMediaSize();

if (isMobileSize) {
  // サイズ毎の処理
}
```

しかしこの処理で判定を行ってしまうと、SSR時の初回描画で問題が発生する

SSR実施時の処理の流れとしては

1. リクエスト受付
2. nodeでhtml構築
3. htmlをブラウザに送信
4. htmlとcssを解釈して表示
5. ページを構築したJavaScriptをブラウザに送信（hydrate）
6. JavaScriptによってブラウザを再描画（以降、JavaScriptが利用可能）

となる。

この時、2の時点ではブラウザのスクリーン情報がないためJavaScriptで画面サイズを判定することができない

よって、4の描画結果と6の描画結果が異なる結果となる可能性があり、 その場合画面の表示が一瞬切り替わるような症状となる

この問題は抜本的に解決することが不可能で、レスポンシブ対応が必要な場合は下記の優先度で対処することを推奨する。

1. コンポーネントのCSS設定でmedia queryを記載する
   - メリット
     - CSSによる描画時に対応するためSSRかどうかが関係がない
     - サイズの動的変更に対応している
   - デメリット
     - 設計時に注意しないと、各コンポーネントに処理が入り乱れる
     - display:noneとする対応となるため、htmlとしては出力されてしまう
2. useMediaQueryを利用する
   - メリット
     - コンポーネント構築時に手軽に画面サイズ判定を行える
     - サイズの動的変更に対応している
   - デメリット
     - SSR時には正常に動作しない
3. リクエスト時の情報（User-Agentなど）で初期表示タイプを強制する
   - メリット
     - CSSで解決不可能な場合でもサーバー側で処理が可能
     - SSR実行時の判定はある程度柔軟に実行できる
   - デメリット
     - ブラウザを小さくした状態のPC接続など、特定の条件を判定する事ができない
     - ブラウザの種類によって渡される情報にばらつきがある

## コンポーネントのCSS設定でmedia queryを記載する

基本はこの方法で対応する。

display:noneとなる場合は二重に出力されて問題ない情報か確認すること。

- 簡易的に表示を切り替えたい場合は下記のコンポーネントで囲むだけでOK

```
<DisplayDesktop>
  <YourComponent>
</DisplayDesktop>
<DisplayMobile>
  <YourComponent>
</DisplayMobile>
```

- 各コンポーネントのSxPropsで記載する場合は下記

```
<Box
  sx={{
    display: {
      xs: 'none',
      sm: 'none',
      md: 'block',
      lg: 'block',
      xl: 'block',
    },
  }}
/>

※省略したサイズはデフォルト値になるので、全部指定する必要がある場合とそうでない場合がある
```

- StyledComponentsの形式で切り出す場合は下記

```
const DisplaySwitchBox = styled(Box)((props) => ({
  [`&.${boxClasses.root}`]: {
    [props.theme.breakpoints.up('md')]: {
      display: 'none',
    }
  }
}))
```

- mediaを直接記載しても問題ない

```
sx = {{
  @media screen and (min-width:1024px)' : { display: "none" }
}}
```

- 各CSSで設定しているサイズは下記でカスタム可能

```
breakpoints: {
  values: {
    xs: 0,
    sm: 375,
    md: 1024,
    lg: 1200,
    xl: 1536,
  },
},
```

## useMediaQueryを利用する

描画処理自体が実施されないため、判定後の表示・処理については検討すべき項目はない。

利用方法はドキュメント前半を参照。

SSR時と判定が変わるようなレイアウト処理には向かないが、hydrate後、JavaScriptでトリガする処理では原則問題なし

## リクエスト時の情報（User-Agentなど）で初期表示タイプを強制する

※サンプルの実装は現時点(2024/03)ではナシ

PHP側でInertia::renderをコールする前にrequest-headerを参照することができる（1.リクエスト受付直後）

このheaderのUser-Agentや、Chromeの拡張フラグ（sec-ch-ua-mobileなど）でユーザー接続環境を推定することができる。


このフラグをInertia::render経由でReactのPropsに受け渡せば、判定タイプによる処理分岐は可能。

処理としては
- Desktop版として処理する
- Mobile版として処理する
- 両方の処理をする
- 両方の処理を行わない

など柔軟に対応できる

ブラウザによって受け取る情報が異なり、小さくしたブラウザなどの判定ができないためどうしても必要な場合に実施する