<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountSettingController;


// use App\Http\Controllers\BiometricoController;

// afiliaciones
use App\Http\Controllers\Content\Afiliaciones\EmpleadoController;
use App\Http\Controllers\Content\Afiliaciones\HorarioController;
use App\Http\Controllers\Content\Afiliaciones\RepEmpleadoController;
use App\Http\Controllers\Content\Afiliaciones\AsigEmpleadoController;

// inasistencia
use App\Http\Controllers\Content\Inasistencia\FeriadosController;
use App\Http\Controllers\Content\Inasistencia\PermisosController;
use App\Http\Controllers\Content\Inasistencia\VacacionesController;


// evaluacion asistenacia   
use App\Http\Controllers\Content\EvaluacionAsistencia\EvaluacionController;


// conexion   
use App\Http\Controllers\Content\Conexion\ConexionController;

//reportes
use App\Http\Controllers\reportes\ReporteAsistenciaController;

//solicitar permisos vacacion
use App\Http\Controllers\Content\Permiso_Vacacion\SolicitarPermisoController;
use App\Http\Controllers\Content\Permiso_Vacacion\SolicitarVacacionController;

// usuario roles
use App\Http\Controllers\Content\Usuarios_Rol\UsuariosRolController;

use App\Http\Controllers\Content\opciones\OpcionesController;


use App\Models\Permiso;
use App\Models\Vacacion;
use Illuminate\Support\Facades\View;

/** for side bar menu active */
function set_active($route) {
    if (is_array($route )){
        return in_array(Request::path(), $route) ? 'active' : '';
    }
    return Request::path() == $route ? 'active' : '';
}
/** for side bar menu show */
function set_show($route) {
    if (is_array($route )){
        return in_array(Request::path(), $route) ? 'show' : '';
    }
    return Request::path() == $route ? 'show' : '';
}



// Route::get('/', function () {
//     return view('auth.login');
// });
Route::get('/', function () {
    return view('index');
});

Route::get('/', function () {
    return view('index');
})->name('inicio');

// Route::post('/procesar-asistencia', [BiometricoController::class, 'procesarAsistencia']);



