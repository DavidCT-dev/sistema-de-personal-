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
        Schema::create('feriados', function (Blueprint $table) {
            $table->id();

            // Campos solicitados
            $table->date('fecha'); // Fecha del feriado
            $table->string('descripcion'); // Descripción del feriado
            $table->text('observacion')->nullable(); // Observaciones adicionales
            $table->time('hora_inicio'); // Hora de inicio
            $table->time('hora_fin'); // Hora de fin
            // $table->string('tipo'); // Tipo de feriado
            $table->string('sexo'); // Género al que aplica

            $table->unsignedBigInteger('motivo_feriado_id')->nullable();
            $table->foreign('motivo_feriado_id')->references('id')->on('motivo_feriado');


            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feriados');
    }
};
