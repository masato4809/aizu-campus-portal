# 会津大学版：授業投稿DB・バックエンド仕様

この仕様が従来の投稿仕様を置き換えます。対象はバックエンドです。フォーム・大学名の画面表示・新しい投稿詳細表示はフロント担当が別途更新します。

## 変更したファイル

| ファイル（wportal2-app/から） | 役割 |
| --- | --- |
| `database/migrations/2026_09_13_020000_update_campus_review_specification.php` | 新項目・5評価追加、既存ユーザネームの補完、既存学生・授業の大学名変更 |
| `app/Campus/CampusOptions.php` | 大学名、ユーザ属性・評価方式・学部学科・5評価等の選択肢 |
| `app/Http/Requests/Campus/StoreReviewRequest.php` | 必須・選択肢・500字・URL・1〜5の入力チェック |
| `app/Campus/ReviewWriter.php` | 名前と投稿時刻をサーバーで記録し、評価方式をJSON保存 |
| `app/Campus/CourseCatalog.php` | 新評価の平均・有効評価件数を取得し、評価方式JSONを配列に変換 |
| `app/Http/Controllers/Campus/ProfileController.php` | 登録時の大学を会津大学に固定 |
| `app/Http/Controllers/Campus/PageController.php` | フロントへ各選択肢を渡す |
| `tests/Feature/Campus/ReviewTest.php` | 新投稿・更新・境界値・作者保護・集計の検証 |
| `tests/Feature/Campus/ReviewMigrationTest.php` | 既存データが保持され、大学名が変わることの検証 |

## 保存先と項目

保存先はMySQLの`campus_reviews`です。授業名・教授名は引き続き`campus_courses`に保存します。

| 入力name / DBカラム | 型 | 入力・意味 |
| --- | --- | --- |
| `course` → courses.name | VARCHAR(120) | 必須、授業名 |
| `professor` → courses.professor | VARCHAR(80) | 必須、教授名 |
| `student_id` | 外部キー | ログイン中の学生。リクエストからの指定は無視 |
| `username` | VARCHAR(80) | 保存時点のプロフィール名をサーバーで記録。リクエストからの指定は無視 |
| `user_type` | VARCHAR(30) | 必須、`cost_performance`：コスパタイプ／`gpa`：GPA必要タイプ／`learning`：学問をしっかりしたいタイプ |
| `has_past_exam` | BOOLEAN | 必須、1：あり／0：なし |
| `faculty` | VARCHAR(120) | 必須、選択した学部 |
| `department` | VARCHAR(120) | 必須、その学部に属する学科 |
| `evaluation_methods` | JSON | 必須、配列で1〜3項目。`test`：テスト／`report`：レポート／`attendance`：出席。重複不可 |
| `assignment_load` | VARCHAR(10) | 必須、`many`：多め／`few`：少なめ／`none`：なし |
| `remote_level` | VARCHAR(10) | 必須、`many`：多め／`few`：少なめ／`none`：なし |
| `materials` | TEXT | 必須、教材名等2,000字以内。不要なら「なし」 |
| `materials_url` | VARCHAR(2048) | 任意、http/httpsのURLのみ。空欄はnull |
| `class_size` | VARCHAR(10) | 必須、`large`：大規模／`medium`：中規模／`small`：小規模 |
| `body` | TEXT | 必須、授業コメント500字以内。サーバー側で文字数制限 |
| `academic_year` | UNSIGNED SMALLINT | 必須、1900〜サーバーの現在年の整数 |
| `semester` | VARCHAR(10) | 必須、`first`：前期／`second`：後期 |
| `credit_ease` | UNSIGNED TINYINT | 必須、1〜5：単位の取りやすさ |
| `grade_ease` | UNSIGNED TINYINT | 必須、1〜5：高成績の取りやすさ |
| `assignment_lightness` | UNSIGNED TINYINT | 必須、1〜5：課題の少なさ |
| `satisfaction` | UNSIGNED TINYINT | 必須、1〜5：満足度 |
| `past_exam_similarity` | UNSIGNED TINYINT | 必須、1〜5：過去問の出題傾向 |
| `created_at` | TIMESTAMP | 初回投稿日時をサーバーで自動記録。再投稿しても維持 |
| `updated_at` | TIMESTAMP | 最終保存日時をサーバーで自動記録 |

