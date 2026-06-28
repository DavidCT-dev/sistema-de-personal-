<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Persona;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
    
        // creacion de roles
        $adminRole = Role::create(['name' => 'Super Admin']);
        $userStandarRole = Role::create(['name' => 'Empleado']);
// $adminRole = Role::where('name', 'Super Admin')->first();


        // creacion de permisos
        Permission::create(['name' => 'menu'])->syncRoles([$adminRole]);
        Permission::create(['name' => 'solicitud'])->syncRoles([$adminRole,$userStandarRole]);
        Permission::create(['name' => 'app'])->syncRoles([$adminRole,$userStandarRole]);

        Permission::create(['name' => 'dashboard'])->syncRoles([$adminRole]);
        Permission::create(['name' => 'afiliaciones'])->syncRoles([$adminRole]);
        Permission::create(['name' => 'inasistencia'])->syncRoles([$adminRole]);
        Permission::create(['name' => 'evaluacion'])->syncRoles([$adminRole]);
        Permission::create(['name' => 'conexion'])->syncRoles([$adminRole]);
        Permission::create(['name' => 'administracion'])->syncRoles([$adminRole]);
        Permission::create(['name' => 'perfil'])->syncRoles([$adminRole, $userStandarRole]);
        Permission::create(['name' => 'usuarios'])->syncRoles([$adminRole]);
        Permission::create(['name' => 'notificaciones'])->syncRoles([$adminRole]);


        Permission::create(['name' => 'solicitar-vacacion'])->syncRoles([$adminRole, $userStandarRole]);
        Permission::create(['name' => 'solicitar-permiso'])->syncRoles([$adminRole, $userStandarRole]);

        $permisos = [
    'crear_empleado', 'editar_empleado', 'eliminar_empleado',
    'crear_horario', 'editar_horario', 'asignar_horario_empleado_semanal',
    'crear_feriado', 'crear_feriados_generales',
    'crear_permiso', 'editar_permiso', 'aceptar_rechazar_permiso',
    'generar_reporte_permiso', 'crear_vacacion', 'editar_vacacion',
    'generar_reporte_vacacion', 'aceptar_rechazar_vacacion',
    'evaluacion_reporte_todos', 'evaluacion_reporte_editar', 'evaluacion_editar_marcacion',
    'crear_biometrico', 'biometrico_conexion_red', 'biometrico_conexion_usb',
    'crear_tipo_contrato', 'crear_lugar_trabajo', 'crear_cargo',
    'crear_motivo_permiso', 'crear_motivo_feriado',
    'editar_tipo_contrato', 'editar_lugar_trabajo', 'editar_cargo',
    'editar_motivo_permiso', 'editar_motivo_feriado',
    'eliminar_tipo_contrato', 'eliminar_lugar_trabajo', 'eliminar_cargo',
    'eliminar_motivo_permiso', 'eliminar_motivo_feriado',
    'crear_rol', 'editar_rol', 'cambiar_rol','subir_excel_vacacion'
];


foreach ($permisos as $permiso) {
    $perm = Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
    $perm->syncRoles([$adminRole]);
}


    }
}
