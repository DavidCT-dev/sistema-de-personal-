@extends('layouts.master')

@section('script')

<script src="{{ asset('assets/js/datatables/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/libs/flatpickr/l10n/es.js') }}"></script>

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
        flatpickr("#fechaInput, #fechaFeriadoUpdate", {
            enableTime: false, // Deshabilitar la selección de tiempo
            dateFormat: "d-m-Y", // Formato de fecha
            time_24hr: true, // Usar formato de 24 horas
            weekNumbers: true, // Mostrar números de la semana
            allowInput: true, // Habilita la escritura manual
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
            buttons: [{
                extend: 'pdfHtml5',
                className: 'mb-2 dt-button-custom bg-red-500 text-white hover:bg-red-600 focus:ring-2 focus:ring-red-300 rounded-lg px-4 py-2 shadow-md',
                title: 'Reporte de Feriados',
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
            }],
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
    function deleteModalFeriado(element) {
        document.getElementById('feriadoId').value = element.dataset.id;
    }

    function fillModal(element) {
        // Asegúrate de que todos los IDs son correctos
        // document.getElementById('id_personaSelectUpdate').value = element.dataset.id_persona;
        console.log(element.dataset)
        const formatearFecha = (fechaStr) => {
            if (!fechaStr) return '';
            const [año, mes, dia] = fechaStr.split('-');
            return `${dia}-${mes}-${año}`;
        }
        const tipoFeriadoSelect = document.getElementById('tipoFeriadoUpdate');
        if (tipoFeriadoSelect) {
            const choicesInstance = tipoFeriadoSelect._choices;
            if (choicesInstance) {
                // Usar la API de Choices para establecer el valor
                choicesInstance.setChoiceByValue(element.dataset.tipo);
            } else {
                // Si Choices.js no está inicializado (fallback)
                tipoFeriadoSelect.value = element.dataset.tipo;
            }
        }
        // document.getElementById('tipoFeriadoUpdate').value = element.dataset.tipo;
        document.getElementById('fechaFeriadoUpdate').value = formatearFecha(element.dataset.fecha);
        document.getElementById('descripcionFeriadoUpdate').value = element.dataset.descripcion;
        document.getElementById('observacionUpdate').value = element.dataset.observacion;
        // Corregir la lógica para el campo de género
        const generoSelectUpdate = document.getElementById('generoSelectUpdate');
        switch (element.dataset.sexo) {
            case 'M':
                generoSelectUpdate.value = 'M'; // Asumiendo que 'Masculino' es el value para M
                break;
            case 'F':
                generoSelectUpdate.value = 'F'; // Asumiendo que 'Femenino' es el value para F
                break;
            case 'T':
                generoSelectUpdate.value = 'T'; // Asumiendo que 'Todos' es el value para T
                break;
            default:
                generoSelectUpdate.value =
                    ''; // Si no coincide, dejamos el valor vacío o asignamos un valor predeterminado
        }
        document.getElementById('inicioUpdate').value = element.dataset.hora_inicio;
        document.getElementById('finUpdate').value = element.dataset.hora_fin;
        // Establecer la acción del formulario con la URL correcta
        const modal = document.querySelector('#modalFormUpdateFeriado');
        modal.action = element.dataset.url;
        // Agregar el input para el método PUT si no existe
        let methodInput = modal.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            modal.appendChild(methodInput);
        }
        // Agregar el token CSRF si no existe
        let csrfInput = modal.querySelector('input[name="_token"]');
        if (!csrfInput) {
            csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
            modal.appendChild(csrfInput);
        }
    }
</script>
@endsection

@section('content')
@if ($errors->hasAny(['tipo', 'fecha', 'descripcion', 'observacion', 'sexo', 'hora_inicio', 'hora_fin']))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const requestMethod = "{{ old('request_method') }}";
        // Abrir el modal correspondiente según el método de solicitud
        if (requestMethod === 'POST') {
            const largeModalButton = document.getElementById(
                'extraLargeModalButton'); // Botón del modal de creación
            if (largeModalButton) {
                largeModalButton.click(); // Simular clic en el botón del modal de creación
            }
        } else if (requestMethod === 'PUT') {
            const updateFeriadoModalButton = document.querySelector(
                '[data-modal-target="updateFeriadoModal"]'); // Botón del modal de actualización
            if (updateFeriadoModalButton) {
                updateFeriadoModalButton.click(); // Simular clic en el botón del modal de actualización
            }
        }
        // Limpiar campos y errores cuando se cierra el modal
        const modals = document.querySelectorAll('[data-modal-close]');
        modals.forEach(modalCloseButton => {
            modalCloseButton.addEventListener('click', function() {
                clearFormAndErrors();
            });
        });
        // También limpiar cuando se cierra el modal automáticamente (por ejemplo, al hacer clic fuera del modal)
        const modalContainers = document.querySelectorAll(
            '.modal-container'); // Ajusta el selector según tu estructura HTML
        modalContainers.forEach(modalContainer => {
            modalContainer.addEventListener('hidden.bs.modal', function() {
                clearFormAndErrors();
            });
        });
    });

    function clearFormAndErrors() {
        // Seleccionar el formulario correcto basado en el método de solicitud
        const requestMethod = "{{ old('request_method') }}";
        let form;
        if (requestMethod === 'POST') {
            form = document.querySelector('#modalFormferiado'); // Formulario del modal de creación
        } else if (requestMethod === 'PUT') {
            form = document.querySelector('#modalFormUpdateFeriado'); // Formulario del modal de actualización
        }
        // Limpiar todos los campos del formulario si existe
        if (form) {
            form.reset(); // Restablecer los valores del formulario
        }
        // Eliminar los mensajes de error visibles generados por Blade
        const errorMessages = document.querySelectorAll('.text-red-500');
        errorMessages.forEach(errorMessage => {
            errorMessage.remove();
        });
        // Quitar las clases de error de los campos
        const errorFields = document.querySelectorAll('.border-red-500');
        errorFields.forEach(field => {
            field.classList.remove('border-red-500');
        });
    }
