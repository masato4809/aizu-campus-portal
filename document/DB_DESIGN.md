# DB設計ドキュメント

## 1. 主要なテーブルの一覧と役割

全21テーブルを **認証系 / マスタ系 / トランザクション系** に分類。

### 認証系（1テーブル）

| テーブル名 | 役割 |
|---|---|
| `auth_user` | 認証ユーザー。Google OAuth / レガシーログイン / SPログイン（2FA付き）に対応。パスワード、ワンタイムパスワード、アーカイブレベルを保持。 |

### マスタ系（2テーブル）

| テーブル名 | 役割 |
|---|---|
| `mst_reward` | リワード（報酬）マスタ。種別(`reward_kind`)・ランク(`reward_rank`)・名称・説明・ゴールド報酬額・繰り返し可否を定義。 |
| `mst_goods` | 商品マスタ（ショップ機能用）。商品名・説明・カテゴリ・画像パス・著者メールアドレス・価格を保持。 |

### トランザクション系（18テーブル）

| テーブル名 | 役割 |
|---|---|
| `trn_user` | アプリユーザー情報。`auth_user`と1:1で紐づき、ニックネーム・誕生日・自己紹介・顔画像パス・ゴールド（現在/累計）・アーカイブレベルを保持。 |
| `trn_user_slack_profile` | ユーザーのSlack連携プロフィール。SlackユーザーID/名前/チームIDを保持。 |
| `trn_user_authority` | ユーザー権限管理。ROOT_PRIVILEGE(1)、ADMIN_PRIVILEGE(100)、ADMIN_COMMAND(101)の3種の権限を管理。 |
| `trn_division` | 部署マスタ。部署名・説明・表示順序を保持。 |
| `trn_division_user` | 部署とユーザーの中間テーブル（多対多）。 |
| `trn_project` | プロジェクトマスタ。プロジェクト名・説明・表示順序を保持。 |
| `trn_project_user` | プロジェクトとユーザーの中間テーブル（多対多）。 |
| `trn_project_notification` | プロジェクト通知設定。通知タイプ（例: Slackチャンネル）と通知先値を保持。 |
| `trn_attendance_state` | 出退勤ステータス履歴。出勤(オフィス/テレワーク)・休憩開始/終了・勤務場所切替・退勤を記録。 |
| `trn_user_project_priority` | ユーザーごとのプロジェクト表示優先度。 |
| `trn_user_division_priority` | ユーザーごとの部署表示優先度。 |
| `trn_user_reward` | ユーザーのリワード達成・受取履歴。達成回数/受取回数/日時を記録。 |
| `trn_batch_release_history` | バッチ処理のリリース履歴。バッチ識別子(`batch_identify`, unique)と実行回数を管理。 |
| `trn_shop_aggregate` | ショップの購入集計。どのユーザーがどの商品を購入したかを記録。 |
| `trn_shuffle_lunch_entry` | シャッフルランチのエントリー。ユーザー・イベント日・時間帯を記録。 |
| `trn_shuffle_lunch_group` | シャッフルランチのグループ割り当て。日付・時間帯・グループID・ユーザー・rakumoブロック状態を記録。 |
| `trn_good_job` | GoodJob（称賛）機能。作成者・発信元(ユーザー/部署/プロジェクト/ラベル)・発信先(同)・タイトル・内容を記録。 |
| `trn_seat` | 座席管理。ラベル・座標(x,y)・電話番号を保持。 |

---

## 2. 設計上の特徴や注意点

### 2.1. 論理削除パターン（`e_archive_level`）

全テーブルに `e_archive_level` カラムが存在し、Laravelの `SoftDeletes` ではなく独自の論理削除を採用している。

| 値 | 定数 | 意味 |
|---|---|---|
| 0 | `INVALID` | 無効値 |
| 1 | `ALIVE` | 生存（有効） |
| 2 | `ARCHIVE` | アーカイブ |
| 3 | `DELETE` | 削除設定 |

`EloquentBuilder` を拡張して `alive()` スコープと `archive()` メソッドを提供している（`app/Models/App/EloquentBuilder.php`）。全モデルのリレーション定義で `->alive()` を付与し、アーカイブ済みレコードを自動的に除外する。

**注意点**: `ARCHIVE(2)` と `DELETE(3)` の2段階があるため、単純な論理削除と異なり「アーカイブ（非表示だが復帰可能）」と「削除予定」を区別する運用が想定される。

### 2.2. DB外部キー制約なし（アプリケーション層での関連管理）

