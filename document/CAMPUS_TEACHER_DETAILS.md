# 先生の詳細：DBとバックエンド

## データの保存先

| 内容 | 保存先/取得方法 |
| --- | --- |
| 教えている授業 | campus_course_teachersのrole=instructorからcampus_coursesへ。責任者のみの授業は含めない |
| 研究室 | campus_teachers.laboratory_name / laboratory_url。未取得はnull |
| どんな先生かのコメント | campus_teacher_reviews.body（学生投稿、必須、500字以内） |
| 先生評価 | campus_teacher_reviews.rating（整数1〜5）。全投稿の平均と件数を取得 |
| みんなの授業評価の平均 | 担当授業のcampus_reviewsから既存5項目をそれぞれ平均 |

新テーブルcampus_teacher_reviews: id, teacher_id, student_id, rating, body, created_at, updated_at。teacher_idとstudent_idは外部キーで、組み合わせが一意。同じ学生が同じ先生に再投稿すると更新します。投稿者IDはログインセッションから取得します。

平均値はDBに固定保存せず、取得時に集計します。未評価はnull、件数0。授業平均は全年度の担当授業に付いた全口コミを1投稿1票で集計し、小数点以下2桁に丸めます。複数の役割を持つ先生でも同じ口コミを二重計上しません。共同担当授業の口コミは各担当教員の授業平均に含まれ、個人への評価とは区別します。

## フロントへの受け渡し

ログイン必須。所属大学が異なる先生と存在しない先生は404。

`GET /campus/teachers/{teacher_id}` はJSONを返します。

- teacher: 既存教員情報
- courses: 担当授業（id, course_code, name, name_en, academic_year, semester, syllabus_url）
- laboratory: name, url
- comments: 20件ずつのLaravelページネーション。data内にid, rating, body, created_at, updated_at, author。次ページは `?comments_page=2`
- teacher_rating_average / teacher_review_count: 先生評価平均・投稿数
- course_review_averages: credit_ease, grade_ease, assignment_lightness, satisfaction, past_exam_similarityの平均
- course_review_rating_counts: 各項目の有効評価数
- course_review_count: 対象授業口コミ数

`POST /campus/teachers/{teacher_id}/reviews` に `rating` と `body` を送ります。既存Campusと同じセッション・CSRFトークンが必要です。成功時JSONメッセージを返します。JSONリクエストの入力エラーは422。画面で表示するコメントは通常のエスケープを使用してください。

今回はバックエンドのみです。既存の教授一覧画面は変更していません。

## 適用

```sh
docker exec -w /var/www/html/wportal2-app wportal2_web php artisan migrate
```

チームメンバーもコードをpullした後に実行してください。既存Import・CSV Export・授業/教員データを変更する必要はありません。

## ファイル

- wportal2-app/database/migrations/2026_09_14_010000_create_campus_teacher_reviews.php: 投稿テーブルの追加
- wportal2-app/app/Campus/TeacherDetails.php: 詳細と平均の取得
- wportal2-app/app/Http/Controllers/Campus/TeacherController.php: JSON取得・投稿検証・保存
- wportal2-app/routes/campus.php: 取得と投稿のルート追加
- wportal2-app/tests/Feature/Campus/TeacherDetailsTest.php: 集計、権限、入力チェック、再投稿のテスト
- wportal2-app/tests/Feature/Campus/CampusTestCase.php: テストDBに新テーブルを作成

## 画面からの利用

- `/campus/professors`: 公式DBの教員一覧。日本語名・英語名・研究室・研究分野で検索、24人ずつ表示。
- `/campus/professors/{teacher_id}`: 研究室、先生評価、全担当授業の口コミ平均、担当授業リンク、コメント一覧と投稿フォーム。
- `/campus/courses/{course_id}`: 対象の授業と口コミ、担当教員へのリンク、授業概要・公式シラバス。

教授詳細から担当授業へ、授業のカードから教員詳細へ移動できます。紐付いていない担当者は名前のみ表示し、別人へのリンクを推測しません。既存JSON APIは維持し、HTMLフォームで先生評価を送った場合は教授詳細へリダイレクトします。

画面の追加・変更ファイル:
- app/Http/Controllers/Campus/CatalogPageController.php: 教授/授業の詳細画面（大学境界を確認）
- app/Http/Controllers/Campus/PageController.php: DB教員一覧の検索・ページ分割
- resources/views/campus/pages/professors.blade.php: 教授一覧
- resources/views/campus/pages/professor-detail.blade.php: 教授詳細・投稿
- resources/views/campus/pages/course-detail.blade.php: 授業詳細
- resources/views/campus/components/course-card.blade.php: IDによる相互リンク

パスはwportal2-app配下です。追加のmigrationはありません。