</script>
@endif
<div
    class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-16">Feriados</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li
                    class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="#" class="text-slate-400 dark:text-zink-200">Inasistencia</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    feriado
                </li>
            </ul>

        </div>

        <div class="flex justify-between items-center space-x-4">
            <!-- Botón "Crear Feriado" (Alineado a la izquierda) -->
            @can('crear_feriado')
              <button data-modal-target="largeModal" type="button" id="extraLargeModalButton"
                class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:focus:ring-custom-400/20">
                Crear Feriado
            </button>  
            @endcan
            

            <!-- Formulario con botón "Crear Feriados Generales" (Alineado a la derecha) -->
            @can('crear_feriados_generales')
               <form action="{{ route('crear-feriados') }}" class="ml-auto">
                <button type="submit"
                    class="text-white bg-purple-500 border-purple-500 btn hover:text-white hover:bg-purple-600 hover:border-purple-600 focus:text-white focus:bg-purple-600 focus:border-purple-600 focus:ring focus:ring-purple-100 active:text-white active:bg-purple-600 active:border-purple-600 active:ring active:ring-purple-100 dark:ring-purple-400/10">
                    Crear Feriados Generales
                </button>
            </form> 
            @endcan
            
        </div>

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
                                        <th class="py-3 px-4 border-b border-gray-300">Fecha</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Observación</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Tipo</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Descripción</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Inicio</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Fin</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Género</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($feriados as $feriado)
                                    <tr>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            <div class="relative dropdown">
                                                <button id="orderAction1" data-bs-toggle="dropdown"
                                                    class="flex items-center justify-center size-[30px] dropdown-toggle p-0 text-slate-500 btn bg-slate-100 hover:text-white hover:bg-slate-600 focus:text-white focus:bg-slate-600 focus:ring focus:ring-slate-100 active:text-white active:bg-slate-600 active:ring active:ring-slate-100 dark:bg-slate-500/20 dark:text-slate-400 dark:hover:bg-slate-500 dark:hover:text-white dark:focus:bg-slate-500 dark:focus:text-white dark:active:bg-slate-500 dark:active:text-white dark:ring-slate-400/20"><i
                                                        data-lucide="more-horizontal" class="size-3"></i></button>
                                                <ul class="absolute z-50 hidden py-2 mt-1 ltr:text-left rtl:text-right list-none bg-white rounded-md shadow-md dropdown-menu min-w-[10rem] dark:bg-zink-600"
                                                    aria-labelledby="orderAction1">

                                                    <li>
                                                        <a data-modal-target="updateFeriadoModal"
                                                            data-fecha="{{ $feriado->fecha }}"
                                                            data-descripcion="{{ $feriado->descripcion }}"
                                                            data-observacion="{{ $feriado->observacion }}"
                                                            data-hora_inicio="{{ $feriado->hora_inicio }}"
                                                            data-hora_fin="{{ $feriado->hora_fin }}"
                                                            data-tipo="{{ $feriado->motivo_feriado_id }}"
                                                            data-sexo="{{ $feriado->sexo }}"
                                                            data-url="{{ route('feriados.update', ['feriado' => $feriado->id]) }}"
                                                            class="block px-4 py-1.5 text-base transition-all duration-200 ease-linear text-slate-600 dropdown-item hover:bg-slate-100 hover:text-slate-500 focus:bg-slate-100 focus:text-slate-500 dark:text-zink-100 dark:hover:bg-zink-500 dark:hover:text-zink-200 dark:focus:bg-zink-500 dark:focus:text-zink-200"
                                                            onclick="fillModal(this)">
                                                            <i data-lucide="file-edit"
                                                                class="inline-block size-3 ltr:mr-1 rtl:ml-1"></i>
                                                            <span class="align-middle">Editar</span>
                                                        </a>

                                                    </li>
                                                    <li>
                                                        <a id="deleteButton" data-id="{{ $feriado->id }}"
                                                            data-modal-target="deleteModal"
                                                            class="block px-4 py-1.5 text-base transition-all duration-200 ease-linear text-slate-600 dropdown-item hover:bg-slate-100 hover:text-slate-500 focus:bg-slate-100 focus:text-slate-500 dark:text-zink-100 dark:hover:bg-zink-500 dark:hover:text-zink-200 dark:focus:bg-zink-500 dark:focus:text-zink-200"
                                                            href="#!" onclick="deleteModalFeriado(this)"><i
                                                                data-lucide="trash-2"
                                                                class="inline-block size-3 ltr:mr-1 rtl:ml-1"></i>
                                                            <span class="align-middle">Dar Baja</span></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $feriado->fecha }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $feriado->observacion }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $feriado->tipoFeriado->descripcion }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $feriado->descripcion }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $feriado->hora_inicio }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $feriado->hora_fin }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $feriado->sexo == 'M' ? 'Masculino' : ($feriado->sexo == 'F' ? 'Femenino' : 'Todos') }}
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr class="bg-gray-100 text-left text-gray-600 text-sm font-medium uppercase">
                                    <tr class="bg-gray-100 text-left text-gray-600 text-sm font-medium uppercase">
                                        <th class="py-3 px-4 border-b border-gray-300">Acciones</th>

                                        <th class="py-3 px-4 border-b border-gray-300">Fecha</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Observación</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Descripción</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Tipo</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Inicio</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Fin</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Género</th>

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

