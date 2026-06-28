<?php

namespace App\Http\Controllers\Content\Usuarios_Rol;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Toastr;
use Exception;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;


class UsuariosRolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuarios = User::with(['roles', 'persona'])->get();
        $allRoles = Role::with(['permissions'])->get(); // Obtiene todos los roles disponibles con sus permisos
        // dd($allRoles);

        $permissions = Permission::all();
        // Separar permisos: sin guión = menú, con guión = acción/botón
        $menus = $permissions->filter(function ($permission) {
            return !Str::contains($permission->name, '_');
        });

        $acciones = $permissions->filter(function ($permission) {
            return Str::contains($permission->name, '_');
        })->values(); // Reindexa el array

        // Dividir acciones en 2 columnas
        $half = ceil($acciones->count() / 2);
        $accionesCol2 = $acciones->slice(0, $half);
        $accionesCol3 = $acciones->slice($half);

        return view('content.usuariosRol.index', compact(
            'usuarios',
            'allRoles',
            'menus',
            'accionesCol2',
            'accionesCol3'
        ));
    }



    // En tu controlador de usuarios
    public function updateRoles(Request $request)
    {
        try {
            $user = User::findOrFail($request->user_id);

            // Sincroniza los roles (elimina los anteriores y asigna los nuevos)
            $user->syncRoles($request->roles ?? []);

            Toastr::success('Roles actualizados correctamente :)', 'Success');


            return response()->json([
                'success' => true,
                'message' => 'Roles actualizados correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar roles: ' . $e->getMessage()
            ], 500);
        }
    }




    public function storeRoles(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            // Crear el nuevo rol
            $role = Role::create([
                'name' => $request->input('name'),
                'guard_name' => 'web', // Asegúrate de definir el guard si usas más de uno
            ]);

            // Asignar permisos si se seleccionaron
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->input('permissions'));
            }

            Toastr::success('Rol creado correctamente :)', 'Success');
            return back();
        } catch (\Exception $e) {
            Toastr::error('Error al crear el rol :(', 'Success');

            return back();
        }
    }

    public function updateRol(Request $request)
    {
        // Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
            'name' => 'nullable|string|max:255|unique:roles,name,' . $request->role_id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Obtener el rol a editar
            $role = Role::findOrFail($request->role_id);

            // Actualizar el nombre si ha cambiado
            $role->name = $request->name ?? $role->name;
            $role->save();

            // Obtener los permisos por sus IDs
            $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();

            // Sincronizar los permisos seleccionados
            $role->syncPermissions($permissions);

            Toastr::success('Rol actualizado correctamente.', 'Éxito');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Ocurrió un error al actualizar el rol: ' . $e->getMessage(), 'Error');
            return redirect()->back();
        }
    }
}
