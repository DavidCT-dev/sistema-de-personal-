@extends('layouts.master')

@section('script')
<script src="{{ asset('assets/libs/flatpickr/l10n/es.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr.localize(flatpickr.l10ns.es);
        flatpickr("#datepicker", {
            locale: flatpickr.l10ns.es, // Aplica el idioma español
            dateFormat: "d M, Y", // Formato de la fecha
            mode: "multiple", // Permitir selección de múltiples fechas
            allowInput: true // Permitir escribir en el input
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inicializar el Timepicker en cada campo de entrada y salida
        flatpickr(
            "#entrada1Input, #salida1Input, #entrada2Input, #salida2Input, #entrada3Input, #salida3Input", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });
    });
</script>

<script>
    const horarios = @json($horarios); // Convertimos el objeto PHP a un objeto JS
    function formatTime(timeString) {
        if (!timeString) return ""; // Validación de entrada
        return timeString.substring(0, 5); // Devuelve solo "HH:mm"
    }

    function updateTolerancia(idHorario) {
        // Busca el horario por id y actualiza la tolerancia
        const horario = horarios.find(h => h.id == idHorario);
        const toleranciaInput = document.getElementById('tolerancia');
        const horarioDescripcion = document.getElementById('horarioDescripcionSelector');
        const ingre1 = document.getElementById('ingre1');
        const sali1 = document.getElementById('sali1');
        const ingre2 = document.getElementById('ingre2');
        const sali2 = document.getElementById('sali2');
     
        const horarioId = document.getElementById('id_horario');
        if (horario) {
            toleranciaInput.value = horario.tolerancia.split(':')[1]; // Asigna la tolerancia al input
            horarioDescripcion.value = horario.id; // Asigna la descripción al selector
            ingre1.value = formatTime(horario.ingreso1); // Format and set Ingreso 1
            sali1.value = formatTime(horario.salida1); // Format and set Salida 1
            ingre2.value = formatTime(horario.ingreso2); // Format and set Ingreso 2
            sali2.value = formatTime(horario.salida2); // Format and set Salida 2
         
            horarioId.value = horario.id
        } else {
            toleranciaInput.value = ''; // Limpiar el campo si no se encuentra el horario
            ingre1.value = '';
            sali1.value = '';
            ingre2.value = '';
            sali2.value = '';
          
        }
    }

    function updateToleranciaByDescripcion(descripcion) {
        // Encuentra el id_horario correspondiente a la descripción seleccionada
        const horario = horarios.find(h => h.id == descripcion);
        const toleranciaInput = document.getElementById('tolerancia');
        const idHorario = document.getElementById('id_horario');
        const ingre1 = document.getElementById('ingre1');
        const sali1 = document.getElementById('sali1');
        const ingre2 = document.getElementById('ingre2');
        const sali2 = document.getElementById('sali2');
    
        if (horario) {
            // Actualiza el selector de id_horario
            document.getElementById('selected_id_horario').value = horario.id;
            toleranciaInput.value = horario.tolerancia.split(':')[1]; // Asigna la tolerancia al input
            idHorario.value = horario.id
            // Extrae solo la hora en formato HH:mm
            ingre1.value = formatTime(horario.ingreso1); // Format and set Ingreso 1
            sali1.value = formatTime(horario.salida1); // Format and set Salida 1
            ingre2.value = formatTime(horario.ingreso2); // Format and set Ingreso 2
            sali2.value = formatTime(horario.salida2); // Format and set Salida 2
           
        } else {
            toleranciaInput.value = ''; // Limpiar el campo si no se encuentra el horario
            ingre1.value = '';
            sali1.value = '';
            ingre2.value = '';
            sali2.value = '';
           
        }
    }
</script>

