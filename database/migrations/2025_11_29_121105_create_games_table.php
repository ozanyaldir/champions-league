<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fixture_id');
            $table->unsignedInteger('home_goals')->default(0);
            $table->unsignedInteger('away_goals')->default(0);
            $table->timestamps();

            $table->foreign('fixture_id')
                  ->references('id')
                  ->on('fixtures')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
