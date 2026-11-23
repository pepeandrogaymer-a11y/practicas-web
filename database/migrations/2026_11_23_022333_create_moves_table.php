<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('posicion'); // 0 a 8
            $table->char('simbolo', 1);              // X u O
            $table->timestamps();

            // La base de datos también impide ocupar dos veces la misma casilla
            $table->unique(['game_id', 'posicion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moves');
    }
};