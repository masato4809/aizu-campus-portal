# Qマート（社内フリマ機能） 開発ロードマップ

## 機能概要

社員間で不用品を売買・譲渡するための社内フリマ機能「Qマート」。
社員が不要になったアイテムを出品し、他の社員が購入・問い合わせできる仕組みを提供する。

## 技術スタック

| レイヤー | 技術 |
|---|---|
| Backend Framework | Laravel 10 |
| API | GraphQL |
| Frontend Bridge | Inertia.js |
| Frontend | React + TypeScript |

## アーキテクチャ・命名ルール

| 項目 | ルール |
|---|---|
| ドメイン名 | `Qmart` |
| ビジネスロジック | `app/Pipe` 配下の Pipe クラスに記述 |
| API定義 | GraphQL のみ。`graphql/App/Qmart` 配下に定義 |
| フロントエンド画面 | `resources/script/Pages/Qmart` 配下に作成 |

## 本番環境テスト戦略 (重要)

- 専用の開発環境がないため、**本番環境に直接リリースしながらテストを行う**。
- 開発中であることが一般社員に気づかれないよう、**すべての機能が完成するまで AppBar や Drawer などのメニューにはリンクを追加しない**。
- 開発中の画面の動作確認は、URL（例: `/qmart`）を直接叩いて行う。
- ルーティング設定 (`routes/web.php`) のみ先に行うこと。

## データベース設計

### trn_qmart_items（出品アイテム）

| カラム | 型 | 説明 |
|---|---|---|
| id | bigint (PK) | 自動採番 |
| user_id | unsignedBigInteger (FK: trn_user.id) | 出品者のユーザID |
| title | string | タイトル |
| description | text | 説明文 |
| price | integer | 価格（0 = 無料譲渡） |
| status | integer (default: Selling) | ステータス（EQmartItemStatus） |
| created_at | timestamp | 作成日時 |
| updated_at | timestamp | 更新日時 |
| deleted_at | timestamp (nullable) | 論理削除日時 |

### trn_qmart_item_images（アイテム画像）

| カラム | 型 | 説明 |
|---|---|---|
| id | bigint (PK) | 自動採番 |
| qmart_item_id | unsignedBigInteger (FK: trn_qmart_items.id) | 紐づくアイテムID |
| file_path | string | 画像ファイルパス |
| sort_order | integer | 表示順 |

### trn_qmart_comments（コメント）

| カラム | 型 | 説明 |
|---|---|---|
| id | bigint (PK) | 自動採番 |
| qmart_item_id | unsignedBigInteger (FK: trn_qmart_items.id) | 紐づくアイテムID |
| user_id | unsignedBigInteger (FK: trn_user.id) | コメント投稿者のユーザID |
| comment | text | コメント本文 |
| created_at | timestamp | 作成日時 |

## Enum定義

### EQmartItemStatus（出品ステータス）

| 値 | ラベル | 説明 |
|---|---|---|
| Selling | 出品中 | 購入可能な状態 |
| Negotiating | 交渉中 | 購入希望者と交渉中 |
| Sold | 売却済 | 取引完了 |
| Archived | アーカイブ | 出品者が取り下げ |

## 実装タスクリスト

- [x] 1. Migrationファイルの作成と実行
- [x] 2. Enum (PHP & TypeScript) の作成
- [x] 3. Backend: Models & Services の作成
- [x] 4. Backend: GraphQL Schema (Query/Mutation) 定義
- [x] 5. Frontend/Backend: ルーティング設定と空の一覧画面 (Index) の作成（URL直叩き用）
- [ ] 6. Backend: 出品登録処理の実装 (Pipe)
- [ ] 7. Frontend: 一覧画面の実装
- [ ] 8. Frontend: 出品モーダル/画面 の実装
- [ ] 9. Frontend: 詳細画面 (Show) の実装
- [ ] 10. Backend/Frontend: 購入/問い合わせ機能の実装
- [ ] 11. Backend/Frontend: 取引チャット機能の実装
- [ ] 12. 最終確認: AppBarへのメニュー追加と全社リリース

## Current Status

タスク5（ルーティング設定と空の一覧画面）完了。次は出品登録処理の実装（タスク6）から。

### 完了メモ（タスク5）
- ルーティング: `routes/web.php` に `/qmart` ルート追加（`auth:web` ミドルウェアグループ内）
- PHP Enum: `app/Enum/App/EPages.php` に `QMART = 'qmart'` を追加
- Controller: `app/Http/Controllers/Pages/QmartController.php` 新規作成（Inertiaで `Qmart/Index` をレンダリング）
- TS Enum: `resources/script/Enum/Server/App/EPages.ts` に `QMART` 追加（href, name 対応含む）
- Frontend: `resources/script/Pages/Qmart/Index.tsx`, `Qmart.tsx` 新規作成（タイトル「Qマート」のみ表示の空ページ）
- ※メニューへの導線は未追加（本番テスト戦略に従い、URL直叩きで確認）

### 完了メモ（タスク4）
- GraphQL Schema定義: `graphql/App/Qmart/Qmart.graphql`
  - Query: `AppQmartItemList`（オフセットページネーション）, `AppQmartItemDetail`（詳細取得）
  - Mutation: `AppQmartItemCreate`（出品登録）, `AppQmartItemUpdateStatus`（ステータス更新）, `AppQmartCommentCreate`（コメント投稿）
  - 型定義: `AppQmartItem`, `AppQmartItemImage`, `AppQmartComment`, `AppQmartItemListResponse`
- フロントエンド用クエリ/ミューテーション定義: `graphql/App/Qmart/Queries/Qmart.graphql`, `graphql/App/Qmart/Mutations/Qmart.graphql`
- Query Resolver: `app/GraphQL/App/Qmart/Queries/AppQmartItemListQuery.php`, `AppQmartItemDetailQuery.php`
- Mutation Resolver（仮実装）: `app/GraphQL/App/Qmart/Mutations/AppQmartItemCreate.php`, `AppQmartItemUpdateStatus.php`, `AppQmartCommentCreate.php`
- `schema.graphql` に `#import App/Qmart/Qmart.graphql` を追加済み
- 既存の `AppTrnUser` 型を再利用（出品者・コメント投稿者のリレーション用）

### 完了メモ（タスク2・3）
- PHP Enum: `app/Enum/App/Qmart/EQmartItemStatus.php` (SELLING=1, NEGOTIATING=2, SOLD=3, ARCHIVED=4)
- TS Enum: `resources/script/Enum/Server/App/Qmart/EQmartItemStatus.ts`
- Models: `TrnQmartItem`（SoftDeletes使用）, `TrnQmartItemImage`, `TrnQmartComment`
- Services / Facades / Providers: 各3ファイル作成、`bootstrap/providers.php` に登録済み
