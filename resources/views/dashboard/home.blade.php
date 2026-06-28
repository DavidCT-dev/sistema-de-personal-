@extends('layouts.master')

@section('content')
    <!-- Page-content -->
    <div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">Panel Estaditico</h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="#!" class="text-slate-400 dark:text-zink-200">Panel Estaditico</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        Home
                    </li>
                </ul>
            </div>

            
            <div class="grid grid-cols-12 w-full gap-x-5">
                <!-- Total de Empleados -->
                <div class="col-span-12 card md:col-span-6 lg:col-span-4 xl:col-span-3 2xl:col-span-3">
                    <div class="text-center card-body">
                        <div class="flex items-center justify-center mx-auto rounded-full size-14 bg-custom-100 text-custom-500 dark:bg-custom-500/20">
                            <i data-lucide="users"></i>
                        </div>
                        <h5 class="mt-4 mb-2">{{$totalPersonas}}</h5>
                        <p class="text-slate-500 dark:text-zink-200">Total de Empleados</p>
                    </div>
                </div>
                <!--end col-->
            
                <!-- Cantidad de permisos en el mes -->
                <div class="col-span-12 card md:col-span-6 lg:col-span-4 xl:col-span-3 2xl:col-span-3">
                    <div class="text-center card-body">
                        <div class="flex items-center justify-center mx-auto text-purple-500 bg-purple-100 rounded-full size-14 dark:bg-purple-500/20">
                            <i data-lucide="file-text"></i>
                        </div>
                        <h5 class="mt-4 mb-2">{{$permisosMesActual}}</h5>
                        <p class="text-slate-500 dark:text-zink-200">Cantidad de permisos en el mes</p>
                    </div>
                </div>
                <!--end col-->
            
                <!-- Cantidad de personas con vacaciones -->
                <div class="col-span-12 card md:col-span-6 lg:col-span-4 xl:col-span-3 2xl:col-span-3">
                    <div class="text-center card-body">
                        <div class="flex items-center justify-center mx-auto text-green-500 bg-green-100 rounded-full size-14 dark:bg-green-500/20">
                            <i data-lucide="calendar-off"></i>
                        </div>
                        <h5 class="mt-4 mb-2">{{$vacacionesAnioActual}}</h5>
                        <p class="text-slate-500 dark:text-zink-200">Cantidad de personas con vacación</p>
                    </div>
                </div>
                <!--end col-->
            
                <!-- Datos adicionales (Horarios, Unidades, Tipos de Contrato) -->
                <div class="col-span-12 card md:col-span-6 lg:col-span-4 xl:col-span-3 2xl:col-span-3">
                    <div class="text-center card-body">
                        <div class="flex items-center justify-center mx-auto text-red-500 bg-red-100 rounded-full size-14 dark:bg-red-500/20">
                            <i data-lucide="grid"></i>
                        </div>
                        <h5 class="mt-4">{{$totalHorarios}}</h5>
                        <h5>{{$totalLugaresTrabajo}}</h5>
                        <h5>{{$totalTiposContrato}}</h5>
                        <p class="text-slate-500 dark:text-zink-200">Cantidad de Horarios</p>
                        <p class="text-slate-500 dark:text-zink-200">Cantidad de Unidades</p>
                        <p class="text-slate-500 dark:text-zink-200">Cantidad de tipos de contrato</p>
                    </div>
                </div>
                <!--end col-->
            </div>
            <!--end grid-->
        </div>
        <!-- container-fluid -->
    </div>
    <!-- End Page-content -->
   
@endsection
