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
        flatpickr("#fechaPermiso, #fecha_permisoUpdate,#fecha_fin_varios_dias,#fecha_fin_varios_diasUpdate", {
            enableTime: false, // Deshabilitar la selección de tiempo
            dateFormat: "d-m-Y", // Formato de fecha
            time_24hr: true, // Usar formato de 24 horas
            weekNumbers: true, // Mostrar números de la semana
            allowInput: true, // Habilita la escritura manual
        });
        flatpickr("#fechaFinReporte, #fechaInicioReporte", {
            // mode: "range", // Permite seleccionar un rango de fechas
            dateFormat: "d-m-Y", // Formato de fecha deseado
            locale: "es", // Para idioma español (requiere incluir el locale si necesario)
            // allowInput: false, // Evita que el usuario escriba manualmente
            //altInput: true,
            // altFormat: "F j, Y", // Formato alternativo visible
            altFormat: "d-m-Y",
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
            dom: 'Brt',
            buttons: [{
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
            //paging: true,
            pageLength: 5,
            //lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            //scrollX: true,
            language: translations,
            //"lengthMenu": [5, 10, 25],
        });
    });
</script>
<script>
    function deleteModalPermiso(element) {
        document.getElementById('permisoId').value = element.dataset.id;
    }

    function fillModal(element) {
        const formatearFecha = (fechaStr) => {
            if (!fechaStr) return '';
            const [año, mes, dia] = fechaStr.split('-');
            return `${dia}-${mes}-${año}`;
        }
        // Asegúrate de que todos los IDs son correctos
        document.getElementById('fecha_permisoUpdate').value = formatearFecha(element.dataset.fecha_permiso);
        document.getElementById('id_personaSelectUpdate').value = element.dataset.id_persona;
        document.getElementById('motivoUpdate').value = element.dataset.motivo;
        document.getElementById('observacionUpdate').value = element.dataset.observacion;
        // Convertir el dataset de empleado a un objeto JSON
        let empleado = JSON.parse(element.dataset.empleado);
        document.getElementById('tipoContratoUpdate').value = empleado.tipo_contrato.descripcion ? ? 'sin contrato';
        // Seleccionar correctamente la duración del permiso
        const duracionPermiso = element.dataset.duracion_permiso;
        document.getElementById('diaCompletoUpdate').checked = duracionPermiso === 'dia_completo';
        document.getElementById('medioDiaUpdate').checked = duracionPermiso === 'medio_dia';
        document.getElementById('variosDiasUpdate').checked = duracionPermiso === 'varios_dias';
        // Manejo de visibilidad según el tipo de permiso
        document.getElementById('horariosMedioDiaUpdate').classList.toggle('hidden', duracionPermiso !== 'medio_dia');
        document.getElementById('fechasVariosDiasUpdate').classList.toggle('hidden', duracionPermiso !== 'varios_dias');
        // Asignar valores según el tipo de permiso
        if (duracionPermiso === 'medio_dia') {
            document.getElementById('hora_inicioUpdate').value = element.dataset.hora_inicio || '';
            document.getElementById('hora_finUpdate').value = element.dataset.hora_fin || '';
        } else {
            document.getElementById('hora_inicioUpdate').value = '';
            document.getElementById('hora_finUpdate').value = '';
        }
        if (duracionPermiso === 'varios_dias') {
            document.getElementById('fecha_fin_varios_diasUpdate').value = formatearFecha(element.dataset
                .fecha_fin_varios_dias) || '';
        } else {
            document.getElementById('fecha_fin_varios_diasUpdate').value = '';
        }
        // Establecer la acción del formulario con la URL correcta
        const modal = document.querySelector('#modalFormUpdatePermiso');
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

{{-- script anteriores --}}
<div class="flex-1">
    <label for="empleadoSelect" class="inline-block mb-2 text-base font-medium">
        Empleado <span class="text-red-500">*</span>
    </label>
    <select id="empleadoSelect" name="id_persona"
        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('id_persona')) border-red-500 @endif"
        data-choices data-choices-sorting-false>
        <option value="" selected disabled>Seleccione Empleado</option>
        @foreach ($empleados as $empleado)
        <option value="{{ $empleado->id }}"
            data-contrato="{{ $empleado->tipoContrato->descripcion ?? 'Sin contrato' }}"
            {{ old('request_method') === 'POST' && old('id_persona') == $empleado->id ? 'selected' : '' }}>
            {{ $empleado->ci }} - {{ $empleado->nombres }}
            {{ $empleado->apellido_pat }} {{ $empleado->apellido_mat }}
        </option>
        @endforeach
    </select>
    {{-- Mensaje de error --}}
    @if (old('request_method') === 'POST')
    @error('id_persona')
    <div id="empleado-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
    @enderror
    @endif
</div>

<div class="flex-1">
    <label for="tipoContrato" class="inline-block mb-2 text-base font-medium">
        Tipo de Contrato
    </label>
    <input type="text" id="tipoContrato" readonly
        class="form-input border-slate-200 dark:border-zink-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 dark:text-zink-200 disabled:text-slate-500 dark:bg-zink-700 dark:placeholder:text-zink-200"
        placeholder="Seleccione un empleado" />
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const empleadoSelect = document.getElementById("empleadoSelect");
    const tipoContratoInput = document.getElementById("tipoContrato");
    
    // Función para actualizar el tipo de contrato
    function actualizarTipoContrato() {
        const selectedOption = empleadoSelect.options[empleadoSelect.selectedIndex];
        const tipoContrato = selectedOption.getAttribute("data-contrato") || "Sin contrato";
        tipoContratoInput.value = tipoContrato;
        
        // Si es la opción por defecto, limpiar el campo
        if(empleadoSelect.selectedIndex === 0) {
            tipoContratoInput.value = "";
        }
    }
    
    // Escuchar cambios en el select
    empleadoSelect.addEventListener("change", actualizarTipoContrato);
    
    // Actualizar al cargar si ya hay un valor seleccionado
    if(empleadoSelect.selectedIndex > 0) {
        actualizarTipoContrato();
    }
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
        flatpickr("#fechaInput, #fechaFeriadoUpdate", {
            enableTime: false, // Deshabilitar la selección de tiempo
            dateFormat: "Y-m-d", // Formato de fecha
            time_24hr: true, // Usar formato de 24 horas
            weekNumbers: true // Mostrar números de la semana
        });
    });