{{-- create modal --}}
<div id="largeModal" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div class="w-screen xl:w-[55rem] lg:w-[55rem]  md:w-[40rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col" style="max-height: 90vh;"> <!-- Reduje el ancho a 35rem -->
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-16">Días Festivos</h5>
            <button data-modal-close="largeModal" type="reset" form="modalFormferiado"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500"><i
                    data-lucide="x" class="size-5"></i></button>
        </div>
        <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
            <form id="modalFormferiado" action="{{ route('feriados.store') }}" method="POST">
                @csrf

                <div class="mt-3 flex flex-wrap items-center gap-4">
                    {{-- <div class="flex-1">
                            <label for="tipoInput" class="inline-block mb-2 text-base font-medium">
                                Tipo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="tipoInput" name="tipo" required
                                value="{{ old('request_method') === 'POST' ? old('tipo') : '' }}"
                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500
                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300
                    dark:disabled:border-zink-500
                    dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700
                    dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200
                    @if (old('request_method') === 'POST' && $errors->has('tipo')) border-red-500 @endif"
                    placeholder="Ingrese el tipo">
                    @if (old('request_method') === 'POST')
                    @error('tipo')
                    <div id="tipo-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                    @endif
                </div> --}}

                <div class="flex-1">
                    <label for="tipoInput" class="inline-block mb-2 text-base font-medium">
                        Tipo de Feriado <span class="text-red-500">*</span>
                    </label>
                    <select id="tipoInput" name="tipo"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if($errors->has('tipo')) border-red-500 @endif"
                        data-choices data-choices-sorting-false>
                        <option value="" selected disabled>Seleccione Motivo</option>
                        @foreach ($motivoFeriados as $motivoF)
                        <option value="{{ $motivoF->id }}" {{ old('tipo') == $motivoF->id ? 'selected' : '' }}>
                            {{ $motivoF->descripcion }}
                        </option>
                        @endforeach
                    </select>

                    @error('tipo')
                    <div id="tipo-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex-1">
                    <label for="fechaInput" class="inline-block mb-2 text-base font-medium">
                        Fecha del Feriado <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="fechaInput" name="fecha" required
                        value="{{ old('request_method') === 'POST' ? old('fecha') : '' }}"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                           disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                           dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                           dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                           @if (old('request_method') === 'POST' && $errors->has('fecha')) border-red-500 @endif" data-provider="flatpickr" data-date-format="d M Y"
                        {{-- data-date-format="Y-m-d"  --}} {{-- data-alt-format="d M, Y"  --}} placeholder="DD-MM-YYYY"
                        data-alt-format="d-m-Y" {{-- data-multiple-date="true" --}}>
                    {{-- Mensaje de error --}}
                    @if (old('request_method') === 'POST')
                    @error('fecha')
                    <div id="fecha-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                    @endif
                </div>

        </div>

        <div class="mt-3 flex flex-wrap items-center gap-4">
            <div class="flex-1">
                <label for="descripcionInput" class="inline-block mb-2 text-base font-medium">
                    Descripción Día Feriado <span class="text-red-500">*</span>
                </label>
                <input type="text" id="descripcionInput" name="descripcion" required
                    value="{{ old('request_method') === 'POST' ? old('descripcion') : '' }}"
                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                           disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                           dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                           dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                           @if (old('request_method') === 'POST' && $errors->has('descripcion')) border-red-500 @endif" placeholder="Ingrese la descripción">
                {{-- Mensaje de error --}}
                @if (old('request_method') === 'POST')
                @error('descripcion')
                <div id="descripcion-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                @enderror
                @endif
            </div>
            <div class="flex-1">

                <label for="observacionInput" class="inline-block mb-2 text-base font-medium">
                    Observación <span class="text-red-500">*</span>
                </label>
                <input type="text" id="observacionInput" name="observacion" required
                    value="{{ old('request_method') === 'POST' ? old('observacion') : '' }}"
                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                           disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                           dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                           dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                           @if (old('request_method') === 'POST' && $errors->has('observacion')) border-red-500 @endif" placeholder="Ingrese la observación">
                {{-- Mensaje de error --}}
                @if (old('request_method') === 'POST')
                @error('observacion')
                <div id="observacion-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                @enderror
                @endif
            </div>

        </div>

        <div class="mt-3 flex flex-wrap items-center gap-4">

            <div class="flex-1">
                <label for="generoSelect" class="inline-block mb-2 text-base font-medium">
                    Género <span class="text-red-500">*</span>
                </label>
                <select id="generoSelect" name="sexo" required
                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                           disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                           dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                           dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                           @if (old('request_method') === 'POST' && $errors->has('sexo')) border-red-500 @endif">
                    <option value="" disabled {{ !old('sexo') ? 'selected' : '' }}>
                        Seleccione Género
                    </option>
                    <option value="T" {{ old('request_method') === 'POST' && old('sexo') == 'T' ? 'selected' : '' }}>
                        Todos
                    </option>
                    <option value="M" {{ old('request_method') === 'POST' && old('sexo') == 'M' ? 'selected' : '' }}>
                        Masculino
                    </option>
                    <option value="F" {{ old('request_method') === 'POST' && old('sexo') == 'F' ? 'selected' : '' }}>
                        Femenino
                    </option>
                </select>
                {{-- Mensaje de error --}}
                @if (old('request_method') === 'POST')
                @error('sexo')
                <div id="sexo-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                @enderror
                @endif
            </div>
            <div class="flex-1">
                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <!-- Inicio -->
                    <div class="flex-1">
                        <label for="inicio" class="inline-block mb-2 text-base font-medium">
                            Inicio <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="inicio" name="hora_inicio" required
                            value="{{ old('request_method') === 'POST' ? old('hora_inicio') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                       dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                       dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'POST' && $errors->has('hora_inicio')) border-red-500 @endif">
                        {{-- Mensaje de error --}}
                        @if (old('request_method') === 'POST')
                        @error('hora_inicio')
                        <div id="horaInicio-error" class="mt-1 text-sm text-red-500">{{ $message }}
                        </div>
                        @enderror
                        @endif
                    </div>

                    <!-- Fin -->
                    <div class="flex-1">
                        <label for="fin" class="inline-block mb-2 text-base font-medium">
                            Fin <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="fin" name="hora_fin" required
                            value="{{ old('request_method') === 'POST' ? old('hora_fin') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                       dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                       dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'POST' && $errors->has('hora_fin')) border-red-500 @endif" data-provider="flatpickr" data-date-format="HH:mm">
                        {{-- Mensaje de error --}}
                        @if (old('request_method') === 'POST')
                        @error('hora_fin')
                        <div id="horaFin-error" class="mt-1 text-sm text-red-500">{{ $message }}
                        </div>
                        @enderror
                        @endif
                    </div>
                </div>
            </div>

        </div>

        </form>

    </div>
    <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">
        <button type="reset" form="modalFormferiado" data-modal-close="largeModal"
            class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">
            <i data-lucide="x" class="inline-block size-4"></i> <span class="align-middle">Cancelar</span>
        </button>

        <button type="submit" form="modalFormferiado"
            class="ml-2 text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
            Guardar
        </button>
    </div>
