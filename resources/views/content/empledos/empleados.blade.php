@extends('layouts.master')

@section('script')
<script src="{{ asset('assets/js/datatables/vfs_fonts.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded",
        function() {
            // Configuración de localización en español
            flatpickr.localize(flatpickr.l10ns.es);
            // Inicializar el Timepicker en los campos seleccionados
            flatpickr("#fechaNacInput, #fechIngInput, #fechBajInput", {
                enableTime: false, // Deshabilitar la selección de tiempo
                dateFormat: "d-m-Y", // Formato de fecha
                time_24hr: true, // Usar formato de 24 horas
                weekNumbers: true, // Mostrar números de la semana
                allowInput: true, // Habilita la escritura manual
            });
            flatpickr("#fechaNacInputUpdate, #fechIngInputUpdate, #fechBajInputUpdate", {
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
                extend: 'excel',
                className: 'mb-2 dt-button-custom bg-green-500 text-white hover:bg-green-600 focus:ring-2 focus:ring-green-300 rounded-lg px-4 py-2 shadow-md transition duration-300 ease-in-out transform hover:scale-105',
                text: '<i class="ri-file-excel-2-fill mr-2"></i> Exportar a Excel',
                title: 'Reporte de Empleados',
                exportOptions: {
                    columns: function(idx, data, node) {
                        return idx !== 0; // Excluir la columna con índice 0
                    }
                },
                customize: function(xlsx) {
                    // Personalizar el archivo Excel generado
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    // Aplicar estilos a las celdas
                    $('row c', sheet).each(function() {
                        // Aplicar bordes a todas las celdas
                        $(this).attr('s', '25');
                        // Aplicar estilo de fuente y alineación
                        $(this).attr('s', '2'); // Estilo 2: negrita y centrado
                    });
                    // Estilo para la primera fila (encabezados)
                    $('row:first c', sheet).each(function() {
                        $(this).attr('s',
                            '27'
                        ); // Estilo 27: fondo verde, texto blanco, negrita
                    });
                }
            }],
            paging: true,
            pageLength: 5,
            lengthMenu: [5, 10, 15, 25],
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            language: translations,
            //scrollX: true,
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButton = document.getElementById('deleteButton');
        const modal = document.getElementById('deleteModal');
        // Captura el ID del empleado desde el botón
        const empleadoId = deleteButton.getAttribute('data-id');
        // Función para mostrar el modal y pasar el ID
        deleteButton.addEventListener('click', function() {
            // Muestra el modal
            modal.classList.remove('hidden');
            // Puedes usar el ID en cualquier parte del modal
            // Por ejemplo, asignarlo a un campo oculto en el modal:
            const idInput = modal.querySelector('#empleadoId');
            if (idInput) {
                idInput.value = empleadoId;
            }
        });
        // Cerrar el modal al hacer clic en el botón de cerrar
        const closeButton = modal.querySelector('[data-modal-close="deleteModal"]');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                modal.classList.add('hidden');
            });
        }
    });
    // Función para mostrar/ocultar campo en edición
    function toggleBiometricoFieldUpdate(checkbox) {
        const container = document.getElementById('biometricoFieldContainerUpdate');
        const select = document.getElementById('biometricoSelectUpdate');
        if (checkbox.checked) {
            container.classList.remove('hidden');
            select.setAttribute('required', true);
        } else {
            container.classList.add('hidden');
            select.removeAttribute('required');
            // Limpiar selección previa
            Array.from(select.options).forEach(option => option.selected = false);
        }
    }

    function fillModal(element) {
        console.log(element.dataset)
        // Asegúrate de que todos los IDs son correctos
        document.getElementById('nombresInputUpdate').value = element.dataset.nombres;
        document.getElementById('apellidoPatInputUpdate').value = element.dataset.apellido_pat;
        document.getElementById('apellidoMatInputUpdate').value = element.dataset.apellido_mat;
        const formatearFecha = (fechaStr) => {
            if (!fechaStr) return '';
            const [año, mes, dia] = fechaStr.split('-');
            return `${dia}-${mes}-${año}`;
        }
        document.getElementById('fechaNacInputUpdate').value = formatearFecha(element.dataset.fecha_nac);
        document.getElementById('fechIngInputUpdate').value = formatearFecha(element.dataset.fecha_ing);
        document.getElementById('fechBajInputUpdate').value = formatearFecha(element.dataset.fecha_baja);
        document.getElementById('direccionInputUpdate').value = element.dataset.direccion;
        document.getElementById('ciInputUpdate').value = element.dataset.ci;
        document.getElementById('celularInputUpdate').value = element.dataset.celular;
        document.getElementById('itemInputUpdate').value = element.dataset.item;
        document.getElementById('cargoSelectUpdate').value = element.dataset.cargo;
        document.getElementById('lugarTrabajoSelectUpdate').value = element.dataset.lugar_trabajo;
        document.getElementById('tipoContratoSelectUpdate').value = element.dataset.tipo_contrato;
        document.getElementById('generoSelectUpdate').value = element.dataset.genero;
        document.getElementById('kardexVisibleInputUpdate').checked = element.dataset.kardex == "1" ? true : false;
        document.getElementById('autoSabadosInputUpdate').checked = element.dataset.autosabados == "1" ? true : false;

        const horarioSelect = document.getElementById('horarioSelectUpdate');
if (horarioSelect && horarioChoicesInstance) {
    horarioChoicesInstance.removeActiveItems(); // limpiar
    const ids = element.dataset.horarios ? element.dataset.horarios.split(',') : [];
    ids.forEach(id => {
        horarioChoicesInstance.setChoiceByValue(id);
    });
}

        // Configuración del biométrico
        // Checkbox
        const checkbox = document.getElementById('biometricoRegistroInputUpdate');
        // Checkbox
        document.getElementById('biometricoRegistroInputUpdate').checked = element.dataset.biometricoregistro === "1";
        // Mostrar/ocultar campo
        toggleBiometricoFieldUpdate(document.getElementById('biometricoRegistroInputUpdate'));
        // Establecer la acción del formulario con la URL correcta
        const modal = document.querySelector('#modalFormUpdateEmpleado');
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

    function fillModalEmployee(element) {
        const horarios = JSON.parse(element.dataset.horarios || '[]'); // Asegura un array válido
        console.log(horarios);
        document.getElementById('dataEmployee').innerText = `Horario de la persona: ${element.dataset.nombres}`;
        let descripcion = '';
        let tolerancia = '';
        let ingreso1 = '';
        let salida1 = '';
        let ingreso2 = '';
        let salida2 = '';
     
        let observaciones = '';
        horarios.forEach((horario) => {
            descripcion += `${horario.descripcion ?? 'No especificado'}\n`;
            tolerancia += `${horario.tolerancia ?? 'No especificado'}\n`;
            ingreso1 += `${horario.ingreso1 ?? 'No especificado'}\n`;
            salida1 += `${horario.salida1 ?? 'No especificado'}\n`;
            ingreso2 += `${horario.ingreso2 ?? 'No especificado'}\n`;
            salida2 += `${horario.salida2 ?? 'No especificado'}\n`;
    
            observaciones += `${horario.observaciones ?? 'No especificado'}\n`;
        });
        document.getElementById('descripcionInputSchedule').value = descripcion.trim();
document.getElementById('toleranciaInput').value = tolerancia.trim();
document.getElementById('entrada1Input').value = ingreso1.trim();
document.getElementById('salida1Input').value = salida1.trim();
document.getElementById('entrada2Input').value = ingreso2.trim();
document.getElementById('salida2Input').value = salida2.trim();

document.getElementById('observacionesInput').value = observaciones.trim();
    }

    function deleteModalEmployee(element) {
        document.getElementById('empleadoId').value = element.dataset.id;
    }

    function submitFormUpdate() {
        const modal = document.querySelector('#modalFormUpdateEmpleado');
        modal.submit();
    }
</script>
<script>
    function toggleBiometricoField(checkbox) {
        const container = document.getElementById('biometricoFieldContainer');
        const select = document.getElementById('biometricoSelect');
        if (checkbox.checked) {
            container.classList.remove('hidden');
            select.setAttribute('required', true);
        } else {
            container.classList.add('hidden');
            select.removeAttribute('required');
            select.value = ''; // Limpiar selección
        }
    }
</script>
@endsection

@section('content')
@if (
$errors->hasAny([
'nombres',
'apellido_pat',
'apellido_mat',
'fecha_nac',
'ci',
'telefono',
'celular',
'antiguedad',
'fech_ing',
'fech_baj',
'item',
'tipo_contrato_id',
'horario_id',
'lugar_trabajo_id',
'cargo_id',
'genero_id',
]))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const requestMethod = "{{ old('request_method') }}";
        if (requestMethod == 'POST') {
            const extraLargeModalButton = document.getElementById('extraLargeModalButton');
            if (extraLargeModalButton) {
                extraLargeModalButton.click();
            }
        } else if (requestMethod == 'PUT') {
            const existingButton = document.querySelector('[data-modal-target="updateEmpleadoModal"]');
            if (existingButton) {
                existingButton.click(); // Simular clic en el botón existente
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
            form = document.querySelector('#modalFormPersona');
        } else if (requestMethod === 'PUT') {
            form = document.querySelector('#modalFormUpdateEmpleado');
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
                <h5 class="text-16">Afiliaciones</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li
                    class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="#!" class="text-slate-400 dark:text-zink-200">Afiliaciones</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    Empleado
                </li>
            </ul>
        </div>
        @can('crear_empleado')
        <button data-modal-target="extraLargeModal" type="button" id="extraLargeModalButton"
            class="mb-4 text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:focus:ring-custom-400/20">Crear
            Empleado</button>
        @endcan

        <div class="grid grid-cols-1 2xl:grid-cols-12 gap-x-5">
            <div class="2xl:col-span-12">
                <div class="card">
                    <div class="card-body">

                        <div
                            class="flex items-center justify-center bg-purple-100 rounded-md size-12 dark:bg-purple-500/20 ltr:float-right rtl:float-left">
                            <i data-lucide="users" class="text-purple-500 fill-purple-200 dark:fill-purple-500/30"></i>
                        </div>

                        <div class="overflow-x-auto">
                            <table id="rowSelectionDeletion" class="display" style="width:100%">
                                <thead>
                                    <tr class="bg-gray-100 text-left text-gray-600 text-sm font-medium uppercase">
                                        <th class="py-3 px-4 border-b border-gray-300">Acciones</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Nombres</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Antiguedad</th>
                                        <th class="py-3 px-4 border-b border-gray-300">CI</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Fecha de Nacimiento</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Dirección</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Celular</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Fecha Ingreso</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Fecha Baja</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Item</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Tipo Contrato</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Horario</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Lugar de Trabajo</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Cargo</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Género</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($empleados as $empleado)
                                    <tr>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            <div class="relative dropdown">
                                                <button id="orderAction1" data-bs-toggle="dropdown"
                                                    class="flex items-center justify-center size-[30px] dropdown-toggle p-0 text-slate-500 btn bg-slate-100 hover:text-white hover:bg-slate-600 focus:text-white focus:bg-slate-600 focus:ring focus:ring-slate-100 active:text-white active:bg-slate-600 active:ring active:ring-slate-100 dark:bg-slate-500/20 dark:text-slate-400 dark:hover:bg-slate-500 dark:hover:text-white dark:focus:bg-slate-500 dark:focus:text-white dark:active:bg-slate-500 dark:active:text-white dark:ring-slate-400/20"><i
                                                        data-lucide="more-horizontal" class="size-3"></i></button>
                                                <ul class="absolute z-50 hidden py-2 mt-1 ltr:text-left rtl:text-right list-none bg-white rounded-md shadow-md dropdown-menu min-w-[10rem] dark:bg-zink-600"
                                                    aria-labelledby="orderAction1">
                                                    <li>
                                                        <a data-modal-target="extraLargeModalSchedule"
                                                            class="block px-4 py-1.5 text-base transition-all duration-200 ease-linear text-slate-600 dropdown-item hover:bg-slate-100 hover:text-slate-500 focus:bg-slate-100 focus:text-slate-500 dark:text-zink-100 dark:hover:bg-zink-500 dark:hover:text-zink-200 dark:focus:bg-zink-500 dark:focus:text-zink-200"
                                                            data-nombres="{{ $empleado->nombres . ' ' . ($empleado->apellido_pat ?? '') . ' ' . ($empleado->apellido_mat ?? '') }}"
                                                            data-horarios='@json($empleado->horarios)'
                                                            onclick="fillModalEmployee(this)">
                                                            <i data-lucide="eye"
                                                                class="inline-block size-3 ltr:mr-1 rtl:ml-1"></i>
                                                            <span class="align-middle">Ver Horario</span>
                                                        </a>

                                                    </li>
                                                    @can('editar_empleado')
                                                    <li>
                                                        <a data-modal-target="updateEmpleadoModal"
                                                            data-nombres="{{ $empleado->nombres }}"
                                                            data-apellido_pat="{{ $empleado->apellido_pat }}"
                                                            data-apellido_mat="{{ $empleado->apellido_mat }}"
                                                            data-fecha_nac="{{ $empleado->fecha_nac }}"
                                                            data-direccion="{{ $empleado->direccion }}"
                                                            data-ci="{{ $empleado->ci }}"
                                                            data-celular="{{ $empleado->celular }}"
                                                            data-fecha_ing="{{ $empleado->fech_ing }}"
                                                            data-fecha_baja="{{ $empleado->fech_baj }}"
                                                            data-item="{{ $empleado->item }}"
                                                            data-kardex="{{ $empleado->kardex_visible }}"
                                                            data-autoSabados="{{ $empleado->auto_sabados }}"
                                                            data-biometricoRegistro="{{ $empleado->biometrico_registro }}"
                                                            data-tipo_contrato="{{ $empleado->tipo_contrato_id }}"
                                                            data-horarios="{{ implode(',', $empleado->horarios->pluck('id')->toArray()) }}"
                                                            data-lugar_trabajo="{{ $empleado->lugar_trabajo_id }}"
                                                            data-cargo="{{ $empleado->cargo_id }}"
                                                            data-genero="{{ $empleado->genero_id }}"
                                                            data-biometricos="{{ implode(',', $empleado->biometricos->pluck('id')->toArray()) }}"
                                                            data-url="{{ route('empleados.update', ['empleado' => $empleado->id]) }}"
                                                            class="block px-4 py-1.5 text-base transition-all duration-200 ease-linear text-slate-600 dropdown-item hover:bg-slate-100 hover:text-slate-500 focus:bg-slate-100 focus:text-slate-500 dark:text-zink-100 dark:hover:bg-zink-500 dark:hover:text-zink-200 dark:focus:bg-zink-500 dark:focus:text-zink-200"
                                                            onclick="fillModal(this)">
                                                            <i data-lucide="file-edit"
                                                                class="inline-block size-3 ltr:mr-1 rtl:ml-1"></i>
                                                            <span class="align-middle">Editar</span>
                                                        </a>
                                                    </li>
                                                    @endcan
                                                    @can('eliminar_empleado')
                                                    <li>
                                                        <a id="deleteButton" data-id="{{ $empleado->id }}"
                                                            data-modal-target="deleteModal"
                                                            class="block px-4 py-1.5 text-base transition-all duration-200 ease-linear text-slate-600 dropdown-item hover:bg-slate-100 hover:text-slate-500 focus:bg-slate-100 focus:text-slate-500 dark:text-zink-100 dark:hover:bg-zink-500 dark:hover:text-zink-200 dark:focus:bg-zink-500 dark:focus:text-zink-200"
                                                            href="#!" onclick="deleteModalEmployee(this)"><i
                                                                data-lucide="trash-2"
                                                                class="inline-block size-3 ltr:mr-1 rtl:ml-1"></i>
                                                            <span class="align-middle">Dar Baja</span></a>
                                                    </li>
                                                    @endcan

                                                </ul>
                                            </div>
                                        </td>

                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $empleado->apellido_pat }} {{ $empleado->apellido_mat }}
                                            {{ $empleado->nombres }}</td>

                                        <td
                                            class="text-center px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $empleado->antiguedad }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $empleado->ci }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $empleado->fecha_nac }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $empleado->direccion }}</td>

                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $empleado->celular }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $empleado->fech_ing }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $empleado->fech_baj }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $empleado->item }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $empleado->tipoContrato->descripcion ?? 'N/A' }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
    @if(count($empleado->horarios) > 0)
        @if(count($empleado->horarios) <= 2)
            @foreach($empleado->horarios as $horario)
                {{ $horario->descripcion }}@if(!$loop->last), @endif
            @endforeach
        @else
            <div class="text-sm"> <!-- Clase text-sm para hacer el texto más pequeño -->
                @foreach($empleado->horarios as $horario)
                    {{ $horario->descripcion }}@if(!$loop->last), @endif
                @endforeach
            </div>
        @endif
    @else
        N/A
    @endif
