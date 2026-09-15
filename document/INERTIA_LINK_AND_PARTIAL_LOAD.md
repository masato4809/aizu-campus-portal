# InertiaLinkとPartialLoadについて

Inertiaを利用する環境で、要となるInertiaLinkとPartialLoadについて説明します

なお本機能においてはSSRかSPAかは関係ありません。

## はじめに

Inertiaは

- ページ読み込み
- データ取得
- データ再取得

などの処理を同一のLaravel Controllerで処理します

どういう事か、かなりわかりにくいのですが

あるページを読み込む際に、「初回」か「部分指定」かでControllerの挙動が変わります。


具体的には、以下のコントローラーアクセスの場合

```
        return $this->render('CommonIndexProvider/PaginationIndex', [
            'trnFacilityList'      => /** データ取得処理…1 */,
            'trnFacilityListCount' => /** データ取得処理…2 */,
            'fixList'              => fn () => /** データ取得処理…3 */,
            'lazyList'             => Inertia::lazy(fn () => /** データ取得処理…4 */,
        ]);
```

```trnFacilityList``` と ```trnFacilityListCount```は毎回処理されますが

```fixList```は同一ページのリロードの際は処理されず、データに変更ないものとして処理されます。

また```lazyList```に関しては、明示的指定がない場合は処理されません。

Controllerが上記の性質を持つため、例えば

- 検索ワードによって、データをフィルタして表示するコントローラーを作る
  - 初期表示はフィルタなし
  - フィルタを更新するタイミングで、Controllerを「部分指定」+フィルタパラメータで呼び出す
  - 更新したデータで、関連箇所のみ再描画する

というような挙動を実現でき、これがLaravel + Inertiaの要となります。

詳しくは```pagination```のURLで確認できる

```wportal2-app/app/Http/Controllers/Pages/PaginationController.php```

の挙動を確認してください。

一回目のアクセス時のqueryログは下記のようになりますが

```
2024-04-24T07:17:04.623459Z       164 Prepare   select * from `trn_facility` limit 3 offset 0
2024-04-24T07:17:04.623500Z       164 Execute   select * from `trn_facility` limit 3 offset 0
2024-04-24T07:17:04.623718Z       164 Close stmt
2024-04-24T07:17:04.623959Z       164 Prepare   select count(*) as aggregate from `trn_facility`
2024-04-24T07:17:04.624013Z       164 Execute   select count(*) as aggregate from `trn_facility`
2024-04-24T07:17:04.624739Z       164 Close stmt
2024-04-24T07:17:05.626118Z       164 Prepare   select * from `trn_facility` limit 5
2024-04-24T07:17:05.626214Z       164 Execute   select * from `trn_facility` limit 5
```

「次の３件」などで呼び出されるニ回目のアクセス時のqueryログは以下となります

※fixListが呼び出されていない

```
2024-04-24T07:17:52.786402Z       165 Prepare   select * from `trn_facility` limit 3 offset 3
2024-04-24T07:17:52.786538Z       165 Execute   select * from `trn_facility` limit 3 offset 3
2024-04-24T07:17:52.786807Z       165 Close stmt
2024-04-24T07:17:52.787133Z       165 Prepare   select count(*) as aggregate from `trn_facility`
2024-04-24T07:17:52.787213Z       165 Execute   select count(*) as aggregate from `trn_facility`
```

しかしながら、画面は新しいリストで再描画され

固定リストの描画は変動がありません。

SSR時も実現できているため、おそらくですが、bladeに仕込んである```data-page```で実現していると思われます。

上記の恩恵を受け取るためには、リンク処理をInertiaLinkもしくはPartialReloadで呼び出す必要があり、下記でそれぞれ説明します

## InertiaLink

通常```<a href=..>```のタグを配置する場所はMUIの<Link>ではなく、Inertiaの提供するリンクを利用してください

- 特に意図せず利用できるように
- ロード中処理を共通で行えるように

の観点で```wportal2-app/resources/script/Component/Misc/InertiaLink.tsx```を用意しています。

サンプルと同様これを利用してもらえばOKです

良く使いそうなパラメータは下記

- as
  - 何のタグとして配置するか ※as="div"ならdivとして配置される
- data
  - コントローラーに送るパラメータ
  - getのmethodで送信するとクエリストリングに付与される
- method
  - 基本はgetかpost
  - クエリストリングを表示させたくないならpost
- replace
  - ブラウザのhistoryを積むか置き換えるか（戻るボタンに影響）
- only
  - コントローラーが提供するパラメータのうち、再取得処理を行うものを指定する
  - ```データ取得処理…3```のような取得方法を指定しないと意味がない
- preserveScroll
  - スクロール位置を保持するかどうか
- preserveState
  - Reactが保持しているstateをそのまま保持させるかどうか
- onStart
  - コントローラーの呼び出しが始まったタイミングでコール
- onFinish
  - コントローラーの呼び出しが終了したタイミングでコール
- ※on～は他にも複数あり

SSR/SPAを問わず動作し、```データ取得処理…1```のような場合は基本的に影響がないので

リンクはすべて```<InertiaLink />```を利用する形で問題ないものと思います。

また、基本的にほぼ全部のパラメータはデフォルト（指定なし）で問題ありません

## PartialReload

```<InertiaLink>```で実現していることをscriptからコールする場合に利用します

例）
```
import { router } from '@inertiajs/react';

// url指定の場合は.visit(url, {})を利用する
router.reload({
  method: 'post',
  preserveState: true,
  replace: true,
  onStart,
  onFinish,
  data: {
    index: nextIndex,
    step: pageStep,
  },
  only: ['trnFacilityList', 'trnFacilityListCount'],
});
```

動作、パラメータのルールは全て```<InertiaLink>```と同様です

## その他

全体的に、非常に便利な挙動ではありますが、かなり想像しづらい動きになります。

一度、Paginationのサンプルと[TIPS.md](./TIPS.md)のクエリログの確認方法を併用して

挙動を把握することをお勧めします。

2024/04時点では[こちらの記事](https://note.com/mocotech/n/nf82062ca3993)もおすすめ