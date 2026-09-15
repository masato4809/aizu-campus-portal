# チーム共通の固定デモDB

現在の状態を `wportal2-app/database/seeders/data/campus-demo-snapshot.json` に固定しました。

- デモ学生150人（個人アカウント・パスワードは含めない）
- 授業口コミ230件
- 公式授業279件・教員92件・担当関係811件
- サークル39件（アップロード画像は含めない）
- デモ100人は所属85人・未所属15人、架空先輩20人は所属10人・未所属10人
- 架空先輩の口コミ100件中80件が過去問あり。1人3〜7件

## pull後の実行

```sh
git pull --ff-only origin main
docker exec -w /var/www/html/wportal2-app wportal2_web php artisan migrate
docker exec -w /var/www/html/wportal2-app wportal2_web php artisan db:seed --class=CampusSharedDemoSeeder
docker exec -w /var/www/html/wportal2-app wportal2_web php artisan view:clear
```

これだけで固定データを読み込みます。公式サイトの再取得は不要です。数値IDは各環境で異なって構いません。科目コード・教員source_key・デモメールで対応付けます。

再実行は固定状態への更新で、ランダム再抽選はしません。新規デモアカウントのパスワードだけは安全のため環境ごとにランダム生成します。既存デモのパスワードは保持します。

他の学生・口コミは削除しません。そのため他のデータがある環境では全体件数・平均が異なる場合があります。自分で修正した対象デモプロフィール・口コミ・対象公式情報は固定値へ戻ります。画像、実ユーザー、役に立った票、先生へのコメント、サークル投稿は同期対象外です。

**固定状態を保つ場合は、個別のランダムSeederや旧CampusSeniorDemoSeederを追加実行せず、CampusSharedDemoSeederだけを使ってください。** 過去のSeederは所属・過去問・投稿数を上書きするためです。

本機能はDB全体のバックアップではなく、共有するデモデータの固定セットです。
