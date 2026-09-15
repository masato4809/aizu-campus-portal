# Campus Link チーム開発ガイド

## 担当の境界

画面はLaravel Blade、保存と検索はPHPで実装しています。別のフロント用サーバーやJSON APIはまだありません。フロント担当はBlade/CSS、バックエンド担当はController/Request/サービス/DBを編集します。URL・フォームのname・ビュー変数が両者の接点です。その仕様は [CAMPUS_CONTRACT.md](CAMPUS_CONTRACT.md) に記載しています。

- フロント担当：`wportal2-app/resources/views/campus/`、`wportal2-app/public/campus-assets/`。
- バックエンド担当：`wportal2-app/app/Campus/`、`app/Http/Controllers/Campus/`、`app/Http/Requests/Campus/`、`app/Http/Middleware/CampusAuthenticate.php`、Campus用マイグレーション・テスト。
- 共通担当：`routes/campus.php`、`layout.blade.php`、共通CSS、受け渡し仕様書。変更前にチーム内で担当を決めます。

以下のパスは、特記がない限り `wportal2-app/` からの相対パスです。

## バックエンドのファイルと役割

| ファイル | 役割 |
| --- | --- |
| `routes/web.php` | Campus用ルートファイルの読み込みを追加 |
| `routes/campus.php` | ページ・投稿先URLと認証・回数制限の登録 |
| `app/Http/Controllers/Campus/PageController.php` | ページに必要なデータを取得し、機能別ビューへ渡す |
| `app/Http/Controllers/Campus/AuthController.php` | パスワードログイン・ログアウト |
| `app/Http/Controllers/Campus/ProfileController.php` | 学生登録・プロフィール更新 |
| `app/Http/Controllers/Campus/ReviewController.php` | 検証済みの投稿を保存処理に渡す |
| `app/Http/Controllers/Campus/ClubController.php` | サークル情報の保存 |
| `app/Http/Controllers/Campus/MessageController.php` | DMの宛先検証と保存 |
| `app/Http/Middleware/CampusAuthenticate.php` | 未ログインの書き込みをログイン画面へ戻す |
| `app/Http/Requests/Campus/StoreReviewRequest.php` | 投稿項目の必須・文字数・数値範囲とエラー用日本語名 |
| `app/Campus/CurrentStudent.php` | セッションから学生を取得。画面にはメール・パスワードを渡さない |
| `app/Campus/CampusOptions.php` | レーダーチャートの評価軸と性格タイプの共通定義 |
| `app/Campus/CourseCatalog.php` | 授業検索・口コミ取得・評価平均の計算 |
| `app/Campus/ReviewWriter.php` | 授業と評価の保存。同じ投稿は更新し、元の作成日時を維持 |
| `app/Campus/StudentMatches.php` | 上級生の抽出と共通点スコア計算 |
| `app/Campus/ClubFeed.php` | 同じ大学のサークル投稿取得 |
| `app/Campus/Conversations.php` | DM相手一覧と本人・相手の会話だけを取得 |
| `database/migrations/2026_09_13_000000_create_campus_tables.php` | 学生・授業・評価・サークル・DMテーブル作成 |
| `database/migrations/2026_09_13_010000_add_details_to_campus_reviews.php` | 評価に学部・学科・出席方法・課題量・リモート割合・教材を追加。旧投稿はnullで保持 |

以前の一枚の `CampusController.php` は上記へ分割しました。適用済みマイグレーションを直接書き換えて項目を増やさず、追加ファイルを作ります。

## フロントエンドのブロック

| ファイル（`resources/views/campus/`配下） | 役割 |
| --- | --- |
| `layout.blade.php` | HTML全体・共通ヘッダー・エラー表示・フッター |
| `pages/courses.blade.php` | 授業一覧ページのブロック配置 |
| `pages/professors.blade.php` | 教授と担当授業の一覧 |
| `pages/login.blade.php`・`register.blade.php`・`profile.blade.php` | 認証・プロフィールページの入口 |
| `pages/matches.blade.php` | 先輩の共通点と授業投稿 |
| `pages/messages.blade.php` | 宛先一覧・会話・送信フォーム |
| `pages/clubs.blade.php` | サークル一覧と投稿フォーム |
| `components/sidebar.blade.php` | 共通ナビゲーション |
| `components/page-intro.blade.php` | 各ページの見出しと説明 |
| `components/profile-form.blade.php` | 学生登録・ログイン・プロフィールのフォーム |
| `components/course-search.blade.php` | 授業・教授の検索欄 |
| `components/course-card.blade.php` | 授業1件のカードと口コミ欄 |
| `components/radar-chart.blade.php` | サーバーで計算した平均値をSVGに描画 |
| `components/review-form.blade.php` | 授業評価の投稿フォーム |
| `components/review-detail.blade.php` | 1件の投稿詳細。授業一覧・先輩一覧で共用 |

