<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MotivoFeriado;

class MotivoFeriadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $motivos = [
            ['descripcion' => 'FERIADO'],
            ['descripcion' => 'PARO'],
            ['descripcion' => 'BLOQUEO'],          
            ['descripcion' => 'RECESO'],
            ['descripcion' => 'TOLERANCIA'],
            ['descripcion' => 'HORARIO CONTINUO'],

        ];

        foreach ($motivos as $motivo) {
            MotivoFeriado::create($motivo);
        }
    }
}
