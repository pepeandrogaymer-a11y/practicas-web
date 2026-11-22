<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion');
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->decimal('precio', 10, 2);
            $table->unsignedInteger('existencias');
            $table->string('estado'); // Disponible / Agotado
            $table->timestamps();     // created_at = fecha de registro
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