<script>
    $(document).ready(function() {
        // Ocultar el campo de lugar de trabajo inicialmente
        $('#lugarTrabajoContainer').hide();
        // Manejar el cambio en el select de persona
        $('#personaSelect').change(function() {
            if ($(this).val() === 'todos') {
                $('#lugarTrabajoContainer').show();
                $('#lugarTrabajoSelect').prop('required', true);
            } else {
                $('#lugarTrabajoContainer').hide();
                $('#lugarTrabajoSelect').prop('required', false);
            }
        });
        // Manejar el cambio en el select de horario
        $('#horarioSelect').change(function() {
            var selectedOption = $(this).find('option:selected');
            // Mostrar u ocultar turnos según existan valores
            if (selectedOption.data('ingreso2') && selectedOption.data('salida2')) {
                $('#turno2').removeClass('hidden');
                $('#ingreso2').text(selectedOption.data('ingreso2'));
                $('#salida2').text(selectedOption.data('salida2'));
            } else {
                $('#turno2').addClass('hidden');
            }
            if (selectedOption.data('ingreso3') && selectedOption.data('salida3')) {
                $('#turno3').removeClass('hidden');
                $('#ingreso3').text(selectedOption.data('ingreso3'));
                $('#salida3').text(selectedOption.data('salida3'));
            } else {
                $('#turno3').addClass('hidden');
            }
            // Siempre mostrar turno 1 (asumimos que es obligatorio)
            $('#ingreso1').text(selectedOption.data('ingreso1'));
            $('#salida1').text(selectedOption.data('salida1'));
        });
        // Inicializar flatpickr para las fechas
        flatpickr("#fechaInicio", {
            dateFormat: "d-m-Y",
            allowInput: true
        });
        flatpickr("#fechaFin", {
            dateFormat: "d-m-Y",
            allowInput: true
        });
        // Disparar el evento change al cargar la página si ya hay un valor seleccionado
        if ($('#horarioSelect').val()) {
            $('#horarioSelect').trigger('change');
        }
    });
</script>

@endsection