</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $empleado->lugarTrabajo->descripcion ?? 'N/A' }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $empleado->cargo->descripcion ?? 'N/A' }}</td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $empleado->genero->genero ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr class="text-gray-600 text-sm font-medium uppercase">
                                        <th class="py-3 px-4 border-t border-gray-300">Acciones</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Nombres</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Antiguedad</th>
                                        <th class="py-3 px-4 border-t border-gray-300">CI</th>
                                        <th class="py-3 px-4 border-t border-gray-300">Fecha de Nacimiento</th>
                                        <th class="py-3 px-4 border-t border-gray-300">Dirección</th>
                                        <th class="py-3 px-4 border-t border-gray-300">Celular</th>
                                        <th class="py-3 px-4 border-t border-gray-300">Fecha Ingreso</th>
                                        <th class="py-3 px-4 border-t border-gray-300">Fecha Baja</th>
                                        <th class="py-3 px-4 border-t border-gray-300">Item</th>
                                        <th class="py-3 px-4 border-t border-gray-300">Tipo Contrato</th>
                                        <th class="py-3 px-4 border-t border-gray-300">Horario</th>
                                        <th class="py-3 px-4 border-t border-gray-300">Lugar de Trabajo</th>
                                        <th class="py-3 px-4 border-t border-gray-300">Cargo</th>
                                        <th class="py-3 px-4 border-t border-gray-300">Género</th>
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

