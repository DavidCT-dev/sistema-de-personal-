@extends('layouts.master')

@section('script')
<script src="{{ asset('assets/js/datatables/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/libs/readExcel/xlsx.full.min.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr.localize(flatpickr.l10ns.es);
        flatpickr("#fecha_inicioPost,#fecha_finPost,#fecha_inicioUpdate,#fecha_finUpdate", {
            // enableTime: false, // Deshabilitar la selección de tiempo
            dateFormat: "d-m-Y", // Formato de fecha
            time_24hr: true, // Usar formato de 24 horas
            weekNumbers: true, // Mostrar números de la semana
            dateFormat: "d-m-Y", // Formato de fecha deseado
            allowInput: true, // Habilita la escritura manual
        });
        flatpickr("#fechaFinReporte, #fechaInicioReporte", {
            // mode: "range", // Permite seleccionar un rango de fechas
            dateFormat: "d-m-Y", // Formato de fecha deseado
            // locale: "es", // Para idioma español (requiere incluir el locale si necesario)
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
        $('#vacacionesTable').DataTable({
            responsive: true,
            // dom: 'Bflrtip',
            dom: 'B<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
            buttons: [{
                    extend: 'excel',
                    className: 'dt-button-custom bg-green-500 text-white hover:bg-green-600 focus:ring-2 focus:ring-green-300 rounded-lg px-4 py-2 shadow-md',
                    text: '<i class="ri-file-excel-2-fill"></i> Exportar EXCEL',
                    title: 'Reporte de Vacaciones',
                    exportOptions: {
                        columns: ':not(:first-child)'
                    },
                },
                {
                    extend: 'pdfHtml5',
                    className: 'dt-button-custom bg-red-500 text-white hover:bg-red-600 focus:ring-2 focus:ring-red-300 rounded-lg px-4 py-2 shadow-md',
                    title: 'Reporte de Vacaciones',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    text: '<i class="ri-file-pdf-2-fill mr-2"></i> Exportar PDF',
                    exportOptions: {
                        columns: ':not(:first-child)'
                    },
                    customize: function(doc) {
                        doc.pageMargins = [20, 20, 20, 20];
                        doc.defaultStyle.fontSize = 9;
                        doc.styles.tableHeader.fontSize = 10;
                        doc.styles.title = {
                            fontSize: 12,
                            alignment: 'center',
                        };
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                            .length).fill('auto');
                        doc.content[1].table.body.forEach(function(row) {
                            row.forEach(function(cell) {
                                cell.margin = [2, 2, 2, 2];
                            });
                        });
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
            lengthMenu: [5, 10, 25],
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            language: translations,
        });
    });

    function deleteVacacionModal(element) {
        document.getElementById('vacacion_id').value = element.dataset.id;
    }

    function fillVacacionModal(element) {
        document.getElementById('fecha_finUpdate').value = element.dataset.fecha_fin;
        document.getElementById('fecha_inicioUpdate').value = element.dataset.fecha_inicio;
        document.getElementById('empleado_select_update').value = element.dataset.id_persona;
        const modalForm = document.querySelector('#modalFormUpdateVacacion');
        modalForm.action = element.dataset.url;
        let methodInput = modalForm.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            modalForm.appendChild(methodInput);
        }
        let csrfInput = modalForm.querySelector('input[name="_token"]');
        if (!csrfInput) {
            csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
            modalForm.appendChild(csrfInput);
        }
    }

    function fillModal(element) {
        // Obtener el formulario de actualización de vacaciones
        const formatearFecha = (fechaStr) => {
            if (!fechaStr) return '';
            const [año, mes, dia] = fechaStr.split('-');
            return `${dia}-${mes}-${año}`;
        }
        // Llenar campos básicos
        document.getElementById('fecha_inicioUpdate').value = formatearFecha(element.dataset.fecha_inicio);
        document.getElementById('fecha_finUpdate').value = formatearFecha(element.dataset.fecha_fin);
        document.getElementById('empleado_select_update').value = element.dataset.id_persona;
        document.getElementById('observacion_update').value = element.dataset.observacion;
        // Establecer la acción del formulario con la URL correcta
        const modal = document.querySelector('#modalFormUpdateVacacion');
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

<script>
    function showRejectModal(vacacionId) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        // Configurar la acción del formulario con la ruta nombrada
        form.action = `/reject-vacacion/${vacacionId}`;
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
@if ($errors->hasAny(['fecha_inicio', 'fecha_fin', 'id_persona', 'observacion']))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const requestMethod = "{{ old('request_method') }}";
        if (requestMethod === 'POST') {
            const modalButton = document.getElementById('vacacionModalButton');
            if (modalButton) modalButton.click();
        } else if (requestMethod === 'PUT') {
            const updateButton = document.querySelector('[data-modal-target="updateVacacionModal"]');
            if (updateButton) updateButton.click();
        }
        const modals = document.querySelectorAll('[data-modal-close]');
        modals.forEach(modalCloseButton => {
            modalCloseButton.addEventListener('click', function() {
                clearFormAndErrors();
            });
        });
        const modalContainers = document.querySelectorAll('.modal-container');
        modalContainers.forEach(modalContainer => {
            modalContainer.addEventListener('hidden.bs.modal', function() {
                clearFormAndErrors();
            });
        });
    });

    function clearFormAndErrors() {
        const requestMethod = "{{ old('request_method') }}";
        let form;
        if (requestMethod === 'POST') {
            form = document.querySelector('#vacacionModal');
        } else if (requestMethod === 'PUT') {
            form = document.querySelector('#updateVacacionModal');
        }
        if (form) form.reset();
        const errorMessages = document.querySelectorAll('.text-red-500');
        errorMessages.forEach(errorMessage => errorMessage.remove());
        const errorFields = document.querySelectorAll('.border-red-500');
        errorFields.forEach(field => field.classList.remove('border-red-500'));
    }
