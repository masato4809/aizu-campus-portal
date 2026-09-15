<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'trn_qmart_items';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('出品者ユーザID');
            $table->string('title')->comment('タイトル');
            $table->text('description')->comment('説明文');
            $table->integer('price')->default(0)->comment('価格（0=無料譲渡）');
            $table->integer('status')->default(1)->comment('ステータス（EQmartItemStatus: 1=Selling）');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('trn_user');
            $table->index('status');
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