@section('content')
<div
    class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-16">Horarios</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li
                    class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="#" class="text-slate-400 dark:text-zink-200">Afiliaciones</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    Horarios
                </li>
            </ul>

        </div>
        @can('crear_horario')
        <button data-modal-target="largeModal" type="button"
            class="mb-4 text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:focus:ring-custom-400/20">
            Crear Horario
        </button>
        @endcan

        @can('editar_horario')
        <button type="submit" form="formHorario"
            class="ml-2 text-orange-500 bg-orange-100 btn hover:text-white hover:bg-orange-600 focus:text-white focus:bg-orange-600 focus:ring focus:ring-orange-100 active:text-white active:bg-orange-600 active:ring active:ring-orange-100 dark:bg-orange-500/20 dark:text-orange-500 dark:hover:bg-orange-500 dark:hover:text-white dark:focus:bg-orange-500 dark:focus:text-white dark:active:bg-orange-500 dark:active:text-white dark:ring-orange-400/20">Editar
            Horario</button>

        @endcan
        @can('asignar_horario_empleado_semanal')
        <button data-modal-target="editarHorarioModal" type="button"
            class="ml-2 text-orange-500 bg-orange-100 btn hover:text-white hover:bg-orange-600
    focus:text-white focus:bg-orange-600 focus:ring focus:ring-orange-100 active:text-white active:bg-orange-600
    dark:bg-orange-500/20 dark:text-orange-500 dark:hover:bg-orange-500 dark:hover:text-white
    dark:focus:bg-orange-500 dark:focus:text-white dark:active:bg-orange-500 dark:active:text-white dark:ring-orange-400/20">
            Asignación Horario Temporal
        </button>
        @endcan

        <div class="grid grid-cols-1 gap-x-5 w-full">
            <div class="2xl:col-span-3">
                <div class="card">
                    <div class="card-body">
                        <div
                            class="flex items-center justify-center bg-purple-100 rounded-md size-12 dark:bg-purple-500/20 ltr:float-right rtl:float-left">
                            <i data-lucide="clock" class="text-purple-500 fill-purple-200 dark:fill-purple-500/30"></i>
                        </div>

                        <h5>HORARIO DE REFERENCIA 1</h5>

                        <div class="mb-3 mt-3 flex items-center">
                            <!-- Etiqueta para el horario -->
                            <label for="selected_id_horario" class="mr-2">HORARIO:</label>

                            <!-- Selector que muestra el id_horario -->
                            <select name="id_horario" id="selected_id_horario"
                                class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 mr-2 w-24"
                                onchange="updateTolerancia(this.value)">
                                <option value="">...</option>
                                @foreach ($horarios as $horario)
                                <option value="{{ $horario->id }}">{{ $horario->id }}</option>
                                @endforeach
                            </select>

                            <!-- Selector que muestra la descripción -->
                            <select name="id_horario_descripcion" id="horarioDescripcionSelector"
                                class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 mr-2"
                                onchange="updateToleranciaByDescripcion(this.value)">
                                <option value="">Seleccione un Horario</option>
                                @foreach ($horarios as $horario)
                                <option value="{{ $horario->id }}">{{ $horario->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>

                        <form id="formHorario" action="{{ route('horarios.update', ':id') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="horario_id" id="id_horario">

                            <div class="form-group mb-2 flex items-center">
                                <!-- Etiqueta para la tolerancia -->
                                <label for="tolerancia" class="mr-2">TOLERANCIA:</label>

                                <!-- Campo de entrada para la tolerancia -->
                                <input type="number" name="tolerancia" id="tolerancia"
                                    class="form-input w-20 border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                    placeholder="...">
                            </div>

                            <div class="grid grid-cols-2 gap-8 ">
                                <!-- Columna de Horarios -->

                                <div>
                                    <h5 class="text-lg font-bold text-gray-800 mb-4">HORARIO DE REFERENCIA 2</h5>
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">

                                            <label for="ingre1" class="text-gray-700 text-sm">Ingreso 1:</label>
                                            <input type="time" name="ingre1" id="ingre1"
                                                class="form-input w-28 border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 text-sm">
                                            <label for="sali1" class="text-gray-700 text-sm">Salida 1:</label>
                                            <input type="time" name="sali1" id="sali1"
                                                class="form-input w-28 border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 text-sm">
                                        </div>
                                        <div class="flex items-center justify-between">

                                            <label for="ingre2" class="text-gray-700 text-sm">Ingreso 2:</label>
                                            <input type="time" name="ingre2" id="ingre2"
                                                class="form-input w-28 border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 text-sm">
                                            <label for="sali2" class="text-gray-700 text-sm">Salida 2:</label>
                                            <input type="time" name="sali2" id="sali2"
                                                class="form-input w-28 border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 text-sm">
                                        </div>
                                       
                                    </div>
                                </div>

                            </div>

                        </form>

                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Modal asignacion horario temporal-->
<div id="editarHorarioModal" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div
        class="w-screen md:w-[40rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-16">Editar Asignación Horario</h5>
            <button type="reset" form="formEditarHorario" data-modal-close="editarHorarioModal"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                <i data-lucide="x" class="size-5"></i>
            </button>
        </div>

        <form id="formEditarHorario" method="POST" action="{{ route('horarios.update', ':id') }}"
            class="flex flex-col p-4 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-3">
                {{-- Persona data-choices="" data-choices-sorting-false=""--}}
                <div class="flex-1">
                    <label for="personaSelect" class="inline-block mb-2 text-base font-medium">
                        Persona <span class="text-red-500">*</span>
                    </label>

                    <!-- Input de búsqueda (se agregará dinámicamente) -->
                    <input type="text" id="personaSearchInput" placeholder="Buscar persona..."
                        class="form-input mb-2 w-full hidden border-slate-200 dark:border-zink-500">

                    <!-- Select original (se mantiene para el formulario) -->
                    <select id="personaSelect" name="persona_id"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 w-full"
                        required data-choices="" data-choices-sorting-false="" style="display: none;">
                        <option value="" disabled selected>Seleccione una persona</option>
                        <option value="todos">Todos</option>
                        @foreach ($personas as $persona)
                        <option value="{{ $persona->id }}"
                            data-search="{{ strtolower($persona->ci.' '.$persona->nombres.' '.$persona->apellido_pat.' '.$persona->apellido_mat) }}">
                            {{ $persona->ci }} - {{ $persona->nombres }} {{ $persona->apellido_pat }}
                            {{ $persona->apellido_mat }}
                        </option>
                        @endforeach
                    </select>

                    <!-- Lista de resultados (se mostrará dinámicamente) -->
                    <div id="personaResults"
                        class="hidden max-h-60 overflow-y-auto border border-slate-200 dark:border-zink-500 rounded-md mt-1 bg-white dark:bg-zink-700">
                        <!-- Los resultados se cargarán aquí -->
                    </div>
                </div>

                {{-- Lugar de Trabajo --}}
                <div id="lugarTrabajoContainer">
                    <label for="lugarTrabajoSelect" class="inline-block mb-2 text-base font-medium">
                        Lugar de Trabajo <span class="text-red-500">*</span>
                    </label>
                    <select id="lugarTrabajoSelect" name="lugar_trabajo_id"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 w-full"
                        data-choices="" data-choices-sorting-false="" placeholder="Seleccione lugar de trabajo">
                        <option value="" disabled selected>Seleccione lugar de trabajo</option>
                        <option value="todos">Todos</option>
                        @foreach ($lugarTrabajos as $lugar)
                        <option value="{{ $lugar->id }}">{{ $lugar->descripcion }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Horario --}}
                <div>
                    <label for="horarioSelect" class="inline-block mb-2 text-base font-medium">
                        Horario a Asignar <span class="text-red-500">*</span>
                    </label>
                    <select id="horarioSelect" name="horario_id"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 w-full"
                        required data-choices="" data-choices-sorting-false="">
                        <option value="" disabled selected>Seleccione un horario</option>
                        @foreach ($horarios as $h)
                        <option value="{{ $h->id }}" data-ingreso1="{{ $h->ingreso1 }}" data-salida1="{{ $h->salida1 }}"
                            data-ingreso2="{{ $h->ingreso2 }}" data-salida2="{{ $h->salida2 }}"
                            data-ingreso3="{{ $h->ingreso3 }}" data-salida3="{{ $h->salida3 }}"
                            data-tolerancia="{{ $h->tolerancia }}" data-observaciones="{{ $h->observaciones }}">
                            {{ $h->descripcion }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Visualización de Horario --}}
                <div>
                    <label class="inline-block mb-2 text-base font-medium">
                        Detalle del Horario
                    </label>
                    <div id="visualizacionHorario"
                        class="p-3 border border-slate-200 dark:border-zink-500 rounded-md bg-slate-50 dark:bg-zink-600">
                        <div class="grid grid-cols-3 gap-2 mb-2 font-semibold border-b pb-2">
                            <div>Ingresos</div>
                            <div>Salidas</div>
                        </div>
                        <div id="turno1" class="grid grid-cols-3 gap-2">
                            <div id="ingreso1"></div>
                            <div id="salida1"></div>
                        </div>
                        <div id="turno2" class="grid grid-cols-3 gap-2 hidden">
                            <div id="ingreso2"></div>
                            <div id="salida2"></div>
                        </div>
                        <div id="turno3" class="grid grid-cols-3 gap-2 hidden">
                            <div id="ingreso3"></div>
                            <div id="salida3"></div>
                        </div>

                    </div>
                </div>

                {{-- Fecha Inicio --}}
                <div class="">
                    <label for="fechaInicio" class="inline-block mb-2 text-base font-medium">
                        Fecha Inicio <span class="text-red-500">*</span>
                    </label>
                    <input type="text" required id="fechaInicio" name="fecha_inicio"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                        data-provider="flatpickr" data-date-format="d M Y" data-week-number="" readonly="readonly"
                        placeholder="DD-MM-YYYY">
                </div>

                {{-- Fecha Fin --}}
                <div class="">
                    <label for="fechaFin" class="inline-block mb-2 text-base font-medium">
                        Fecha Fin <span class="text-red-500">*</span>
                    </label>
                    <input type="text" required id="fechaFin" name="fecha_fin"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                        data-provider="flatpickr" data-date-format="d M Y" data-week-number="" readonly="readonly"
                        placeholder="DD-MM-YYYY">
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">
                <button type="reset" data-modal-close="editarHorarioModal" form="formEditarHorario"
                    class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">
                    <i data-lucide="x" class="inline-block size-4"></i> <span class="align-middle">Cancel</span>
                </button>
                <button type="submit"
                    class="ml-2 text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>








