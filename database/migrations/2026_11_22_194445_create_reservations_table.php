<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('computer_id')->constrained()->cascadeOnDelete();
            $table->string('estado')->default('activa'); // activa / cancelada
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps(); // created_at = fecha de la reservación
            $table->index(['computer_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
