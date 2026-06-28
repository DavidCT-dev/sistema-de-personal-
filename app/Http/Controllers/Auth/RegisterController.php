<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Brian2694\Toastr\Facades\Toastr;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

    /** regiter page */
    public function register()
    {
        return view('auth.register');
    }

    /** insert new users */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255|unique:users',
            'password'  => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ], [
            'name.required' => 'El nombre de usuario es obligatorio.',
            'name.string' => 'El nombre de usuario debe ser una cadena de texto.',
            'name.max' => 'El nombre de usuario no puede tener más de 255 caracteres.',
            'name.unique' => 'El nombre de usuario ya está en uso.',
        
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        
            'password_confirmation.required' => 'Debe confirmar su contraseña.',
        ]);
      
        
        try {
            $dt        = Carbon::now();
            $todayDate = $dt->toDayDateTimeString();
            
            $register            = new User;
            $register->name      = $request->name;
            $register->join_date = $todayDate;
            $register->status    = 'Active';
            $register->password  = Hash::make($request->password);
            $register->save();

            
            Toastr::success('Nueva cuenta creada exitosamente :)','Success');
            return redirect('login');
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('Add new employee fail :)','Error'. $e->getMessage());
            return redirect()->back();
        }
    }

}