</script>
@endif
<div
    class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-16">Vacaciones</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li
                    class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1 before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="#" class="text-slate-400 dark:text-zink-200">Inasistencia</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    Vacaciones
                </li>
            </ul>
        </div>

        <div class="flex justify-between items-center gap-4">
            @can('crear_vacacion')
              <button data-modal-target="vacacionModal" type="button" id="vacacionModalButton"
                class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:focus:ring-custom-400/20">
                Crear Vacación
            </button>  
            @endcan
            
            @can('generar_reporte_vacacion')
               <button data-modal-target="modalGenerarReporteVacacion" type="button"
                class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                Generar Reporte
            </button> 
            @endcan
            

            <!-- Formulario para subir archivo Excel -->
            @can('subir_excel_vacacion')
               <label for="excelUpload"
                class="cursor-pointer text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                Subir Excel
                <input type="file" name="archivo_excel" id="excelUpload" accept=".xlsx,.xls" class="hidden">
            </label> 
            @endcan
            

            <script>
                document.getElementById('excelUpload').addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const data = new Uint8Array(e.target.result);
                        const workbook = XLSX.read(data, {
                            type: 'array'
                        });
                        const firstSheetName = workbook.SheetNames[0];
                        const worksheet = workbook.Sheets[firstSheetName];
                        const jsonData = XLSX.utils.sheet_to_json(worksheet, {
                            header: 1,
                            defval: ""
                        });
                        // Procesar los datos
                        const processedData = processExcelData(jsonData);
                        console.log("Datos procesados:", processedData);
                        // Mostrar los datos en el modal
                        displayDataInModal(processedData);
                        // Mostrar el modal
                        const modal = document.getElementById('extraLargeModal');
                        modal.classList.remove('hidden');
                        document.body.classList.add('modal-open');
                    };
                    reader.readAsArrayBuffer(file);
                });

                function processExcelData(rawData) {
                    // Encontrar las filas con datos del usuario
                    const userInfo = {};
                    for (let i = 0; i < rawData.length; i++) {
                        const row = rawData[i];
                        if (!row || row[0] === undefined || row[0] === null) continue;
                        const key = String(row[0]).trim();
                        switch (key) {
                            case 'Nombre completo':
                                userInfo.nombre = row[1] || "";
                                break;
                            case 'CI':
                                userInfo.ci = row[1] || "";
                                break;
                            case 'Correo':
                                userInfo.correo = row[1] || "";
                                break;
                            case 'Tipo de empleado':
                                userInfo.tipo_empleado = row[1] || "";
                                break;
                            case 'Fecha de ingreso':
                                userInfo.fecha_ingreso = row[1] || "";
                                break;
                            case 'Cargo':
                                userInfo.cargo = row[1] || "";
                                break;
                        }
                    }
                    // Procesar vacaciones
                    const vacationsStart = rawData.findIndex(row =>
                        row && row[0] !== undefined && String(row[0]).trim() === 'ID'
                    );
                    const vacations = [];
                    if (vacationsStart !== -1) {
                        const headers = rawData[vacationsStart].map(header =>
                            header !== undefined && header !== null ? convertToSnakeCase(String(header)) : null
                        );
                        for (let i = vacationsStart + 1; i < rawData.length; i++) {
                            const row = rawData[i];
                            if (!row || row[0] === undefined || typeof row[0] !== 'number') continue;
                            const vacacion = {};
                            headers.forEach((header, index) => {
                                if (header && row[index] !== undefined && row[index] !== null && row[index] !==
                                    "") {
                                    vacacion[header] = row[index];
                                }
                            });
                            if (Object.keys(vacacion).length > 0) {
                                vacations.push(vacacion);
                            }
                        }
                    }
                    // Procesar resumen
                    const summary = {};
                    const summaryStart = rawData.findIndex(row =>
                        row && row[0] !== undefined && String(row[0]).trim() === 'Resumen de días por estado'
                    );
                    if (summaryStart !== -1) {
                        for (let i = summaryStart + 1; i < rawData.length; i++) {
                            const row = rawData[i];
                            if (!row || row[0] === undefined || row[0] === null) continue;
                            const key = String(row[0]).trim();
                            if (key === 'AUTHORIZED') {
                                summary.authorized = Number(row[1]) || 0;
                            } else if (key === 'SUSPENDED') {
                                summary.suspended = Number(row[1]) || 0;
                            } else if (key === 'Total días tomados') {
                                summary.total_dias = Number(row[1]) || 0;
                            }
                        }
                    }
                    return {
                        ...userInfo,
                        vacaciones: vacations,
                        resumen: summary
                    };
                }

                function convertToSnakeCase(str) {
                    if (!str) return '';
                    return String(str)
                        // Convertir a minúsculas
                        .toLowerCase()
                        // Reemplazar caracteres acentuados y especiales por sus equivalentes sin acento
                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                        // Reemplazar ñ por 'n' (o puedes dejarla como 'n' con tilde si prefieres)
                        .replace(/ñ/g, 'n')
                        // Reemplazar espacios y guiones por _
                        .replace(/[\s-]+/g, '_')
                        // Reemplazar cualquier otro carácter no alfanumérico por _
                        .replace(/[^a-z0-9_]/g, '_')
                        // Eliminar múltiples _ consecutivos
                        .replace(/_+/g, '_')
                        // Eliminar _ al inicio y final
                        .replace(/^_+|_+$/g, '');
                }

                function displayDataInModal(data) {
                    const modalContent = document.querySelector('#extraLargeModal .overflow-y-auto');
                    // Crear HTML para mostrar los datos
                    let html = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded">
                <h6 class="font-semibold mb-2">Información del Empleado</h6>
                <p><strong>Nombre:</strong> ${data.nombre || 'N/A'}</p>
                <p><strong>CI:</strong> ${data.ci || 'N/A'}</p>
                <p><strong>Correo:</strong> ${data.correo || 'N/A'}</p>
                <p><strong>Tipo Empleado:</strong> ${data.tipo_empleado || 'N/A'}</p>
                <p><strong>Fecha Ingreso:</strong> ${data.fecha_ingreso || 'N/A'}</p>
                <p><strong>Cargo:</strong> ${data.cargo || 'N/A'}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <h6 class="font-semibold mb-2">Resumen de Vacaciones</h6>
                <p><strong>Días Autorizados:</strong> ${data.resumen.authorized || 0}</p>
                <p><strong>Días Suspendidos:</strong> ${data.resumen.suspended || 0}</p>
                <p><strong>Total Días Tomados:</strong> ${data.resumen.total_dias || 0}</p>
            </div>
        </div>
    `;
                    // Agregar tabla de vacaciones si existen
                    if (data.vacaciones && data.vacaciones.length > 0) {
                        html += `
    <div class="mb-6">
        <h6 class="text-lg font-semibold mb-3 text-gray-800 dark:text-gray-200">Registros de Vacaciones</h6>
        <div class="border border-gray-200 dark:border-zink-500 rounded-lg overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead class="bg-gray-50 dark:bg-zink-600">
                        <tr class="[&>th]:px-6 [&>th]:py-3 [&>th]:text-left [&>th]:text-xs [&>th]:font-medium [&>th]:text-gray-500 [&>th]:dark:text-zink-200 [&>th]:uppercase [&>th]:tracking-wider">
    `;
                        // Encabezados dinámicos
                        Object.keys(data.vacaciones[0]).forEach(key => {
                            html += `
    <th class="px-5 py-3.5 text-sm font-semibold text-gray-800 dark:text-zink-100 bg-gray-100 dark:bg-zink-600 border-b border-gray-200 dark:border-zink-500 first:rounded-tl-lg last:rounded-tr-lg first:border-l border-r border-gray-200 dark:border-zink-500">
        <div class="flex items-center justify-between gap-1.5">
            <span>${key.replace(/_/g, ' ')}</span>
            <span class="text-gray-400 dark:text-zink-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-down">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <polyline points="19 12 12 19 5 12"></polyline>
                </svg>
            </span>
        </div>
    </th>
    `;
                        });
                        html += `
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zink-500 bg-white dark:bg-zink-700">
    `;
                        // Filas de datos
                        data.vacaciones.forEach((vac, index) => {
                            const rowClass = index % 2 === 0 ? 'bg-white dark:bg-zink-700' :
                                'bg-gray-50 dark:bg-zink-600';
                            html +=
                                `<tr class="${rowClass} hover:bg-gray-100 dark:hover:bg-zink-500 transition-colors">`;
                            Object.values(vac).forEach(val => {
                                html += `
            <td class="px-6 py-4 text-sm text-gray-800 dark:text-zink-100 border-b border-gray-200 dark:border-zink-500">
                ${val || '-'}
            </td>`;
                            });
                            html += `</tr>`;
                        });
                        html += `
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-zink-600">
                        <tr class="[&>th]:px-6 [&>th]:py-3 [&>th]:text-left [&>th]:text-xs [&>th]:font-medium [&>th]:text-gray-500 [&>th]:dark:text-zink-200 [&>th]:uppercase [&>th]:tracking-wider">
    `;
                        html += `
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    `;
                    }
                    // Agregar formulario oculto con los datos
                    html += `
        <form id="vacationDataForm" method="POST" accept-charset="UTF-8" action="{{ route('subir-excel') }}" class="hidden">
            @csrf
            <input type="hidden" name="employee_data" value='${JSON.stringify(data)}'>
        </form>
    `;
                    modalContent.innerHTML = html;
                    document.getElementById('openModalExcel').click();
                    // Configurar el evento de cierre para resetear el input file
                    const closeButtons = modal.querySelectorAll('[data-modal-close]');
                    closeButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            resetExcelForm();
                        });
                    });
                }
                // Función para resetear el formulario Excel
                function resetExcelForm() {
                    const excelForm = document.getElementById('excelForm');
                    excelForm.reset();
                    // También puedes limpiar el modal si es necesario
                    const modalContent = document.querySelector('#extraLargeModal .overflow-y-auto');
                    modalContent.innerHTML = '';
                }
                // Modifica el event listener del input file
                document.getElementById('excelUpload').addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    // Limpiar el modal antes de cargar nuevos datos
                    resetExcelForm();
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // ... (mantén el resto del código de procesamiento)
                        // Mostrar los datos en el modal
                        displayDataInModal(processedData);
                    };
                    reader.readAsArrayBuffer(file);
                });

                function submitVacationData() {
                    document.getElementById('vacationDataForm').submit();
                }
            </script>

        </div>

        <div class="grid grid-cols-12 gap-4 mt-2">
            <div class="col-span-12 md:col-span-12">
                <div class="card">
                    <div class="card-body">
                        <div class="overflow-x-auto">
                            <table id="vacacionesTable" class="display" style="width:100%">
                                <thead>
                                    <tr class="bg-gray-100 text-left text-gray-600 text-sm font-medium uppercase">
                                        <th class="py-3 px-4 border-b border-gray-300">Acciones</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Estado</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Fecha Inicio </th>
                                        <th class="py-3 px-4 border-b border-gray-300">Fecha Fin </th>
                                        <th class="py-3 px-4 border-b border-gray-300">Cantidad Días</th>
                                        <th class="py-3 px-4 border-b border-gray-300">Empleados</th>

                                        <th class="py-3 px-4 border-b border-gray-300">Observaciones</th>
                                        {{-- <th class="py-3 px-4 border-b border-gray-300">Unidad - Cargo</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vacaciones as $vacacion)
                                    <tr>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            <div class="relative dropdown">
                                                <button id="orderAction1" data-bs-toggle="dropdown"
                                                    class="flex items-center justify-center size-[30px] dropdown-toggle p-0 text-slate-500 btn bg-slate-100 hover:text-white hover:bg-slate-600 focus:text-white focus:bg-slate-600 focus:ring focus:ring-slate-100 active:text-white active:bg-slate-600 active:ring active:ring-slate-100 dark:bg-slate-500/20 dark:text-slate-400 dark:hover:bg-slate-500 dark:hover:text-white dark:focus:bg-slate-500 dark:focus:text-white dark:active:bg-slate-500 dark:active:text-white dark:ring-slate-400/20">
                                                    <i data-lucide="more-horizontal" class="size-3"></i>
                                                </button>
                                                <ul class="absolute z-50 hidden py-2 mt-1 ltr:text-left rtl:text-right list-none bg-white rounded-md shadow-md dropdown-menu min-w-[10rem] dark:bg-zink-600"
                                                    aria-labelledby="orderAction1">
                                                    <!-- Editar -->
                                                    @if ($vacacion->id_persona)
                                                    @can('editar_vacacion')
                                                    <li>
                                                        <a data-modal-target="updateVacacionModal"
                                                            data-fecha_inicio="{{ $vacacion->fecha_inicio }}"
                                                            data-fecha_fin="{{ $vacacion->fecha_fin }}"
                                                            data-id_persona="{{ $vacacion->id_persona }}"
                                                            data-id="{{ $vacacion->id }}"
                                                            data-observacion="{{ $vacacion->observacion }}"
                                                            data-url="{{ route('vacaciones.update', ['vacacione' => $vacacion->id]) }}"
                                                            class="block px-4 py-1.5 text-base transition-all duration-200 ease-linear text-slate-600 dropdown-item hover:bg-slate-100 hover:text-slate-500 focus:bg-slate-100 focus:text-slate-500 dark:text-zink-100 dark:hover:bg-zink-500 dark:hover:text-zink-200 dark:focus:bg-zink-500 dark:focus:text-zink-200"
                                                            onclick="fillModal(this)">
                                                            <i data-lucide="file-edit"
                                                                class="inline-block size-3 ltr:mr-1 rtl:ml-1"></i>
                                                            <span class="align-middle">Editar</span>
                                                        </a>
                                                    </li>
                                                    @endcan

                                                    @endif

                                                    <!-- Aceptar -->

                                                    @if ($vacacion->estado != 'cancelado')
                                                    <!-- Aceptar -->
                                                    @can('aceptar_rechazar_vacacion')
                                                    <li>
                                                        <form action="{{ route('vacaciones-approve', $vacacion->id) }}"
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
                                                            onclick="showRejectModal({{ $vacacion->id }})"
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
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $vacacion->fecha_inicio }}
                                        </td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $vacacion->fecha_fin }}
                                        </td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $vacacion->total_dias }}
                                        </td>
                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ optional($vacacion->empleado)->apellido_pat
                                                        ? optional($vacacion->empleado)->apellido_pat .
                                                            ' ' .
                                                            optional($vacacion->empleado)->apellido_mat .
                                                            ' ' .
                                                            optional($vacacion->empleado)->nombres
                                                        : (optional($vacacion->solicitante->persona)->apellido_pat
                                                            ? optional($vacacion->solicitante->persona)->apellido_pat .
                                                                ' ' .
                                                                optional($vacacion->solicitante->persona)->apellido_mat .
                                                                ' ' .
                                                                optional($vacacion->solicitante->persona)->nombres
                                                            : optional($vacacion->solicitante)->name) }}
                                        </td>

                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                            {{ $vacacion->observacion }}
                                        </td>
                                        {{-- <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                    <div class="flex flex-col space-y-1">
                                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            Unidad: 
                                                            {{
                                                                optional(optional($vacacion->empleado)->lugarTrabajo)->descripcion
                                                                    ?: optional(optional(optional($vacacion->solicitante)->persona)->lugarTrabajo)->descripcion
                                                            }}
                                        </span>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            Cargo:
                                            {{
                                                                optional(optional($vacacion->empleado)->cargo)->descripcion
                                                                    ?: optional(optional(optional($vacacion->solicitante)->persona)->cargo)->descripcion
                                                            }}
                                        </span>
                        </div>

                        </td> --}}
                        </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 text-left text-gray-600 text-sm font-medium uppercase">
                                <th class="py-3 px-4 border-b border-gray-300">Acciones</th>
                                <th class="py-3 px-4 border-b border-gray-300">Estado</th>
                                <th class="py-3 px-4 border-b border-gray-300">Fecha Inicio </th>
                                <th class="py-3 px-4 border-b border-gray-300">Fecha Fin </th>
                                <th class="py-3 px-4 border-b border-gray-300">Cantidad Días</th>
                                <th class="py-3 px-4 border-b border-gray-300">Empleados</th>

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

<!-- Modal para crear vacaciones -->
<div id="vacacionModal" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div class="w-screen md:w-[40rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full">
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-16">Nueva Vacación</h5>
            <button data-modal-close="vacacionModal"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                <i data-lucide="x" class="size-5"></i>
            </button>
        </div>
        <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
            <form id="modalFormVacacion" action="{{ route('vacaciones.store') }}" method="POST">
                @csrf

                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <div class="flex-1">
                        <label for="empleado_select" class="inline-block mb-2 text-base font-medium">
                            Empleado <span class="text-red-500">*</span>
                        </label>
                        <select id="empleado_select" name="id_persona"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('id_persona')) border-red-500 @endif"
                            data-choices data-choices-sorting-false>
                            <option value="" selected disabled>Seleccione Empleado</option>
                            @foreach ($empleados as $empleado)
                            <option value="{{ $empleado->id }}"
                                data-contrato="{{ $empleado->tipoContrato->descripcion ?? 'sin contrato' }}"
                                {{ old('request_method') === 'POST' && old('id_persona') == $empleado->id ? 'selected' : '' }}>
                                {{ $empleado->ci }} - {{ $empleado->nombres }}
                                {{ $empleado->apellido_pat }} {{ $empleado->apellido_mat }}
                            </option>
                            @endforeach
                        </select>
                        @if (old('request_method') === 'POST')
                        @error('id_persona')
                        <div id="empleado-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <!-- Fecha Inicio -->
                    <div class="flex-1">
                        <label for="fecha_inicioPost" class="inline-block mb-2 text-base font-medium">
                            Fecha Inicio <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="fecha_inicioPost" name="fecha_inicio"
                            value="{{ old('request_method') === 'POST' ? old('fecha_inicio') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('fecha_inicio')) border-red-500 @endif"
                            data-provider="flatpickr" data-date-format="d-m-Y" data-week-number="" readonly="readonly"
                            placeholder="DD-MM-YYYY">
                        @if (old('request_method') === 'POST')
                        @error('fecha_inicio')
                        <div id="fecha_inicio-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif

                    </div>

                    <!-- Fecha Fin (nuevo campo) -->
                    <div class="flex-1">
                        <label for="fecha_finPost" class="inline-block mb-2 text-base font-medium">
                            Fecha Fin <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="fecha_finPost" name="fecha_fin"
                            value="{{ old('request_method') === 'POST' ? old('fecha_fin') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'POST' && $errors->has('fecha_fin')) border-red-500 @endif"
                            data-provider="flatpickr" data-date-format="d-m-Y" data-week-number="" readonly="readonly"
                            placeholder="DD-MM-YYYY">
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

<!-- Modal para actualizar vacaciones -->
<div id="updateVacacionModal" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div class="w-screen md:w-[40rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full">
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-16">Actualizar Vacación</h5>
            <button data-modal-close="updateVacacionModal"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                <i data-lucide="x" class="size-5"></i>
            </button>
        </div>
        <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
            <form id="modalFormUpdateVacacion" action="{{ route('vacaciones.update', ':id') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <div class="flex-1">
                        <label for="empleado_select_update" class="inline-block mb-2 text-base font-medium">
                            Empleado <span class="text-red-500">*</span>
                        </label>
                        <select id="empleado_select_update" name="id_persona"
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

                </div>

                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <!-- Fecha Inicio -->
                    <div class="flex-1">
                        <label for="fecha_inicioUpdate" class="inline-block mb-2 text-base font-medium">
                            Fecha Inicio <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="fecha_inicioUpdate" name="fecha_inicio"
                            value="{{ old('request_method') === 'PUT' ? old('fecha_inicio') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'PUT' && $errors->has('fecha_inicio')) border-red-500 @endif"
                            data-provider="flatpickr" data-date-format="d-m-Y" data-week-number=""
                            placeholder="Seleccione fecha inicio">
                        @if (old('request_method') === 'PUT')
                        @error('fecha_inicio')
                        <div id="fecha_inicio-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    <!-- Fecha Fin (campo añadido) -->
                    <div class="flex-1">
                        <label for="fecha_finUpdate" class="inline-block mb-2 text-base font-medium">
                            Fecha Fin <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="fecha_finUpdate" name="fecha_fin"
                            value="{{ old('request_method') === 'PUT' ? old('fecha_fin') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'PUT' && $errors->has('fecha_fin')) border-red-500 @endif"
                            data-provider="flatpickr" data-date-format="d-m-Y" data-week-number=""
                            placeholder="Seleccione fecha fin">
                        @if (old('request_method') === 'PUT')
                        @error('fecha_fin')
                        <div id="fecha_fin-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <div class="flex-1">
                        <label for="observacion_update" class="inline-block mb-2 text-base font-medium">
                            Observacion <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="observacion_update" name="observacion"
                            value="{{ old('request_method') === 'PUT' ? old('observacion') : '' }}"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @if (old('request_method') === 'PUT' && $errors->has('observacion')) border-red-500 @endif"
                            placeholder="Obs...">
                        @if (old('request_method') === 'PUT')
                        @error('observacion')
                        <div id="observacion-error" class="mt-1 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>
                </div>

            </form>
        </div>
        <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">
            <button type="reset" data-modal-close="updateVacacionModal" form="modalFormUpdateVacacion"
                class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">
                <i data-lucide="x" class="inline-block size-4"></i> <span class="align-middle">Cancelar</span>
            </button>
            <button type="submit" form="modalFormUpdateVacacion"
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

                    @if (isset($vacacion->id))
                    <form action="{{ route('vacaciones.destroy', $vacacion->id) }}" method="POST" class="inline mt-6">
                        @csrf
                        @method('DELETE')
                        <button type="reset" data-modal-close="deleteModal"
                            class="bg-white text-slate-500 btn hover:text-slate-500 hover:bg-slate-100 focus:text-slate-500 focus:bg-slate-100 active:text-slate-500 active:bg-slate-100 dark:bg-zink-600 dark:hover:bg-slate-500/10 dark:focus:bg-slate-500/10 dark:active:bg-slate-500/10">Cancelar</button>

                        <input type="hidden" id="vacacionId" name="vacacionId">

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
                        ¿Estás seguro que deseas rechazar la vacación? Por favor indica el motivo.
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

{{-- generar reporte --}}
<div id="modalGenerarReporteVacacion" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div class="w-screen xl:w-[35rem] lg:w-[35rem]  md:w-[35rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col" style="max-height: 90vh;"> <!-- Reduje el ancho a 35rem -->
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-16">Reporte</h5>
            <button data-modal-close="modalGenerarReporteVacacion"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                <i data-lucide="x" class="size-5"></i>
            </button>
        </div>
        <div class="flex flex-col" style="height: calc(90vh - 120px);">
            <div class="overflow-y-auto flex-1 p-4 custom-scroll">
                <form id="formReporte" action="{{ route('pdf-generate-vacacion') }}" method="POST" target="_blank">
                    @csrf
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

                <!-- Empleado data-choices=""
                        data-choices-sorting-false=""-->
                <div class="flex-1 min-w-[250px] w-full md:w-auto">
    <label for="empleadoId" class="mb-2 text-base font-medium block">
        Empleado <span class="text-red-500">*</span>
    </label>
    <select id="empleadoId" name="empleado" required 
            class="form-input w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                   disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                   dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                   placeholder:text-slate-400 dark:placeholder:text-zink-200 p-2 rounded-md"
            data-search-enabled="true" data-choices=""
                        data-choices-sorting-false="">
        <option value="" selected disabled>Buscar empleado por CI, nombres o apellidos...</option>
        <option value="todos">TODOS</option>
        @foreach ($empleados as $e)
        <option value="{{ $e->id }}" 
                data-horario="{{ $e->horario ? $e->horario->descripcion : '' }}"
                data-search="{{ strtolower($e->ci.' '.$e->apellido_pat.' '.$e->apellido_mat.' '.$e->nombres) }}"
                {{ old('empleado_id') == $e->id ? 'selected' : '' }}>
            {{ $e->ci }} - {{ $e->apellido_pat }} {{ $e->apellido_mat }} {{ $e->nombres }}
        </option>
        @endforeach
    </select>
</div>
            </form>

        </div>
        <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">
            <button type="reset" data-modal-close="modalGenerarReporteVacacion" form="formReporte"
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

{{-- modal excel --}}
<button data-modal-target="extraLargeModal" id="openModalExcel" type="button" hidden></button>

<div id="extraLargeModal" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div class="w-screen lg:w-[55rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full">
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-16">Datos de Vacaciones</h5>
            <button data-modal-close="extraLargeModal"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                <i data-lucide="x" class="size-5"></i>
            </button>
        </div>
        <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
            <!-- Los datos se insertarán aquí dinámicamente -->
        </div>
        <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">
            <button data-modal-close="extraLargeModal" type="reset" form="vacationDataForm"
                class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">
                Cancelar
            </button>
            <button type="button" onclick="submitVacationData()"
                class="text-white bg-green-500 hover:bg-green-600 px-4 py-2 rounded">
                Guardar Datos
            </button>
        </div>

    </div>
</div>
@endsection