</script>

<script>
    function showRejectModal(permisoId) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        // Configurar la acción del formulario con la ruta nombrada
        form.action = `/reject-permiso/${permisoId}`;
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
@if ($errors->hasAny(['motivo_rechazo']))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Buscar el enlace de rechazo
        const rejectLink = document.querySelector('a[data-modal-target="rejectModal"]');
        // Si existe el enlace y hay errores, hacer click automáticamente
        if (rejectLink) {
            rejectLink.click();
        }
    });
</script>
@endif

@if (
$errors->hasAny([
'fecha_permiso',
'id_persona',
'motivo',
'observacion',
'duracion_permiso',
'hora_inicio',
'hora_fin',
'fecha_fin_varios_dias',
]))
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
                '[data-modal-target="updatePermisoModal"]'); // Botón del modal de actualización
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
            form = document.querySelector('#largeModal'); // Formulario del modal de creación
        } else if (requestMethod === 'PUT') {
            form = document.querySelector('#updatePermisoModal'); // Formulario del modal de actualización
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
                <h5 class="text-16">Permisos</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li
                    class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="#" class="text-slate-400 dark:text-zink-200">Inasistencia</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    permiso
                </li>
            </ul>

        </div>

        <div class="flex justify-between items-center">
            @can('crear_permiso')
            <button data-modal-target="largeModal" type="button" id="extraLargeModalButton"
                class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:focus:ring-custom-400/20">Crear
                Permiso </button>
            @endcan

            @can('generar_reporte_permiso')
            <button data-modal-target="defaultModal" type="button"
                class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Generar
                Reporte</button>
            @endcan

        </div>

        <div class="grid grid-cols-12 gap-4 mt-2">

            <!-- Sección de 8 columnas -->
            <div class="col-span-12 md:col-span-12">
                <div class="card">
                    <div class="card-body">
                        <div class="overflow-x-auto">
                        <form method="GET" action="{{ route('permisos.index') }}" class="mb-6">
    <div class="flex items-center">
        <input 
            type="text" 
            name="search" 
            value="{{ request('search') }}" 
            placeholder="Buscar por CI..." 
            class="form-input w-full max-w-xs border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:focus:border-custom-800 bg-white dark:bg-zink-700 text-slate-600 dark:text-zink-100 placeholder:text-slate-400 dark:placeholder:text-zink-200 rounded-l-md"
        />
        <button 
            type="submit" 
            class="px-4 py-2 bg-custom-500 border-custom-500 text-white hover:bg-custom-600 hover:border-custom-600 focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:bg-custom-600 active:border-custom-600 dark:ring-custom-400/20 rounded-r-md transition-colors"
        >
            <i data-lucide="search" class="inline-block size-4 mr-1"></i> Buscar
        </button>
        @if(request()->has('search') && request('search') != '')
        <a 
            href="{{ route('permisos.index') }}" 
            class="ml-2 px-3 py-2 bg-slate-100 border-slate-200 text-slate-600 hover:bg-slate-200 hover:border-slate-300 dark:bg-zink-600 dark:border-zink-600 dark:text-zink-200 dark:hover:bg-zink-500 rounded-md transition-colors"
        >
            <i data-lucide="x" class="inline-block size-4"></i>
        </a>
        @endif
    </div>
</form>
                            <table id="rowSelectionDeletion" class="display" style="width:100%">
                                <thead>
                                    <tr class="bg-gray-100 text-left text-gray-600 text-sm font-medium uppercase">
                                        <th class="py-3 px-4 border-b border-gray-300">Acciones</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Estado</th>

                                        <th class="py-3 px-4 border-b border-gray-300">Fecha</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Empleado</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Motivo</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Observación</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Duración Permiso</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permisos as $permiso)
                                    <tr>
                                        <!-- Acciones (Editar / Dar Baja) -->
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            <div class="relative dropdown">
                                                <button id="orderAction1" data-bs-toggle="dropdown"
                                                    class="flex items-center justify-center size-[30px] dropdown-toggle p-0 text-slate-500 btn bg-slate-100 hover:text-white hover:bg-slate-600 focus:text-white focus:bg-slate-600 focus:ring focus:ring-slate-100 active:text-white active:bg-slate-600 active:ring active:ring-slate-100 dark:bg-slate-500/20 dark:text-slate-400 dark:hover:bg-slate-500 dark:hover:text-white dark:focus:bg-slate-500 dark:focus:text-white dark:active:bg-slate-500 dark:active:text-white dark:ring-slate-400/20">
                                                    <i data-lucide="more-horizontal" class="size-3"></i>
                                                </button>
                                                <ul class="absolute z-50 hidden py-2 mt-1 ltr:text-left rtl:text-right list-none bg-white rounded-md shadow-md dropdown-menu min-w-[10rem] dark:bg-zink-600"
                                                    aria-labelledby="orderAction1">
                                                    <!-- Editar -->

                                                    @if ($permiso->id_persona)
                                                    @can('editar_permiso')
                                                    <li>
                                                        <a data-modal-target="updatePermisoModal"
                                                            data-fecha_permiso="{{ $permiso->fecha_permiso }}"
                                                            data-id_persona="{{ $permiso->id_persona }}"
                                                            data-empleado="{{ $permiso->empleado }}"
                                                            data-motivo="{{ $permiso->motivo_permiso_id }}"
                                                            data-observacion="{{ $permiso->observacion }}"
                                                            data-duracion_permiso="{{ $permiso->duracion_permiso }}"
                                                            data-hora_inicio="{{ $permiso->hora_inicio }}"
                                                            data-hora_fin="{{ $permiso->hora_fin }}"
                                                            data-fecha_fin_varios_dias="{{ $permiso->fecha_fin_varios_dias }}"
                                                            data-url="{{ route('permisos.update', ['permiso' => $permiso->id]) }}"
                                                            class="block px-4 py-1.5 text-base transition-all duration-200 ease-linear text-slate-600 dropdown-item hover:bg-slate-100 hover:text-slate-500 focus:bg-slate-100 focus:text-slate-500 dark:text-zink-100 dark:hover:bg-zink-500 dark:hover:text-zink-200 dark:focus:bg-zink-500 dark:focus:text-zink-200"
                                                            onclick="fillModal(this)">
                                                            <i data-lucide="file-edit"
                                                                class="inline-block size-3 ltr:mr-1 rtl:ml-1"></i>
                                                            <span class="align-middle">Editar</span>
                                                        </a>
                                                    </li>
                                                    @endcan

                                                    @endif

                                                    <!-- Dar Baja -->
                                                    {{-- <li>
                                                                <a id="deleteButton" data-id="{{ $permiso->id }}"
                                                    data-modal-target="deleteModal"
                                                    class="block px-4 py-1.5 text-base transition-all duration-200
                                                    ease-linear text-slate-600 dropdown-item hover:bg-slate-100
                                                    hover:text-slate-500 focus:bg-slate-100 focus:text-slate-500
                                                    dark:text-zink-100 dark:hover:bg-zink-500 dark:hover:text-zink-200
                                                    dark:focus:bg-zink-500 dark:focus:text-zink-200"
                                                    href="#!" onclick="deleteModalPermiso(this)">
                                                    <i data-lucide="trash-2"
                                                        class="inline-block size-3 ltr:mr-1 rtl:ml-1"></i>
                                                    <span class="align-middle">Dar Baja</span>
                                                    </a>
                                                    </li> --}}

                                                    @if ($permiso->estado != 'cancelado')
                                                    @can('aceptar_rechazar_permiso')
                                                    <li>
                                                        <form action="{{ route('permisos-approve', $permiso->id) }}"
                                                            method="POST" class="inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit"
                                                                class="w-full text-left px-4 py-1.5 text-base transition-all duration-200 ease-linear text-green-600 dropdown-item hover:bg-green-50 hover:text-green-500 focus:bg-green-50 focus:text-green-500 dark:text-green-400 dark:hover:bg-green-500/20 dark:hover:text-green-300 dark:focus:bg-green-500/20 dark:focus:text-green-300">
                                                                <i data-lucide="check-circle"
                                                                    class="inline-block size-3 ltr:mr-1 rtl:ml-1"></i>
                                                                <span class="align-middle">Aceptar</span>
                                                            </button>
                                                        </form>
                                                    </li>

                                                    <!-- Rechazar -->
                                                    <li>
                                                        <!-- Botón que abre el modal -->
                                                        <a href="#" data-modal-target="rejectModal"
                                                            onclick="showRejectModal({{ $permiso->id }})"
                                                            class="flex items-center w-full px-4 py-1.5 text-base transition-all duration-200 ease-linear text-red-600 dropdown-item hover:bg-red-50 hover:text-red-500 focus:bg-red-50 focus:text-red-500 dark:text-red-400 dark:hover:bg-red-500/20 dark:hover:text-red-300 dark:focus:bg-red-500/20 dark:focus:text-red-300">
                                                            <i data-lucide="x-circle"
                                                                class="inline-block size-3 ltr:mr-1 rtl:ml-1"></i>
                                                            <span class="align-middle">Rechazar</span>
                                                        </a>
                                                    </li>
                                                    @endcan

                                                    @else
                                                    <li>
                                                        <a
                                                            class="py-1 text-xs text-slate-500 bg-slate-200 border-slate-200 btn
                                                                 hover:text-slate-600 hover:bg-slate-300 hover:border-slate-300 
                                                                 focus:text-slate-600 focus:bg-slate-300 focus:border-slate-300 focus:ring focus:ring-slate-100 active:text-slate-600 active:bg-slate-300 active:border-slate-300 active:ring active:ring-slate-100 dark:bg-zink-600 dark:hover:bg-zink-500 dark:border-zink-600 dark:hover:border-zink-500 dark:text-zink-200 dark:ring-zink-400/50">
                                                            Cancelado
                                                        </a>
                                                    </li>
                                                    @endif

                                                </ul>
                                            </div>
                                        </td>

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

                                       <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                @php
                                                    $nombreCompleto = '';
                                                    
                                                    // Primero intentar con empleado
                                                    if ($permiso->id_persona && $permiso->empleado) {
                                                        $nombreCompleto = trim(
                                                            ($permiso->empleado->nombres ?? '') . ' ' .
                                                            ($permiso->empleado->apellido_pat ?? '') . ' ' .
                                                            ($permiso->empleado->apellido_mat ?? '')
                                                        );
                                                    }
                                                    // Si no hay empleado, intentar con solicitante->persona
                                                    elseif ($permiso->solicitante_id && $permiso->solicitante && $permiso->solicitante->persona) {
                                                        $nombreCompleto = trim(
                                                            ($permiso->solicitante->persona->nombres ?? '') . ' ' .
                                                            ($permiso->solicitante->persona->apellido_pat ?? '') . ' ' .
                                                            ($permiso->solicitante->persona->apellido_mat ?? '')
                                                        );
                                                    }
                                                @endphp

                                                {{ $nombreCompleto ?: 'N/D' }}
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
                                        <th class="py-3 px-4 border-b border-gray-300">Empleado</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Motivo</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Observación</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Duración Permiso</th>

                                    </tr>
                                </tfoot>
                            </table>


      
                          <div class="mt-4 flex justify-center space-x-1">
    {{ $permisos->onEachSide(1)->links('vendor.pagination.tailwind-navigation') }}
