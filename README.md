# 環境構築の手順

## プロジェクト概要

会津大学の学生向け授業口コミ・学生交流Webアプリです。学生は大学メールアドレスで登録し、性格・学び方診断を行ったあと、授業や教員を検索できます。

主な機能：

- 授業検索、授業評価、投稿者属性による口コミ絞り込み
- 教員情報、教員評価、授業との相互リンク
- 先輩との一致度（性格3軸・学習目標・学部・サークル）表示
- 過去問所持数、サークル所属、サークル紹介投稿
- 授業・教員・サークルのデモデータ投入

登録には `@u-aizu.ac.jp` の大学メールアドレスが必要です。

## 前提

### Windows

- Docker Desktop for Windowsが利用可能になっている
- WSL2 + Ubuntuのセットアップが完了している
  - 参考）[WSL2 + Ubuntuのセットアップ](./document/WSL2.md)
- hostsファイルに ```127.0.0.1 localhost```のような記載があり、localhostでアクセスが可能になっている

### Mac

- Docker Desktop for Mac が利用可能になっている
- hostsファイルに ```127.0.0.1 localhost```のような記載があり、localhostでアクセスが可能になっている

## 注意事項
- 以降の構築手順はエディター内のターミナルタブ、Windowsターミナルなどどこから実施してもOKです
- dockerディレクトリ以下に```CRLF```ファイルがあるとビルドに失敗します
- CTRL+選択した状態でCTRL+SHIFT+Aでアクションを実施→```LF - Unix and MacOS```を実施すると一括でLFに変更できます
- インストール時の権限でファイル所有者がrootなどになっていると正常に動作しない場合があるので、その場合はchownで対応してください
    - 例）`sudo chown {name}:{group} -R {target_dir}`

## 環境構築

### リポジトリをcloneする

1. 任意の場所にディレクトリを作成する
  - 例 `/home/user-name/workspace/wportal2`
2. 上記のディレクトリをエディターで開く
3. 本リポジトリをcloneする

### .envファイルを用意する

1. 以下のコマンドを実行する

```
cd wportal2-app
cp .env.local.sample .env
```

2. Backlogのwiki[[接続情報]]に記載の以下の環境変数を設定する

```
# Backlog Wiki「接続情報」に記載された値を設定してください
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
```

### コンテナを起動する

```
cd ..
docker-compose build
docker-compose up -d
```

### composerとnpmをインストールする

```
docker exec -it wportal2_web bash
> composer install
> npm ci
```

### データベースを初期化する

```
## 初めて初期化する場合
> php artisan migrate

## 再度初期化する場合
> php artisan migrate:refresh
```

### ログファイルの権限を修正する

- コンテナから抜け出してUbuntu上などのターミナルで実行する
- /wportal2-appの直下で実行する

```
sudo chmod -R 777 storage/
```

### minioの設定

1. http://localhost:19001/login へアクセスする
2. `.env`に記載の以下の情報でログインする

```
WPORTAL_STORAGE_ACCESS_KEY=
WPORTAL_STORAGE_SECRET_KEY=
```

3. http://localhost:19001/buckets へアクセスする
4. `Create Bucket +`のボタンをクリックする
5. `Bucket Name`に`.env`に記載の以下の情報を入力する

```
WPORTAL_STORAGE_BUCKET=
```

6. `Create Bucket`のボタンをクリックする

### 初期データを投入する

```
docker exec -it wportal2_web bash
> php artisan db:seed --class=TestCaseSeeder
```

### Campusの動作確認

1. 開発サーバーを起動する

```bash
docker exec -it wportal2_web npm run dev
```

2. [http://localhost:9190/campus](http://localhost:9190/campus)へアクセスする
3. 会津大学のメールアドレス（`@u-aizu.ac.jp`）で登録する
4. 性格・学び方診断を完了する
5. 授業検索、授業口コミ、教員ページ、先輩との一致度、サークル情報を確認する

## precommitの設定（任意）

### precommitを設定する

- コンテナから抜け出してUbuntu上などのターミナルで実行する
- /wportal2-appの直下で実行する

```
(> exit)
npm run prepare
```

### コミット時の動作確認

1. 任意のブランチを作成する
2. `wportal2-app/resources/script/Pages/MvcValue/MvcValue.tsx`に動作確認のコードを追加する

```
const i = 0;
```

3. 下記のコマンドを実施する

```
git add .
docker exec -it wportal2_web bash
> npm run lefthook
```

4. 以下のようなエラー表示がされてコミットができないことを確認する

```
/var/www/html/wportal2-app/resources/script/Pages/MvcValue/MvcValue.tsx
  10:9  error  'i' is assigned a value but never used  @typescript-eslint/no-unused-vars

✖ 1 problem (1 error, 0 warnings)
```

## 付録

## Campus（会津大学 授業口コミ）

Campus機能は会津大学の授業検索・口コミ・教員情報・先輩との一致度・サークル情報を提供します。

### 初期データ

マイグレーション後、以下で固定デモデータを投入できます。何度実行しても既存データを重複登録しません。

```bash
docker exec -it wportal2_web php artisan migrate
docker exec -it wportal2_web php artisan db:seed
```

デモデータには授業・教員、最大1000人の学生、サークル紹介、授業口コミ、教員口コミ（ファン トゥアン アン先生を含む）が含まれます。

サークル情報編集用パスワードは、各自の `.env` に設定します（Gitには登録しません）。

```env
CAMPUS_CLUB_EDIT_PASSWORD=campus-club-2026
```

設定後は次を実行してください。

```bash
docker exec wportal2_web php artisan config:clear
```

Campus画面は `http://localhost:9190/campus` から開けます。

- [利用パッケージについて](document/PACKAGE.md)
- [Laravel/Inertiaの基本的な導線とページ作成ガイド](./document/PAGE_CREATION_GUIDE.md)
- [推奨するEnumの利用・自動出力について](./document/ENUM_EXPORT.md)
- [レスポンシブ対応（mediaQuery)について](document/MEDIA_QUERY.md)
- [GraphQLの利用方法について](./document/GRAPHQL.md)
- [バリデーション（フロント/サーバー）について](./document/VALIDATION.md)
- [SPAビルドについて](./document/SPA.md)
- [InertiaLinkとPartialLoadについて](./document/INERTIA_LINK_AND_PARTIAL_LOAD.md)
- [TIPS](./document/TIPS.md)
- [テスト実行ガイド（VS Code: チャット/フラスコ対応）](./document/TESTING.md)
