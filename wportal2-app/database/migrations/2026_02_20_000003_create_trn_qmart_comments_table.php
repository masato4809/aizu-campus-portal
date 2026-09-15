<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'trn_qmart_comments';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qmart_item_id')->comment('紐づくアイテムID');
            $table->unsignedBigInteger('user_id')->comment('コメント投稿者ユーザID');
            $table->text('comment')->comment('コメント本文');
            $table->timestamp('created_at')->nullable();

            $table->foreign('qmart_item_id')->references('id')->on('trn_qmart_items');
            $table->foreign('user_id')->references('id')->on('trn_user');
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