</div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Modal de Rechazo -->
<div id="rejectModal" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4">
    <div class="w-screen lg:w-[40rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full">
        <!-- Encabezado del Modal -->
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <div class="flex items-center">
                <h5 class="text-16">Rechazar Permiso</h5>
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
                        ¿Estás seguro que deseas rechazar este permiso? Por favor indica el motivo.
                    </p>
                </div>

                <div class="mt-3">
                    <label for="motivo_rechazo" class="inline-block mb-2 text-base font-medium">
                        Motivo del rechazo <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @error('motivo_rechazo') border-red-500 @enderror"
                        name="motivo_rechazo" id="motivo_rechazo" rows="3"
                        placeholder="Describe el motivo del rechazo...">
                            @if (old('request_method') === 'POST')
{{ old('motivo_rechazo') }}
@endif
                            </textarea>

                    @error('motivo_rechazo')
                    <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
                    @enderror
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
            <h5 class="text-16">Permisos</h5>
            <button data-modal-close="largeModal"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500"><i
                    data-lucide="x" class="size-5"></i></button>
        </div>
        <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
            <form id="modalFormPermiso" action="{{ route('permisos.store') }}" method="POST">
                @csrf

                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <div class="flex-1">
                        <label for="fechaPermiso" class="inline-block mb-2 text-base font-medium">
                            Fecha del Permiso <span class="text-red-500">*</span>
                        </label>
                        <input type="text" value="{{ old('request_method') === 'POST' ? old('fecha_permiso') : '' }}"
                            id="fechaPermiso" name="fecha_permiso"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('fecha_permiso')) border-red-500 @endif"
                            data-provider="flatpickr" data-date-format="d M Y" data-week-number="" readonly="readonly"
                            placeholder="DD-MM-YYYY">
                        {{-- Mensaje de error --}}
                        @if (old('request_method') === 'POST')
                        @error('fecha_permiso')
                        <div id="tipo-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                   <div class="flex-1">
    <label for="empleadoSelect" class="inline-block mb-2 text-base font-medium">
        Empleado <span class="text-red-500">*</span>
    </label>
    <select id="empleadoSelect" name="id_persona"
        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('id_persona')) border-red-500 @endif"
        data-choices data-choices-sorting-false>
        <option value="" selected disabled>Seleccione Empleado</option>
        @foreach ($empleados as $empleado)
        <option value="{{ $empleado->id }}"
            data-contrato="{{ $empleado->tipoContrato->descripcion ?? 'Sin contrato' }}"
            {{ old('request_method') === 'POST' && old('id_persona') == $empleado->id ? 'selected' : '' }}>
            {{ $empleado->ci }} - {{ $empleado->nombres }}
            {{ $empleado->apellido_pat }} {{ $empleado->apellido_mat }}
        </option>
        @endforeach
    </select>
    {{-- Mensaje de error --}}
    @if (old('request_method') === 'POST')
    @error('id_persona')
    <div id="empleado-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
    @enderror
    @endif
