<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
           public function run(): void
        { 
            // Crear usuario admin
            $userId = DB::table('users')->insertGetId([
                'name' => 'admin',
                'join_date' => now()->toDateString(),
                'last_login' => null,
                'phone_number' => '1234567890',
                'status' => 'active',
                'password' => Hash::make('admin'),    
            ]);
            
            // Asignar rol "Super Admin" al usuario creado
            DB::table('model_has_roles')->insert([
                'role_id' => DB::table('roles')->where('name', 'Super Admin')->first()->id,
                'model_type' => 'App\Models\User',
                'model_id' => $userId,
            ]);
    
            // Insertar una persona en la base de datos
            $persona = Persona::create([
                'nombres' => 'Juan Carlos',
                'apellido_pat' => 'Perez',
                'apellido_mat' => 'Lopez',
                'fecha_nac' => '1990-05-15',
                'direccion' => 'Av. Siempre Viva 123',
                'ci' => '3661253',
                'celular' => '78901234',
                'fech_ing' => '2020-01-10',
                'fech_baj' => null,
                'item' => '1234',
                'tipo_contrato_id' => 1,
                'horario_id' => 1,
                'lugar_trabajo_id' => 3,
                'cargo_id' => 4,
                'genero_id' => 1,
            ]);
    
            // Asignar persona al usuario
            User::where('id', $userId)->update(['persona_id' => $persona->id]);






            // Crear segundo usuario con CI 123456789
    $userId = DB::table('users')->insertGetId([
        'name' => '123456789',
        'join_date' => now()->toDateString(),
        'last_login' => null,
        'phone_number' => '987654321',
        'status' => 'active',
        'password' => Hash::make('123456789'),    
    ]);
    
    // Asignar rol básico al segundo usuario (ajusta según tus necesidades)
    DB::table('model_has_roles')->insert([
        'role_id' => DB::table('roles')->where('name', 'Empleado')->first()->id, // Ajusta el nombre del rol
        'model_type' => 'App\Models\User',
        'model_id' => $userId,
    ]);
             // Insertar persona para el segundo usuario con CI 123456789
    $personaUser = Persona::create([
        'nombres' => 'Usuario',
        'apellido_pat' => 'Demo',
        'apellido_mat' => 'Sistema',
        'fecha_nac' => '1995-01-01',
        'direccion' => 'Av. Ejemplo 456',
        'ci' => '123456789',
        'celular' => '76543210',
        'fech_ing' => '2021-01-01',
        'fech_baj' => null,
        'item' => '5678',
        'tipo_contrato_id' => 1,
        'horario_id' => 1,
        'lugar_trabajo_id' => 1,
        'cargo_id' => 2,
        'genero_id' => 1,
    ]);

    // Asignar personas a los usuarios
    User::where('id', $userId)->update(['persona_id' => $personaUser->id]);
        }
}
