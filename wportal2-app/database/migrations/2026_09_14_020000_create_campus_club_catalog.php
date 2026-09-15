<?php

declare(strict_types=1);

use App\Campus\CampusOptions;
use App\Campus\ClubCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('campus_club_catalog')) {
            Schema::create('campus_club_catalog', function (Blueprint $table) {
                $table->id();
                $table->string('university', 120);
                $table->string('name', 120);
                $table->string('category', 40);
                $table->text('tags')->nullable();
                $table->string('contact_url', 2048)->nullable();
                $table->string('website_url', 2048)->nullable();
                $table->text('source_url');
                $table->timestamps();
                $table->unique(['university', 'name'], 'campus_club_catalog_university_name_unique');
            });
        }

        $now = now();
        foreach ($this->officialClubs() as $club) {
            DB::table('campus_club_catalog')->updateOrInsert(
                ['university' => CampusOptions::UNIVERSITY, 'name' => $club['name']],
                [
                    'category'   => $club['category'],
                    'tags'       => json_encode($club['tags'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'source_url' => ClubCatalog::SOURCE_URL,
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_club_catalog');
    }

    /**
     * @return array<int, array{name: string, category: string, tags: array<int, string>}>
     */
    // @phpstan-ignore missingType.iterableValue
    private function officialClubs(): array
    {
        return [
            ['name' => 'Aizu Progressive xr Lab', 'category' => '文化サークル', 'tags' => ['技術']],
            ['name' => 'ARC', 'category' => '文化サークル', 'tags' => ['テーブルゲーム']],
            ['name' => 'CUO', 'category' => '文化サークル', 'tags' => ['音楽']],
            ['name' => 'DMC', 'category' => '文化サークル', 'tags' => ['音楽']],
            ['name' => 'REMs', 'category' => '文化サークル', 'tags' => ['技術']],
            ['name' => 'Zli', 'category' => '文化サークル', 'tags' => ['技術']],
            ['name' => 'アウトドアサークル', 'category' => '運動サークル', 'tags' => ['フィールドワーク']],
            ['name' => 'カンフー部', 'category' => '運動サークル', 'tags' => ['武術']],
            ['name' => 'サークル自治会執行部', 'category' => '自治機関', 'tags' => []],
            ['name' => 'サッカー部', 'category' => '運動サークル', 'tags' => ['球技']],
            ['name' => 'スコティッシュサークル', 'category' => '運動サークル', 'tags' => ['球技']],
            ['name' => 'ストリートダンスサークルO.M.G', 'category' => '運動サークル', 'tags' => ['ダンス']],
            ['name' => 'スノースポーツサークル', 'category' => '運動サークル', 'tags' => ['屋外スポーツ']],
            ['name' => 'ダーツサークル', 'category' => '文化サークル', 'tags' => []],
            ['name' => 'トライアスロン部', 'category' => '運動サークル', 'tags' => ['屋外スポーツ']],
            ['name' => 'バイクサークル', 'category' => '運動サークル', 'tags' => ['フィールドワーク']],
            ['name' => 'バドミントン部', 'category' => '運動サークル', 'tags' => ['球技', '屋内スポーツ']],
            ['name' => 'バレーボールサークル', 'category' => '運動サークル', 'tags' => ['球技']],
            ['name' => 'フォトサークル', 'category' => '文化サークル', 'tags' => ['フィールドワーク']],
            ['name' => 'フライングディスク部', 'category' => '運動サークル', 'tags' => ['球技']],
            ['name' => 'よさこい部', 'category' => '運動サークル', 'tags' => ['ダンス']],
            ['name' => '映画研究部', 'category' => '文化サークル', 'tags' => []],
            ['name' => '会津大学アニメ研究会', 'category' => '文化サークル', 'tags' => ['サブカル']],
            ['name' => '会津大学バスケットボール部', 'category' => '運動サークル', 'tags' => ['球技']],
            ['name' => '会津大学ポケモンサークル', 'category' => '文化サークル', 'tags' => ['サブカル']],
            ['name' => '会津大学学園祭実行委員会', 'category' => '自治機関', 'tags' => []],
            ['name' => '会津大学管弦楽団Dolce', 'category' => '文化サークル', 'tags' => ['音楽']],
            ['name' => '会津大学軽音学部', 'category' => '文化サークル', 'tags' => ['音楽']],
            ['name' => '会津大学吹奏楽団', 'category' => '文化サークル', 'tags' => ['音楽']],
            ['name' => '会津大学麻雀部', 'category' => '文化サークル', 'tags' => ['テーブルゲーム']],
            ['name' => '会津大学漫画研究部CCC', 'category' => '文化サークル', 'tags' => ['サブカル']],
            ['name' => '学生会執行部', 'category' => '自治機関', 'tags' => []],
            ['name' => '企画開発部', 'category' => '文化サークル', 'tags' => ['技術', 'サブカル']],
            ['name' => '競技プログラミング部(ICPC)', 'category' => '文化サークル', 'tags' => ['技術']],
            ['name' => '剣道部', 'category' => '運動サークル', 'tags' => ['武術']],
            ['name' => '硬式テニス部', 'category' => '運動サークル', 'tags' => ['球技']],
            ['name' => '水泳部', 'category' => '運動サークル', 'tags' => ['屋内スポーツ']],
            ['name' => '総合武道サークル', 'category' => '運動サークル', 'tags' => ['武術']],
            ['name' => '軟式テニス部', 'category' => '運動サークル', 'tags' => ['球技']],
        ];
    }
};