{{-- modal create --}}
<div id="extraLargeModal" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div
        class="w-screen xl:w-[55rem] lg:w-[50rem]  md:w-[40rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-16"> Crear Empleado</h5>
            <button data-modal-close="extraLargeModal" type="reset" form="modalFormPersona"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                <i data-lucide="x" class="size-5"></i>
            </button>
        </div>
        <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
            <form id="modalFormPersona" action="{{ old('action', route('empleados.store')) }}" method="POST">
                @csrf
                <!-- Nombres y Apellidos -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-3">
                    <!-- Campo Nombres -->
                    <div>
                        <label for="nombresInput" class="inline-block mb-2 text-base font-medium">
                            Nombres <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombresInput" name="nombres" required
                            value="{{ old('request_method') === 'POST' ? old('nombres') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                @if (old('request_method') === 'POST' && $errors->has('nombres')) border-red-500 @endif" placeholder="Ingrese los nombres">
                        {{-- Mensaje de error --}}
                        @if (old('request_method') === 'POST')
                        @error('nombres')
                        <div id="username-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    <!-- Campo Apellido Paterno -->
                    <div>
                        <label for="apellidoPatInput" class="inline-block mb-2 text-base font-medium">
                            Apellido Paterno <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="apellidoPatInput" name="apellido_pat"
                            value="{{ old('request_method') === 'POST' ? old('apellido_pat') : '' }}" required
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200
                                @if (old('request_method') === 'POST' && $errors->has('apellido_pat')) border-red-500 @endif" placeholder="Ingrese el apellido paterno">
                        @if (old('request_method') === 'POST')
                        @error('apellido_pat')
                        <div id="apellidoPat-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    <!-- Campo Apellido Materno -->
                    <div>
                        <label for="apellidoMatInput" class="inline-block mb-2 text-base font-medium">
                            Apellido Materno <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="apellidoMatInput" name="apellido_mat"
                            value="{{ old('request_method') === 'POST' ? old('apellido_mat') : '' }}" required
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200
                                @if (old('request_method') === 'POST' && $errors->has('apellido_mat')) border-red-500 @endif" placeholder="Ingrese el apellido materno">
                        @if (old('request_method') === 'POST')
                        @error('apellido_mat')
                        <div id="apellidoMat-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-4 mb-3">
                    <div>
                        <!-- Campo Fecha de Nacimiento -->
                        <label for="fechaNacInput" class="inline-block mb-2 text-base font-medium">
                            Fecha de Nacimiento <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="fechaNacInput" name="fecha_nac"
                            value="{{ old('request_method') === 'POST' ? old('fecha_nac') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200
                                @if (old('request_method') === 'POST' && $errors->has('fecha_nac')) border-red-500 @endif" data-provider="flatpickr" data-date-format="d M, Y"
                            data-week-number="" readonly="readonly" placeholder="DD-MM-YYYY">
                        {{-- Mensaje de error --}}
                        @if (old('request_method') === 'POST')
                        @error('fecha_nac')
                        <div id="fecha_nac-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    <div>
                        <!-- Campo Celular -->
                        <label for="celularInput" class="inline-block mb-2 text-base font-medium">
                            Celular <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="celularInput" name="celular"
                            value="{{ old('request_method') === 'POST' ? old('celular') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200
                                @if (old('request_method') === 'POST' && $errors->has('celular')) border-red-500 @endif" placeholder="Ingrese el celular">
                        {{-- Mensaje de error --}}
                        @if (old('request_method') === 'POST')
                        @error('celular')
                        <div id="celular-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    <div>
                        <!-- Campo Item -->
                        <label for="itemInput" class="inline-block mb-2 text-base font-medium">
                            Item <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="itemInput" name="item" required
                            value="{{ old('request_method') === 'POST' ? old('item') : '' }}" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200
                                @if (old('request_method') === 'POST' && $errors->has('item')) border-red-500 @endif"
                            placeholder="Ingrese el item">
                        {{-- Mensaje de error --}}
                        @if (old('request_method') === 'POST')
                        @error('item')
                        <div id="item-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>
                    <div>
                        <!-- Campo antiguedadInput -->
                        <label for="antiguedadInput" class="inline-block mb-2 text-base font-medium">
                            Antiguedad <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="antiguedadInput" name="antiguedad" required
                            value="{{ old('request_method') === 'POST' ? old('antiguedad') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200
                                @if (old('request_method') === 'POST' && $errors->has('antiguedad')) border-red-500 @endif" placeholder="Ingrese el antiguedad">
                        {{-- Mensaje de error --}}
                        @if (old('request_method') === 'POST')
                        @error('antiguedad')
                        <div id="item-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>
                </div>

                <!-- Fechas de Ingreso y Baja -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-3">
                    <!-- Campo Fecha de Ingreso -->
                    <div>
                        <label for="fechIngInput" class="inline-block mb-2 text-base font-medium">
                            Fecha de Ingreso <span class="text-red-500">*</span>
                        </label>
                        <input type="text" value="{{ old('request_method') === 'POST' ? old('fech_ing') : '' }}"
                            id="fechIngInput" name="fech_ing" required
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                 @if (old('request_method') === 'POST' && $errors->has('fech_ing')) border-red-500 @endif" data-provider="flatpickr"
                            data-date-format="d M, Y" data-week-number="" readonly="readonly" placeholder="DD-MM-YYYY">
                        @if (old('request_method') === 'POST')
                        @error('fech_ing')
                        <div id="fech_ing-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    <!-- Campo Fecha de Baja -->
                    <div>
                        <label for="fechBajInput" class="inline-block mb-2 text-base font-medium">
                            Fecha de Baja <span class="text-red-500">*</span>
                        </label>
                        <input type="text" value="{{ old('request_method') === 'POST' ? old('fech_baj') : '' }}"
                            id="fechBajInput" name="fech_baj"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('fech_baj')) border-red-500 @endif"
                            data-provider="flatpickr" data-date-format="d M, Y" data-week-number="" readonly="readonly"
                            placeholder="DD-MM-YYYY">
                        @if (old('request_method') === 'POST')
                        @error('fech_baj')
                        <div id="fech_baj-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    <!-- Campo CI -->
                    <div>
                        <label for="ciInput" class="inline-block mb-2 text-base font-medium">
                            CI <span class="text-red-500">*</span>
                        </label>
                        <input type="text" value="{{ old('request_method') === 'POST' ? old('ci') : '' }}" id="ciInput"
                            name="ci" required
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('ci')) border-red-500 @endif"
                            placeholder="Ingrese el CI">
                        @if (old('request_method') === 'POST')
                        @error('ci')
                        <div id="ci-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-3">
                    <!-- Dirección -->
                    <div>
                        <label for="direccionInput" class="inline-block mb-2 text-base font-medium">
                            Dirección <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="direccionInput" name="direccion"
                            value="{{ old('request_method') === 'POST' ? old('direccion') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500
                                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500
                                    dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700
                                    dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200
                                    @if (old('request_method') === 'POST' && $errors->has('direccion')) border-red-500 @endif" placeholder="Ingrese la dirección">
                        @if (old('request_method') === 'POST')
                        @error('direccion')
                        <div id="item-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    <!-- Tipo de Contrato -->
                    <div>
                        <label for="tipoContratoSelect" class="inline-block mb-2 text-base font-medium">
                            Tipo de Contrato <span class="text-red-500">*</span>
                        </label>
                        <select id="tipoContratoSelect" name="tipo_contrato_id"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500
                                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500
                                    dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700
                                    dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200
                                    @if (old('request_method') === 'POST' && $errors->has('tipo_contrato_id')) border-red-500 @endif" required data-choices=""
                            data-choices-sorting-false="">
                            <option value="" disabled selected>Seleccione
                                tipo de Contrato</option>
                            @foreach ($tipoContratos as $tipoContrato)
                            <option value="{{ $tipoContrato->id }}"
                                {{ old('request_method') === 'POST' && old('tipo_contrato_id') == $tipoContrato->id ? 'selected' : '' }}>
                                {{ $tipoContrato->descripcion }}
                            </option>
                            @endforeach
                        </select>
                        @if (old('request_method') === 'POST')
                        @error('tipo_contrato_id')
                        <div id="item-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    <!-- Horario -->
                    <div>
                        <label for="horarioSelect" class="inline-block mb-2 text-base font-medium">
                            Horarios <span class="text-red-500">*</span>
                        </label>
                        <select id="horarioSelect" name="horarios[]" multiple class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500
                disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500
                dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700
                dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200
                @if (old('request_method') === 'POST' && $errors->has('horarios')) border-red-500 @endif" required
                            data-choices data-choices-sorting-false data-choices-removeItemButton="true">

                            @foreach ($horarios as $horario)
                            <option value="{{ $horario->id }}"
                                {{ (is_array(old('horarios')) && in_array($horario->id, old('horarios'))) ? 'selected' : '' }}
                                {{ (isset($persona) && $persona->horarios->contains($horario->id)) ? 'selected' : '' }}>
                                {{ $horario->descripcion }}
                            </option>
                            @endforeach
                        </select>

                        @if (old('request_method') === 'POST')
                        @error('horarios')
                        <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                </div>

                <!-- Lugar de Trabajo, Cargo y Género -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-3">
                    <!-- Lugar de Trabajo -->
                    <div>
                        <label for="lugarTrabajoSelect" class="inline-block mb-2 text-base font-medium">
                            Lugar de Trabajo <span class="text-red-500">*</span>
                        </label>
                        <select id="lugarTrabajoSelect" name="lugar_trabajo_id"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500
                                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500
                                    dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700
                                    dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200
                                    @if (old('request_method') === 'POST' && $errors->has('lugar_trabajo_id')) border-red-500 @endif" required data-choices
                            data-choices-sorting-false>
                            <!-- Opción por defecto -->
                            <option value="" disabled selected>Seleccione Lugar de Trabajo</option>
                            <!-- Iteración sobre los lugares de trabajo -->
                            @foreach ($lugaresTrabajo as $lugarTrabajo)
                            <option value="{{ $lugarTrabajo->id }}"
                                {{ old('request_method') === 'POST' && old('lugar_trabajo_id') == $lugarTrabajo->id ? 'selected' : '' }}>
                                {{ $lugarTrabajo->descripcion }}
                            </option>
                            @endforeach
                        </select>
                        <!-- Mostrar mensaje de error si existe -->
                        @if (old('request_method') === 'POST')
                        @error('lugar_trabajo_id')
                        <div id="item-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    <!-- Cargo -->
                    <div>
                        <label for="cargoSelect" class="inline-block mb-2 text-base font-medium">
                            Cargo <span class="text-red-500">*</span>
                        </label>
                        <select id="cargoSelect" name="cargo_id"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500
                                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500
                                    dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700
                                    dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200
                                    @if (old('request_method') === 'POST' && $errors->has('cargo_id')) border-red-500 @endif" required data-choices
                            data-choices-sorting-false>
                            <!-- Opción por defecto -->
                            <option value="" disabled selected>Seleccione Cargo</option>
                            <!-- Iteración sobre los cargos -->
                            @foreach ($cargos as $cargo)
                            <option value="{{ $cargo->id }}"
                                {{ old('request_method') === 'POST' && old('cargo_id') == $cargo->id ? 'selected' : '' }}>
                                {{ $cargo->descripcion }}
                            </option>
                            @endforeach
                        </select>
                        <!-- Mostrar mensaje de error si existe -->
                        @if (old('request_method') === 'POST')
                        @error('cargo_id')
                        <div id="item-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>
                    <!-- Género -->
                    <div>
                        <label for="generoSelect" class="inline-block mb-2 text-base font-medium">
                            Género <span class="text-red-500">*</span>
                        </label>
                        <select id="generoSelect" name="genero_id"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500
                                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500
                                    dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700
                                    dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200
                                    @if (old('request_method') === 'POST' && $errors->has('genero_id')) border-red-500 @endif" required data-choices=""
                            data-choices-sorting-false="">
                            <option value="" disabled selected>Seleccione Género
                            </option>
                            @foreach ($generos as $genero)
                            <option value="{{ $genero->id }}"
                                {{ old('request_method') === 'POST' && old('genero_id') == $genero->id ? 'selected' : '' }}>
                                {{ $genero->genero }}
                            </option>
                            @endforeach
                        </select>
                        @if (old('request_method') === 'POST')
                        @error('genero_id')
                        <div id="item-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-3">

                    <div class="flex flex-col items-start space-y-4">
                        <!-- Kardex Visible -->

                        <div>
                            <input type="checkbox" id="kardexVisibleInput" name="kardex_visible" value="0"
                                class="border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-slate-700 checked:border-slate-700 dark:checked:bg-zink-400 dark:checked:border-zink-400 checked:disabled:bg-zink-500 checked:disabled:border-zink-500"
                                onchange="this.value = this.checked ? '0' : '1'">
                            <label for="kardexVisibleInput" class="inline-block text-base font-medium ml-2">
                                Kardex Visible
                            </label>
                        </div>

                        <!-- Auto Sábados -->
                        <div>
                            <input type="checkbox" id="autoSabadosInput" name="auto_sabados" value="0"
                                class="border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-slate-700 checked:border-slate-700 dark:checked:bg-zink-400 dark:checked:border-zink-400 checked:disabled:bg-zink-500 checked:disabled:border-zink-500"
                                onchange="this.value = this.checked ? '0' : '1'">
                            <label for="autoSabadosInput" class="inline-block text-base font-medium ml-2">
                                Auto Sábados
                            </label>
                        </div>

                    </div>

                    <!-- Checkbox para habilitar registro en biométrico -->
                    <div class="flex items-center mb-4">
                        <input type="checkbox" id="biometricoRegistroInput" name="biometrico_registro" value="1" class="border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 
                  dark:bg-zink-600 dark:border-zink-500 checked:bg-slate-700 checked:border-slate-700 
                  dark:checked:bg-zink-400 dark:checked:border-zink-400 checked:disabled:bg-zink-500 
                  dark:checked:border-zink-400" @if (old('biometrico_registro')) checked @endif
                            onchange="toggleBiometricoField(this)">

                        <label for="biometricoRegistroInput" class="inline-block text-base font-medium ml-2">
                            Registrar en Biométrico?
                        </label>
                    </div>

                    <!-- Contenedor condicional para el campo de biometricos -->
                    <div id="biometricoFieldContainer"
                        class="grid grid-cols-1 gap-4 md:grid-cols-1 mb-3 @if (!old('biometrico_registro')) hidden @endif">
                        <!-- Selector de Biométricos -->
                        <div>
                            <label for="biometricoSelect" class="inline-block mb-2 text-base font-medium">
                                Biométricos <span class="text-red-500">*</span>
                            </label>
                            <select id="biometricoSelect" name="biometricos[]" multiple
                                class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500
                                            disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500
                                            dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700
                                            dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200
                                            @if (old('biometrico_registro') && $errors->has('biometricos')) border-red-500 @endif" @if (old('biometrico_registro')) required
                                @endif data-choices data-choices-sorting-false data-choices-removeItemButton="true">
                                @foreach ($biometricos as $biometrico)
                                <option value="{{ $biometrico->id }}"
                                    {{ (is_array(old('biometricos')) && in_array($biometrico->id, old('biometricos'))) ? 'selected' : '' }}
                                    {{ (isset($persona) && $persona->biometricos->contains($biometrico->id)) ? 'selected' : '' }}>
                                    {{ $biometrico->nombre_biometrico }} (IP: {{ $biometrico->ip_biometrico }})
                                </option>
                                @endforeach
                            </select>

                            @if (old('biometrico_registro') && $errors->has('biometricos'))
                            <div class="mt-1 text-sm text-red-500">{{ $errors->first('biometricos') }}</div>
                            @endif
                        </div>
                    </div>

                </div>

            </form>
        </div>

        <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">

            <button type="button" data-modal-close="extraLargeModal" type="reset" form="modalFormPersona"
                class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">
                <i data-lucide="x" class="inline-block size-4"></i> <span class="align-middle">Cancel</span>
            </button>

            <button type="submit" form="modalFormPersona"
                class="ml-2 text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                Guardar
            </button>
        </div>
    </div>
