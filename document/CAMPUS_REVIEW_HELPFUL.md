# 授業口コミの「役に立った」

授業一覧と授業詳細の口コミに、役に立った件数と投票/取り消しボタンを表示します。ログイン中の学生が1口コミに1票を保存できます。自分の口コミへの投票は不可です。投票後は対象授業の口コミへ戻り、詳細画面では口コミを展開して表示します。

## DB

`campus_review_helpful_votes`: id, review_id, student_id, created_at, updated_at。

review_id＋student_idの一意制約で重複防止。口コミまたは学生の削除時は関連票も削除します。件数は保存行から取得し、投票数だけのカラムを別途更新する方式ではありません。口コミ本文が更新されても同じ口コミIDの票は維持します。

## API

`POST /campus/reviews/{review}/helpful` に helpful=1（投票）または0（取消）を送ります。学生IDはセッションから取得。同じ値を再送信しても結果は変わりません。ログイン・CSRF・大学の一致を確認し、既存Campusの投稿頻度制限を適用します。

JSONを要求した場合は course_id, helpful, helpful_count を返し、通常フォームは `/campus/courses/{course_id}#review-{review_id}` へリダイレクトします。

## ファイル（wportal2-app配下）

- database/migrations/2026_09_14_020000_create_campus_review_helpful_votes.php: 投票テーブル追加
- app/Http/Controllers/Campus/ReviewHelpfulController.php: 検証・投票/取消・結果返却
- app/Campus/CourseCatalog.php: helpful_countとhelpful_by_meを口コミに付与
- routes/campus.php: 投稿先ルート
- resources/views/campus/components/review-detail.blade.php: ボタン・件数・投票済み状態
- resources/views/campus/components/course-card.blade.php: 詳細画面の口コミを展開
- tests/Feature/Campus/ReviewHelpfulTest.php: 重複、取消、権限、表示、削除連動の検証
- tests/Feature/Campus/CampusTestCase.php: テストDBのmigration追加

## チームメンバーの適用

コードをpullした後、以下を実行してください。

```sh
docker exec -w /var/www/html/wportal2-app wportal2_web php artisan migrate
```