全マイグレーションでFK列に `integer` 型を使用しており、`foreignId()` や `$table->foreign()` による **DB レベルの外部キー制約は一切設定されていない**。

リレーションはすべてEloquentモデルの `HasOne` / `HasMany` で定義されており、整合性はアプリケーション層で担保する設計。

**注意点**: DB側でカスケード削除や参照整合性チェックが行われないため、データ不整合が発生する可能性がある。`e_archive_level` による論理削除と組み合わせることで、物理削除を避ける運用でカバーしている形。

### 2.3. インデックス設定

明示的にインデックスが設定されているのは以下のみ。

| テーブル | カラム | 種別 | 意図 |
|---|---|---|---|
| `auth_user` | `email` | UNIQUE | メールアドレスでのログイン一意性保証 |
| `trn_user` | `auth_id` | INDEX | `auth_user` との結合高速化 |
| `trn_user_authority` | `trn_user_id` | INDEX | ユーザー権限の頻繁な参照に対応 |
| `trn_shuffle_lunch_entry` | `event_date` | INDEX | 日付での検索・フィルタ高速化 |
| `trn_shuffle_lunch_group` | `event_date` | INDEX | 同上 |
| `trn_batch_release_history` | `batch_identify` | UNIQUE | バッチ識別子の一意性保証 |

**注意点**: 中間テーブル（`trn_division_user`, `trn_project_user` 等）のFK列にインデックスがないため、部署・プロジェクトのメンバー一覧取得時にフルスキャンが発生する可能性がある。データ量が増えた場合はインデックス追加を検討すべき。

### 2.4. 認証の分離設計（`auth_user` と `trn_user` の分離）

認証情報（`auth_user`: Laravelの `Authenticatable` を継承）とアプリユーザー情報（`trn_user`: `ModelBase` を継承）を明確に分離している。

- `auth_user`: パスワード、GoogleID、OTP、SPログイン情報など認証に特化
- `trn_user`: ニックネーム、顔画像、ゴールドなどアプリケーション固有情報

1:1で `auth_id` で紐づけることで、認証方式の変更がアプリユーザー情報に影響しない設計。

### 2.5. 複数認証方式のサポート

`auth_user` テーブルは3つの認証方式に対応。

| 方式 | 関連カラム |
|---|---|
| Google OAuth | `google_id` |
| レガシーログイン | `e_enable_legacy_login`, `password`, `onetime_password`, `onetime_password_expired` |
| SPログイン（2FA対応） | `e_enable_sp_login`, `sp_password`, `sp_google_2fa_secret`, `sp_login_failed_count` |

各方式は `EEnableFlag`（0:不許可, 1:許可）で個別に有効化/無効化できる。

### 2.6. 命名規則

| 接頭辞 | 用途 | 例 |
|---|---|---|
| `auth_` | 認証系テーブル | `auth_user` |
| `mst_` | マスタテーブル | `mst_reward`, `mst_goods` |
| `trn_` | トランザクションテーブル | `trn_user`, `trn_project` |
| `e_` | Enum値カラム | `e_archive_level`, `e_attendance_state` |

モデルクラスもテーブル名に対応（`TrnUser`, `MstReward`, `AuthUser`）。

### 2.7. `toPayload()` パターン（GraphQL/API向けシリアライズ）

全モデルに `toPayload(array $with, array $history)` メソッドが実装されており、GraphQL等でのデータ受け渡しに使用されている。`$with` パラメータで必要なリレーションを制御し、`$history` で循環参照を防止する仕組み。

### 2.8. GoodJob テーブルの多方向参照設計

`trn_good_job` は `from_target_type` / `to_target_type`（`ETargetType`: USER=1, DIVISION=2, PROJECT=3, LABEL=4）で発信元/先の種別を指定し、対応するID列（`from_trn_user_id`, `from_trn_division_id` 等）を参照するポリモーフィック風の設計。

**注意点**: `target_type` に応じて使用されるFK列が変わるため、未使用のFK列にはダミー値が入る可能性があり、通常のJOINでは意図しないデータが取得されるリスクがある。

### 2.9. ゲーミフィケーションシステム（ゴールド通貨）

`trn_user` に `current_gold`（現在所持）と `accumulation_gold`（累計獲得）の2つのゴールドカラムがあり、`mst_reward`（報酬）→ `trn_user_reward`（達成/受取履歴）→ `mst_goods`（商品）→ `trn_shop_aggregate`（購入履歴）というゲーミフィケーションの流れが構築されている。
