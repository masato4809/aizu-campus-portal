# wportal2
Laravel + Inertia + SSR + (React/Vite) + (MUI)のサンプル
利用パッケージについて

※主に利用しているものだけ記載

## composer(composer.json)

### Laravel

標準で利用しているフレームワーク、機能の説明は割愛する。
migration/seeder/artisanなどの環境構築やバッチ機能のほかサーバー側処理の殆どを対応する。

これを変更する場合は先にインフラとlocal環境からproduction環境までの全てを調整すること。

### Inertia

SSRを実施する時に、routingまでをapache+laravelで行い、ページの描画をコンテナ内のnodeサーバーで行うために導入したアダプタ。

- local環境
  - ViteでのHMRを実施し、動的にhtmlを出力
- production環境
  - routingまでをLaravelで実施後、nodeを利用して生成したhtmlを返す。ページを生成したJavaScriptは後追いでページに適用する

### pint

Laravelに組み込まれたphp-cs-fixer

```pint.json```記載のルールに沿ってPHPコードの自動整形を行う

ルール一覧は[こちら](https://mlocati.github.io/php-cs-fixer-configurator/#version:3.52)

### laravel-ide-helper

IDE上で各PHPコードの自動補完を行うため下記のファイルを出力する

- ```_ide_helper.php```
- ```_ide_helper_models.php```

### nuwave/lighthouse

graphql利用時にのみ利用。

Laravel上でgraphqlを利用するために使う.

設定ファイルは```config/lighthouse.php```

IDE用設定ファイルは```_lighthouse_ide_helper.php```

## npm(package.json)

### Vite

Laravel標準の高速な開発サーバー

- ※類似
  - webpack

### TypeScript

型制約を強化したいため導入、以降フロントのライブラリ選定はTypeScriptとの連携のしやすさで決定

ビルド時にJavaScriptに変換される。

### React

linter/formatter+TypeScriptの親和性が高い(2023年末時点)のため採用

Vueでも周辺ライブラリが揃っていれば特に問題ない

コンポーネントの階層構造でページを作成するため、不要になったりリファクタしたい場合は根本から削除できるのが強み

### MUI

UIフレームワークのため他を選んでも問題ない

サンプルでの選定理由は以下のみ

- CSS in JS (emotion)の形式を採用している
  - sassやscssは設定ファイルの管理が煩雑なため避けたい
- 複数PJでの利用実績がある
- 使いたい機能を使えば良いだけで制約は特に生まれない

### ESLint

構文エラーやコーディング規約に関する静的解析ツール、 コードの一貫性を保つために利用

ベースはairbnbの規約を利用する

ルールは下記のファイルに記載

- ```.eslintrc.yml```
- ```.eslintignore```

### prettier

コードの自動整形ツール

一行の最大文字数や改行位置などに一貫性を持たせるために利用する

ルールは下記のファイルに記載

- ```.prettierrc```
- ```.prettierignore```

### husky

後述のlefthookをコミット時に動作させるため利用

自動出力の```.husky```フォルダにhooksが存在する

### lefthook

コミット時に上記の

- ESLint
- prettier
- pint
- tsc(TypeScriptのトランスパイラ/静的チェッカー)

を、修正したコードに対して実行する

コミット時のコマンド指定は```lefthook.yml```

### graphql-codegen

graphql利用時にのみ利用。

スキーマ駆動開発を行う際に、宣言したスキーマからTypeScript側の関数を自動出力する

出力されるファイルはApolloClient向けのコード

- スキーマ宣言
  - ```app/grapqh```以下
- 生成ルール
  - ```codegen.yml```
- 生成ファイル
  - ```resources/script/Graphql/codegen/graphql.tsx```
- その他IDE用関連ファイル
  - programmatic-type.graphql
  - schema-directives.graphql

