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
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_persona')->nullable();
            $table->foreign('id_persona')->references('id')->on('personas');

            // Campos solicitados
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('total_dias',5,1)->default(0)->unsigned(); // Evita valores negativos

            $table->text('observacion')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'cancelado'])->default('pendiente');
            $table->timestamp('aprobado_en')->nullable();

            $table->unsignedBigInteger('solicitante_id')->nullable();
            $table->foreign('solicitante_id')->references('id')->on('users');


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
        Schema::dropIfExists('vacaciones');
    }
};
