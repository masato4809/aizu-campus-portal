<?php

declare(strict_types=1);

use App\Enum\App\EArchiveLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const TABLE_NAME = 'mst_goods';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('商品名');
            $table->string('explain')->comment('商品説明');
            $table->string('comment')->nullable()->comment('コメント');
            $table->integer('category')->comment('種別');
            $table->string('image_path')->comment('画像パス');
            $table->string('author_email')->comment('著者メールアドレス');
            $table->integer('price')->default(0)->comment('価格');
            $table->integer('e_archive_level')->default(EArchiveLevel::ALIVE->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
