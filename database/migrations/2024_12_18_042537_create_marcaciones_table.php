<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('marcaciones', function (Blueprint $table) {
            $table->id();
            $table->date('fecha'); // Usamos 'date' para almacenar la fecha (YYYY-MM-DD)
            $table->time('hora'); // Usamos 'time' para almacenar solo la hora (HH:MM:SS)
            $table->string('ci'); // 'ci' es un string, ya que puede ser alfanumérico
            $table->string('ip')->nullable(); // 'ip' es un string, adecuado para almacenar direcciones IP
            $table->string('estado')->nullable(); // 'ip' es un string, adecuado para almacenar direcciones IP
            $table->timestamps(); // Para los campos created_at y updated_at
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marcaciones');
    }
};
