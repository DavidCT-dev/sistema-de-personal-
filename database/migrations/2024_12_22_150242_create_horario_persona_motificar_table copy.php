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
        Schema::create('asignacion_horario_persona', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_persona')->nullable();
            $table->foreign('id_persona')->references('id')->on('personas');

            $table->unsignedBigInteger('id_horario')->nullable();
            $table->foreign('id_horario')->references('id')->on('horarios');

            // Campos solicitados
            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_horario_persona');
    }
};