</div>

<div class="flex-1">
    <label for="tipoContrato" class="inline-block mb-2 text-base font-medium">
        Tipo de Contrato
    </label>
    <input type="text" id="tipoContrato" readonly
        class="form-input border-slate-200 dark:border-zink-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 dark:text-zink-200 disabled:text-slate-500 dark:bg-zink-700 dark:placeholder:text-zink-200"
        placeholder="Seleccione un empleado" />
</div>



                </div>

                <div class="mt-3 flex flex-wrap items-center gap-4">

                    {{-- <div class="flex-1">
                            <label for="motivoPermiso" class="inline-block mb-2 text-base font-medium">
                                Motivo <span class="text-red-500">*</span>
                            </label>
                            <input list="feriadosList" id="motivoPermiso" name="motivo" required
                                value="{{ old('request_method') === 'POST' ? old('motivo') : '' }}"
                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500
                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300
                    dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100
                    dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400
                    dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('motivo'))
                    border-red-500 @endif"
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
                <div class="flex-1">
                    <label for="motivoSelect" class="inline-block mb-2 text-base font-medium">
                        Motivo <span class="text-red-500">*</span>
                    </label>
                    <select id="motivoSelect" name="motivo"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if ($errors->has('motivo')) border-red-500 @endif"
                        data-choices data-choices-sorting-false required>
                        <option value="" selected disabled>Seleccione Motivo</option>
                        @foreach ($motivoPermisos as $motivoP)
                        <option value="{{ $motivoP->id }}" {{ old('motivo') == $motivoP->id ? 'selected' : '' }}>
                            {{ $motivoP->descripcion }}
                        </option>
                        @endforeach
                    </select>

                    {{-- Mensaje de error --}}
                    @error('motivo')
                    <div id="motivo-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                </div>
                <div class="flex-1">
                    <label for="observacion" class="inline-block mb-2 text-base font-medium">
                        Observación <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="observacion" name="observacion"
                        value="{{ old('request_method') === 'POST' ? old('observacion') : '' }}"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('observacion')) border-red-500 @endif"
                        placeholder="Ingrese obs" />

                    @if (old('request_method') === 'POST')
                    @error('observacion')
                    <div id="observacion-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                    @endif
                </div>
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
                                <label for="fecha_fin_varios_dias" class="inline-block mb-2 text-base font-medium">
                                    Fecha Fin <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="fecha_fin_varios_dias" name="fecha_fin_varios_dias"
                                    value="{{ old('request_method') === 'POST' ? old('fecha_fin_varios_dias') : '' }}"
                                    class="form-input w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('fecha_fin_varios_dias')) border-red-500 @endif"
                                    placeholder="DD-MM-YYYY" data-provider="flatpickr" data-date-format="d M Y"
                                    {{-- data-date-format="Y-m-d"  --}} {{-- data-alt-format="d M, Y"  --}}
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

