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
        Schema::create('biometricos', function (Blueprint $table) {
            $table->id();

            $table->string('nombre_biometrico'); // Nombre del biometrico
            $table->string('ip_biometrico'); // Dirección IP del biometrico
            $table->string('nombre_base_de_datos'); // nombre de base de datos
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometricos');
    }
};
