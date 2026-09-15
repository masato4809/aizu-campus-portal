# Laravel/Inertiaの基本的な導線とページ作成ガイド

## Laravel/Inertiaの基本的な導線

[Inertia.js](https://inertiajs.com/)

Laravel/InertiaでSSRページを構築する際の流れはおよそ下記のようになります。

1. webサーバーがリクエストを受付
2. 要求URLを元にroutingを実施(```routes/web.php```)
3. routingで各Controllerへ処理を移動
4. 必要に応じてModel/Eloquentを利用してデータを取得
5. ```Inertia::render```処理によって、特定のtsxをnodeに描画させる
6. 描画結果のhtmlをブラウザへ送信する

1.～5.までの処理の流れは基本的に従来のLaravelと変わらないため
この範囲でmiddlewareを利用したりModelを介してDBから値を取得したり等は従来通り行えます。

ごっそり入れ替わるのはLaravelで言うところの```View::render```処理（blade.phpで行う処理）です。

ただReactが特定のコンポーネントを起点に入れ子でコンポーネントを呼び出してhtmlを生成する流れとなり
blade.phpを細かく切って```@include```を繰り返していく状況をイメージしてもらえれば良いかと思います。

## ページ作成ガイド

新しいページを追加する場合、以下の流れで各処理を追加していくことで追加可能です。

※必ずしも下記の通りの順に作業をする必要はありません。

※```/mvc_value```に対する処理を参考にしてください。

### 例)```/sample```のURLで新規ページを追加する場合

- ```routes/web.php```に処理を追加してControllerを呼び出す
- 対応するControllerを```Http/Controllers```以下に作成
- ページの描画に必要なデータがあればModel/Eloquentで取得
- URLに対応するエントリポイントとなる```/resources/script/Pages/SampleProvider```を作成
  - nodeで呼び出せるように、エントリポイントのみ```export default```宣言が必要です
  - ControllerでデータをReactに渡すことが出来ます。ここでProviderに保持しておけば、以降のコンポーネントにPropsで伝達する必要がなくなります。
- ページ本体となるSample.tsxを作成し、これをSampleProviderから呼び出す
- 以降は各ページのデザインに沿ってコンポーネントを配置していく

### 実行時の注意

```npm run dev```によって、ローカル環境ですぐに描画結果を確認することが可能ですが

上記の状態はHMRとなり、フロント側でhtmlを構築した結果となります。

一通り作成したあと```php artisan inertia:start-ssr```のコマンドでSSR待ち受け状態にし、ローカルでも
SSR描画が正常に行われることを確認してください。