{{-- modal update --}}
<div id="updatePermisoModal" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div class="w-screen lg:w-[55rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full">
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-16">Actualizar Permiso</h5>
            <button data-modal-close="updatePermisoModal"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                <i data-lucide="x" class="size-5"></i>
            </button>
        </div>
        <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
            <form id="modalFormUpdatePermiso" action="{{ route('permisos.update', ':id') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <!-- Fecha del Permiso -->
                    <div class="flex-1">
                        <label for="fecha_permisoUpdate" class="inline-block mb-2 text-base font-medium">
                            Fecha del Permiso <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="fecha_permisoUpdate" name="fecha_permiso"
                            value="{{ old('request_method') === 'PUT' ? old('fecha_permiso') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'PUT' && $errors->has('fecha_permiso')) border-red-500 @endif"
                            data-provider="flatpickr" data-date-format="d M Y" data-week-number="" readonly="readonly"
                            placeholder="Select Date">
                        @if (old('request_method') === 'PUT')
                        @error('fecha_permiso')
                        <div id="fecha_permiso-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    <!-- Empleado -->
                    <div class="flex-1">
                        <label for="id_personaSelectUpdate" class="inline-block mb-2 text-base font-medium">
                            Empleado <span class="text-red-500">*</span>
                        </label>
                        <select id="id_personaSelectUpdate" name="id_persona"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'PUT' && $errors->has('id_persona')) border-red-500 @endif">
                            <option value="" selected disabled>Seleccione Empleado</option>
                            @foreach ($empleados as $empleado)
                            <option value="{{ $empleado->id }}"
                                data-contrato="{{ $empleado->tipoContrato->descripcion ?? 'sin contrato' }}"
                                {{ old('request_method') === 'PUT' && old('id_persona') == $empleado->id ? 'selected' : '' }}>
                                {{ $empleado->ci }} - {{ $empleado->nombres }}
                                {{ $empleado->apellido_pat }} {{ $empleado->apellido_mat }}
                            </option>
                            @endforeach
                        </select>
                        @if (old('request_method') === 'PUT')
                        @error('id_persona')
                        <div id="id_persona-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    <!-- Tipo de Contrato -->
                    <div class="flex-1">
                        <label for="tipoContratoUpdate" class="inline-block mb-2 text-base font-medium">
                            Tipo de Contrato
                        </label>
                        <input type="text" id="tipoContratoUpdate" readonly
                            class="form-input border-slate-200 dark:border-zink-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 dark:text-zink-200 disabled:text-slate-500 dark:bg-zink-700 dark:placeholder:text-zink-200"
                            placeholder="Seleccione un empleado" />
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <!-- Motivo -->

                    <div class="flex-1">
                        <label for="motivoUpdate" class="inline-block mb-2 text-base font-medium">
                            Empleado <span class="text-red-500">*</span>
                        </label>
                        <select id="motivoUpdate" name="motivo"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'PUT' && $errors->has('id_persona')) border-red-500 @endif">
                            <option value="" selected disabled>Seleccione Motivo</option>
                            @foreach ($motivoPermisos as $motivoP)
                            <option value="{{ $motivoP->id }}"
                                {{ old('request_method') === 'PUT' && old('motivo') == $motivoP->id ? 'selected' : '' }}>
                                {{ $motivoP->descripcion }}
                            </option>
                            @endforeach
                        </select>
                        @if (old('request_method') === 'PUT')
                        @error('motivo')
                        <div id="id_persona-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- <div class="flex-1">
                            <label for="motivoUpdate" class="inline-block mb-2 text-base font-medium">
                                Motivo <span class="text-red-500">*</span>
                            </label>
                            <input list="feriadosList" id="motivoUpdate" name="motivo" required
                                value="{{ old('request_method') === 'PUT' ? old('motivo') : '' }}"
                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500
                    disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300
                    dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100
                    dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400
                    dark:placeholder:text-zink-200 @if (old('request_method') === 'PUT' && $errors->has('motivo'))
                    border-red-500 @endif"
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
                    @if (old('request_method') === 'PUT')
                    @error('motivo')
                    <div id="motivo-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                    @endif
                </div> --}}

                <!-- Observación -->
                <div class="flex-1">
                    <label for="observacionUpdate" class="inline-block mb-2 text-base font-medium">
                        Observación <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="observacionUpdate" name="observacion"
                        value="{{ old('request_method') === 'PUT' ? old('observacion') : '' }}"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'PUT' && $errors->has('observacion')) border-red-500 @endif"
                        placeholder="Ingrese obs" />
                    @if (old('request_method') === 'PUT')
                    @error('observacion')
                    <div id="observacion-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                    @endif
                </div>

        </div>

        <div class="mt-3">
            <label for="duracion_permisoUpdate" class="inline-block mb-2 text-base font-medium">
                Duración <span class="text-red-500">*</span>
            </label>
            <div class="flex flex-col">
                <!-- Día completo -->
                <div class="flex items-center">
                    <input type="radio" id="diaCompletoUpdate" name="duracion_permiso" value="dia_completo"
                        {{ (old('request_method') === 'PUT' && old('duracion_permiso') === 'dia_completo') || old('duracion_permiso') === 'dia_completo' ? 'checked' : '' }}
                        required>
                    <label for="diaCompletoUpdate" class="ml-2">Día completo</label>
                </div>

                <!-- Medio día -->
                <div class="items-center mt-2">
                    <input type="radio" id="medioDiaUpdate" name="duracion_permiso" value="medio_dia"
                        {{ (old('request_method') === 'PUT' && old('duracion_permiso') === 'medio_dia') || old('duracion_permiso') === 'medio_dia' ? 'checked' : '' }}>
                    <label for="medioDiaUpdate" class="ml-2">Medio día</label>

                    <div id="horariosMedioDiaUpdate"
                        class="ml-4 {{ (old('request_method') === 'PUT' && old('duracion_permiso') === 'medio_dia') || old('duracion_permiso') === 'medio_dia' ? '' : 'hidden' }}">
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="flex-1">
                                <label for="hora_inicioUpdate" class="inline-block mb-2 text-base font-medium">
                                    Inicio <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="hora_inicioUpdate" name="hora_inicio"
                                    value="{{ old('request_method') === 'PUT' ? old('hora_inicio') : '' }}"
                                    class="form-input w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'PUT' && $errors->has('hora_inicio')) border-red-500 @endif"
                                    placeholder="Selecciona una hora">
                                @if (old('request_method') === 'PUT')
                                @error('hora_inicio')
                                <div id="hora_inicio-error" class="mt-1 text-sm text-red-500">
                                    {{ $message }}</div>
                                @enderror
                                @endif
                            </div>
                            <div class="flex-1">
                                <label for="hora_finUpdate" class="inline-block mb-2 text-base font-medium">
                                    Fin <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="hora_finUpdate" name="hora_fin"
                                    value="{{ old('request_method') === 'PUT' ? old('hora_fin') : '' }}"
                                    class="form-input w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'PUT' && $errors->has('hora_fin')) border-red-500 @endif"
                                    placeholder="Selecciona una hora">
                                @if (old('request_method') === 'PUT')
                                @error('hora_fin')
                                <div id="hora_fin-error" class="mt-1 text-sm text-red-500">
                                    {{ $message }}</div>
                                @enderror
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Varios días -->
                <div class="items-center mt-2">
                    <input type="radio" id="variosDiasUpdate" name="duracion_permiso" value="varios_dias"
                        {{ (old('request_method') === 'PUT' && old('duracion_permiso') === 'varios_dias') || old('duracion_permiso') === 'varios_dias' ? 'checked' : '' }}>
                    <label for="variosDiasUpdate" class="ml-2">Varios días</label>

                    <div id="fechasVariosDiasUpdate"
                        class="ml-4 {{ (old('request_method') === 'PUT' && old('duracion_permiso') === 'varios_dias') || old('duracion_permiso') === 'varios_dias' ? '' : 'hidden' }}">
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="flex-1">
                                <label for="fecha_fin_varios_diasUpdate"
                                    class="inline-block mb-2 text-base font-medium">
                                    Fecha Fin <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="fecha_fin_varios_diasUpdate" name="fecha_fin_varios_dias"
                                    value="{{ old('request_method') === 'PUT' ? old('fecha_fin_varios_dias') : '' }}"
                                    class="form-input w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'PUT' && $errors->has('fecha_fin_varios_dias')) border-red-500 @endif"
                                    placeholder="DD-MM-YYYY" data-provider="flatpickr" data-date-format="d M Y"
                                    {{-- data-date-format="Y-m-d"  --}} {{-- data-alt-format="d M, Y"  --}}
                                    data-alt-format="d M Y" />
                                @if (old('request_method') === 'PUT')
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
            @if (old('request_method') === 'PUT')
            @error('duracion_permiso')
            <div id="duracion_permiso-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
            @enderror
            @endif
        </div>
        </form>
    </div>
    <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">
        <button type="reset" data-modal-close="updatePermisoModal" form="modalFormUpdatePermiso"
            class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">
            <i data-lucide="x" class="inline-block size-4"></i> <span class="align-middle">Cancelar</span>
        </button>

        <button type="submit" form="modalFormUpdatePermiso"
            class="ml-2 text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
            Actualizar
        </button>
    </div>