</div>
</div>

{{-- modal update --}}
<div id="updateFeriadoModal" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div class="w-screen lg:w-[55rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full">
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-16">Actualizar Feriado</h5>
            <button data-modal-close="updateFeriadoModal"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                <i data-lucide="x" class="size-5"></i>
            </button>
        </div>
        <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
            <form id="modalFormUpdateFeriado" method="POST">
                @csrf
                @method('PUT')

                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <!-- Tipo -->

                    <div class="flex-1">
                        <label for="tipoFeriadoUpdate" class="inline-block mb-2 text-base font-medium">
                            Tipo de Feriado <span class="text-red-500">*</span>
                        </label>
                        <select id="tipoFeriadoUpdate" name="tipo"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if($errors->has('tipo')) border-red-500 @endif"
                            data-choices data-choices-sorting-false>
                            <option value="" selected disabled>Seleccione Motivo</option>
                            @foreach ($motivoFeriados as $motivoF)
                            <option value="{{ $motivoF->id }}" {{ old('tipo') == $motivoF->id ? 'selected' : '' }}>
                                {{ $motivoF->descripcion }}
                            </option>
                            @endforeach
                        </select>

                        @error('tipo')
                        <div id="tipo-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- <div class="flex-1">
                                <label for="tipoFeriadoUpdate" class="inline-block mb-2 text-base font-medium">
                                    Tipo <span class="text-red-500">*</span>
                                </label>
                                <input list="tipoFeriadoList" id="tipoFeriadoUpdate" name="tipo" required
                                    value="{{ old('request_method') === 'PUT' ? old('tipo') : '' }}"
                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500
                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300
                    dark:disabled:border-zink-500
                    dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700
                    dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200
                    @if (old('request_method') === 'PUT' && $errors->has('tipo')) border-red-500 @endif"
                    placeholder="Seleccione o escriba un tipo" />
                    <datalist id="tipoFeriadoList">
                        <option value="FERIADO"></option>
                        <option value="PARO"></option>
                        <option value="BLOQUEO"></option>
                        <option value="RECESO"></option>
                        <option value="TOLERANCIA"></option>
                        <option value="HORARIO CONTINUO"></option>
                    </datalist>
                    @if (old('request_method') === 'PUT')
                    @error('tipo')
                    <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                    @endif
                </div> --}}

                <!-- Fecha del Feriado -->
                <div class="flex-1">
                    <label for="fechaFeriadoUpdate" class="inline-block mb-2 text-base font-medium">
                        Fecha del Feriado <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="fechaFeriadoUpdate" name="fecha" required
                        value="{{ old('request_method') === 'PUT' ? old('fecha') : '' }}"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                           disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                           dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                           dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                           @if (old('request_method') === 'PUT' && $errors->has('fecha')) border-red-500 @endif" data-provider="flatpickr" data-date-format="Y-m-d"
                        data-alt-format="d M, Y" readonly="readonly" placeholder="Seleccione Fecha">
                    {{-- Mensaje de error --}}
                    @if (old('request_method') === 'PUT')
                    @error('fecha')
                    <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                    @endif
                </div>
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-4">
            <!-- Descripción Día Feriado -->
            <div class="flex-1">
                <label for="descripcionFeriadoUpdate" class="inline-block mb-2 text-base font-medium">
                    Descripción Día Feriado <span class="text-red-500">*</span>
                </label>
                <textarea list="feriadosList" id="descripcionFeriadoUpdate" name="descripcion" required
                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                           disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                           dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                           dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                           @if (old('request_method') === 'PUT' && $errors->has('descripcion')) border-red-500 @endif"
                    placeholder="Seleccione o escriba una descripción">{{ old('request_method') === 'PUT' ? old('descripcion') : '' }}</textarea>
                <datalist id="feriadosList">
                    <option value="AÑO NUEVO"></option>
                    <option value="NAVIDAD"></option>
                    <option value="LUNES DE CARNAVAL"></option>
                    <option value="MARTES DE CARNAVAL"></option>
                </datalist>
                {{-- Mensaje de error --}}
                @if (old('request_method') === 'PUT')
                @error('descripcion')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
                @enderror
                @endif
            </div>

            <!-- Observación -->
            <div class="flex-1">
                <label for="observacionUpdate" class="inline-block mb-2 text-base font-medium">
                    Observación <span class="text-red-500">*</span>
                </label>
                <textarea type="text" id="observacionUpdate" name="observacion" required
                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                           disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                           dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                           dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                           @if (old('request_method') === 'PUT' && $errors->has('observacion')) border-red-500 @endif"
                    placeholder="Ingrese observación">{{ old('request_method') === 'PUT' ? old('observacion') : '' }}</textarea>
                {{-- Mensaje de error --}}
                @if (old('request_method') === 'PUT')
                @error('observacion')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
                @enderror
                @endif
            </div>
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-4">
            <!-- Género -->
            <div class="flex-1">
                <label for="generoSelectUpdate" class="inline-block mb-2 text-base font-medium">
                    Género <span class="text-red-500">*</span>
                </label>
                <select id="generoSelectUpdate" name="sexo" required
                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                           disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                           dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                           dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                           @if (old('request_method') === 'PUT' && $errors->has('sexo')) border-red-500 @endif">
                    <option value="" disabled {{ !old('sexo') ? 'selected' : '' }}>Seleccione Género</option>
                    <option value="T" {{ old('request_method') === 'PUT' && old('sexo') == 'T' ? 'selected' :  ''}}>
                        Todos
                    </option>
                    <option value="M" {{ old('request_method') === 'PUT' && old('sexo') == 'M' ? 'selected' : '' }}>
                        Masculino
                    </option>
                    <option value="F" {{ old('request_method') === 'PUT' && old('sexo') == 'F' ? 'selected' :  '' }}>
                        Femenino
                    </option>
                </select>
                {{-- Mensaje de error --}}
                @if (old('request_method') === 'PUT')
                @error('sexo')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
                @enderror
                @endif
            </div>

            <!-- Inicio -->
            <div class="flex-1">
                <label for="inicioUpdate" class="inline-block mb-2 text-base font-medium">
                    Inicio <span class="text-red-500">*</span>
                </label>
                <input type="time" id="inicioUpdate" name="hora_inicio" required
                    value="{{ old('request_method') === 'PUT' ? old('hora_inicio') : '' }}"
                    class="form-input w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                           disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                           dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                           dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                           @if (old('request_method') === 'PUT' && $errors->has('hora_inicio')) border-red-500 @endif" placeholder="Seleccione hora">
                {{-- Mensaje de error --}}
                @if (old('request_method') === 'PUT')
                @error('hora_inicio')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
                @enderror
                @endif
            </div>

            <!-- Fin -->
            <div class="flex-1">
                <label for="finUpdate" class="inline-block mb-2 text-base font-medium">
                    Fin <span class="text-red-500">*</span>
                </label>
                <input type="time" id="finUpdate" name="hora_fin" required
                    value="{{ old('request_method') === 'PUT' ? old('hora_fin') : '' }}"
                    class="form-input w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                           disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                           dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                           dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                           @if (old('request_method') === 'PUT' && $errors->has('hora_fin')) border-red-500 @endif" placeholder="Seleccione hora">
                {{-- Mensaje de error --}}
                @if (old('request_method') === 'PUT')
                @error('hora_fin')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
                @enderror
                @endif
            </div>
        </div>

        </form>
    </div>
    <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">
        <button type="reset" form="modalFormUpdateFeriado" data-modal-close="updateFeriadoModal"
            class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">
            <i data-lucide="x" class="inline-block size-4"></i> <span class="align-middle">Cancelar</span>
        </button>

        <button type="submit" form="modalFormUpdateFeriado"
            class="ml-2 text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
            Actualizar
        </button>
    </div>
