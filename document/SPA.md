# SPAビルドについて

本環境のデフォルトのビルド形式はSSRとなっています。

## ビルド方式の切り替えについて

SPAのビルドを用いたい時はpackage.jsonを下記のように変更してください

### SSRビルド時
```
    "scripts": {
        "dev": "vite",
        "build": "vite build && vite build --ssr",
```

### SPAビルド時
```
    "scripts": {
        "dev": "vite",
        "build": "vite build",
```

以降、```npm run build```でSPAとなる.jsがビルドされます。（サイズがSSRより大きくなります）

実行後に```localhost:9190```ページにアクセスするとSPAアプリケーションが起動します。

この場合```php artisan inertia:start-ssr```の実行は不要です

## SPAの場合に不要な処理について

そのまま残っていても問題はありませんがSPAでは不要な処理について記載します

### Locationの処理

```Http/Controllers/Controller.php```で行っている下記の処理が不要です

ただ、どちらを利用しても問題ないのでそのままでも構いません。

```
                'location'       => [
                    'current' => Request::url(),
                    'path'    => Request::path(),
                ],
```

※SSRではブラウザから情報が拾えないためサーバーから送信していますが、SPAの場合任意のタイミングで
```window.location```から情報を取得できます。

あわせて```wportal2-app/resources/script/Provider/CommonIndexProvider.tsx```で
行っているlocationの受取も不要となります

※1.下記のlocationに関わる部分
```
/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  csrfToken: string;
  authUser: IAuthUser;
  location: ILocation;
};
export const context = React.createContext<IndexContext>({
  csrfToken: '',
  authUser: {
    id: 0,
    name: '',
  },
  location: getDefaultLocation(),
});
```

※2.下記のlocationに関わる部分
```
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(
    () => ({
      csrfToken: csrfToken ?? '',
      authUser: {
        id: authUser?.id ? Number(authUser.id) : 0,
        name: authUser?.name ? String(authUser.name) : '',
      },
      location: parseLocation(location),
    }),
    [csrfToken, authUser, location],
  );
```