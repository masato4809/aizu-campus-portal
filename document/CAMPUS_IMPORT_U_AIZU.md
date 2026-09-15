# 会津大学・2026年度公式授業データの取り込み

既存のCampus授業・口コミDBを拡張するバックエンド機能です。フロント画面の変更や追加パッケージはありません。

## 実行方法

Macのターミナルで、リポジトリ直下から実行します。Dockerが起動している必要があります。

```sh
cd /Users/imamuramasato/Downloads/wportal2
docker exec -w /var/www/html/wportal2-app wportal2_web php artisan migrate
docker exec -w /var/www/html/wportal2-app wportal2_web php artisan campus:import-u-aizu --year=2026
```

コンテナ内の `/var/www/html/wportal2-app` では `php artisan campus:import-u-aizu --year=2026` だけで実行できます。Gitで共有されるのはコードであり、DBの中身ではありません。他のメンバーもmigrationとimportを実行してください。

- `--dry-run`: DBへ書き込まず取得・解析・名寄せを確認（キャッシュとレポートは作成）。
- `--limit=5`: 科目コード5件で試行。教員一覧は全件確認。
- `--refresh`: 24時間のHTTPキャッシュを無視して再取得。
- `--delay=1000`: リクエスト間隔をミリ秒で指定。既定750、下限250。
- 2026以外の年度はエラーにします。

HTTPはタイムアウト・限定的な再試行・公式ドメインのみのリダイレクト追跡に対応。同じURLは実行中に再取得せず、失敗も記憶します。キャッシュは `storage/app/u-aizu-cache/`、レポートは `storage/logs/u-aizu-import-*.jsonl` です。これらをGitに追加する必要はありません。

## 情報源

学部・大学院の日本語と英語の一覧から詳細ページへ進みます。