</div>

{{-- modal update --}}
<div id="updateEmpleadoModal" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div class="w-screen lg:w-[55rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full">
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-16">Actualizar Empleado</h5>
            <button data-modal-close="updateEmpleadoModal"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                <i data-lucide="x" class="size-5"></i>
            </button>
        </div>
        <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
            <form id="modalFormUpdateEmpleado" action="{{ route('empleados.update', ':id') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Nombres y Apellidos -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-3">
                    <div>
                        <label for="nombresInputUpdate" class="inline-block mb-2 text-base font-medium">
                            Nombres <span class="text-red-500">*</span>
                        </label>

                        {{-- Input con validaciones --}}
                        <input type="text" id="nombresInputUpdate" name="nombres" required
                            value="{{ old('request_method') === 'PUT' ? old('nombres') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                                       dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                                       dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                       placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'PUT' && $errors->has('nombres')) border-red-500 @endif" placeholder="Ingrese los nombres">

                        {{-- Mensaje de error --}}
                        @if (old('request_method') === 'PUT')
                        @error('nombres')
                        <div id="username-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    <div>
                        <label for="apellidoPatInputUpdate" class="inline-block mb-2 text-base font-medium">
                            Apellido Paterno <span class="text-red-500">*</span>
                        </label>

                        {{-- Input con validaciones --}}
                        <input type="text" id="apellidoPatInputUpdate" name="apellido_pat" required
                            value="{{ old('request_method') === 'PUT' ? old('apellido_pat') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                                       dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                                       dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                       placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'PUT' && $errors->has('apellido_pat')) border-red-500 @endif" placeholder="Ingrese el apellido paterno">

                        {{-- Mensaje de error --}}
                        @if (old('request_method') === 'PUT')
                        @error('apellido_pat')
                        <div id="apellidoPat-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    <div>
                        <label for="apellidoMatInputUpdate" class="inline-block mb-2 text-base font-medium">
                            Apellido Materno <span class="text-red-500">*</span>
                        </label>

                        {{-- Input con validaciones --}}
                        <input type="text" id="apellidoMatInputUpdate" name="apellido_mat" required
                            value="{{ old('request_method') === 'PUT' ? old('apellido_mat') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                                       dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                                       dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                       placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'PUT' && $errors->has('apellido_mat')) border-red-500 @endif" placeholder="Ingrese el apellido materno">

                        {{-- Mensaje de error --}}
                        @if (old('request_method') === 'PUT')
                        @error('apellido_mat')
                        <div id="apellidoMat-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                </div>

                <!-- Fecha de Nacimiento y Contacto -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-3">
                    {{-- Fecha de Nacimiento --}}
                    <div>
                        <label for="fechaNacInputUpdate" class="inline-block mb-2 text-base font-medium">
                            Fecha de Nacimiento <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="fechaNacInputUpdate" name="fecha_nac"
                            value="{{ old('request_method') === 'PUT' ? old('fecha_nac') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                                       dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                                       dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                       placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'PUT' && $errors->has('fecha_nac')) border-red-500 @endif" data-provider="flatpickr" data-date-format="Y-m-d"
                            data-week-number="" readonly="readonly" placeholder="Seleccione la fecha">

                        @if (old('request_method') === 'PUT')
                        @error('fecha_nac')
                        <div id="fechaNac-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- Celular --}}
                    <div>
                        <label for="celularInputUpdate" class="inline-block mb-2 text-base font-medium">
                            Celular <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="celularInputUpdate" name="celular"
                            value="{{ old('request_method') === 'PUT' ? old('celular') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                                       dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                                       dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                       placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'PUT' && $errors->has('celular')) border-red-500 @endif" placeholder="Ingrese el celular">

                        @if (old('request_method') === 'PUT')
                        @error('celular')
                        <div id="celular-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- Ítem --}}
                    <div>
                        <label for="itemInputUpdate" class="inline-block mb-2 text-base font-medium">
                            Ítem <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="itemInputUpdate" name="item" required
                            value="{{ old('request_method') === 'PUT' ? old('item') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                                       dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                                       dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                       placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'PUT' && $errors->has('item')) border-red-500 @endif" placeholder="Ingrese el ítem">

                        @if (old('request_method') === 'PUT')
                        @error('item')
                        <div id="item-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>
                </div>

                <!-- Fechas de Ingreso y Baja -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-3">
                    {{-- Fecha de Ingreso --}}
                    <div>
                        <label for="fechIngInputUpdate" class="inline-block mb-2 text-base font-medium">
                            Fecha de Ingreso <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="fechIngInputUpdate" name="fech_ing" required
                            value="{{ old('request_method') === 'PUT' ? old('fech_ing') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                                       dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                                       dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                       placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'PUT' && $errors->has('fech_ing')) border-red-500 @endif" data-provider="flatpickr" data-date-format="Y-m-d"
                            data-week-number="" readonly="readonly" placeholder="Seleccione la fecha">
                        @if (old('request_method') === 'PUT')
                        @error('fech_ing')
                        <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- Fecha de Baja --}}
                    <div>
                        <label for="fechBajInputUpdate" class="inline-block mb-2 text-base font-medium">
                            Fecha de Baja <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="fechBajInputUpdate" name="fech_baj"
                            value="{{ old('request_method') === 'PUT' ? old('fech_baj') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                                       dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                                       dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                       placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'PUT' && $errors->has('fech_baj')) border-red-500 @endif" data-provider="flatpickr" data-date-format="Y-m-d"
                            data-week-number="" readonly="readonly" placeholder="Seleccione la fecha">
                        @if (old('request_method') === 'PUT')
                        @error('fech_baj')
                        <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- CI --}}
                    <div>
                        <label for="ciInputUpdate" class="inline-block mb-2 text-base font-medium">
                            CI <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="ciInputUpdate" name="ci" required
                            value="{{ old('request_method') === 'PUT' ? old('ci') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                                       dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                                       dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                       placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'PUT' && $errors->has('ci')) border-red-500 @endif" placeholder="Ingrese el CI">
                        @if (old('request_method') === 'PUT')
                        @error('ci')
                        <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-3">

                    {{-- Dirección --}}
                    <div>
                        <label for="direccionInputUpdate" class="inline-block mb-2 text-base font-medium">
                            Dirección
                        </label>
                        <input type="text" id="direccionInputUpdate" name="direccion"
                            value="{{ old('request_method') === 'PUT' ? old('direccion') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                                       dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                                       dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                       placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'PUT' && $errors->has('direccion')) border-red-500 @endif" placeholder="Ingrese la dirección">
                        @if (old('request_method') === 'PUT')
                        @error('direccion')
                        <div id="direccion-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- Tipo de Contrato --}}
                    <div>
                        <label for="tipoContratoSelectUpdate" class="inline-block mb-2 text-base font-medium">
                            Tipo de Contrato <span class="text-red-500">*</span>
                        </label>
                        <select id="tipoContratoSelectUpdate" name="tipo_contrato_id"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                                       dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                                       dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                       placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'PUT' && $errors->has('tipo_contrato_id')) border-red-500 @endif">
                            <option value="">Seleccione un tipo de contrato</option>
                            @foreach ($tipoContratos as $tipoContrato)
                            <option value="{{ $tipoContrato->id }}"
                                {{ old('request_method') === 'PUT' && old('tipo_contrato_id') == $tipoContrato->id ? 'selected' : '' }}>
                                {{ $tipoContrato->descripcion }}
                            </option>
                            @endforeach
                        </select>
                        @if (old('request_method') === 'PUT')
                        @error('tipo_contrato_id')
                        <div id="tipoContrato-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- Horario --}}
                    <div>
    <label for="horarioSelectUpdate" class="inline-block mb-2 text-base font-medium">
        Horarios <span class="text-red-500">*</span>
    </label>
    <select id="horarioSelectUpdate" name="horarios[]"
        multiple
        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
               disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
               dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
               dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
               placeholder:text-slate-400 dark:placeholder:text-zink-200 
               @if (old('request_method') === 'PUT' && $errors->has('horarios')) border-red-500 @endif">
        @foreach ($horarios as $horario)
            <option value="{{ $horario->id }}"
                {{ collect(old('horarios', []))->contains($horario->id) ? 'selected' : '' }}>
                {{ $horario->descripcion }}
            </option>
        @endforeach
    </select>
    @if (old('request_method') === 'PUT')
        @error('horarios')
        <div id="horario-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
        @enderror
    @endif