ユーザ属性はプロフィールの性格タイプとは別に、**投稿ごとに1つ**選びます。評価方式は**複数選択**です。履修人数・課題量・リモート割合は区分であり、具体的な人数や%には自動変換しません。

学部学科の初期選択肢はコンピュータ理工学部 → コンピュータ理工学科です。[会津大学公式の履修案内](https://u-aizu.ac.jp/curriculum/undergraduate/guide/)を参照。選択肢追加時は`CampusOptions::DEPARTMENTS`を変更してください。

「過去問の出題傾向」の1〜5の説明文はフロントで明示してください。例：1は類似する出題が少ない、5は多い。現仕様は過去問なしでも5評価全て必須です。未確認・評価不可を設ける場合は、チームで決めて入力チェックも変更します。

## フロントとの受け渡し

URLは既存の`POST /campus/review`を維持します。HTMLフォームに`@csrf`を付けます。JSON APIではありません。成功時は`/campus`へリダイレクトし、失敗時は`$errors`・`old()`を使います。

評価方式のチェックボックスは`name="evaluation_methods[]"`、値は`test`等とします。過去問の「なし」は空欄でなく`0`を送ってください。

PageControllerから追加される変数：

- `userTypes`：ユーザ属性コード→表示名
- `evaluationMethods`：評価方式コード→表示名
- `levels`：多め・少なめ・なし
- `classSizes`：履修人数区分
- `semesters`：前期・後期
- `departments`：学部→学科配列
- `ratings`：新しい5評価キー→表示名（従来の5評価を置き換え）

口コミの`evaluation_methods`はDBではJSON、画面へ渡す時点では配列です。`username`がnullの旧データは`author`を代わりに表示できます。`author`は現在のプロフィール名、`username`は保存時の名前です。

授業の`averages`は新5評価の平均、`rating_counts`は各評価の有効件数です。未評価のキーは平均0・件数0を返します。フロントは件数0を「未評価」と表示し、0点の評価として描画しないでください。旧評価は新しい平均に混ぜません。

## データ移行

既存2マイグレーションが適用済みの環境で、次をMac側から実行します。

```bash
docker exec wportal2_web php artisan migrate --path=database/migrations/2026_09_13_020000_update_campus_review_specification.php
```

- 新カラムは旧投稿を残すためnullable。ただし新しい投稿では上記必須ルールを適用。
- 旧5評価・出席方法・旧課題量・旧リモート%はそのまま保持。新仕様には推測で変換しない。
- 旧500字超のコメントも切り捨てず保存。再投稿時から500字以内。
- 既存投稿のusernameを学生プロフィールから補完。
- 学生・授業の「金沢大学」を「会津大学」に更新し、IDと紐付けは保持。学部・学科の旧値は勝手に書き換えない。
- rollbackは新カラムを削除するため新投稿の追加情報が失われる。大学名と旧評価のnullable化は戻さない。通常は追加マイグレーションで修正する。

## 検証

```bash
docker exec wportal2_web php artisan test tests/Feature/Campus
docker exec wportal2_web vendor/bin/phpstan analyse app/Campus app/Http/Controllers/Campus app/Http/Requests/Campus tests/Feature/Campus database/migrations/2026_09_13_020000_update_campus_review_specification.php
```

## 統合時にフロント担当が行うこと

1. 表示の金沢大学・KANAZAWA UNIVERSITYを会津大学・UNIVERSITY OF AIZUへ変更。
2. 投稿フォームを上記項目・name・選択肢へ変更。従来フォームのままでは必須項目不足で保存できない。
3. 投稿詳細にusername・属性・過去問・評価方式・人数・履修時期・投稿時間を表示。
4. 教材URLをエスケープしてリンク表示（新規タブならrel="noopener noreferrer"）。
5. 新しい5軸チャートと未評価状態を表示。`rating_counts`を使用する。
6. DBマイグレーションとフロント変更を合わせて統合・動作確認する。

バックエンド担当のこの変更には、画面・CSSの編集は含めません。
