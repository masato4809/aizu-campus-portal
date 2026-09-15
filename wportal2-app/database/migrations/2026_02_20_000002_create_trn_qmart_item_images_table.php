<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'trn_qmart_item_images';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qmart_item_id')->comment('紐づくアイテムID');
            $table->string('file_path')->comment('画像ファイルパス');
            $table->integer('sort_order')->default(0)->comment('表示順');

            $table->foreign('qmart_item_id')->references('id')->on('trn_qmart_items');
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