</div>


                </div>

                <!-- Lugar de Trabajo, Cargo y Género -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-3">
                    {{-- Lugar de Trabajo --}}
                    <div>
                        <label for="lugarTrabajoSelectUpdate" class="inline-block mb-2 text-base font-medium">
                            Lugar de Trabajo <span class="text-red-500">*</span>
                        </label>
                        <select id="lugarTrabajoSelectUpdate" name="lugar_trabajo_id"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                       dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                       dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'PUT' && $errors->has('lugar_trabajo_id')) border-red-500 @endif">
                            @foreach ($lugaresTrabajo as $lugarTrabajo)
                            <option value="{{ $lugarTrabajo->id }}"
                                {{ old('request_method') === 'PUT' && old('lugar_trabajo_id') == $lugarTrabajo->id ? 'selected' : '' }}>
                                {{ $lugarTrabajo->descripcion }}
                            </option>
                            @endforeach
                        </select>

                        {{-- Mensaje de error --}}
                        @if (old('request_method') === 'PUT')
                        @error('lugar_trabajo_id')
                        <div id="lugarTrabajo-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- Cargo --}}
                    <div>
                        <label for="cargoSelectUpdate" class="inline-block mb-2 text-base font-medium">
                            Cargo <span class="text-red-500">*</span>
                        </label>
                        <select id="cargoSelectUpdate" name="cargo_id"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                       dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                       dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'PUT' && $errors->has('cargo_id')) border-red-500 @endif">
                            @foreach ($cargos as $cargo)
                            <option value="{{ $cargo->id }}"
                                {{ old('request_method') === 'PUT' && old('cargo_id') == $cargo->id ? 'selected' : '' }}>
                                {{ $cargo->descripcion }}
                            </option>
                            @endforeach
                        </select>

                        {{-- Mensaje de error --}}
                        @if (old('request_method') === 'PUT')
                        @error('cargo_id')
                        <div id="cargo-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- Género --}}
                    <div>
                        <label for="generoSelectUpdate" class="inline-block mb-2 text-base font-medium">
                            Género <span class="text-red-500">*</span>
                        </label>
                        <select id="generoSelectUpdate" name="genero_id"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                       disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                       dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                       dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                       @if (old('request_method') === 'PUT' && $errors->has('genero_id')) border-red-500 @endif">
                            @foreach ($generos as $genero)
                            <option value="{{ $genero->id }}"
                                {{ old('request_method') === 'PUT' && old('genero_id') == $genero->id ? 'selected' : '' }}>
                                {{ $genero->genero }}
                            </option>
                            @endforeach
                        </select>

                        {{-- Mensaje de error --}}
                        @if (old('request_method') === 'PUT')
                        @error('genero_id')
                        <div id="genero-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-3">
                    <div class="flex flex-col items-start space-y-4">

                        <!-- Kardex Visible -->
                        <div class="flex items-center">
                            <input type="checkbox" id="kardexVisibleInputUpdate" name="kardex_visible" value="0"
                                class="border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-slate-700 checked:border-slate-700 dark:checked:bg-zink-400 dark:checked:border-zink-400 checked:disabled:bg-zink-500 checked:disabled:border-zink-500"
                                onchange="this.value = this.checked ? '0' : '1'">
                            <label for="kardexVisibleInputUpdate" class="inline-block text-base font-medium ml-2">
                                Kardex Visible
                            </label>
                        </div>

                        <!-- Auto Sábados -->
                        <div class="flex items-center">
                            <input type="checkbox" id="autoSabadosInputUpdate" name="auto_sabados" value="0"
                                class="border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-slate-700 checked:border-slate-700 dark:checked:bg-zink-400 dark:checked:border-zink-400 checked:disabled:bg-zink-500 checked:disabled:border-zink-500"
                                onchange="this.value = this.checked ? '0' : '1'">

                            <label for="autoSabadosInputUpdate" class="inline-block text-base font-medium ml-2">
                                Auto Sábados
                            </label>
                        </div>

                    </div>

                    <div class="flex items-center mb-4">
                        <input type="checkbox" id="biometricoRegistroInputUpdate" name="biometrico_registro" value="1"
                            class="border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 
                  dark:bg-zink-600 dark:border-zink-500 checked:bg-slate-700 checked:border-slate-700 
                  dark:checked:bg-zink-400 dark:checked:border-zink-400 checked:disabled:bg-zink-500 
                  dark:checked:border-zink-400" onchange="toggleBiometricoFieldUpdate(this)">

                        <label for="biometricoRegistroInputUpdate" class="inline-block text-base font-medium ml-2">
                            Registrar en Biométrico?
                        </label>
                    </div>

                    <!-- Contenedor condicional para el campo de biometricos (Modo edición) -->
                    <div id="biometricoFieldContainerUpdate"
                        class="grid grid-cols-1 gap-4 md:grid-cols-1 mb-3 {{ !old('biometrico_registro') ? 'hidden' : '' }}">

                        <div>
                            <label for="biometricoSelectUpdate" class="inline-block mb-2 text-base font-medium">
                                Biométricos <span class="text-red-500">*</span>
                            </label>

                            <select id="biometricoSelectUpdate" name="biometricos[]" multiple class="form-input"
                                {{ old('biometrico_registro') ? 'required' : '' }}>
                                @foreach ($biometricos as $biometrico)
                                <option value="{{ $biometrico->id }}">
                                    {{ $biometrico->nombre_biometrico }} (IP: {{ $biometrico->ip_biometrico }})
                                </option>
                                @endforeach
                            </select>

                            @if (old('biometrico_registro') && $errors->has('biometricos'))
                            <div class="mt-1 text-sm text-red-500">{{ $errors->first('biometricos') }}</div>
                            @endif
                        </div>
                    </div>

                    <script>
    let horarioChoicesInstance = null;
    let biometricoChoicesInstance = null;

    document.addEventListener("DOMContentLoaded", function () {
        const horarioSelect = document.getElementById("horarioSelectUpdate");
        if (horarioSelect && !horarioChoicesInstance) {
            horarioChoicesInstance = new Choices(horarioSelect, {
                removeItemButton: true,
                shouldSort: false,
                placeholder: true,
            });
        }

        const biometricoSelect = document.getElementById("biometricoSelectUpdate");
        if (biometricoSelect && !biometricoChoicesInstance) {
            biometricoChoicesInstance = new Choices(biometricoSelect, {
                removeItemButton: true,
                shouldSort: false,
                placeholder: true,
            });
        }

        // Manejo de botones para biométricos
        document.querySelectorAll("[data-biometricos]").forEach(el => {
            el.addEventListener("click", () => {
                const ids = el.dataset.biometricos.split(",");
                if (biometricoChoicesInstance) {
                    biometricoChoicesInstance.removeActiveItems();
                    ids.forEach(id => {
                        biometricoChoicesInstance.setChoiceByValue(id);
                    });
                }
            });
        });

        // Manejo de botones para horarios
        document.querySelectorAll("[data-horarios]").forEach(el => {
            el.addEventListener("click", () => {
                const ids = el.dataset.horarios.split(",");
                if (horarioChoicesInstance) {
                    horarioChoicesInstance.removeActiveItems();
                    ids.forEach(id => {
                        horarioChoicesInstance.setChoiceByValue(id);
                    });
                }
            });
        });
    });
