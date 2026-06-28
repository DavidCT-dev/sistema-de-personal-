<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cargos;

class CargosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lista de descripciones de cargos
        $descriptions = [
            "CHOFER II",
            "LABORATORISTA II",
            "COORDINADORA PROGRAMAS POST GRADO",
            "RESPONSABLE ADM. CARRERAS UNCIA",
            "JEFE TESORO",
            "ASISTENTE ADMINISTRATIVO",
            "AUXILIAR BIBLIOTECA I",
            "ENCARGADO INGRESOS",
            "INVESTIGADOR I.B.B.A.",
            "TÉCNICO REVALORIZACIÓN DE BIENES",
            "BIBLIOTECARIO II",
            "ENCARGADO IMPRENTA",
            "RESPONSABLE DE PLANEAMIENTO ORGANICO",
            "SECRETARIA III",
            "TECNICO TITULOS A",
            "SERENO",
            "ENCARGADA ALMACENES",
            "AUXILIAR SERVICIOS I",
            "JEFE AUDITORIA INTERNA",
            "JEFE BIENES E INVENTARIOS",
            "AUXILIAR DE ADQUISICIONES",
            "ELECTRICISTA",
            "MECANICO",
            "INVENTARIADOR",
            "SECRETARIA II",
            "BIBLIOTECARIO I",
            "LABORATORISTA III",
            "ENCARGADO MEDIOS Y LABORATORIO",
            "AYUDANTE SERVICIOS",
            "JEFE DE FINANZAS",
            "LINOTIPISTA",
            "SECRETARIA IV",
            "AYUDANTE SERVICIOS",
            "AUXILIAR TITULOS",
            "BIBLIOTECARIA I",
            "SERENO LIMPIEZA BARTOLILLO",
            "SECRETARIO III",
            "ENCARGADO TITULOS",
            "CHOFER",
            "SERENO",
            "ASISTENTE SECRETARIA GENERAL",
            "CAMAROGRAFO PRENSA",
            "SERENO CIUDADELA",
            "TECNICO ASISTENTE",
            "ENCARGADA DE INGRESOS",
            "SECRETARIA IV RECTORADO",
            "CHOFER MENSAJERO",
            "PRENSISTA",
            "TÉCNICO ADMINISTRATIVO DE BIENES",
            "ALBAÑIL",
            "MENSAJERO",
            "TRABAJADORA SOCIAL",
            "BIOQUIMICA",
            "ENCARGADO FINCA OPLOCA TUPIZA",
            "AUXILIAR SERVICIOS TITULOS",
            "AYUDANTE SERVICIOS IMPRENTA",
            "AUXILIAR SERVICIOS VETERINARIA TUPIZA",
            "APOYO BIENESTAR UNIVERSITARIO",
            "APOYO FACULTAD INGENIERIA",
            "TECNICO PRESUPUESTOS",
            "ARCHIVERO",
            "ENCARGADO DE CAJA",
            "CAJERO",
            "CONSERJE AMBIENTES",
            "APOYO SEGURIDAD LIMPIEZA TUPIZA",
            "APOYO CARRERA VETERINARIA",
            "APOYO ELECTRICISTA",
            "TECNICO JUNIOR GRANJA PUNA",
            "ENCARGADO VENTANILLA UNICA",
            "SECRETARIA III",
            "APOYO PLANIFICACION",
            "BIBLIOTECARIO I",
            "AUXILIAR SERVICIOS I",
            "LABORATORISTA I",
            "PERSONAL DE APOYO SECRETARIA DAF",
            "ENC. SEGURIDAD Y LIMPIEZA",
            "AUXILIAR VICERRECTORADO",
            "APOYO D.A.F.",
            "APOYO PERSONAL DATA CENTER",
            "APOYO INFRAESTRUCTURA",
            "PERSONAL DE APOYO LIMPIEZA",
            "PORTERO-SERENO EDIF. CINE UNIVERSITARIO",
            "APOYO GRANJAS",
            "APOYO DIR. EVAL. Y ACREDITACION",
            "ASISTENTE ADMINISTRATIVO VET. TUPIZA",
            "APOYO DESAROLLO RURAL",
            "APOYO LIMPIEZA Y PORTERIA",
            "APOYO ASESORIA JURIDICA",
            "CHOFER-MENSAJERO",
            "APOYO TÉCNICO LAB. QUIMICO CIMA JICA",
            "RESPONSABLE ADM. CARRERA VILLAZON",
            "PERSONAL APOYO SALUD",
            "OBRERO GRANJAS UNIV.",
            "LABORATORISTA AUTAPO",
            "TECNICO INFORMATICO DPER.",
            "PERSONAL DE LIMPIEZA CONSERJERIA",
            "EMPASTADOR",
            "APOYO INFRAESTRUCTURA - CINE UNIV",
        ];

        // Insertar en la base de datos
        foreach ($descriptions as $description) {
            Cargos::create(['descripcion'=>$description]);
        }
    }
}
