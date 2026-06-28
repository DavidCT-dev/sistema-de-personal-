<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LugarTrabajo;

class LugarTrabajoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lugares_trabajo = [
            ['descripcion' => 'HUMANISTICAS'],
            ['descripcion' => 'DECANATURA ENFERMERIA'],
            ['descripcion' => 'CONSULTORIA'],
            ['descripcion' => 'ADMINISTRATIVO'],
            ['descripcion' => 'TELEVISION UNIVERSITARIA'],
            ['descripcion' => 'VETERINARIA Y ZOOTECNIA - TUPIZA'],
            ['descripcion' => 'CMVZ'],
            ['descripcion' => 'MEDICINA'],
            ['descripcion' => 'FAC. TECNOLOGICA'],
            ['descripcion' => 'MINAS'],
            ['descripcion' => 'ARTES'],
            ['descripcion' => 'ENFERMERIA'],
            ['descripcion' => 'GRANJAS'],
        ];

        foreach ($lugares_trabajo as $lugar) {
            LugarTrabajo::create($lugar);
        }
    }
}
