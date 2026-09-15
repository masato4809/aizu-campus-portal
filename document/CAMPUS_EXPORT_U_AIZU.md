# 会津大学DBのCSV出力

ImportやDB構造を変更せず、保存済みDBを読み取る独立コマンドです。HTTPアクセス・DB更新は行いません。

## 実行

コンテナ内の `wportal2-app` で:

```sh
php artisan campus:export-aizu-csv --year=2026
```

Macのターミナルから:

```sh
docker exec -w /var/www/html/wportal2-app wportal2_web php artisan campus:export-aizu-csv --year=2026
```

Mac側の保存先は `wportal2-app/storage/app/campus-export/2026/`、コンテナ側は `/var/www/html/wportal2-app/storage/app/campus-export/2026/` です。再実行は同名ファイルを置き換えます。CSVはGit除外です。年は1900〜9999の整数を受け付け、該当授業がなければヘッダーのみ出力します。

## 出力とカラム

UTF-8 BOM付き、CRLF改行、1行目ヘッダー。PHP標準 `fputcsv` で引用符・カンマ・改行をエスケープします。nullは空欄。メールを含め値を推測・補完しません。

| CSV | カラム（左から順） |
| --- | --- |
| courses.csv | course_id, course_code, course_name, course_name_en, academic_year, semester, syllabus_url, day, period, credits, teaching_mode, classroom, description, offerings_json |
| teachers.csv | teacher_id, external_teacher_id, name, name_en, email, department, position, research_field, laboratory_name, profile_url, laboratory_url, personal_url |
| course_teachers.csv | course_id, course_code, course_name, teacher_id, teacher_name, teacher_name_en, email, role |
| aizu_catalog_2026.csv | course_code, course_name, academic_year, semester, teacher_name, teacher_name_en, email, role, syllabus_url, profile_url |
| aizu_import_issues_2026.csv | issue_type, course_code, course_name, teacher_name, details |

科目コード順、科目内は教員名・教員ID・役割順。教員CSVは氏名・ID順です。1人がinstructorとcoordinatorを兼ねる場合は役割ごとに出力します。これは中間テーブルの異なる関係であり重複ではありません。

確認用メインCSVは担当教員がいない授業も教員欄を空にして1行残します。そのため関係CSVより行数が多くなります。

## 取得元

- `campus_courses`: 大学が会津大学かつ指定年度の授業。`name`→course_name、`name_en`→course_name_en。曜日・時限・単位・形態・教室は `offerings` JSON内の各値を重複除去し ` / ` 区切りで表示。クラスと値の厳密な対応は offerings_json に保存された元データを確認してください。
- `campus_teachers`: 会津大学の全教員。教員テーブルに年度がないため、指定年度への紐付けがない教員も含みます。
- `campus_course_teachers`: 対象年度の授業IDに紐付く関係。授業・教員の表示情報は上記2テーブルから結合します。

DBの実データ行を削除・統合しません。一意制約を持つ授業ID・教員ID・役割ごとの関係を一度ずつ出力します。異なるIDの重複疑いは問題一覧で報告します。

## 問題一覧

担当者0人、シラバスURLなし、公開メールなし、プロフィールURLなし、科目コード重複、教員の重複疑い、無効な教員関係を検査します。

`offerings` に残る元の教員名を、既存ImportのNameMatcherと現在のDBで読み取り専用照合します。未照合は unmatched_teacher、一致しても関係行がなければ missing_teacher_relation。過去ログを混ぜないため、過去のImport失敗全件を再現する一覧ではありません。重複候補は確認の手がかりであり、同一人物との断定や自動統合はしません。問題一覧の同一内容は1回だけ出力します。

## 今回の実出力

- courses.csv: 279行
- teachers.csv: 92行
- course_teachers.csv: 811行
- aizu_catalog_2026.csv: 823行（関係811行＋教員関係なし12授業）
- aizu_import_issues_2026.csv: 304行

すべてヘッダーを除く件数です。今後のDB更新により変わります。

## 追加・変更ファイル

- `wportal2-app/app/Console/Commands/ExportAizuCsv.php`: Artisan引数・実行・件数/保存先表示。
- `wportal2-app/app/Campus/Export/AizuCsvExporter.php`: 3テーブルの取得、結合、品質確認、CSV保存。ファイルごとに一時ファイルを書いてから置換し、書き込み失敗を通知します。
- `wportal2-app/tests/Feature/Campus/AizuExportTest.php`: UTF-8 BOM、引用符/改行/カンマ、null、年度/大学絞り込み、複数教員、未照合、再出力、空結果を検証。
- `wportal2-app/.gitignore`: 出力CSVの除外。
- この手順書と `README_CAMPUS.md`: 実行手順へのリンク。

```sh
docker exec -w /var/www/html/wportal2-app wportal2_web php artisan test --compact tests/Feature/Campus
```
