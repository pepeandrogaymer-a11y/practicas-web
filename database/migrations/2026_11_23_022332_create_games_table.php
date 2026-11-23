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
            // Si se elimina permanentemente un usuario, la partida se conserva con ese campo en NULL
            $table->foreignId('player_x_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('player_o_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('esperando'); // esperando / en_curso / terminada / cancelada
            $table->string('resultado')->nullable();        // victoria / empate / abandono / cancelada
            $table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};