</div>
</div>

<!--delete modal-->
{{-- <div id="deleteModal" modal-center=""
        class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
        <div class="w-screen md:w-[25rem] bg-white shadow rounded-md dark:bg-zink-600">
            <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto px-6 py-8">
                <div class="float-right">
                    <button data-modal-close="deleteModal"
                        class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500"><i
                            data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAMAAAD04JH5AAAC8VBMVEUAAAD/6u7/cZD/3uL/5+r/T4T9O4T/4ub9RIX/ooz/7/D/noz+PoT/3uP9TYf/XoX/m4z/oY39Tob/oYz/oo39O4T9TYb/po3/n4z/4Ob/3+X/nIz+fon/4eb/nI39Xoj9fIn/8fP9SoX9coj/noz/XYb/6e38R4b/XIf/cIn/ZYj/Rof/6+//cIr/oYz/a4P/7/L+X4f+bYn+QoX/pIz/7vH/noz/8PH/7O7/4ub/oIz/moz/oY3/O4X/cYn/RYX+aIj/5+r9QYX+XYf+cYn+Z4j+i5j9PoT/po3/8vT/ucD/09f+hYr/8vT8R4X8UYb/3uH+ZIn+W4f+cIn/7O/+hIr+VYf+b4j+ZYj+VYb/6Ov9RYX9UIb9bYn9O4T/oIz9Y4f9WIb/gov/bIj/dYr/gYr/pY3/7e//dYr9PoX/pY3/8vL/PID/7/L+hor+hor/8fP/8fP/o43/o43/7O//n4v/n47/nI7/8PL/6+7/6ez/5+v9QIX/7fD9SoX9SIX9RYX9Q4X+YIf/6u7/7/H+g4r+gYr+gIr+for+fYr+cYn9O4T+e4n+a4j+ZYj+VYb9T4b9PYT+eIn9TYb/8vT+dYn+c4n+don+cIj+Zoj+bYj+aIj+XYf+Yof+W4f/xs/+Wof9U4b+V4b/0Nf/ur3+hor+hYr/1Nv/oY39TIb+eon/1t3/3eL/3+T/0dn/y9P/m4z+aoj9Uob+WYf9UYb/ydL/yNH/2+H/ztb/xM7/197/2uD/0tr/zNT/2d//zdX/noz/w83/4eb/oIz/2N//o43/pI3/nYz/uMX/qr7/u8f/pY3/vcn/p7v/wcv/tMP/ssL/r8H/rb//usf/wMv/tcP+kKL+h5f/sr7/o7f/oLT/k6/+mav+kKr+lKH+fqH+bZf+dJb+hJH9X5H+e4z/v8n+iKX+h6H/rL//rbr/mrP/mbD+dp3+fpz+jJv+fpf9ZJT+e5D+aZD/qbf+oa/+hp3+bpD+co/+ZI/+Xoz9Vos1azWoAAAAeHRSTlMAvwe8iBv3u3BtPR61ZUcx9/Xy7ebf3dHPt7Gtqqebm5aMh4V3cXBcW1pGMSUaEgX729qtqqmll3VlRT84Ny8g/vr48fDw7u7t5tzVz8vIx8bGxsW/u7KwsLCmnZybko6Ghn1wb2hkX0Q+KhMT+eTjx8bDwa1NSEgfarKCAAAHAElEQVR42uzTv2qDQBwH8F/cjEtEQUEQBOkUrIMxRX2AZMiWPVsCCYX+rxacmkfIQzjeIwRK28GXKvQ0talytvg7MvRz2/c47ntwP/i7tehpkzyfaJ64Bu4EUcsrNFEArpbq2xF1CfxIN681biXgJFSyWkoEXARy1kAOgINIzhrJEaBz1Jcvur9Y+HolUB3AZuxLii3RSLKVQ+gBsvt9yaw81jEP8QPg0t8LInwjlrkOqB5JwYYjNikEgMkglNG85QMiYUA+DST4QSr3zgFPSCgTapiECqEDfWs2jXediaczq/+b669iBNetK1zQA7sOF2VBK+MYzbjd+xGdAdPwMkbkDoFltEU1AoaNu0XlbhgFVimyFWsEUmSsUbxLkLE+wTxJUsSVJHNGgV6CrHfyBZ6RnX6BJ2T/BT5orWOXBOIogOMPCoTg/gBFQQiCoAiaagmCaKiGlpbGKGiqP8C51HA60MYGqyF/56ig4CAOIuIk3g1yg5yDiyD6B+Tdc/i9Gn734Odn/HLv8bjppzrgNrVmt6rXWGrNtkDh6DS1RqdhXiQ7m0uf2vlbd/YgrKcvzZ6B5+pbsyvguXnR7AZ44i+axYEn+apZEnjuXjW7A56HtGYPENZxIhKJXF+kNbu4Xq5NHINStBmoZDSr4N4oKBhNVMxoVmwi1T9IWKiU1axkoVjIA0RWMxHyAMNaGeW0GlkrBihELWTntLItFAUlI7axdHn+89fIHf1r3nTqhfrw/NLfGjMgtLhJeR0hhJOj0S0LUXZp8xwhRMczqThwJU2qI3wT0uya32o2iRPh65hUEri23wlbBBqeHB2MjtzMWtCqNp3fBq57usAVaCrHHrae3KYCuXT+Hrh288SgigZy7GHrKT707QLXY56wq2ioOmBYRTadfwSukwIxq6OFHPvY+nJb1NGMzp8A136ByLdw71x1wBxbK0/n94HroPBGFBsBR25jbGO5OdiKdLpwAGxndEUFF7dVB7SxfdDpM+A7pCvGrUBfbl1sXbn1aVs5BL7fVsjktYkwDOMvAwk5hAQEey1USmuLiHp2QRFvigouuKB4EvwTxO2ouOHFfT2ICAaXiBFFvNWQybSJFZI0JKGQaFtpLbiexHm/+eZ7AlXnnfnd5sf7PN+TbL8MjL90yZquwK5guiy7cUxvp+DsxIpPXPzoXwMesfuE6Z0UnH1XgepD5rThCqwKhjqtzqqY3kfBWYIVE6r5i+HyrPKG+qLOJjC9hIJz6CzwQTXPGs4bYKhZdfYB04coOEux4ut9pmMOYGUO6Kizr5heSsEZwopZ1Wz+tDKrsvlHqbNZTA9RcNKPge+qecJw3gBDTaiz75heQ8FZdg14/Iqbq4YbYTViqCqrV48xvYyCY63DjswrF9scwMocYLPKYHadRQI2XgHec/WYobwBhhpj9R6zG0nCCiwZeeQy8ndVRqVYSRK2ngNKXP3WUN4AQ71lVcLsVpKwC0sqXJ0x1DircUNlWFUwu4sk9GLJ9D3mijGAjTHgijqaxmwvSThwA6ir7m++8gb45ps6qmP2AEnox5KO6m75ymHj+KaljjqY7ScJg6eAz6r7s6+8AQsdaQZJwhCWtF4wHV+Nshn1TVsdtTA7RBLSWDKvuut/G1BXR/OYTZOE2Cnk9RuXaWMAG2PANJvXXdEYSbCuIzkur/jGG+CbCptcV9QiERuwpfzaxfbNGJsx37xjU8bkBpKx4iagnhs1DQ/wzSgaxQqSsQ1r7IxL3hjAxnguz8bG5DaSseM2MMXlOd+U2JR8k2MzhcndJKMXa2pcnr2+8IDrWTY1TPaSjINPgXaW+aFNiUVJix/qpI3JgySj/y7QUO1NbbwBWjTVSQOT/SRjEGtaz5kZbT6y+KjFjDppYXKQZKTOA/OqvaGNN0CLhjqZx2SKZKSx5uctpq3NOxbvtGirk5+YTJOM2HlEtdcXHlBXJ13BGMmw7iAFbp/SwhugxRSLQlfQIiGLsMfh+srCAyosHMwtIik9TwDvvQDCpYekbHkGVHMujhY2C1sLh0UVc1tIyo4LQI3ry1p4A7Qos6hhbjdJ2YtFjbcutr+IRc1fxKKBub0kpQ+LfjlufVOLycKf78KkFk33wPmFuT6SkriETNrFYn7GEE2nWHSahpjJF4v2ZFcsQVIG3DxMmHsC3xfm5vDgyZz7PDBAUlIPIiFFUoaPRcIwSVkbzYAYSbGiGWCRmEXHI2ARyemJYkAPydkcxYDNJCd5IgJWkZw9UQzYQ3L6ohjQR3ISJyMgQXIGohgwQHKGoxgwTHKs9UdDs345hWBV+AGrKAyp8AMOUyiSYd9PUjjWbroYik1rKSSr42Hejx+m0KxefEbM4tUUAUf2x2XPx/cfoWiIJZKLA46IL04mYvQf/AaSGokYCo6ekAAAAABJRU5ErkJggg=="
                    alt="" class="block h-12 mx-auto">
                <div class="mt-5 text-center">
                    <h5 class="mb-1">Estas Seguro?</h5>
                    <p class="text-slate-500 dark:text-zink-200">Estas seguro de Eliminar?</p>
                    <div class="flex justify-center gap-2 mt-6">

                        @if (isset($permiso->id))
                            <form action="{{ route('permisos.destroy', $permiso->id) }}" method="POST"
class="inline mt-6">
@csrf
@method('DELETE')
<button type="reset" data-modal-close="deleteModal"
    class="bg-white text-slate-500 btn hover:text-slate-500 hover:bg-slate-100 focus:text-slate-500 focus:bg-slate-100 active:text-slate-500 active:bg-slate-100 dark:bg-zink-600 dark:hover:bg-slate-500/10 dark:focus:bg-slate-500/10 dark:active:bg-slate-500/10">Cancelar</button>

<input type="hidden" id="permisoId" name="permisoId">

<button type="submit" id="deleteRecord" data-modal-close="deleteModal"
    class="text-white bg-red-500 border-red-500 btn hover:text-white hover:bg-red-600 hover:border-red-600 focus:text-white focus:bg-red-600 focus:border-red-600 focus:ring focus:ring-red-100 active:text-white active:bg-red-600 active:border-red-600 active:ring active:ring-red-100 dark:ring-custom-400/20">Si,
    Eliminalo!</button>
</form>
@endif

</div>
</div>
</div>
</div>
</div> --}}

