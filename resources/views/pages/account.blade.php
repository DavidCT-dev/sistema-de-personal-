@extends('layouts.master')

@section('content')
    <!-- Page-content -->
    <div
        class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

            <div class="mt-1 ml-3 mr-3 rounded-none card">
                <div class="card-body !px-2.5">
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-12 2xl:grid-cols-12">
                        <!-- Imagen de perfil -->
                        <div class="lg:col-span-2 xl:col-span-1">
                            <div class="relative inline-block rounded-full shadow-md size-20 bg-slate-100 profile-user xl:size-28">
                                <img src="{{ asset('uatf/icon.jpg') }}" alt="Foto de perfil"
                                    class="object-cover border-0 rounded-full img-thumbnail user-profile-image">
                                <div class="absolute bottom-0 flex items-center justify-center rounded-full size-8 ltr:right-0 rtl:left-0 profile-photo-edit">
                                    <!-- Contenido adicional si es necesario -->
                                </div>
                            </div>
                        </div>
                        <!-- Información del usuario -->
                        <div class="lg:col-span-10 xl:col-span-10">
                            <h5 class="mb-1">
                                {{ auth()->user()->persona ? auth()->user()->persona->nombres . ' ' . auth()->user()->persona->apellido_pat . ' ' . auth()->user()->persona->apellido_mat : auth()->user()->name }}
                                <i data-lucide="badge-check"
                                    class="inline-block size-4 text-sky-500 fill-sky-100 dark:fill-custom-500/20"></i>
                            </h5>
                            <p class="mt-4 text-slate-500 dark:text-zink-200">
                                
                                        {{ auth()->user()->descripcion ?? 'Añada informacion' }}
                                    
                               
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Navegación -->
                <div class="card-body !px-2.5 !py-0">
                    <ul class="flex flex-wrap w-full text-sm font-medium text-center nav-tabs">
                        <li class="group active">
                            <a href="javascript:void(0);" data-tab-toggle="" data-target="overviewTabs"
                                class="inline-block px-4 py-2 text-base transition-all duration-300 ease-linear rounded-t-md 
                                text-slate-500 dark:text-zink-200 border-b border-transparent 
                                group-[.active]:text-custom-500 dark:group-[.active]:text-custom-500 
                                group-[.active]:border-b-custom-500 dark:group-[.active]:border-b-custom-500 
                                hover:text-custom-500 dark:hover:text-custom-500 active:text-custom-500 dark:active:text-custom-500 -mb-[1px]">
                                Descripción General
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!--end card-->

            <div class="tab-content">
                <div class="block tab-pane" id="overviewTabs">
                    <div class="grid grid-cols-1 gap-x-5 2xl:grid-cols-12">
                        <div class="2xl:col-span-9">

                        </div><!--end col-->
                        <div class="2xl:col-span-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-4 text-15">Información personal</h6>
                                    <div class="overflow-x-auto">
                                        <table class="w-full ltr:text-left rtl:text-right">
                                            <tbody>
                                                <tr>
                                                    <th class="py-2 font-semibold ps-0" scope="row">Nombre y Apellido
                                                    </th>
                                                    <td class="py-2 text-right text-slate-500 dark:text-zink-200">
                                                        {{ auth()->user()->persona ? auth()->user()->persona->nombres . ' ' . auth()->user()->persona->apellido_pat . ' ' . auth()->user()->persona->apellido_mat : auth()->user()->name }}
                                                        </td>
                                                </tr>
                                                <tr>
                                                    <th class="py-2 font-semibold ps-0" scope="row">Cedula de Identidad
                                                    </th>
                                                    <td class="py-2 text-right text-slate-500 dark:text-zink-200">
                                                        {{ auth()->user()->persona ? auth()->user()->persona->ci : '' }}
                                                        </td>
                                                </tr>
                                                <tr>
                                                    <th class="py-2 font-semibold ps-0" scope="row">Nombre de Usuario
                                                    </th>
                                                    <td class="py-2 text-right text-slate-500 dark:text-zink-200">
                                                        {{ auth()->user()->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="py-2 font-semibold ps-0" scope="row">Fecha de Ingreso</th>
                                                    <td class="py-2 text-right text-slate-500 dark:text-zink-200">
                                                        {{ \Carbon\Carbon::parse(auth()->user()->join_date)->translatedFormat('j F Y') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="py-2 font-semibold ps-0" scope="row">Último Inicio de
                                                        Sesión</th>
                                                    <td class="py-2 text-right text-slate-500 dark:text-zink-200">
                                                        {{ \Carbon\Carbon::parse(auth()->user()->last_login)->translatedFormat('l, j F Y') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="py-2 font-semibold ps-0" scope="row">Número de Teléfono
                                                    </th>
                                                    <td class="py-2 text-right text-slate-500 dark:text-zink-200">
                                                        {{ auth()->user()->phone_number ?? 'sin numero telefonico o celular' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="py-2 font-semibold ps-0" scope="row">Estado</th>
                                                    <td class="py-2 text-right text-slate-500 dark:text-zink-200">
                                                        {{ auth()->user()->status }}</td>
                                                </tr>
                                                
                                                <tr>
                                                    <th class="py-2 font-semibold ps-0" scope="row">Contraseña</th>
                                                    <td class="py-2 text-right text-slate-500 dark:text-zink-200">********
                                                    </td>
                                                </tr>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div><!--end card-->

                        </div><!--end col-->
                    </div><!--end grid-->


                </div><!--end tab pane-->
                <!--end tab pane-->
            </div><!--end tab content-->

        </div>
        <!-- container-fluid -->
    </div>
    <!-- End Page-content -->
@endsection
