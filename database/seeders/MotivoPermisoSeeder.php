<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MotivoPermiso;

class MotivoPermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $motivos = [
            ['descripcion' => 'CUENTA VACACION'],
            ['descripcion' => 'CUENTA HABER'],
            ['descripcion' => 'VACACION'],          
            ['descripcion' => 'COMISION INTERNA'],
            ['descripcion' => 'COMISION CON VEATICOS'],
            ['descripcion' => 'BAJA MEDICA'],
            ['descripcion' => 'OTRO MOTIVO'],
            ['descripcion' => 'TOLERANCIA'],
            ['descripcion' => 'COMPENSACION']
        ];

        foreach ($motivos as $motivo) {
            MotivoPermiso::create($motivo);
        }
    }
}
