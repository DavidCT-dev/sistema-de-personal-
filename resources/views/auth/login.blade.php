@extends('layouts.app')
@section('script')
<script>
    document.getElementById("togglePassword").addEventListener("click", function () {
    let passwordInput = document.getElementById("password");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        this.textContent = "👁️"; 
    } else {
        passwordInput.type = "password";
        this.textContent = "🔒"
    }
});

</script>
@endsection
@section('content')
    <div class="mb-0 w-screen lg:mx-auto lg:w-[500px] card shadow-lg border-none shadow-slate-100 relative">
        <div class="!px-10 !py-12 card-body">
            <a href="#!">
                <img src="{{ asset('uatf/AUTF.png') }}" alt="" class="hidden h-14 mx-auto dark:block">
                <img src="{{ asset('uatf/AUTF.png') }}" alt="" class="block h-14 mx-auto dark:hidden">
            </a>

            <div class="mt-8 text-center">
                <h4 class="mb-1 text-custom-500 dark:text-custom-500">Bienvenido de nuevo !</h4>
                <p class="text-slate-500 dark:text-zink-200">Inicie sesión para continuar en Personal.</p>
            </div>

            <form action="{{ route('login') }}" class="mt-10" id="" method="POST">
                @csrf
                <div class="hidden px-4 py-3 mb-3 text-sm text-green-500 border border-green-200 rounded-md bg-green-50 dark:bg-green-400/20 dark:border-green-500/50" id="successAlert">
                    You have <b>successfully</b> signed in.
                </div>
                <div class="mb-3">
                    <label for="name" class="inline-block mb-2 text-base font-medium">Nombre de Usuario</label>
                    <input type="text" value="{{ old('name') }}" 
                    id="name" name="name" 
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                        @error('name') border-red-500 @enderror" 
                        placeholder="Introduzca Usuario" autocomplete="off">
                    @error('name')
                    <div id="username-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3 relative">
                    <label for="password" class="inline-block mb-2 text-base font-medium">Contraseña</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" 
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                            @error('password') border-red-500 @enderror w-full pr-10" 
                            placeholder="Introducir contraseña" autocomplete="off">
                        <button type="button" id="togglePassword" 
                            class="absolute inset-y-0 right-2 flex items-center text-gray-500 dark:text-zink-200">
                            🔒
                        </button>
                    </div>
                    @error('password')
                    <div id="password-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                </div>
                
                
                
                <div class="mt-10">
                    <button type="submit" class="w-full text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Iniciar sesión</button>
                </div>

                <div class="relative text-center my-9 before:absolute before:top-3 before:left-0 before:right-0 before:border-t before:border-t-slate-200 dark:before:border-t-zink-500">
                    <h5 class="inline-block px-2 py-0.5 text-sm bg-white text-slate-500 dark:bg-zink-600 dark:text-zink-200 rounded relative"></h5>
                </div>

                

                {{-- <div class="mt-10 text-center">
                    <p class="mb-0 text-slate-500 dark:text-zink-200">¿No tienes una cuenta? 
                        <a href="{{ route('register') }}" class="font-semibold underline transition-all duration-150 ease-linear text-slate-500 dark:text-zink-200 hover:text-custom-500 dark:hover:text-custom-500"> Regístrate</a>
                    </p>
                </div> --}}
            </form>
        </div>
    </div>

    @section('script')
       
    @endsection
@endsection
