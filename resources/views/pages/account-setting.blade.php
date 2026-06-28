@extends('layouts.master')

@section('script')
<script>
    // Función para mostrar/ocultar la contraseña
    function togglePasswordVisibility(inputId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = passwordInput.nextElementSibling.querySelector('i');

        if (passwordInput.type === "password") {
            passwordInput.type = "text"; // Mostrar contraseña
            eyeIcon.classList.replace("ri-eye-fill", "ri-eye-off-fill"); // Cambiar ícono
        } else {
            passwordInput.type = "password"; // Ocultar contraseña
            eyeIcon.classList.replace("ri-eye-off-fill", "ri-eye-fill"); // Cambiar ícono
        }
    }

</script>
@endsection

@section('content')
<!-- Page-content -->
    <div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

            <div class="mt-1 -ml-3 -mr-3 rounded-none card">
                <div class="card-body !px-2.5">
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-12 2xl:grid-cols-12">
                        <div class="lg:col-span-2 xl:col-span-1 ">
                            <div class="relative inline-block rounded-full shadow-md size-20 bg-slate-100 profile-user xl:size-28">
                                <img src="{{ asset('uatf/icon.jpg') }}" alt="" class="object-cover border-0 rounded-full img-thumbnail user-profile-image">
                                <div class="absolute bottom-0 flex items-center justify-center rounded-full size-8 ltr:right-0 rtl:left-0 profile-photo-edit">
                                   
                                  
                                </div>
                            </div>
                        </div><!--end col-->
                        <div class="lg:col-span-10 xl:col-span-9">
                            <h5 class="mb-1">    {{ auth()->user()->persona ? auth()->user()->persona->nombres . ' ' . auth()->user()->persona->apellido_pat . ' ' . auth()->user()->persona->apellido_mat : auth()->user()->name }}
                                <i data-lucide="badge-check" class="inline-block size-4 text-sky-500 fill-sky-100 dark:fill-custom-500/20"></i></h5>
                           
                           
                            <p class="mt-4 text-slate-500 dark:text-zink-200">
                                {{ auth()->user()->descripcion ?? 'Añada informacion' }}
                            </p>
                            
                        </div>
                       
                    </div><!--end grid-->
                </div>
                <div class="card-body !px-2.5 !py-0">
                    <ul class="flex flex-wrap w-full text-sm font-medium text-center nav-tabs">
                       
                        <li class="group active">
                            <a href="javascript:void(0);" data-tab-toggle="" data-target="personalTabs" class="inline-block px-4 py-2 text-base transition-all duration-300 ease-linear rounded-t-md text-slate-500 dark:text-zink-200 border-b border-transparent group-[.active]:text-custom-500 dark:group-[.active]:text-custom-500 group-[.active]:border-b-custom-500 hover:text-custom-500 dark:hover:text-custom-500 active:text-custom-500 dark:active:text-custom-500 -mb-[1px]">Información Personal</a>
                        </li>
                        
                        <li class="group">
                            <a href="javascript:void(0);" data-tab-toggle="" data-target="changePasswordTabs" class="inline-block px-4 py-2 text-base transition-all duration-300 ease-linear rounded-t-md text-slate-500 dark:text-zink-200 border-b border-transparent group-[.active]:text-custom-500 dark:group-[.active]:text-custom-500 group-[.active]:border-b-custom-500 hover:text-custom-500 dark:hover:text-custom-500 active:text-custom-500 dark:active:text-custom-500 -mb-[1px]">Cambiar la contraseña</a>
                        </li>

                    </ul>
                </div>
            </div><!--end card-->

            <div class="tab-content">
                <div class="block tab-pane" id="personalTabs">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-1 text-15">Personal Information</h6>
                            <p class="mb-4 text-slate-500 dark:text-zink-200">Actualice sus datos personales aquí fácilmente.</p>


                            <form action="{{ route('user.updateProfile') }}" method="POST">
                                @csrf
                                @method('PUT')
                            
                                <div class="grid grid-cols-1 gap-5 xl:grid-cols-12">
                                    <!-- Campo: Nombre y Apellido -->
                                    <div class="xl:col-span-6">
                                        <label class="inline-block mb-2 text-base font-medium">Nombre de usuario</label>
                                        <input type="text" name="name" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" 
                                               value="{{ old('name', auth()->user()->name) }}" 
                                               placeholder="Ingrese nombre completo">
                                        @error('name')
                                            <span class="text-sm text-red-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                            
                                    <!-- Campo: Número de Teléfono -->
                                    <div class="xl:col-span-6">
                                        <label class="inline-block mb-2 text-base font-medium">Número de Teléfono</label>
                                        <input type="text" name="phone_number" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                               value="{{ old('phone_number', auth()->user()->phone_number) }}"
                                               placeholder="Ingrese su número de teléfono">
                                        @error('phone_number')
                                            <span class="text-sm text-red-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                            
                                   
                            
                                    <!-- Campo: Descripción -->
                                    <div class="xl:col-span-12">
                                        <label class="block mb-2 text-base font-medium">Descripción</label>
                                        <textarea name="descripcion" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" rows="3"
                                                  placeholder="Ingresa descripción">{{ old('descripcion', auth()->user()->descripcion) }}</textarea>
                                        @error('descripcion')
                                            <span class="text-sm text-red-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            
                                <!-- Botones: Actualizar y Cancelar -->
                                <div class="flex justify-end mt-6 gap-x-4">
                                    <button type="submit" class="btn bg-custom-500 text-white">Actualizar</button>
                                    <button type="reset" class="btn bg-red-100 text-red-500">Cancelar</button>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                </div>
                
                <div class="hidden tab-pane" id="changePasswordTabs">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-4 text-15">Cambia la contraseña</h6>
                            <form action="{{ route('change.password') }}" method="POST">
                                @csrf <!-- Token CSRF para seguridad -->
                                <div class="grid grid-cols-1 gap-5 xl:grid-cols-12">
                                    <!-- Campo: Old Password -->
                                    <div class="xl:col-span-4">
                                        <label for="oldPasswordInput" class="inline-block mb-2 text-base font-medium">Contraseña anterior*</label>
                                        <div class="relative">
                                            <input type="password" name="old_password" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" id="oldPasswordInput" placeholder="Enter current password">
                                            <button class="absolute top-2 ltr:right-4 rtl:left-4" type="button" onclick="togglePasswordVisibility('oldPasswordInput')">
                                                <i class="align-middle ri-eye-fill text-slate-500 dark:text-zink-200"></i>
                                            </button>
                                        </div>
                                        @error('old_password')
                                            <span class="text-sm text-red-500">{{ $message }}</span>
                                        @enderror
                                    </div><!--end col-->
                            
                                    <!-- Campo: New Password -->
                                    <div class="xl:col-span-4">
                                        <label for="newPasswordInput" class="inline-block mb-2 text-base font-medium">Nueva contraseña*</label>
                                        <div class="relative">
                                            <input type="password" name="new_password" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" id="newPasswordInput" placeholder="Enter new password">
                                            <button class="absolute top-2 ltr:right-4 rtl:left-4" type="button" onclick="togglePasswordVisibility('newPasswordInput')">
                                                <i class="align-middle ri-eye-fill text-slate-500 dark:text-zink-200"></i>
                                            </button>
                                        </div>
                                        @error('new_password')
                                            <span class="text-sm text-red-500">{{ $message }}</span>
                                        @enderror
                                    </div><!--end col-->
                            
                                    <!-- Campo: Confirm Password -->
                                    <div class="xl:col-span-4">
                                        <label for="confirmPasswordInput" class="inline-block mb-2 text-base font-medium">Confirmar Contraseña*</label>
                                        <div class="relative">
                                            <input type="password" name="new_password_confirmation" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" id="confirmPasswordInput" placeholder="Confirm password">
                                            <button class="absolute top-2 ltr:right-4 rtl:left-4" type="button" onclick="togglePasswordVisibility('confirmPasswordInput')">
                                                <i class="align-middle ri-eye-fill text-slate-500 dark:text-zink-200"></i>
                                            </button>
                                        </div>
                                        @error('new_password_confirmation')
                                            <span class="text-sm text-red-500">{{ $message }}</span>
                                        @enderror
                                    </div><!--end col-->
                            
                                    <!-- Botón: Change Password -->
                                    <div class="flex justify-end xl:col-span-6">
                                        <button type="submit" class="text-white bg-green-500 border-green-500 btn hover:text-white hover:bg-green-600 hover:border-green-600 focus:text-white focus:bg-green-600 focus:border-green-600 focus:ring focus:ring-green-100 active:text-white active:bg-green-600 active:border-green-600 active:ring active:ring-green-100 dark:ring-green-400/10">Change Password</button>
                                    </div>
                                </div><!--end grid-->
                            </form>
                        </div>
                    </div>
                </div>
                
            </div>



        </div>
        <!-- container-fluid -->
    </div>
<!-- End Page-content -->
@endsection
