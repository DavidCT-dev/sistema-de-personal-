@extends('layouts.app')
@section('content')
    <div class="mb-0 w-screen lg:w-[500px] card shadow-lg border-none shadow-slate-100 relative">
        <div class="!px-10 !py-12 card-body">
            <a href="#!">
                <img src="{{ asset('uatf/AUTF.png') }}" alt="" class="hidden h-14 mx-auto dark:block">
                <img src="{{ asset('uatf/AUTF.png') }}" alt="" class="block h-14 mx-auto dark:hidden">
            </a>

            <div class="mt-8 text-center">
                <h4 class="mb-1 text-custom-500 dark:text-custom-500">Crea tu cuenta</h4>
            </div>

            <form action="{{ route('register') }}" class="mt-10" method="POST">
                @csrf
            
                <!-- Nombre de Usuario -->
                <div class="mb-3">
                    <label for="username-field" class="inline-block mb-2 text-base font-medium">Nombre de Usuario</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="username-field" 
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @error('name') border-red-500 @enderror" 
                        placeholder="Introducir usuario" 
                        value="{{ old('name') }}"
                    >
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            
                <!-- Contraseña -->
                <div class="mb-3">
                    <label for="password" class="inline-block mb-2 text-base font-medium">Contraseña</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @error('password') border-red-500 @enderror" 
                        placeholder="Introducir contraseña"
                    >
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            
                <!-- Confirmación de Contraseña -->
                <div class="mb-3">
                    <label for="password_confirmation" class="inline-block mb-2 text-base font-medium">Confirmación de contraseña</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @error('password_confirmation') border-red-500 @enderror" 
                        placeholder="Introducir la confirmación de contraseña"
                    >
                    @error('password_confirmation')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            
                <!-- Botón de Registro -->
                <div class="mt-10">
                    <button type="submit" class="w-full text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                        Registrarse
                    </button>
                </div>
            
                <!-- Link de Inicio de Sesión -->
                <div class="mt-10 text-center">
                    <p class="mb-0 text-slate-500 dark:text-zink-200">
                        ¿Ya tienes una cuenta?
                        <a href="{{ route('login') }}" class="font-semibold underline transition-all duration-150 ease-linear text-slate-500 dark:text-zink-200 hover:text-custom-500 dark:hover:text-custom-500">
                            Iniciar sesión
                        </a>
                    </p>
                </div>
            </form>
            
        </div>
    </div>

    @section('script')
       
    @endsection
@endsection