- [2026学部・日本語](https://web-ext.u-aizu.ac.jp/official/curriculum/syllabus/2026_1_J_000.html)
- [2026学部・英語](https://web-ext.u-aizu.ac.jp/official/curriculum/syllabus/2026_1_E_000.html)
- [2026大学院・日本語](https://web-ext.u-aizu.ac.jp/official/curriculum/syllabus/2026_2_J_000.html)
- [2026大学院・英語](https://web-ext.u-aizu.ac.jp/official/curriculum/syllabus/2026_2_E_000.html)
- [公式教員一覧](https://u-aizu.ac.jp/intro/faculty/cse/)
- [教員プロフィールの例](https://u-aizu.ac.jp/research/faculty/detail?cd=90137)

教員一覧は取得時点のものです。2026年度当時の全担当教員の在籍を保証する資料ではありません。非常勤講師など一覧にいない担当者は、シラバスの表記を保存して未照合として報告します。外部サイトから補完しません。メールは公式プロフィールで公開されている値のみ保存し、推測しません。

## DBと互換性

- `campus_courses` を拡張: `academic_year`, `course_code`, `name_en`, `semester`, `syllabus_url`, `description`, `offerings`, `source_fetched_at`, `legacy_key`。
- 公式授業の一意キーは大学＋年度＋科目コード。既存の `name` と `professor` は画面互換のため維持します。
- 同一コードの複数クラス・日本語/英語ページは `offerings` JSONに保存。各項目にURL、セクションID、学期、担当者、取得できた単位・曜日・時限・教室・形態などを残します。代表値は日本語を優先します。
- `campus_teachers`: 公式ID、日英氏名、公開メール、所属、職位、研究分野、研究室、プロフィール・個人ページURL、別名、取得日時。未掲載項目はnullです。
- `campus_course_teachers`: 授業と教員の多対多。`role` はinstructor/coordinator/responsible、`match_method` は照合方法。授業＋教員＋役割が一意です。
- 従来の手入力授業は `legacy_key` で重複防止。既存授業を公式データに移行するのは、名前・担当者が一致して候補が一つで、別年度の口コミがない場合に限ります。口コミの授業IDを維持します。
- 更新時の取得失敗・未照合があれば、既存の担当者関係を安易に削除しません。
- `source_fetched_at` は取り込み時刻です。キャッシュ使用時は元のHTTP取得時刻とは異なります。

追加migrationはデータ保護のためrollback不可です。適用前のバックアップを利用するか、変更用の新規migrationを追加してください。初期セットアップ時の重複カラムを避けるため、010000は旧詳細項目、020000は新投稿仕様を担当します。

## 教員の照合

公式ID → 英語名の正規化完全一致 → 姓名順の入れ替え → 空白・大小文字・句読点等の正規化 → 日本語名/公式別名、の順です。同順位に候補が複数ある場合、未知の公式IDが指定された場合、略称しか一致しない場合は結合しません。編集距離による推測はしません。

未照合でも授業は保存し、元の担当者名は `offerings` に残します。レポートの `Teachers matched/unmatched` は日英ページ・役割ごとの担当者表記の件数であり、実人数ではありません。`Course-teacher relations` は対象科目の保存済み関係数です。

## フロント担当への受け渡し

`CourseCatalog` は科目コード・英語授業名・紐付いた教員の日英名でも検索できます。返却する授業には既存項目に加え `teachers` が付き、各教員に `role` を含みます。

投稿では `course_id` を渡すと公式科目を確実に指定できます。この場合 `course` / `professor` は省略可能です。所属大学と履修年度の一致をバックエンドで確認します。既存の授業名・教授名入力も利用できますが、同名科目で特定できない場合は入力エラーにします。フロントの授業選択UIは今後このIDを送る形にしてください。

## 主な実装ファイル

| ファイル（wportal2-app配下） | 役割 |
| --- | --- |
| app/Console/Commands/ImportAizuCatalog.php | Artisanの引数・ロック・結果表示 |
| app/Campus/Import/AizuImporter.php | 一覧取得からDB保存までの制御 |
| app/Campus/Import/OfficialHttp.php、OfficialUrl.php | 通信・間隔・キャッシュ・URL制限 |
| app/Campus/Import/SyllabusParser.php、TeacherParser.php、Html.php | 公式HTML解析 |
| app/Campus/Import/NameMatcher.php | 教員の安全な名寄せ |
| app/Campus/Import/ImportStore.php、ImportReport.php | 再実行可能な保存とJSONLログ |
| database/migrations/2026_09_14_000000_add_official_campus_catalog.php | 既存授業拡張・教員/中間テーブル |
| app/Campus/CourseIdentity.php、ReviewWriter.php | 投稿先科目の特定・既存投稿との接続 |
| tests/Feature/Campus/AizuImportTest.php | HTTPを偽装した解析・保存・失敗系テスト |

## 結果とエラーの扱い

今回の初回実行では授業279件、教員92件、関係811件を保存しました。教員一覧の93件中1件はプロフィール本文を取得できずエラーになりました。未照合の担当者表記は277件でした。件数は公式サイトの更新で変わり得ます。

エラーがあっても他の対象を処理します。終了コード0はエラーなし、1は一部失敗・引数不正・全授業取得失敗などを示します。終了コード1でも成功分は保存済みです。JSONLの `error`、`unmatched_teacher`、`missing_field` を確認し、必要に応じて再実行してください。メールなど任意項目の欠落はインポート全体の失敗にはしません。

## テスト

```sh
docker exec -w /var/www/html/wportal2-app wportal2_web php artisan test --compact tests/Feature/Campus
```

授業コード・名前・複数担当者、壊れたHTMLの科目境界、プロフィールメール、名前の順序/全半角/曖昧性、再実行、口コミID維持、複数クラス、dry-run、年度制限、HTTP失敗、外部リダイレクト拒否を検証します。テストはインメモリSQLiteとHTTPの偽装を使い、実DBや公式サイトを変更しません。
