@extends('layouts.master')

@section('script')
    <script src="{{ asset('assets/js/datatables/vfs_fonts.js') }}"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr.localize(flatpickr.l10ns.es);


                flatpickr("#fecha_inicio,#fecha_fin", {
                enableTime: false, // Deshabilitar la selección de tiempo
                dateFormat: "Y-m-d", // Formato de fecha
                time_24hr: true, // Usar formato de 24 horas
                weekNumbers: true // Mostrar números de la semana
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", async function() {
            const response = await fetch("{{ asset('assets/lang-datatables/Spanish.json') }}");
            translations = await response.json();

            $('#rowSelectionDeletion').DataTable({
                responsive: true,
                paging: true,
                pageLength: 5,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                //scrollX: true,
                language: translations,
                "lengthMenu": [5, 10, 25],

            });

        });

        
        
    </script>



    <script>
        function showRejectModal(vacacionId) {
            const form = document.getElementById('rejectForm');

            // Configurar la acción del formulario con la ruta nombrada
            form.action = `/solicitar-vacacion/${vacacionId}`;

            // Asegurarse de que el método sea PUT
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput); // Corregido: agregar al form, no al modal
            } else {
                methodInput.value = 'PUT';
            }

            // Asegurarse de que existe el token CSRF
            let csrfInput = form.querySelector('input[name="_token"]');
            if (!csrfInput) {
                csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
                form.appendChild(csrfInput); // Corregido: agregar al form, no al modal
            }

        }



        // Manejar envío del formulario
        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Enviar el formulario
            this.submit();
        });
    </script>
@endsection