</script>


                </div>
                <div
                    class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">

                    <button type="button" data-modal-close="updateEmpleadoModal"
                        class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">
                        <i data-lucide="x" class="inline-block size-4"></i> <span class="align-middle">Cancel</span>
                    </button>

                    <button type="submit" onclick="submitFormUpdate" form="modalFormUpdateEmpleado"
                        class="ml-2 text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- modal schedule --}}
<div id="extraLargeModalSchedule" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div class="w-screen lg:w-[55rem] xl:w-[55rem] md:w-[55rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full">
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-16" id="dataEmployee"></h5>
            <button data-modal-close="extraLargeModalSchedule"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                <i data-lucide="x" class="size-5"></i>
            </button>
        </div>
        <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
            <form id="extraLargeModalScheduleSend">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-4 mb-3">
        <!-- Descripción -->
        <div>
            <label for="descripcionInputSchedule" class="inline-block mb-2 text-base font-medium">
                Descripción <span class="text-red-500">*</span>
            </label>
            <textarea disabled id="descripcionInputSchedule"
                class="form-textarea min-h-[3rem] w-full resize-none border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                    dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                    dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 
                    dark:placeholder:text-zink-200"></textarea>
        </div>

        <!-- Tolerancia -->
        <div>
            <label for="toleranciaInput" class="inline-block mb-2 text-base font-medium">
                Tolerancia <span class="text-red-500">*</span>
            </label>
            <textarea disabled id="toleranciaInput"
                class="form-textarea min-h-[3rem] w-full resize-none border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                    dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                    dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 
                    dark:placeholder:text-zink-200"></textarea>
        </div>

        <!-- Entrada 1 -->
        <div>
            <label for="entrada1Input" class="inline-block mb-2 text-base font-medium">
                Entrada 1 <span class="text-red-500">*</span>
            </label>
            <textarea disabled id="entrada1Input"
                class="form-textarea min-h-[3rem] w-full resize-none border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                    dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                    dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 
                    dark:placeholder:text-zink-200"></textarea>
        </div>
        <!-- Salida 1 -->
        <div>
            <label for="salida1Input" class="inline-block mb-2 text-base font-medium">
                Salida 1 <span class="text-red-500">*</span>
            </label>
            <textarea disabled id="salida1Input"
                class="form-textarea min-h-[3rem] w-full resize-none border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                    dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                    dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 
                    dark:placeholder:text-zink-200"></textarea>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4 mb-3">

        <!-- Entrada 2 -->
        <div>
            <label for="entrada2Input" class="inline-block mb-2 text-base font-medium">
                Entrada 2 <span class="text-red-500">*</span>
            </label>
            <textarea disabled id="entrada2Input"
                class="form-textarea min-h-[3rem] w-full resize-none border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                    dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                    dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 
                    dark:placeholder:text-zink-200"></textarea>
        </div>

        <!-- Salida 2 -->
        <div>
            <label for="salida2Input" class="inline-block mb-2 text-base font-medium">
                Salida 2 <span class="text-red-500">*</span>
            </label>
            <textarea disabled id="salida2Input"
                class="form-textarea min-h-[3rem] w-full resize-none border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                    dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                    dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 
                    dark:placeholder:text-zink-200"></textarea>
        </div>
        <!-- Observaciones -->
        <div>
            <label for="observacionesInput" class="inline-block mb-2 text-base font-medium">
                Observaciones <span class="text-red-500">*</span>
            </label>
            <textarea disabled id="observacionesInput"
                class="form-textarea min-h-[3rem] w-full resize-none border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 
                    dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 
                    dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 
                    dark:placeholder:text-zink-200"></textarea>
        </div>
    </div>
