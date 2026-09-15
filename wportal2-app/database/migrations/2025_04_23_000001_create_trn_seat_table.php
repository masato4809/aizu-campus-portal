<?php

declare(strict_types=1);

use App\Enum\App\EArchiveLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'trn_seat';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('position_x');
            $table->integer('position_y');
            $table->string('phone_number')->nullable();
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
