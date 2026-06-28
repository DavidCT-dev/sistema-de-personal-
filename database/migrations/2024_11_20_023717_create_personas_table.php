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
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellido_pat')->nullable();
            $table->string('apellido_mat')->nullable();
            $table->date('fecha_nac')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ci')->unique();
            $table->string('celular')->nullable();
            $table->date('fech_ing');
            $table->date('fech_baj')->nullable();
            $table->string('item')->nullable();
            $table->boolean('kardex_visible')->default(false);
            $table->boolean('auto_sabados')->default(false);
            $table->boolean('biometrico_registro')->default(false);
            $table->integer('antiguedad')->default(0);

            $table->decimal('total_dias_vacacion',5,1)->default(0); 


            $table->string('uid_bio')->nullable();

            // Foreign IDs con convención Laravel
            $table->foreignId('tipo_contrato_id')->nullable()->constrained('tipo_contratos');
            $table->foreignId('horario_id')->nullable()->constrained('horarios');
            $table->foreignId('lugar_trabajo_id')->nullable()->constrained('lugar_trabajos');
            $table->foreignId('cargo_id')->nullable()->constrained('cargos');
            $table->foreignId('genero_id')->nullable()->constrained('generos');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