以前の一枚の `index.blade.php` は削除し、上記へ分割しました。

| CSS | 役割 |
| --- | --- |
| `public/campus.css` | CSS読み込みの入口。通常は編集不要 |
| `public/campus-assets/base.css` | 色・入力欄・ボタン等の共通スタイル |
| `public/campus-assets/layout.css` | ページ配置・サイドバー・レスポンシブ対応 |
| `public/campus-assets/courses.css` | 授業・評価・投稿詳細・レーダーチャート |
| `public/campus-assets/messages.css` | DM一覧と吹き出し |
| `public/campus-assets/matches.css` | 先輩カードのスコア・共通点表示 |
| `public/campus-assets/profile.css` | プロフィールフォームの配置 |

## テスト

`tests/Feature/Campus/` の `AuthTest.php` は認証、`ReviewTest.php` は新しい投稿項目・境界値・作者の保護・既存投稿、`CommunityTest.php` はマッチング・DMの閲覧制限・サークルを確認します。`CampusTestCase.php` は専用のメモリ内DBを準備します。

```bash
docker exec wportal2_web php artisan test tests/Feature/Campus
```

## Gitで共有する手順

以下はMacのターミナルで実行します。共有先は確認済みのチームDです。`.env`やstorageの権限変更を含めないよう、対象を指定しています。

```bash
cd /Users/imamuramasato/Downloads/wportal2
git remote set-url origin https://scat919.backlog.jp/git/ENG_INTERN_202609/wportal2-team-d.git
git switch -c feature/campus-team-blocks

git add README_CAMPUS.md document/CAMPUS.md document/CAMPUS_TEAM.md document/CAMPUS_CONTRACT.md
git add wportal2-app/routes/web.php wportal2-app/routes/campus.php
git add wportal2-app/app/Campus wportal2-app/app/Http/Controllers/Campus
git add wportal2-app/app/Http/Middleware/CampusAuthenticate.php wportal2-app/app/Http/Requests/Campus
git add wportal2-app/database/migrations/2026_09_13_000000_create_campus_tables.php wportal2-app/database/migrations/2026_09_13_010000_add_details_to_campus_reviews.php
git add wportal2-app/resources/views/campus wportal2-app/public/campus.css wportal2-app/public/campus-assets
git add wportal2-app/tests/Feature/Campus

git diff --cached --stat
git diff --cached --check
git commit -m "Add Campus Link feature blocks and course review details"
git push -u origin feature/campus-team-blocks
```

初期版を別途コミットした環境では、分割で削除した旧 `app/Http/Controllers/CampusController.php` も `git add -u wportal2-app/app/Http/Controllers/CampusController.php` でステージします。この作業環境では旧ファイルは未追跡だったため不要です。

既に同名ブランチがあれば別名にしてください。Backlogの認証を求められたら自分のアカウントで認証します。push後はBacklogでプルリクエストを作成してレビューします。認証情報をコードに記載しません。

## メンバーが受け取った後

リポジトリの初回構築はルートREADMEに従います。アプリ追加後は次の2つのマイグレーションを実行します。既に適用済みならスキップされます。

```bash
docker exec wportal2_web php artisan migrate --path=database/migrations/2026_09_13_000000_create_campus_tables.php
docker exec wportal2_web php artisan migrate --path=database/migrations/2026_09_13_010000_add_details_to_campus_reviews.php
```

http://localhost:9190/campus を開きます。この画面は `npm run dev` 不要です。自分の担当ブロックごとにブランチを作り、フォーム項目名・URL・データの形を変更する場合だけ相手担当と仕様書を同時に更新してください。
