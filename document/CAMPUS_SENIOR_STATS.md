# 先輩カードの投稿数・過去問所持数

## 表示されなかった原因

1. StudentMatchesの明示SELECTにcampus_students.past_exam_countがなかった。Bladeのdata_get(...,0)によりエラーにならず、12問でも0として表示されていた。
2. 投稿数・過去問所持数の表示がdetails内部（summaryより前）にあり、折りたたみを開かないと見えなかった。
3. 先輩は「同じ大学・ログインユーザーより上の学年」の学生のみ。スコアは順位付けであり、最低点による除外はない。

routes/campus.phpのGET /campus/matchesはPageController::indexへ進む。実際の変数名は$seniorsではなく$matchesで、ControllerとBladeは一致していた。ルートや変数名の不一致が原因ではない。

確認した実DB: 架空先輩20人、口コミ100件、2年7人・3年7人・4年6人。会津大学1年なら20人、2年なら13人、3年なら6人、4年以上なら0人が対象。他大学のログインでは対象外。

## 修正ファイル（wportal2-app配下）

- app/Campus/StudentMatches.php: past_exam_countをSELECTに追加。絞り込み、共通点の加点、並び順は維持。
- app/Http/Controllers/Campus/PageController.php: 既存$reviewsを一度groupBy('student_id')しreviewsByStudentとして渡す。
- resources/views/campus/pages/matches.blade.php: バッジをdetailsの外に配置。投稿数と展開後口コミは同じコレクションを使用。0問は「過去問なし」、正数は「過去問 N問所持」。既存tags/pillを再利用。授業へのリンクはIDを使用。
- tests/Feature/Campus/CampusTestCase.php: 既存past_exam_count migrationをテストDBにも適用。
- tests/Feature/Campus/StudentMatchesTest.php: 3/7投稿、0/12問、折りたたみ外表示、同大学/上級生の条件、スコア順、Query数を検証。

BladeでDBアクセスはしない。既存CourseCatalogの口コミをメモリ上で1回グループ化するため、学生ごとの追加Queryは発生しない。新しいmigrationやCSS、Seederの変更はない。

## 実DB表示例

- 架空先輩01（デモ）: 投稿 3件 / 過去問なし
- 架空先輩12（デモ）: 投稿 5件 / 過去問 12問所持
- 架空先輩20（デモ）: 投稿 7件 / 過去問 11問所持

Docker内でControllerとBladeの実DBレンダリングを実行して20枚のデモカードを確認。HTTP経由の表示と認証はFeatureテストで検証。ブラウザの実ログイン中アカウントの学年は特定していない。

## pull後

```sh
docker exec -w /var/www/html/wportal2-app wportal2_web php artisan migrate
docker exec -w /var/www/html/wportal2-app wportal2_web php artisan view:clear
docker exec -w /var/www/html/wportal2-app wportal2_web php artisan test --compact tests/Feature/Campus
```

migrationは既存のpast_exam_countカラムを未適用の環境へ反映するため。データも必要な別環境のみ、公式Import完了後に既存の `php artisan db:seed --class=CampusSeniorDemoSeeder` を実行。DBデータ自体はGitのpullでは共有されない。
