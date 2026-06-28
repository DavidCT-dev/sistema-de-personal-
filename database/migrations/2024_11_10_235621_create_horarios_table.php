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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');          // Descripción del horario o actividad
            $table->time('tolerancia')->default('00:00:00'); // Tolerancia permitida para el horario
            $table->time('ingreso1');   // Hora de primer ingreso
            $table->time('salida1');    // Hora de primera salida
            $table->time('ingreso2')->nullable();   // Hora de segundo ingreso
            $table->time('salida2')->nullable();    // Hora de segunda salida
            $table->text('observaciones')->nullable(); // Observaciones adicionales
            $table->timestamps();
            $table->softDeletes();  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
