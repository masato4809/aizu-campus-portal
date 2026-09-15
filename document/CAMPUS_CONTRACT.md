# Campus Link フロント・バックエンド受け渡し仕様

> **バックエンド仕様更新：** 対象大学を会津大学へ変更し、授業投稿のDB項目を更新しました。[最新のDB・受け渡し仕様](CAMPUS_REVIEW_DB.md)を参照してください。以下の従来の投稿項目・大学名の説明は旧仕様です。画面の新仕様対応はフロント担当との統合作業として残っています。

## 共通ルール

現在はBladeをサーバーで描画し、HTMLフォームをPOSTする構成です。JSON APIではありません。すべてのPOSTに `@csrf` が必要です。正常時はリダイレクトと `session('status')`、入力エラーはLaravel標準の `$errors` と `old('項目名')` を使用します。未認証の非公開画面・書き込みは `/campus/login` へ移動します。HTMLには `{{ }}` のエスケープ出力を使います。

フロント側でDBや保存サービスを呼び出さず、バックエンド側でHTMLやCSSを生成しないことを基本とします。

## 授業評価：POST /campus/review

「名前」はひとまず授業名と解釈。投稿者名はプロフィールから自動表示し、クライアントが作者を指定できないようにしています。教授検索のため教授名も維持しています。

| フォームのname | 意味・制約 |
| --- | --- |
| `course` | 授業名。必須、120文字以内 |
| `professor` | 教授名。必須、80文字以内 |
| `faculty` | 対象授業の学部・学域。必須、120文字以内 |
| `department` | 対象授業の学科・学類。必須、120文字以内 |
| `clarity` | わかりやすさ、整数1〜5 |
| `interest` | おもしろさ、整数1〜5 |
| `ease` | 単位の取りやすさ、整数1〜5 |
| `workload` | 負担の軽さ、整数1〜5（5が軽い） |
| `recommendation` | おすすめ度、整数1〜5 |
| `attendance_method` | 出席方法。必須、200文字以内。例：学生証、出席票、確認なし |
| `assignment_amount` | 課題の量。必須、整数1〜5。1ほとんどない／2少ない／3普通／4多い／5とても多い |
| `remote_percentage` | リモート割合。必須、整数0〜100。0が全対面、100が全リモート |
| `materials` | 教材。必須、2,000文字以内。不要なら「なし」 |
| `body` | 補足・口コミ。任意、2,000文字以内 |

`workload` と `assignment_amount` は別項目です。教材等の事実情報はチャートの軸に混ぜず、各口コミに表示します。チャートは5つの主観評価の平均を表示します。同じ大学・授業名・教授名が授業の識別キーで、同じ学生の再投稿は更新です。追加前の投稿の新項目はnull（未登録）で、0%とは区別します。

## 性格・学び方診断：POST /campus/diagnosis

`personality`は自己申告ではなく、この診断の回答から自動計算します。フォームの`personality`項目は廃止し、`/campus/register`・`/campus/profile`では受け付けません。

| フォームのname | 意味・制約 |
| --- | --- |
| `q1`〜`q9` | 性格診断の設問。各`a`または`b`、必須。設問文と選択肢は`PersonalityQuiz::QUESTIONS`が正 |
| `learning_style` | 学び方タイプ。`practical`・`theoretical`・`balanced`のいずれか、必須 |
| `goal_orientation` | 授業への目標。`high_grade`・`min_effort`・`balanced`のいずれか、必須 |

`q1`〜`q9`の回答から3軸（activity/group_size/challenge）それぞれの多数決でA/Bを判定し、3文字の`personality`コード（例：`AAA`）を保存します。コードと日本語ラベルの対応は`CampusOptions::TYPE_LABELS`です。正常時は`/campus/profile`へ移動します。

## 画面へ渡すデータ

全ページ：`page`（ページ名）、`me`（学生またはnull）、`clubOptions`（公式サークル一覧）、`ratings`（評価キー→日本語名）、`types`（性格タイプ一覧、8種類のコード）。

`me`：`id,name,university,faculty,year,circle,personality,learning_style,goal_orientation`。メールとパスワードは含めません。プロフィールのfacultyは従来の学域・学類自由入力であり、投稿のfaculty/departmentとは別です。

| ページ | 追加変数 |
| --- | --- |
| `courses` / `professors` | `courses`, `reviews` |
| `matches` | `courses`, `reviews`, `matches` |
| `clubs` | `clubs`, `clubTab` |
| `messages` | `contacts`, `peer`（未選択時null）, `messages` |
| `diagnosis` | `questions`（性格診断の設問配列）, `learningStyles`, `goalOrientations` |
| `profile` | `typeLabels`, `learningStyles`, `goalOrientations` |
| `login` / `register` | 共通変数のみ |

Laravel Collectionの各要素はオブジェクトです。

- course：`id,university,name,professor,created_at,updated_at,review_count,averages,reviews`。`averages` は5評価キーのfloat値。`reviews` はその授業の投稿Collection。`radar-chart.blade.php` は `course` と `ratings` を受け取り、平均の再計算はしません。
- review：`id,student_id,course_id,clarity,interest,ease,workload,recommendation,body,created_at,updated_at,faculty,department,attendance_method,assignment_amount,remote_percentage,materials,author,year,course_name`。新項目は旧投稿でnullになり得ます。
- match：`id,name,faculty,year,circle,personality,score,reasons`。scoreは整数0〜100、reasonsは共通点の文字列配列。
- club：`catalog_id,name,category,tags,contact_url,website_url,catalog_image_url,catalog_image_mime_type,catalog_image_width,catalog_image_height,member_count,review_count,diagnosed_count,matching_count,similarity,distribution,posts`。`posts` の各要素は `id,student_id,name,body,contact_url,website_url,created_at,updated_at,author,images`。
- contact / peer：`id,name,year`。
- message：`id,sender_id,recipient_id,body,created_at,updated_at`。バックエンドで送受信者に絞り込みます。

GET `/campus` または `/campus/courses` と `/campus/professors` は `q` で検索。GET `/campus/messages?peer=学生ID` で会話を選択します。

## その他のPOST

| URL | フォーム項目 |
| --- | --- |
| `/campus/login` | email, password |
| `/campus/register` | name（80文字）, email（255文字・一意）, password（10〜128文字）, password_confirmation, faculty（120文字）, year（1〜6）, circle（任意120文字） |
| `/campus/profile` | name, faculty, year, circle。大学はサーバーで会津大学固定。personality/learning_style/goal_orientationは`/campus/diagnosis`でのみ変更 |
| `/campus/logout` | CSRFのみ |
| `/campus/club` | name（公式一覧に存在する団体名）, body（必須10〜2,000文字） |
| `/campus/club-catalog/{catalog}`（PUT） | category, tags, contact_url, website_url, image（JPEG/PNG/WebP、5MiB以下）, password（編集用パスワード） |
| `/campus/message` | recipient_id（同大学・自分以外）, body（必須2,000文字以内） |

正常時の移動先：認証・プロフィール・レビューは `/campus`、logoutは `/campus/login`、clubは `/campus/clubs`、messageは `/campus/messages?peer=宛先ID`。既存POSTのURLは分割前から変更していません。