</form>


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
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAMAAAD04JH5AAAC8VBMVEUAAAD/6u7/cZD/3uL/5+r/T4T9O4T/4ub9RIX/ooz/7/D/noz+PoT/3uP9TYf/XoX/m4z/oY39Tob/oYz/oo39O4T9TYb/po3/n4z/4Ob/3+X/nIz+fon/4eb/nI39Xoj9fIn/8fP9SoX9coj/noz/XYb/6e38R4b/XIf/cIn/ZYj/Rof/6+//cIr/oYz/a4P/7/L+X4f+bYn+QoX/pIz/7vH/noz/8PH/7O7/4ub/oIz/moz/oY3/O4X/cYn/RYX+aIj/5+r9QYX+XYf+cYn+Z4j+i5j9PoT/po3/8vT/ucD/09f+hYr/8vT8R4X8UYb/3uH+ZIn+W4f+cIn/7O/+hIr+VYf+b4j+ZYj+VYb/6Ov9RYX9UIb9bYn9O4T/oIz9Y4f9WIb/gov/bIj/dYr/gYr/pY3/7e//dYr9PoX/pY3/8vL/PID/7/L+hor+hor/8fP/8fP/o43/o43/7O//n4v/n47/nI7/8PL/6+7/6ez/5+v9QIX/7fD9SoX9SIX9RYX9Q4X+YIf/6u7/7/H+g4r+gYr+gIr+for+fYr+cYn9O4T+e4n+a4j+ZYj+VYb9T4b9PYT+eIn9TYb/8vT+dYn+c4n+don+cIj+Zoj+bYj+aIj+XYf+Yof+W4f/xs/+Wof9U4b+V4b/0Nf/ur3+hor+hYr/1Nv/oY39TIb+eon/1t3/3eL/3+T/0dn/y9P/m4z+aoj9Uob+WYf9UYb/ydL/yNH/2+H/ztb/xM7/197/2uD/0tr/zNT/2d//zdX/noz/w83/4eb/oIz/2N//o43/pI3/nYz/uMX/qr7/u8f/pY3/vcn/p7v/wcv/tMP/ssL/r8H/rb//usf/wMv/tcP+kKL+h5f/sr7/o7f/oLT/k6/+mav+kKr+lKH+fqH+bZf+dJb+hJH9X5H+e4z/v8n+iKX+h6H/rL//rbr/mrP/mbD+dp3+fpz+jJv+fpf9ZJT+e5D+aZD/qbf+oa/+hp3+bpD+co/+ZI/+Xoz9Vos1azWoAAAAeHRSTlMAvwe8iBv3u3BtPR61ZUcx9/Xy7ebf3dHPt7Gtqqebm5aMh4V3cXBcW1pGMSUaEgX729qtqqmll3VlRT84Ny8g/vr48fDw7u7t5tzVz8vIx8bGxsW/u7KwsLCmnZybko6Ghn1wb2hkX0Q+KhMT+eTjx8bDwa1NSEgfarKCAAAHAElEQVR42uzTv2qDQBwH8F/cjEtEQUEQBOkUrIMxRX2AZMiWPVsCCYX+rxacmkfIQzjeIwRK28GXKvQ0talytvg7MvRz2/c47ntwP/i7tehpkzyfaJ64Bu4EUcsrNFEArpbq2xF1CfxIN681biXgJFSyWkoEXARy1kAOgINIzhrJEaBz1Jcvur9Y+HolUB3AZuxLii3RSLKVQ+gBsvt9yaw81jEP8QPg0t8LInwjlrkOqB5JwYYjNikEgMkglNG85QMiYUA+DST4QSr3zgFPSCgTapiECqEDfWs2jXediaczq/+b669iBNetK1zQA7sOF2VBK+MYzbjd+xGdAdPwMkbkDoFltEU1AoaNu0XlbhgFVimyFWsEUmSsUbxLkLE+wTxJUsSVJHNGgV6CrHfyBZ6RnX6BJ2T/BT5orWOXBOIogOMPCoTg/gBFQQiCoAiaagmCaKiGlpbGKGiqP8C51HA60MYGqyF/56ig4CAOIuIk3g1yg5yDiyD6B+Tdc/i9Gn734Odn/HLv8bjppzrgNrVmt6rXWGrNtkDh6DS1RqdhXiQ7m0uf2vlbd/YgrKcvzZ6B5+pbsyvguXnR7AZ44i+axYEn+apZEnjuXjW7A56HtGYPENZxIhKJXF+kNbu4Xq5NHINStBmoZDSr4N4oKBhNVMxoVmwi1T9IWKiU1axkoVjIA0RWMxHyAMNaGeW0GlkrBihELWTntLItFAUlI7axdHn+89fIHf1r3nTqhfrw/NLfGjMgtLhJeR0hhJOj0S0LUXZp8xwhRMczqThwJU2qI3wT0uya32o2iRPh65hUEri23wlbBBqeHB2MjtzMWtCqNp3fBq57usAVaCrHHrae3KYCuXT+Hrh288SgigZy7GHrKT707QLXY56wq2ioOmBYRTadfwSukwIxq6OFHPvY+nJb1NGMzp8A136ByLdw71x1wBxbK0/n94HroPBGFBsBR25jbGO5OdiKdLpwAGxndEUFF7dVB7SxfdDpM+A7pCvGrUBfbl1sXbn1aVs5BL7fVsjktYkwDOMvAwk5hAQEey1USmuLiHp2QRFvigouuKB4EvwTxO2ouOHFfT2ICAaXiBFFvNWQybSJFZI0JKGQaFtpLbiexHm/+eZ7AlXnnfnd5sf7PN+TbL8MjL90yZquwK5guiy7cUxvp+DsxIpPXPzoXwMesfuE6Z0UnH1XgepD5rThCqwKhjqtzqqY3kfBWYIVE6r5i+HyrPKG+qLOJjC9hIJz6CzwQTXPGs4bYKhZdfYB04coOEux4ut9pmMOYGUO6Kizr5heSsEZwopZ1Wz+tDKrsvlHqbNZTA9RcNKPge+qecJw3gBDTaiz75heQ8FZdg14/Iqbq4YbYTViqCqrV48xvYyCY63DjswrF9scwMocYLPKYHadRQI2XgHec/WYobwBhhpj9R6zG0nCCiwZeeQy8ndVRqVYSRK2ngNKXP3WUN4AQ71lVcLsVpKwC0sqXJ0x1DircUNlWFUwu4sk9GLJ9D3mijGAjTHgijqaxmwvSThwA6ir7m++8gb45ps6qmP2AEnox5KO6m75ymHj+KaljjqY7ScJg6eAz6r7s6+8AQsdaQZJwhCWtF4wHV+Nshn1TVsdtTA7RBLSWDKvuut/G1BXR/OYTZOE2Cnk9RuXaWMAG2PANJvXXdEYSbCuIzkur/jGG+CbCptcV9QiERuwpfzaxfbNGJsx37xjU8bkBpKx4iagnhs1DQ/wzSgaxQqSsQ1r7IxL3hjAxnguz8bG5DaSseM2MMXlOd+U2JR8k2MzhcndJKMXa2pcnr2+8IDrWTY1TPaSjINPgXaW+aFNiUVJix/qpI3JgySj/y7QUO1NbbwBWjTVSQOT/SRjEGtaz5kZbT6y+KjFjDppYXKQZKTOA/OqvaGNN0CLhjqZx2SKZKSx5uctpq3NOxbvtGirk5+YTJOM2HlEtdcXHlBXJ13BGMmw7iAFbp/SwhugxRSLQlfQIiGLsMfh+srCAyosHMwtIik9TwDvvQDCpYekbHkGVHMujhY2C1sLh0UVc1tIyo4LQI3ry1p4A7Qos6hhbjdJ2YtFjbcutr+IRc1fxKKBub0kpQ+LfjlufVOLycKf78KkFk33wPmFuT6SkriETNrFYn7GEE2nWHSahpjJF4v2ZFcsQVIG3DxMmHsC3xfm5vDgyZz7PDBAUlIPIiFFUoaPRcIwSVkbzYAYSbGiGWCRmEXHI2ARyemJYkAPydkcxYDNJCd5IgJWkZw9UQzYQ3L6ohjQR3ISJyMgQXIGohgwQHKGoxgwTHKs9UdDs345hWBV+AGrKAyp8AMOUyiSYd9PUjjWbroYik1rKSSr42Hejx+m0KxefEbM4tUUAUf2x2XPx/cfoWiIJZKLA46IL04mYvQf/AaSGokYCo6ekAAAAABJRU5ErkJggg=="
                alt="" class="block h-12 mx-auto">
            <div class="mt-5 text-center">
                <h5 class="mb-1">Estas Seguro?</h5>
                <p class="text-slate-500 dark:text-zink-200">Estas seguro de Eliminar el Empleado?</p>
                <div class="flex justify-center gap-2 mt-6">

                    @if (isset($empleado->id))
                    <form action="{{ route('empleados.destroy', $empleado->id) }}" method="POST" class="inline mt-6">
                        @csrf
                        @method('DELETE')
                        <button type="reset" data-modal-close="deleteModal"
                            class="bg-white text-slate-500 btn hover:text-slate-500 hover:bg-slate-100 focus:text-slate-500 focus:bg-slate-100 active:text-slate-500 active:bg-slate-100 dark:bg-zink-600 dark:hover:bg-slate-500/10 dark:focus:bg-slate-500/10 dark:active:bg-slate-500/10">Cancelar</button>

                        <input type="hidden" id="empleadoId" name="empleadoId">

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