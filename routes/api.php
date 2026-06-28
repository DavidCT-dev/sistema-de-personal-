<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BiometricoController; 

Route::get('/user', function (Request $request) {
    return 'metodo GET';
    // return $request->user();
});


Route::post('/asistencias', [BiometricoController::class, 'procesarAsistencia']);

Route::post('/registro-empleados', [BiometricoController::class, 'registrarEmpleado']);

Route::post('/guardar-feriados', [BiometricoController::class, 'guardarFeriados']);


