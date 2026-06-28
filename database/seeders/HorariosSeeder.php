<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Horario;

class HorariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $horarios = [
            [
                'descripcion' => 'HORARIO DE OFICINA',
                'tolerancia' => '00:11:00',
                'ingreso1' => '08:00:00',
                'salida1' => '12:00:00',
                'ingreso2' => '14:30:00',
                'salida2' => '18:30:00',
                'observaciones' => 'ADMINISTRATIVOS',
            ],
            [
                'descripcion' => 'HORARIO CONTINUO',
                'tolerancia' => '00:11:00',
                'ingreso1' => '08:00:00',
                'salida1' => '16:00:00',
                'ingreso2' => null,
                'salida2' => null,
                'observaciones' => '',
            ],
            [
                'descripcion' => 'PORTERO1',
                'tolerancia' => '00:11:00',
                'ingreso1' => '07:00:00',
                'salida1' => '19:00:00',
                'ingreso2' => null,
                'salida2' => null,
                'observaciones' => 'PORTEROS EN UN SOLO TURNO',
            ],
            [
                'descripcion' => 'PPORTERO2',
                'tolerancia' => '00:11:00',
                'ingreso1' => '07:00:00',
                'salida1' => '15:00:00',
                'ingreso2' => null,
                'salida2' => null,
                'observaciones' => 'PORTEROS EN UN SOLO TURNO',
            ],
            [
                'descripcion' => 'conserje1',
                'tolerancia' => '00:11:00',
                'ingreso1' => '08:00:00',
                'salida1' => '12:00:00',
                'ingreso2' => '14:00:00',
                'salida2' => '18:00:00',
                'observaciones' => 'conserJe FFCCPP, FISICA, QUIMICA',
            ],
            [
                'descripcion' => 'CONSERJE2',
                'tolerancia' => '00:11:00',
                'ingreso1' => '07:00:00',
                'salida1' => '11:00:00',
                'ingreso2' => '13:00:00',
                'salida2' => '17:00:00',
                'observaciones' => 'CONSERJE INFORMATICA',
            ],
            [
                'descripcion' => 'CONSERJE3',
                'tolerancia' => '00:11:00',
                'ingreso1' => '07:00:00',
                'salida1' => '15:00:00',
                'ingreso2' => null,
                'salida2' => null,
                'observaciones' => 'CONSERJES FAC ECONOMIA Y DERECHO',
            ],
            [
                'descripcion' => 'CONSERJE4',
                'tolerancia' => '00:11:00',
                'ingreso1' => '07:30:00',
                'salida1' => '11:30:00',
                'ingreso2' => '14:00:00',
                'salida2' => '18:00:00',
                'observaciones' => 'CONSERJE TOPOGRAFIA',
            ],
            [
                'descripcion' => 'CONSERJE 5',
                'tolerancia' => '00:11:00',
                'ingreso1' => '07:30:00',
                'salida1' => '14:00:00',
                'ingreso2' => '17:00:00',
                'salida2' => '18:30:00',
                'observaciones' => 'TECNOLOGICA VILLA ESPERANZA',
            ],
            [
                'descripcion' => 'CONSERJE6',
                'tolerancia' => '00:11:00',
                'ingreso1' => '07:00:00',
                'salida1' => '11:00:00',
                'ingreso2' => '14:00:00',
                'salida2' => '18:00:00',
                'observaciones' => 'ING COMERCIAL',
            ],
            [
                'descripcion' => 'CONSERJE7',
                'tolerancia' => '00:11:00',
                'ingreso1' => '07:00:00',
                'salida1' => '11:00:00',
                'ingreso2' => '16:00:00',
                'salida2' => '20:00:00',
                'observaciones' => 'ARQUITECTURA',
            ],
            [
                'descripcion' => 'JARDINERO',
                'tolerancia' => '00:11:00',
                'ingreso1' => '07:00:00',
                'salida1' => '15:00:00',
                'ingreso2' => null,
                'salida2' => null,
                'observaciones' => 'JARDINERO',
            ],
            [
                'descripcion' => 'HORARIO OFI SEDE',
                'tolerancia' => '00:11:00',
                'ingreso1' => '08:00:00',
                'salida1' => '12:00:00',
                'ingreso2' => '14:00:00',
                'salida2' => '18:00:00',
                'observaciones' => 'TUPIZA',
            ],
            [
                'descripcion' => 'CONSERGE8',
                'tolerancia' => '00:11:00',
                'ingreso1' => '15:00:00',
                'salida1' => '22:00:00',
                'ingreso2' => null,
                'salida2' => null,
                'observaciones' => 'CONSERJE TUPIZA AUDITORIA',
            ],
            [
                'descripcion' => 'chofer',
                'tolerancia' => '00:11:00',
                'ingreso1' => '08:00:00',
                'salida1' => '16:00:00',
                'ingreso2' => null,
                'salida2' => null,
                'observaciones' => 'chofer bautoridad',
            ],
            [
                'descripcion' => 'H CONTI',
                'tolerancia' => '00:11:00',
                'ingreso1' => '09:00:00',
                'salida1' => '17:00:00',
                'ingreso2' => null,
                'salida2' => null,
                'observaciones' => 'BIBLIOTECARIO',
            ],
            [
                'descripcion' => 'TEL UNIV',
                'tolerancia' => '00:11:00',
                'ingreso1' => '08:00:00',
                'salida1' => '12:00:00',
                'ingreso2' => '18:00:00',
                'salida2' => '22:00:00',
                'observaciones' => 'LELEVISION UNIV CAMARO',
            ],
            [
                'descripcion' => 'TEL UNIV2',
                'tolerancia' => '00:11:00',
                'ingreso1' => '08:00:00',
                'salida1' => '14:00:00',
                'ingreso2' => '18:00:00',
                'salida2' => '20:00:00',
                'observaciones' => 'TEL UNIV PRESENTADOR',
            ],
            [
                'descripcion' => 'CONSERJE8',
                'tolerancia' => '00:11:00',
                'ingreso1' => '08:00:00',
                'salida1' => '12:00:00',
                'ingreso2' => '13:30:00',
                'salida2' => '17:30:00',
                'observaciones' => 'conserje',
            ],
            [
                'descripcion' => 'conserJe 9',
                'tolerancia' => '00:11:00',
                'ingreso1' => '09:00:00',
                'salida1' => '13:00:00',
                'ingreso2' => '16:00:00',
                'salida2' => '21:00:00',
                'observaciones' => null,
            ],
            [
                'descripcion' => 'CONSERJE10',
                'tolerancia' => '00:11:00',
                'ingreso1' => '07:00:00',
                'salida1' => '11:00:00',
                'ingreso2' => '15:00:00',
                'salida2' => '19:00:00',
                'observaciones' => null,
            ],
            [
                'descripcion' => 'LIMPIEZA',
                'tolerancia' => '00:11:00',
                'ingreso1' => '08:00:00',
                'salida1' => '13:00:00',
                'ingreso2' => '18:00:00',
                'salida2' => '21:00:00',
                'observaciones' => 'LIMPIEZA Y PORTERIA BEATRIZ',
            ],
        ];

        foreach ($horarios as $horario) {
            Horario::create($horario);
        }
    }
}