Route::group(['middleware'=>'auth'],function()
{
    
    View::composer('*', function ($view) {
        // Conteo de vacaciones pendientes
        $vacacionesPendientesCount = Vacacion::where('estado', 'pendiente')->count();
        
        // Conteo de permisos pendientes
        $permisosPendientesCount = Permiso::where('estado', 'pendiente')->count();
    
        // Suma de ambos conteos
        $totalPendientes = $vacacionesPendientesCount + $permisosPendientesCount;
    
        // Compartir los datos con todas las vistas
        $view->with([
            'vacacionesPendientesCount' => $vacacionesPendientesCount,
            'permisosPendientesCount' => $permisosPendientesCount,
            'totalPendientes' => $totalPendientes,
        ]);
    });

// afiliaciones
    Route::resource('afiliaciones/empleados', EmpleadoController::class);
    Route::resource('afiliaciones/horarios', HorarioController::class);
    Route::post('afiliaciones/asignacion-horarios', [HorarioController::class, 'asignarHorario'])->name('horarios.asrignarHorario');

    
Route::put('/horarios/{id}', [HorarioController::class, 'updateHorarios'])->name('horarios.update');

// inasistencia
    Route::resource('inasistencia/feriados', FeriadosController::class);
    Route::get('/crear-feriados-generales', [FeriadosController::class, 'crearFeriados'])->name('crear-feriados');

    Route::resource('inasistencia/permisos', PermisosController::class);
    Route::put('/approve-permiso/{approve}', [PermisosController::class, 'approve'])->name('permisos-approve');
    Route::put('/reject-permiso/{reject}', [PermisosController::class, 'reject'])->name('permisos-reject');

    Route::resource('inasistencia/vacaciones', VacacionesController::class);
    Route::put('/approve-vacacion/{approve}', [VacacionesController::class, 'approve'])->name('vacaciones-approve');
    Route::put('/reject-vacacion/{reject}', [VacacionesController::class, 'reject'])->name('vacaciones-reject');
    
    Route::post('/upload-file-excel', [VacacionesController::class, 'CargarArchivoExcel'])->name('subir-excel');
    
// evaluacion asistenacia   
    Route::resource('evaluacion/asistencia', EvaluacionController::class);
    


    // conexion   
    Route::resource('conexion', ConexionController::class);
    Route::post('/crear-biometricos', [ConexionController::class, 'crearBiometrico'])->name('crear-biometricos');



// reportes
Route::post('/pdf-generate-asistencia', [ReporteAsistenciaController::class, 'generatePdfAsistencia'])->name('pdf-generate-asistencia');
Route::post('/pdf-generate-permiso', [ReporteAsistenciaController::class, 'generarReportePermiso'])->name('pdf-generate-permiso');
Route::post('/pdf-generate-vacacion', [ReporteAsistenciaController::class, 'generarReporteVacacion'])->name('pdf-generate-vacacion');
Route::post('/pdf-generate-asistencia-todos', [ReporteAsistenciaController::class, 'generatePdfAsistenciaTodos'])->name('pdf-generate-asistencia-todos');






   

    Route::resource('mas-opciones', OpcionesController::class);
    
    // crear cargo, lugar de trabajo, tipo de contrato
    Route::post('crear-cargo', [OpcionesController::class, 'storeCargos'])->name('crear-cargo');
    Route::post('crear-lugar-trabajo', [OpcionesController::class, 'storeLugarTrabajo'])->name('crear-lugarTrabajo');
    Route::post('crear-tipo-contrato', [OpcionesController::class, 'storeTipoContrato'])->name('crear-tipoContrato');
    Route::post('crear-motivo-permiso', [OpcionesController::class, 'storeMotivoPermiso'])->name('crear-motivoPermiso');
    Route::post('crear-motivo-feriado', [OpcionesController::class, 'storeMotivoFeriado'])->name('crear-motivoFeriado');

    // actualizar cargo, lugar de trabajo, tipo de contrato
    Route::put('/tipo-contrato/{id}', [OpcionesController::class, 'updateTipoContrato'])->name('actualizar-tipoContrato');
    Route::put('/tipo-lugar-trabajo/{id}', [OpcionesController::class, 'updateLugarTrabajo'])->name('actualizar-lugarTrabajo');
    Route::put('/tipo-cargo/{id}', [OpcionesController::class, 'updateCargo'])->name('actualizar-cargo');
    Route::put('/tipo-motivo-permiso/{id}', [OpcionesController::class, 'updateMotivoPermiso'])->name('actualizar-motivoPermiso');
    Route::put('/tipo-motivo-feriado/{id}', [OpcionesController::class, 'updateMotivoFeriado'])->name('actualizar-motivoFeriado');

    
     // eliminar cargo, lugar de trabajo, tipo de contrato
     Route::delete('/tipo-contrato/{id}', [OpcionesController::class, 'deleteTipoContrato'])->name('eliminar-tipoContrato');
     Route::delete('/lugar-trabajo/{id}', [OpcionesController::class, 'deleteLugarTrabajo'])->name('eliminar-lugarTrabajo');
     Route::delete('/eliminar-cargo/{id}', [OpcionesController::class, 'deleteCargo'])->name('eliminar-cargo');
     Route::delete('/eliminar-motivo-permiso/{id}', [OpcionesController::class, 'deleteMotivoPermiso'])->name('eliminar-motivo-permiso');
     Route::delete('/eliminar-motivo-feriado/{id}', [OpcionesController::class, 'deleteMotivoFeriado'])->name('eliminar-motivo-feriado');

    

     // -------------------------- pages ----------------------//
     Route::resource('cuenta', AccountController::class);
     Route::resource('cuenta-configuracion', AccountSettingController::class);

     Route::put('/perfil/actualizar', [AccountSettingController::class, 'updateProfile'])->name('user.updateProfile');
     Route::post('/change-password', [AccountSettingController::class, 'changePassword'])->name('change.password');


    // -------------------------- Solicitar permisos vacacion ----------------------//

     Route::resource('solicitar-permisos', SolicitarPermisoController::class);
     Route::resource('solicitar-vacacion', SolicitarVacacionController::class);

    //  usuarios rol
     Route::resource('usuarios-rol', UsuariosRolController::class);
     Route::post('/users/update-roles', [UsuariosRolController::class, 'updateRoles'])->name('users.updateRoles');
     Route::post('/users/create-roles', [UsuariosRolController::class, 'storeRoles'])->name('users.createRoles');
Route::put('/roles/update', [UsuariosRolController::class, 'updateRol'])->name('roles.update');


    Route::get('home',function()
    {
        return view('dashboard.home');
    });
    Route::get('home',function()
    {
        return view('dashboard.home');
    });
});

Auth::routes();

Route::group(['namespace' => 'App\Http\Controllers\Auth'],function()
{
    // -----------------------------login----------------------------------------//
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/login', 'authenticate');
        Route::get('/logout', 'logout')->name('logout');
        Route::get('logout/page', 'logoutPage')->name('logout/page');
    });

    // ------------------------------ register ----------------------------------//
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'register')->name('register');
        Route::post('/register','storeUser')->name('register');    
    });

    // ----------------------------- forget password ----------------------------//
    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::get('forget-password', 'getEmail')->name('forget-password');
        Route::post('forget-password', 'postEmail')->name('forget-password');    
    });

});

Route::group(['namespace' => 'App\Http\Controllers'],function()
{
    // -------------------------- main dashboard ----------------------//
    Route::controller(HomeController::class)->group(function () {
        Route::get('/home', 'index')->middleware('auth')->name('home');
    });


});