</div>
</div>

<!--delete modal-->
<div id="deleteModal" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div class="w-screen md:w-[25rem] bg-white shadow rounded-md dark:bg-zink-600">
        <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto px-6 py-8">
            <div class="float-right">
                <button data-modal-close="deleteModal"
                    class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500"><i data-lucide="x"
                        class="w-5 h-5"></i></button>
            </div>
            <img src="data:image/png;base64,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"
                alt="" class="block h-12 mx-auto">
            <div class="mt-5 text-center">
                <h5 class="mb-1">Estas Seguro?</h5>
                <p class="text-slate-500 dark:text-zink-200">Estas seguro de Eliminar?</p>
                <div class="flex justify-center gap-2 mt-6">

                    @if (isset($feriado->id))
                    <form action="{{ route('feriados.destroy', $feriado->id) }}" method="POST" class="inline mt-6">
                        @csrf
                        @method('DELETE')
                        <button type="reset" data-modal-close="deleteModal"
                            class="bg-white text-slate-500 btn hover:text-slate-500 hover:bg-slate-100 focus:text-slate-500 focus:bg-slate-100 active:text-slate-500 active:bg-slate-100 dark:bg-zink-600 dark:hover:bg-slate-500/10 dark:focus:bg-slate-500/10 dark:active:bg-slate-500/10">Cancelar</button>

                        <input type="hidden" id="feriadoId" name="feriadoId">

                        <button type="submit" id="deleteRecord" data-modal-close="deleteModal"
                            class="text-white bg-red-500 border-red-500 btn hover:text-white hover:bg-red-600 hover:border-red-600 focus:text-white focus:bg-red-600 focus:border-red-600 focus:ring focus:ring-red-100 active:text-white active:bg-red-600 active:border-red-600 active:ring active:ring-red-100 dark:ring-custom-400/20">Si,
                            Eliminalo!</button>
                    </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection