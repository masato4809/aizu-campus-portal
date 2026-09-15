# GraphQLの利用方法について

GraphQLを利用する場合

- 送受信するパラメータをスキーマ宣言から見渡せる
- TypeScript(JavaScript)上で送受信が完結する
  - ※GraphQLを利用しなくても実現しますが、結局出来ることは変わらないのに書くコード量が増えます
- 自動生成ツールの恩恵を受けやすい

などのメリットがあり、ページ上での動的なデータ取得/更新には非常に有用で

fetchやXMLHttpRequestなどでPOST通信を作成するならばmutation等の利用を推奨します。

テンプレート上では下記パッケージの組み合わせと

- Lighthouse
  - Laravel上でgraphqlを使うためのライブラリ
- graphql-codegen
  - スキーマ定義を自動でTypeScriptの関数に変換するツール
- ApolloClient
  - React上でgraphqlにアクセスしやすくするためのライブラリ

CustomResolverを利用しており、少し複雑になっているため、fetch/mutation追加時の流れを説明します

なお

|          |                                             |
|----------|---------------------------------------------|
| fetch    | データを取得するためのGraphQL経由のAPI、結果としてデータセットを受け取る   |
| mutation | データを更新するためのGraphQL経由のAPI、結果としてレスポンスコードを受け取る |

の意で呼称しますが、いずれもサーバーへ通信して何かの処理をする点では変わりはありません

mutationとして記載して、データセットを受け取る事も可能です（ただし分かりにくくはなります）

## fetch/mutationの追加の流れ

fetch/mutationを追加する場合はおよそ以下の流れで対応が必要です

- ```graphql/App```以下にschemaを追加
  - 例）```graphql/AppRedis.graphql```
  - QueryとMutationは別の宣言となる点に注意
  - 戻りを構造体のように扱う場合はtype宣言が別途必要
  - ```!```がつくものは必須カラムとなる
  - ```@field```で指定されたPHPファイルが処理ファイル
- graphql/schema.graphqlに```#import```を追加
- fetch/mutationそれぞれに引数と戻り値を宣言するGraphQL文を作成
  - 例）graphql/App/Mutations/Redis.graphql
  - 例）graphql/App/Queries/Redis.graphql
- codegen.ymlのschema/documentsに存在しないディレクトリを追加したら追加する
- PHP側の処理を実装する
  - バリデーションがあるため、引数の受け取りは別クラス(Pipe)で実施する
  - 例）app/GraphQL/App/Queries/RedisQuery.php
  - 例）app/GraphQL/App/Mutations/AppRedisUpdate.php
- コンテナ上で```npm run gen```を実施して```script/Graphql/codegen/graphql.tsx```を更新する
  - ※schemaで宣言したgraphqlのTypeScriptコードが追加されるはずです
- コンポーネントから呼び出すHooksを作成する
  - 例）```script/Hooks/App/Mutations/useMutationAppRedisUpdate.tsx```
    - 生成されたコードをPromiseで呼び出すための関数・通信状況を返します
  - 例）```script/Hooks/App/Queries/useLazyFetchAppRedis.tsx```
    - 要求されたデータセットをPromiseで呼び出すための関数・通信状況を返します
- 利用したい場所で、関数宣言を呼び出し、使いたい場所で使うでOK
  - 例）```script/Pages/Redis/Redis.tsx```
  - ```handleClickLoad```や```handleClickSave```など
  - サーバー側でバリデーションしている場合、戻りを処理する必要があるので注意

手順としては多く見えますが、結果として各API追加時に追加されるべきファイルが定まり

修正・編集の際に修正すべき箇所の特定、修正後の影響範囲などで大きな利点があります

## 補足）fetchとlazyFetchについて

codegenにより自動生成されるfetch処理は通常のfetchとlazyFetchの２種類があります

ざっくり説明だと以下の動作となります

- 通常のfetch
  - ページを構築する際にデータを取りに行く
  - ```const [loading, data] = useFetchFunctionName()```の形で呼び出す
  - 内部的にはCustomHooks
  - コンポーネントを構築する際をトリガにデータを取得し、データ取得が完了されたタイミングでhookする
- lazyFetch
  - 能動的に指定したタイミングでデータを取りに行く
  - ```const [loading, fetchFunc] = useLazyFetchFunctionName()```の形で準備する
  - スクリプトのトリガで```fetchFunc().then()```の形で利用する

従来のSPAだと基本データはfetchで取得し、特殊処理はlazyFetchで行うか、パラメータを変更したfetchで行うという形になります。

ただし、Laravel+Inertiaの組み合わせを利用しているため、ページ構築のためのデータはView経由で受け渡しが可能になり

基本的には通常のfetchを利用する必要はありません。