{{-- generar reporte --}}
<div id="defaultModal" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div class="w-screen xl:w-[55rem] lg:w-[55rem]  md:w-[40rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col" style="max-height: 90vh;"> <!-- Reduje el ancho a 35rem -->
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-16">Reporte</h5>
            <button data-modal-close="defaultModal"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500"><i
                    data-lucide="x" class="size-5"></i></button>
        </div>
        <div class="max-h-[calc(80vh_-_100px)] p-4 overflow-y-auto">
            <!-- Cambio aquí -->
            <form id="formReporte" action="{{ route('pdf-generate-permiso') }}" method="POST" target="_blank">
                @csrf

                <!-- Empleado data-choices=""
                        data-choices-sorting-false="" -->
                <div class="mt-3 flex-1 min-w-[250px] w-full md:w-auto">
    <label for="empleadoId" class="mb-2 text-base font-medium block">
        Empleado <span class="text-red-500">*</span>
    </label>
    <select id="empleadoId" name="empleado" required 
        class="form-input w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
               disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
               dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
               placeholder:text-slate-400 dark:placeholder:text-zink-200 p-2 rounded-md" data-choices=""
                        data-choices-sorting-false="">
        <option value="" selected disabled>Buscar empleado...</option>
        <option value="todos">TODOS</option>
        @foreach ($empleados as $e)
        <option value="{{ $e->id }}" 
                data-horario="{{ $e->horario ? $e->horario->descripcion : '' }}"
                data-search="{{ strtolower($e->ci.' '.$e->apellido_pat.' '.$e->apellido_mat.' '.$e->nombres) }}">
            {{ $e->ci }} - {{ $e->apellido_pat }} {{ $e->apellido_mat }} {{ $e->nombres }}
        </option>
        @endforeach
    </select>
