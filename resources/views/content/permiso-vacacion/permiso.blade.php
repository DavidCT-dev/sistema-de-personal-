@extends('layouts.master')

@section('script')

    <script src="{{ asset('assets/js/datatables/vfs_fonts.js') }}"></script>
       <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr.localize(flatpickr.l10ns.es);

            // Inicializar el Timepicker en cada campo de entrada y salida
            flatpickr(
                "#inicioMedioDia , #finMedioDia", {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i", // Usa "h" para el formato de 12 horas y "K" para AM/PM
                    time_24hr: true
                });


                flatpickr("#fechaPermiso,#fecha_fin_varios_dias", {
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
                dom: 'Bfrtip',
                buttons: [


                    {
                        extend: 'excel',
                        className: 'dt-button-custom bg-green-500 text-white hover:bg-green-600 focus:ring-2 focus:ring-green-300 rounded-lg px-4 py-2 shadow-md',
                        text: '<i class="ri-file-excel-2-fill"></i> Exportar EXCEL',
                        title: 'Nombre del Reporte',
                        exportOptions: {
                            columns: function(idx, data, node) {
                                return idx !== 0; // Excluir la columna con índice 0
                            }
                        },
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'dt-button-custom bg-red-500 text-white hover:bg-red-600 focus:ring-2 focus:ring-red-300 rounded-lg px-4 py-2 shadow-md',
                        title: 'Nombre del Reporte',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        text: '<i class="ri-file-pdf-2-fill mr-2"></i> Exportar PDF',
                        exportOptions: {
                            columns: function(idx, data, node) {
                                return idx !== 0; // Excluir la columna con índice 0
                            }
                        },
                        customize: function(doc) {
                            // Ajuste de márgenes
                            doc.pageMargins = [20, 20, 20, 20];

                            // Ajuste de fuentes y tamaños
                            doc.defaultStyle.fontSize = 9; // Reduce el tamaño de la fuente
                            doc.styles.tableHeader.fontSize =
                                10; // Tamaño de fuente del encabezado
                            doc.styles.title = {
                                fontSize: 12,
                                alignment: 'center',
                            };

                            // Ajusta el ancho de todas las columnas de manera proporcional
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                    .length)
                                .fill('auto');

                            // Reducir la altura de las filas
                            doc.content[1].table.body.forEach(function(row) {
                                row.forEach(function(cell) {
                                    cell.margin = [2, 2, 2,
                                        2
                                    ]; // Márgenes internos
                                });
                            });

                            // Forzar contenido en una sola página
                            doc.content[1].layout = {
                                hLineWidth: function() {
                                    return 0.5;
                                },
                                vLineWidth: function() {
                                    return 0.5;
                                },
                                paddingLeft: function() {
                                    return 4;
                                },
                                paddingRight: function() {
                                    return 4;
                                },
                                paddingTop: function() {
                                    return 4;
                                },
                                paddingBottom: function() {
                                    return 4;
                                }
                            };
                        },
                        action: function(e, dt, button, config) {
                            lucide.createIcons();
                            $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button,
                                config);
                        }
                    }
                ],


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
        document.querySelectorAll('input[name="duracion_permiso"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const isMedioDia = this.value === 'medio_dia';
                const isVariosDias = this.value === 'varios_dias';

                // Mostrar u ocultar los contenedores
                document.getElementById('horariosMedioDia').classList.toggle('hidden', !isMedioDia);
                document.getElementById('fechasVariosDias').classList.toggle('hidden', !isVariosDias);

                // Manejar los atributos "name" de los campos
                const inicioMedioDia = document.getElementById('inicioMedioDia');
                const finMedioDia = document.getElementById('finMedioDia');
                const fechaFinVariosDias = document.getElementById('fecha_fin_varios_dias');

                if (isMedioDia) {
                    inicioMedioDia.setAttribute('name', 'hora_inicio');
                    finMedioDia.setAttribute('name', 'hora_fin');
                    fechaFinVariosDias.removeAttribute('name');
                } else if (isVariosDias) {
                    fechaFinVariosDias.setAttribute('name', 'fecha_fin_varios_dias');
                    inicioMedioDia.removeAttribute('name');
                    finMedioDia.removeAttribute('name');
                } else {
                    // Si es "Día completo", eliminar los atributos "name" de los otros campos
                    inicioMedioDia.removeAttribute('name');
                    finMedioDia.removeAttribute('name');
                    fechaFinVariosDias.removeAttribute('name');
                }
            });

            // Ejecutar el cambio inicial si hay un valor seleccionado al cargar la página
            if (radio.checked) {
                radio.dispatchEvent(new Event('change'));
            }
        });
    </script>
    <script>
        document.querySelectorAll('input[name="duracion_permiso"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const isMedioDia = this.value === 'medio_dia';
                const isVariosDias = this.value === 'varios_dias';

                // Mostrar u ocultar los contenedores
                document.getElementById('horariosMedioDiaUpdate').classList.toggle('hidden', !isMedioDia);
                document.getElementById('fechasVariosDiasUpdate').classList.toggle('hidden', !isVariosDias);

                // Manejar los atributos "name" de los campos
                const inicioMedioDia = document.getElementById('hora_inicioUpdate');
                const finMedioDia = document.getElementById('hora_finUpdate');
                const fechaFinVariosDias = document.getElementById('fecha_fin_varios_diasUpdate');

                if (isMedioDia) {
                    inicioMedioDia.setAttribute('name', 'hora_inicio');
                    finMedioDia.setAttribute('name', 'hora_fin');
                    fechaFinVariosDias.removeAttribute('name');
                } else if (isVariosDias) {
                    fechaFinVariosDias.setAttribute('name', 'fecha_fin_varios_dias');
                    inicioMedioDia.removeAttribute('name');
                    finMedioDia.removeAttribute('name');
                } else {
                    // Si es "Día completo", eliminar los atributos "name" de los otros campos
                    inicioMedioDia.removeAttribute('name');
                    finMedioDia.removeAttribute('name');
                    fechaFinVariosDias.removeAttribute('name');
                }
            });

            // Ejecutar el cambio inicial si hay un valor seleccionado al cargar la página
            if (radio.checked) {
                radio.dispatchEvent(new Event('change'));
            }
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr.localize(flatpickr.l10ns.es);

            // Inicializar el Timepicker en cada campo de entrada y salida
            flatpickr(
                "#inicio, #fin", {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true
                });


        });
    </script>


    <script>
        function showRejectModal(permisoId) {
            const form = document.getElementById('rejectForm');

            // Configurar la acción del formulario con la ruta nombrada
            form.action = `/solicitar-permisos/${permisoId}`;

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
                    <h5 class="text-16">Permisos</h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li
                        class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="#" class="text-slate-400 dark:text-zink-200">Solicitar</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        permiso
                    </li>
                </ul>

            </div>

            <button data-modal-target="largeModal" type="button" id="extraLargeModalButton"
                class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:focus:ring-custom-400/20">Solicitar
                Permiso </button>


            <div class="grid grid-cols-12 gap-4 mt-2">

                <!-- Sección de 8 columnas -->
                <div class="col-span-12 md:col-span-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="overflow-x-auto">
                                <table id="rowSelectionDeletion" class="display" style="width:100%">
                                    <thead>
                                        <tr class="bg-gray-100 text-left text-gray-600 text-sm font-medium uppercase">
                                            <th class="py-3 px-4 border-b border-gray-300">Acciones</th>
                                            <th class="py-3 px-4 border-b border-gray-300">Estado</th>

                                            <th class="py-3 px-4 border-b border-gray-300">Fecha</th>

                                            <th class="py-3 px-4 border-b border-gray-300">Motivo</th>
                                            <th class="py-3 px-4 border-b border-gray-300">Observación</th>
                                            <th class="py-3 px-4 border-b border-gray-300">Duración Permiso</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($permisos as $permiso)
                                            <tr>
                                                <!-- Acciones (Editar / Dar Baja) -->

                                                @if ($permiso->estado === 'rechazado')
                                                    <td
                                                        class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">


                                                    </td>
                                                @elseif($permiso->estado === 'cancelado')
                                                    <td
                                                        class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                        <div class="remove">
                                                            <button type="button"
                                                                class="py-1 text-xs text-slate-500 bg-slate-200 border-slate-200 btn hover:text-slate-600 hover:bg-slate-300 hover:border-slate-300 focus:text-slate-600 focus:bg-slate-300 focus:border-slate-300 focus:ring focus:ring-slate-100 active:text-slate-600 active:bg-slate-300 active:border-slate-300 active:ring active:ring-slate-100 dark:bg-zink-600 dark:hover:bg-zink-500 dark:border-zink-600 dark:hover:border-zink-500 dark:text-zink-200 dark:ring-zink-400/50">
                                                                Cancelado
                                                            </button>
                                                        </div>
                                                    </td>
                                                @elseif($permiso->estado === 'pendiente')
                                                    <td
                                                        class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                        <div class="remove">

                                                            <button data-modal-target="rejectModal" id="delete-record"
                                                                class="py-1 text-xs text-white bg-red-500 border-red-500 btn hover:text-white hover:bg-red-600 hover:border-red-600 focus:text-white focus:bg-red-600 focus:border-red-600 focus:ring focus:ring-red-100 active:text-white active:bg-red-600 active:border-red-600 active:ring active:ring-red-100 dark:ring-custom-400/20 remove-item-btn"
                                                                onclick="showRejectModal({{ $permiso->id }})">Cancelar</button>

                                                        </div>
                                                    </td>
                                                @else
                                                    <td
                                                        class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">

                                                    </td>
                                                @endif



                                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                    @switch($permiso->estado)
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
                                                                {{ $permiso->estado }}
                                                            </span>
                                                    @endswitch
                                                </td>

                                                <!-- Fecha del Permiso -->
                                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                    {{ $permiso->fecha_permiso }}
                                                </td>



                                                <!-- Motivo -->
                                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                    {{ $permiso->tipoPermiso->descripcion }}
                                                </td>


                                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                    {{ $permiso->observacion }}
                                                </td>


                                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                    {{ $permiso->duracion_permiso }}
                                                    @if ($permiso->fecha_fin_varios_dias)
                                                        <br><small><strong>Fecha Fin:</strong>
                                                            {{ $permiso->fecha_fin_varios_dias }}</small>
                                                    @endif
                                                    @if ($permiso->hora_inicio && $permiso->hora_fin)
                                                        <br><small><strong>Horario:</strong> {{ $permiso->hora_inicio }} -
                                                            {{ $permiso->hora_fin }}</small>
                                                    @endif
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr class="bg-gray-100 text-left text-gray-600 text-sm font-medium uppercase">
                                        <tr class="bg-gray-100 text-left text-gray-600 text-sm font-medium uppercase">
                                            <th class="py-3 px-4 border-b border-gray-300">Acciones</th>
                                            <th class="py-3 px-4 border-b border-gray-300">Estado</th>

                                            <th class="py-3 px-4 border-b border-gray-300">Fecha</th>

                                            <th class="py-3 px-4 border-b border-gray-300">Motivo</th>
                                            <th class="py-3 px-4 border-b border-gray-300">Observación</th>
                                            <th class="py-3 px-4 border-b border-gray-300">Duración Permiso</th>

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





    <!-- Modal de Cancelar -->

    <div id="rejectModal" modal-center=""
        class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4">
        <div class="w-screen lg:w-[40rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full">
            <!-- Encabezado del Modal -->
            <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
                <div class="flex items-center">
                    <h5 class="text-16">Cancelar permiso</h5>
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


    {{-- create modal --}}
    <div id="largeModal" modal-center=""
        class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
        <div class="w-screen lg:w-[55rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full">
            <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
                <h5 class="text-16">Solicitar Permiso</h5>
                <button data-modal-close="largeModal"
                    class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500"><i
                        data-lucide="x" class="size-5"></i></button>
            </div>
            <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
                <form id="modalFormPermiso" action="{{ route('solicitar-permisos.store') }}" method="POST">
                    @csrf

                    <div class="mt-3 flex flex-wrap items-center gap-4">
                        <div class="flex-1">
                            <label for="fechaPermiso" class="inline-block mb-2 text-base font-medium">
                                Fecha del Permiso <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                value="{{ old('request_method') === 'POST' ? old('fecha_permiso') : '' }}"
                                id="fechaPermiso" name="fecha_permiso"
                                class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('fecha_permiso')) border-red-500 @endif"
                                data-provider="flatpickr" data-date-format="d M Y" data-week-number=""
                                readonly="readonly" placeholder="Seleccione Fecha de permiso">
                            {{-- Mensaje de error --}}
                            @if (old('request_method') === 'POST')
                                @error('fecha_permiso')
                                    <div id="tipo-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>