@section('content')
    <div
        class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">Solicitar Vacación</h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li
                        class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="#!" class="text-slate-400 dark:text-zink-200">Solicitar</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        vacación
                    </li>
                </ul>
            </div>

            <button data-modal-target="vacacionModal" type="button" id="extraLargeModalButton"
                class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:focus:ring-custom-400/20 mb-3">Solicitar
                Vacación </button>

            <div class="grid grid-cols-1 md:grid-cols-1 gap-x-5 w-full">
                <div class="2xl:col-span-1">


                    <div class="grid grid-cols-12 gap-4 mt-2">

                        <!-- Sección de 8 columnas -->
                        <div class="col-span-12 md:col-span-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="overflow-x-auto">
                                        <table id="rowSelectionDeletion" class="display" style="width:100%">
                                            <thead>
                                                <tr
                                                    class="bg-gray-100 text-left text-gray-600 text-sm font-medium uppercase">
                                                    <th class="py-3 px-4 border-b border-gray-300">Acciones</th>
                                                    <th class="py-3 px-4 border-b border-gray-300">Estado</th>
                                                    <th class="py-3 px-4 border-b border-gray-300">Fecha Inicio</th>

                                                    <th class="py-3 px-4 border-b border-gray-300">Fecha Fin</th>
                                                    <th class="py-3 px-4 border-b border-gray-300">Canitdad dias</th>
                                                    
                                                    <th class="py-3 px-4 border-b border-gray-300">Observaciones</th>


                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($vacaciones as $vacacion)
                                                    <tr>
                                                        <!-- Acciones (Editar / Dar Baja) -->
                                                        @if ($vacacion->estado === 'rechazado')
                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">


                                                            </td>
                                                        @elseif($vacacion->estado === 'cancelado')
                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                                <div class="remove">
                                                                    <button type="button"
                                                                        class="py-1 text-xs text-slate-500 bg-slate-200 border-slate-200 btn hover:text-slate-600 hover:bg-slate-300 hover:border-slate-300 focus:text-slate-600 focus:bg-slate-300 focus:border-slate-300 focus:ring focus:ring-slate-100 active:text-slate-600 active:bg-slate-300 active:border-slate-300 active:ring active:ring-slate-100 dark:bg-zink-600 dark:hover:bg-zink-500 dark:border-zink-600 dark:hover:border-zink-500 dark:text-zink-200 dark:ring-zink-400/50">
                                                                        Cancelado
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        @elseif($vacacion->estado === 'pendiente')
                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                                <div class="remove">

                                                                    <button data-modal-target="rejectModal"
                                                                        id="delete-record"
                                                                        class="py-1 text-xs text-white bg-red-500 border-red-500 btn hover:text-white hover:bg-red-600 hover:border-red-600 focus:text-white focus:bg-red-600 focus:border-red-600 focus:ring focus:ring-red-100 active:text-white active:bg-red-600 active:border-red-600 active:ring active:ring-red-100 dark:ring-custom-400/20 remove-item-btn"
                                                                        onclick="showRejectModal({{ $vacacion->id }})">Cancelar</button>

                                                                </div>
                                                            </td>
                                                        @else
                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">

                                                            </td>
                                                        @endif

                                                        <!-- Fecha del Permiso -->
                                                        <td
                                                            class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                            @switch($vacacion->estado)
                                                                @case('aprobado')
                                                                    <span
                                                                        class="px-2.5 py-0.5 inline-block text-xs font-medium rounded border bg-green-100 border-green-200 text-green-500 dark:bg-green-500/20 dark:border-green-500/20">
                                                                        Aprobado
                                                                    </span>
                                                                @break

                                                                @case('pendiente')
                                                                    <span
                                                                        class="px-2.5 py-0.5 inline-block text-xs font-medium rounded border bg-yellow-100 border-yellow-200 text-yellow-500 dark:bg-yellow-500/20 dark:border-yellow-500/20">
                                                                        Pendiente
                                                                    </span>
                                                                @break

                                                                @case('rechazado')
                                                                    <span
                                                                        class="px-2.5 py-0.5 inline-block text-xs font-medium rounded border bg-red-100 border-red-200 text-red-500 dark:bg-red-500/20 dark:border-red-500/20">
                                                                        Rechazado
                                                                    </span>
                                                                @break

                                                                @case('cancelado')
                                                                    <span
                                                                        class="px-2.5 py-0.5 inline-block text-xs font-medium rounded border bg-purple-100 border-purple-200 text-purple-500 dark:bg-purple-500/20 dark:border-purple-500/20">
                                                                        Cancelado
                                                                    </span>
                                                                @break

                                                                @default
                                                                    <span
                                                                        class="px-2.5 py-0.5 inline-block text-xs font-medium rounded border bg-gray-100 border-gray-200 text-gray-500 dark:bg-gray-500/20 dark:border-gray-500/20">
                                                                        {{ $vacacion->estado }}
                                                                    </span>
                                                            @endswitch
                                                        </td>

                                                        <!-- ID de la Persona -->
                                                        <td
                                                            class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                            {{ $vacacion->fecha_inicio }}
                                                        </td>

                                                        <td
                                                            class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                            {{ $vacacion->fecha_fin }}
                                                        </td>
                                                        <td
                                                        class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                        {{ $vacacion->total_dias }}
                                                    </td>


                                                        <td
                                                            class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                            {{ $vacacion->observacion }}
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-gray-50">
                                                <tr
                                                    class="bg-gray-100 text-left text-gray-600 text-sm font-medium uppercase">
                                                <tr
                                                    class="bg-gray-100 text-left text-gray-600 text-sm font-medium uppercase">
                                                    <th class="py-3 px-4 border-b border-gray-300">Acciones</th>
                                                    <th class="py-3 px-4 border-b border-gray-300">Estado</th>
                                                    <th class="py-3 px-4 border-b border-gray-300">Fecha Inicio</th>
                                                    <th class="py-3 px-4 border-b border-gray-300">Fecha Fin</th>
                                                    <th class="py-3 px-4 border-b border-gray-300">Canitdad dias</th>
                                                    
                                                    <th class="py-3 px-4 border-b border-gray-300">Observaciones</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>
                </div>
            </div>






        </div>





    </div>
    <!-- container-fluid -->
    </div>
    <!-- End Page-wrapper -->





    {{-- create modal --}}

    <!-- Modal para crear vacaciones -->
    <div id="vacacionModal" modal-center=""
        class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
        <div class="w-screen md:w-[40rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full">
            <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
                <h5 class="text-16">Solicitar Vacación</h5>
                <button data-modal-close="vacacionModal" type="reset" form="modalFormVacacion"
                    class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                    <i data-lucide="x" class="size-5"></i>
                </button>
            </div>
            <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
                <form id="modalFormVacacion" action="{{ route('solicitar-vacacion.store') }}" method="POST">
                    @csrf
                
                    <div class="mt-3 flex flex-wrap items-center gap-4">
                        <!-- Fecha de inicio -->
                        <div class="flex-1">
                            <label for="fecha_inicio" class="inline-block mb-2 text-base font-medium">
                                Fecha Inicio <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="fecha_inicio" name="fecha_inicio"
                                value="{{ old('request_method') === 'POST' ? old('fecha_inicio') : '' }}"
                                class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('fecha_inicio')) border-red-500 @endif"
                                data-provider="flatpickr" data-date-format="d M Y" data-week-number="" readonly="readonly"
                                placeholder="Seleccione fecha inicio">
                            @if (old('request_method') === 'POST')
                                @error('fecha_inicio')
                                    <div id="fecha_inicio-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                
                        <!-- Fecha fin -->
                        <div class="flex-1">
                            <label for="fecha_fin" class="inline-block mb-2 text-base font-medium">
                                Fecha Fin <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="fecha_fin" name="fecha_fin"
                                value="{{ old('request_method') === 'POST' ? old('fecha_fin') : '' }}"
                                class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('fecha_fin')) border-red-500 @endif"
                                data-provider="flatpickr" data-date-format="d M Y" data-week-number="" readonly="readonly"
                                placeholder="Seleccione fecha fin">
                            @if (old('request_method') === 'POST')
                                @error('fecha_fin')
                                    <div id="fecha_fin-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>
                
                    
                </form>
                

            </div>
            <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">
                <button type="reset" form="modalFormVacacion" data-modal-close="vacacionModal"
                    class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">
                    <i data-lucide="x" class="inline-block size-4"></i> <span class="align-middle">Cancelar</span>
                </button>
                <button type="submit" form="modalFormVacacion"
                    class="ml-2 text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                    Guardar
                </button>
            </div>
        </div>
    </div>


    <!-- Modal de Cancelar -->

    <div id="rejectModal" modal-center=""
        class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4">
        <div class="w-screen lg:w-[40rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full">
            <!-- Encabezado del Modal -->
            <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
                <div class="flex items-center">
                    <h5 class="text-16">Cancelar vacación</h5>
                </div>
                <button data-modal-close="rejectModal"
                    class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                    <i data-lucide="x" class="size-5"></i>
                </button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
                <form id="rejectForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-zink-200">
                            ¿Estás seguro que deseas cancelar este permiso?
                        </p>
                    </div>
                </form>
            </div>

            <!-- Pie del Modal -->
            <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">
                <button type="button" data-modal-close="rejectModal"
                    class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">
                    <i data-lucide="x" class="inline-block size-4"></i> <span class="align-middle">Cancelar</span>
                </button>

                <button type="submit" form="rejectForm"
                    class="ml-2 text-white btn bg-red-500 border-red-500 hover:text-white hover:bg-red-600 hover:border-red-600 focus:text-white focus:bg-red-600 focus:border-red-600 focus:ring focus:ring-red-100 active:text-white active:bg-red-600 active:border-red-600 active:ring active:ring-red-100 dark:ring-red-400/20">
                    <i data-lucide="check-circle" class="inline-block size-4"></i> <span class="align-middle">Confirmar
                        Rechazo</span>
                </button>
            </div>
        </div>
    </div>
@endsection
