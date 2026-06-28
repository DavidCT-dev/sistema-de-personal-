<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Hash;

class AccountSettingController extends Controller
{
    /** page account profile */
    public function index()
    {
        return view('pages.account-setting');
    }



    public function updateProfile(Request $request)
    {
        $user = Auth::user();
    
        // Validación de los campos
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $user->id,
            'phone_number' => 'nullable|string|regex:/^[0-9]{1,10}$/', // Valida solo números y máximo 10 caracteres
            'descripcion' => 'nullable|string',
        ], [
            // Mensajes personalizados en español
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El campo nombre debe ser una cadena de texto.',
            'name.max' => 'El campo nombre no debe exceder los 255 caracteres.',
            'name.unique' => 'El nombre ya está en uso.',
        
            'phone_number.regex' => 'El campo número de teléfono debe contener solo números y tener un máximo de 10 caracteres.',
        
            'descripcion.string' => 'El campo descripción debe ser una cadena de texto.',
        ]);
        
    
        // Verificar si el correo electrónico ha cambiado
        $nameChanged = $request->input('name') !== $user->name;
    
        // Actualizar los datos del usuario
        $user->update([
            'name' => $request->input('name'),
            'phone_number' => $request->input('phone_number'),
            'descripcion' => $request->input('descripcion'),
        ]);
    
        // Si el correo electrónico cambió, cerrar sesión
        if ($nameChanged) {

            $request->session()->forget('name');
            $request->session()->forget('last_login');
            $request->session()->flush();
            Auth::logout();
            Toastr::success('Datos actualizados correctamente :)','Success');
            return redirect('logout/page');
            // Redirigir al usuario a la página de inicio de sesión con un mensaje
        }
    
        // Si no cambió el correo, redirigir de vuelta con un mensaje de éxito
        Toastr::success('Datos actualizados correctamente :)','Success');
        return back();
    }


    public function changePassword(Request $request)
    {
        // Validación de los campos
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'old_password.required' => 'La contraseña actual es obligatoria.',
            'new_password.required' => 'La nueva contraseña es obligatoria.',
            'new_password.string' => 'La nueva contraseña debe ser una cadena de texto.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->old_password, $user->password)) {
            Toastr::error('La contraseña actual es incorrecta. :(','Success');
            return back();
        }

        // Actualizar la contraseña
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Redirigir con un mensaje de éxito

        
        $request->session()->forget('name');
        $request->session()->forget('last_login');
        $request->session()->flush();
        Auth::logout();
        Toastr::success('Contraseña cambiada exitosamente. :)','Success');

        return redirect('logout/page');
    }

}