<div class="flex-1">
    <label for="motivoPermiso" class="inline-block mb-2 text-base font-medium">
        Motivo <span class="text-red-500">*</span>
    </label>
    <select id="motivoPermiso" name="motivo"
        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if ($errors->has('motivo')) border-red-500 @endif"
        data-choices data-choices-sorting-false required>
        <option value="" selected disabled>Seleccione Motivo</option>
        @foreach ($motivoPermisos as $motivoP)
            <option value="{{ $motivoP->id }}"
                {{ old('motivo') == $motivoP->id ? 'selected' : '' }}>
                {{ $motivoP->descripcion }}
            </option>
        @endforeach
    </select>
    
    {{-- Mensaje de error --}}
    @error('motivo')
        <div id="motivo-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
    @enderror
</div>

                        {{-- <div class="flex-1">
                            <label for="motivoPermiso" class="inline-block mb-2 text-base font-medium">
                                Motivo <span class="text-red-500">*</span>
                            </label>
                            <input list="feriadosList" id="motivoPermiso" name="motivo" required
                                value="{{ old('request_method') === 'POST' ? old('motivo') : '' }}"
                                class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('motivo')) border-red-500 @endif"
                                placeholder="Seleccione o escriba motivo" />

                            <datalist id="feriadosList">
                                <option value="CUENTA VACACION"></option>
                                <option value="CUENTA HABER"></option>
                                <option value="VACACION"></option>
                                <option value="COMISION INTERNA"></option>
                                <option value="COMISION CON VEATICOS"></option>
                                <option value="BAJA MEDICA"></option>
                                <option value="OTRO MOTIVO"></option>
                                <option value="TOLERANCIA"></option>
                                <option value="COMPENSACION"></option>
                            </datalist>

                            @if (old('request_method') === 'POST')
                                @error('motivo')
                                    <div id="motivo-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            @endif
                        </div> --}}

                    </div>






                    <div class="mt-3">
                        <label for="duracion" class="inline-block mb-2 text-base font-medium">
                            Duración <span class="text-red-500">*</span>
                        </label>

                        <div class="flex flex-col">
                            <!-- Día completo -->
                            <div class="flex items-center">
                                <input type="radio" id="diaCompleto" name="duracion_permiso" value="dia_completo"
                                    {{ old('request_method') === 'POST' && old('duracion_permiso') === 'dia_completo' ? 'checked' : '' }}
                                    required>
                                <label for="diaCompleto" class="ml-2">Día completo</label>
                            </div>

                            <!-- Medio día -->
                            <div class="items-center mt-2">
                                <input type="radio" id="medioDia" name="duracion_permiso" value="medio_dia"
                                    {{ old('request_method') === 'POST' && old('duracion_permiso') === 'medio_dia' ? 'checked' : '' }}>
                                <label for="medioDia" class="ml-2">Medio día</label>

                                <div id="horariosMedioDia"
                                    class="ml-4 {{ old('request_method') === 'POST' && old('duracion_permiso') === 'medio_dia' ? '' : 'hidden' }}">
                                    <div class="flex flex-wrap items-center gap-4">
                                        <div class="flex-1">
                                            <label for="inicioMedioDia" class="inline-block mb-2 text-base font-medium">
                                                Inicio <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="inicioMedioDia" name="hora_inicio"
                                                value="{{ old('request_method') === 'POST' ? old('hora_inicio') : '' }}"
                                                class="form-input w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('hora_inicio')) border-red-500 @endif"
                                                placeholder="Selecciona una hora">
                                            @if (old('request_method') === 'POST')
                                                @error('hora_inicio')
                                                    <div id="inicioMedioDia-error" class="mt-1 text-sm text-red-500">
                                                        {{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <label for="finMedioDia" class="inline-block mb-2 text-base font-medium">
                                                Fin <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="finMedioDia" name="hora_fin"
                                                value="{{ old('request_method') === 'POST' ? old('hora_fin') : '' }}"
                                                class="form-input w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('hora_fin')) border-red-500 @endif"
                                                placeholder="Selecciona una hora">
                                            @if (old('request_method') === 'POST')
                                                @error('hora_fin')
                                                    <div id="finMedioDia-error" class="mt-1 text-sm text-red-500">
                                                        {{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Varios días -->
                            <div class="items-center mt-2">
                                <input type="radio" id="variosDias" name="duracion_permiso" value="varios_dias"
                                    {{ old('request_method') === 'POST' && old('duracion_permiso') === 'varios_dias' ? 'checked' : '' }}>
                                <label for="variosDias" class="ml-2">Varios días</label>

                                <div id="fechasVariosDias"
                                    class="ml-4 {{ old('request_method') === 'POST' && old('duracion_permiso') === 'varios_dias' ? '' : 'hidden' }}">
                                    <div class="flex flex-wrap items-center gap-4">
                                        <div class="flex-1">
                                            <label for="fecha_fin_varios_dias"
                                                class="inline-block mb-2 text-base font-medium">
                                                Fecha Fin <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="fecha_fin_varios_dias" name="fecha_fin_varios_dias"
                                                value="{{ old('request_method') === 'POST' ? old('fecha_fin_varios_dias') : '' }}"
                                                class="form-input w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('fecha_fin_varios_dias')) border-red-500 @endif"
                                                placeholder="Selecciona fecha de retorno" data-provider="flatpickr"
                                                data-date-format="d M Y" {{-- data-date-format="Y-m-d"  --}} {{-- data-alt-format="d M, Y"  --}}
                                                data-alt-format="d M Y" />
                                            @if (old('request_method') === 'POST')
                                                @error('fecha_fin_varios_dias')
                                                    <div id="fecha_fin_varios_dias-error" class="mt-1 text-sm text-red-500">
                                                        {{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Mensaje de error para el campo "dias_permiso" --}}
                        @if (old('request_method') === 'POST')
                            @error('duracion_permiso')
                                <div id="dias_permiso-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>



                </form>




            </div>
            <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">
                <button type="reset" form="modalFormPermiso" data-modal-close="largeModal"
                    class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">
                    <i data-lucide="x" class="inline-block size-4"></i> <span class="align-middle">Cancelar</span>
                </button>

                <button type="submit" form="modalFormPermiso"
                    class="ml-2 text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                    Guardar
                </button>
            </div>
        </div>
    </div>
@endsection
