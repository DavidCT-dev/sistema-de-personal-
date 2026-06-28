<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoContrato;

class TipoContratoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos_contrato = [
            ['descripcion' => 'PERMANENTE'],
            ['descripcion' => 'CONTRATO PLAZO FIJO'],
            ['descripcion' => 'EVENTUAL'],          
            ['descripcion' => 'BECA TRABAJO'],
            ['descripcion' => 'CONSULTOR'],
            ['descripcion' => 'AUXILIAR ARTES'],
        ];

        foreach ($tipos_contrato as $tipo) {
            TipoContrato::create($tipo);
        }
    }
}
