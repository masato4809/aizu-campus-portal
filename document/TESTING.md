# テスト実行ガイド

このドキュメントは「開発者が手動でセットアップする必要があること」と「普段の実行方法」を、VS Code / CLI / IDE それぞれで迷わない形にまとめたものです。

## まず押さえること（このリポジトリの前提）

- テストは基本的に Docker Compose 上の `wportal2_web` コンテナで実行します。
	- ホストに PHP/Composer が無い環境でも動くためです。
	- その代わり、VS Code の Testing 表示で「失敗箇所クリックでファイル/行にジャンプ」したい場合は、ホスト↔コンテナのパスマッピングが必要になることがあります。

## クイックスタート

### 1) 必要なもの

- Docker Desktop が起動していること
- VS Code で「リポジトリのルート（`docker-compose.yml` がある階層）」を開いていること

### 2) コンテナ起動（初回/未起動時）

- VS Code から: `Terminal: Run Task...` → `Docker: Up (wportal2_web)`
- 端末から: `docker compose up -d wportal2_web`

### 3) 依存関係インストール（初回のみ / vendor が無い場合）

```sh
docker compose exec -T -w /var/www/html/wportal2-app wportal2_web composer install
```

## VS Code から実行

### A. チャット（Copilot）から実行（手動セットアップは不要）

VS Code の Tasks を定義してあるため、Copilot Chat には次のように依頼できます。

- 「VS Code のタスク `Run Tests` を実行して」
- 「`Run Test (Current File)` で、今開いているファイルのテストを実行して」
- 「`Run Test (Filter Prompt)` で `ShiftScheduleQueryServiceTest` を filter 実行して」

内部的には `docker compose exec ... wportal2_web ... phpunit` を実行します。

### B. Testing サイドバー（フラスコ）から実行（手動セットアップが必要）

#### 1) 拡張機能のインストール（手動）

- `PHPUnit Test Explorer`（拡張ID: `recca0120.vscode-phpunit`）

※推奨拡張はリポジトリルートの `.vscode/extensions.json` に記載しています。

#### 2) パスマッピング設定（手動）

`PHPUnit Test Explorer` はテスト結果のファイルパスを VS Code 上のファイルに紐づけるため、環境によってはホスト↔コンテナのパス変換（パスマッピング）が必要です。

補足:
- テストの「実行」自体は `tools/phpunit` ラッパーで Docker 実行できるため、パスマッピングが無くても動く場合があります。
- 一方で、テスト失敗時にクリックで該当ファイル/行へジャンプできない場合は、`phpunit.paths` の設定が必要です。

`phpunit.paths` は「各自の clone 先が違う」ため、リポジトリには固定値を入れず、各開発者が VS Code の User Settings に追加する運用にします。

設定手順:

1. VS Code → コマンドパレット → `Preferences: Open User Settings (JSON)`
2. 下記を追加（ホスト側のパスは自分の環境に合わせる）

例:

```json
"phpunit.paths": {
	"/absolute/path/to/wportal2": "/var/www/html"
}
```

ホスト側のパスが分からない場合は、リポジトリルートで `pwd` を実行してその値を使ってください。

補足: `.vscode/settings.json` は共有しますが、ここに環境依存の絶対パスは入れない方針です（チームで衝突しやすいため）。

#### 3) テストの検出と実行

1. 左サイドバーのフラスコ（Testing）を開く
2. コマンドパレットで `PHPUnit: Reload tests` を実行（初回の検出）
3. ツリー上の ▶︎ ボタンで suite / ファイル / テスト単体を実行

## CLI から実行（Docker 経由）

### 推奨（ラッパースクリプト）

リポジトリルートに Docker 経由で動くラッパーがあります。

```sh
./tools/phpunit -c wportal2-app/phpunit.xml --testsuite Unit
./tools/phpunit -c wportal2-app/phpunit.xml --testsuite Unit --filter ShiftScheduleQueryServiceTest
```

### 直接 docker compose で実行

```sh
docker compose exec -T -w /var/www/html wportal2_web wportal2-app/vendor/bin/phpunit -c wportal2-app/phpunit.xml --testsuite Unit
```

### `php artisan test` を使う場合（コンテナ内で実行）

`php artisan test` を使う場合は、ホストではなくコンテナ内で実行してください。

```sh
docker compose exec -T -w /var/www/html/wportal2-app wportal2_web php artisan test
docker compose exec -T -w /var/www/html/wportal2-app wportal2_web php artisan test --testsuite=unit
docker compose exec -T -w /var/www/html/wportal2-app wportal2_web php artisan test --testsuite=feature
```

## テストの構成（どこに書くか）

### テスト種別とベースクラス

- Unit テスト: `tests/Unit` 配下
	- `PHPUnit\Framework\TestCase` を継承
	- DB や Laravel カーネルに依存しない軽量テストを想定
- Feature/Integration テスト: `tests/Feature` 配下
	- `Tests\Feature\FeatureTestCase` を継承
	- Laravel アプリを起動し、HTTP 層や DB を含む振る舞いを検証
	- `RefreshDatabase` でテストごとにテーブルをリフレッシュ

### 主要ファイル

- `phpunit.xml`: テストスイートとテスト用環境変数を定義
- `tests/TestCase.php`: Laravel カーネル起動用の基底クラス
- `tests/Feature/FeatureTestCase.php`: Feature/Integration 用ベースクラス

### DB と環境

- テスト環境変数は `phpunit.xml` に定義（`APP_ENV=testing` など）
- Feature テストは `RefreshDatabase` によりマイグレーションを実行し、テストごとに DB をクリーンにします

## IDE から実行（IntelliJ / PhpStorm）

- PHPUnit 設定: `Settings | PHP | PHPUnit` → `+` → `By Remote Interpreter`（Docker/WSL の場合）
- `Default configuration file` に `phpunit.xml` を指定
- Docker デバッグ: Xdebug 3 のデフォルトはポート `9003`。`/var/www/html/wportal2-app` ↔ ローカルプロジェクトの Path mapping を設定

## トラブルシュート

### `./vendor/bin/phpunit` が `127` になる

ホストに `php` が入っていない可能性があります。Docker 経由（VS Code タスク、または `./tools/phpunit`）で実行してください。

### Testing サイドバーにツリーが出ない

- `recca0120.vscode-phpunit` が入っているか確認し、`PHPUnit: Reload tests` を実行してください
- `wportal2_web` が起動していない場合は `Docker: Up (wportal2_web)` を実行してください

### テストは走るが、失敗箇所クリックでファイルが開けない / 行番号が飛ばない

`.vscode/settings.json` の `phpunit.paths`（ホスト↔コンテナの対応）がズレている可能性があります。
clone 先の絶対パスに合わせて更新してください。