</div>

                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <!-- Fecha inicio -->
                    <div class="flex-1 min-w-[250px] w-full md:w-auto">
                        <label for="fechaInicioReporte" class="mb-2 text-base font-medium block">
                            Fecha inicio <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="fechaInicioReporte" name="fechaInicioReporte" class="w-full form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                placeholder:text-slate-400 dark:placeholder:text-zink-200 p-2 rounded-md"
                            data-provider="flatpickr" data-date-format="d-m-Y" readonly="readonly"
                            placeholder="DD-MM-YYYY">
                    </div>

                    <!-- Fecha fin -->
                    <div class="flex-1 min-w-[250px] w-full md:w-auto">
                        <label for="fechaFinReporte" class="mb-2 text-base font-medium block">
                            Fecha fin <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="fechaFinReporte" name="fechaFinReporte" class="w-full form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                placeholder:text-slate-400 dark:placeholder:text-zink-200 p-2 rounded-md"
                            data-provider="flatpickr" data-date-format="d-m-Y" readonly="readonly"
                            placeholder="DD-MM-YYYY">
                    </div>

                </div>

                <div class="flex-1 min-w-[250px] w-full md:w-auto">
                    <label for="motivoSelect" class="inline-block mb-2 text-base font-medium">
                        Motivo <span class="text-red-500">*</span>
                    </label>
                    <select id="motivoSelect" name="motivo"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if ($errors->has('motivo')) border-red-500 @endif"
                        data-choices data-choices-sorting-false>
                        <option value="" selected disabled>Seleccione Motivo</option>
                        @foreach ($motivoPermisos as $motivoP)
                        <option value="{{ $motivoP->id }}" {{ old('motivo') == $motivoP->id ? 'selected' : '' }}>
                            {{ $motivoP->descripcion }}
                        </option>
                        @endforeach
                    </select>

                    {{-- Mensaje de error --}}
                    @error('motivo')
                    <div id="motivo-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                {{-- <div class="flex-1 min-w-[250px] w-full md:w-auto">
                        <label for="motivoPermiso" class="inline-block mb-2 text-base font-medium">
                            Motivo <span class="text-red-500">*</span>
                        </label>
                        <input list="feriadosList" id="motivoPermiso" name="motivo"
                            value="{{ old('request_method') === 'POST' ? old('motivo') : '' }}"
                class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500
                disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500
                dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700
                dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if
                (old('request_method') === 'POST' && $errors->has('motivo')) border-red-500 @endif"
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

        </form>

    </div>
    <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">
        <button type="reset" data-modal-close="defaultModal" form="formReporte"
            class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">
            <i data-lucide="x" class="inline-block size-4"></i> <span class="align-middle">Cancelar</span>
        </button>

        <button type="submit" form="formReporte"
            class="ml-2 text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
            Generar
        </button>
    </div>
</div>
</div>
@endsection