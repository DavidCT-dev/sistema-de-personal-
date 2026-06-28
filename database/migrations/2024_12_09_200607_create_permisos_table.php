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
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_persona')->nullable();
            $table->foreign('id_persona')->references('id')->on('personas');

            $table->date('fecha_permiso');
            // $table->string('motivo');

            $table->unsignedBigInteger('motivo_permiso_id')->nullable();
            $table->foreign('motivo_permiso_id')->references('id')->on('motivo_permiso');


            $table->text('observacion')->nullable();
            $table->string('duracion_permiso'); // Duración: día completo, medio día, varios días
            $table->time('hora_inicio')->nullable(); // Hora de inicio (para medio día)
            $table->time('hora_fin')->nullable(); // Hora de fin (para medio día)
            $table->date('fecha_fin_varios_dias')->nullable(); // Fecha de fin para varios días
            $table->decimal('dias_permiso_total',5,1)->default(0)->unsigned(); // Número de días para varios días


            // Estado y aprobación
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'cancelado'])->default('pendiente');
            $table->timestamp('aprobado_en')->nullable();

              // Relación con la persona que solicita (empleado)
              $table->unsignedBigInteger('solicitante_id')->nullable();
              $table->foreign('solicitante_id')->references('id')->on('users');
                
            
                    // Relación con quien aprobó
            $table->unsignedBigInteger('aprobado_por_id')->nullable();
            $table->foreign('aprobado_por_id')->references('id')->on('users');

          


            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};