<div id="largeModal" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show ">
    <div
        class="w-screen md:w-[40rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-16">Crear Nuevo Horario</h5>
            <button data-modal-close="largeModal"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                <i data-lucide="x" class="size-5"></i>
            </button>
        </div>

        <!-- Formulario dentro del modal -->
        <form id="modalFormHorario" class="flex flex-col p-4 space-y-4" action="{{ route('horarios.store') }}"
            method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-3">
                <div>
                    <label for="descripcionInput" class="inline-block mb-2 text-base font-medium">
                        Descripción <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="descripcionInput"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                        placeholder="Ingrese la descripción" required="" name="descripcion">
                    <div id="descripcionError" class="text-red-500"></div>
                </div>
                <div>
                    <label for="toleranciaInput" class="inline-block mb-2 text-base font-medium">
                        Tolerancia <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="toleranciaInput"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                        placeholder="Ingrese la tolerancia" required="" name="tolerancia">
                    <div id="toleranciaError" class="text-red-500"></div>
                </div>

                <div class="w-full md:w-1/2 flex flex-col">
                    <label for="entrada1Input" class="inline-block mb-2 text-base font-medium">Entrada 1 <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="entrada1Input"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                        placeholder="Ingresar hora" required="" name="ingreso1">
                </div>
                <div class="w-full md:w-1/2 flex flex-col">
                    <label for="salida1Input" class="inline-block mb-2 text-base font-medium">Salida 1 <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="salida1Input"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                        placeholder="Ingresar hora" required="" name="salida1">
                </div>
                <div class="w-full md:w-1/2 flex flex-col">
                    <label for="entrada2Input" class="inline-block mb-2 text-base font-medium">Entrada 2 <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="entrada2Input"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                        placeholder="Ingresar hora" required="" name="ingreso2">
                </div>
                <div class="w-full md:w-1/2 flex flex-col">
                    <label for="salida2Input" class="inline-block mb-2 text-base font-medium">Salida 2 <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="salida2Input"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                        placeholder="Ingresar hora" required="" name="salida2">
                </div>

            </div>

            <div class="w-full">
                <label for="observacionesInput" class="inline-block mb-2 text-base font-medium">
                    Observaciones <span class="text-red-500">*</span>
                </label>
                <input type="text" id="observacionesInput"
                    class="form-input w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 dark:disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                    placeholder="Ingrese observaciones" name="observaciones">
            </div>

        </form>

        <!-- Botones alineados a la derecha -->
        <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">
            <button type="button" data-modal-close="largeModal"
                class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">
                <i data-lucide="x" class="inline-block size-4"></i> <span class="align-middle">Cancel</span>
            </button>
            <button type="submit" form="modalFormHorario"
                class="ml-2 text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                Guardar
            </button>
        </div>
    </div>
</div>
@endsection