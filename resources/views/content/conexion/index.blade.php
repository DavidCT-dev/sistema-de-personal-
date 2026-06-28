@extends('layouts.master')

@section('script')
<script>
    const fileInput = document.getElementById('dropzone-file');
    const fileContentContainer = document.getElementById('file-content');
    const fileTable = document.getElementById('file-table').getElementsByTagName('tbody')[0];
    // Manejar la selección del archivo
    fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        // Validar que sea un archivo .dat
        if (!file || !file.name.match(/\.(dat|DAT)$/)) {
            alert('Por favor, selecciona un archivo con extensión .dat');
            fileInput.value = ''; // Limpia el input
            return;
        }
        // Leer y procesar el archivo
        const reader = new FileReader();
        reader.onload = function(e) {
            const content = e.target.result;
            // Dividir las filas por saltos de línea (\r\n)
            const rows = content.split(/\r?\n/); // Acepta tanto \r\n (Windows) como \n (Unix)
            // Limpiar cualquier contenido previo
            fileTable.innerHTML = '';
            // Iterar a través de las filas y agregar las celdas a la tabla
            rows.forEach((row, index) => {
                // Dividir cada fila por tabulaciones (\t)
                const columns = row.trim().split(/\t/); // Dividir por tabulaciones
                if (columns.length === 6) { // Verificar si la fila tiene las 6 columnas esperadas
                    const tr = document.createElement('tr');
                    // Procesar el tiempo y agregar un minuto más
                    let time = columns[1].trim();
                    if (time) {
                        let date = new Date(time);
                        date.setMinutes(date.getMinutes() + 1); // Sumar 1 minuto al tiempo
                        time = date.toISOString().slice(0, 19).replace('T',
                            ' '); // Formato: YYYY-MM-DD HH:MM:SS
                    }
                    // Agregar el número de fila (índice + 1)
                    const tdNumber = document.createElement('td');
                    tdNumber.className = 'px-4 py-2 ';
                    tdNumber.textContent = index + 1; // Número de fila (1-indexado)
                    tr.appendChild(tdNumber);
                    columns.forEach((col, index) => {
                        const td = document.createElement('td');
                        td.className = 'px-4 py-2 ';
                        if (index === 1) {
                            td.textContent = time; // Usar el tiempo modificado
                        } else {
                            td.textContent = col.trim(); // Eliminar espacios extra
                        }
                        tr.appendChild(td);
                    });
                    fileTable.appendChild(tr);
                }
            });
            // Mostrar el contenedor de contenido
            fileContentContainer.style.display = 'block';
        };
        reader.onerror = function() {
            alert('Error al leer el archivo');
        };
        reader.readAsText(file); // Leer como texto
    });
    const confirmar = () => {
        alert(
            '¿Estás seguro de seguir con la acción?, se creara una tabla en la base de datos con el nombre que ingreso'
        );
    };
</script>

<script>
    document.getElementById('sincronizarBtn').addEventListener('click', async function() {
        const button = this; // Referencia al botón
        const originalText = button.innerHTML; // Guardar el texto original del botón
        // Deshabilitar el botón y agregar el spinner
        button.disabled = true;
        button.innerHTML = `
            <span class="inline-block border-2 rounded-full size-4 animate-spin border-l-transparent border-slate-900 dark:border-zink-200 dark:border-l-transparent"></span>
        `;
        try {
            const selectBiometrico = document.getElementById('id_biometrico');
            const selectedOption = selectBiometrico.options[selectBiometrico.selectedIndex];
            // Verificar si se seleccionó un biométrico
            if (!selectBiometrico.value) {
                alert('Por favor, seleccione un biométrico.');
                return;
            }
            // Obtener los datos adicionales del <option>
            const ipBiometrico = selectBiometrico.value;
            const nombreDB = selectedOption.dataset.nombreBaseDeDatos;
            // Realizar la solicitud fetch
            const BIOMETRIC_API_URL = "{{ env('BIOMETRIC_API_URL') }}";

            const DB_HOST = "{{ env('DB_HOST') }}";
            const DB_PORT = "{{ env('DB_PORT') }}";
            const DB_DATABASE = "{{ env('DB_DATABASE') }}";
            const DB_USERNAME = "{{ env('DB_USERNAME') }}";
            const DB_PASSWORD = "{{ env('DB_PASSWORD') }}";

            const response = await fetch(`${BIOMETRIC_API_URL}/sincronizar-asistencias`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    ip_biometrico: ipBiometrico,
                    nombreDB,
                    DB_HOST,
             DB_PORT,
             DB_DATABASE,
             DB_USERNAME,
             DB_PASSWORD,
                }),
            });
            if (!response.ok) {
                throw new Error('Error en la solicitud');
            }
            // Convertir la respuesta a JSON
            const data = await response.json();
            // Mostrar el mensaje de la API
            alert(data.message);
        } catch (error) {
            // Mostrar un mensaje de error
            console.error('Error al sincronizar:', error);
            alert('Error al sincronizar: ' + error.message);
        } finally {
            // Restaurar el botón a su estado original
            button.disabled = false;
            button.innerHTML = originalText;
        }
    });
</script>

<script>
    document.getElementById('btnEliminar').addEventListener('click', function() {
        document.getElementById('modalEliminar').classList.remove('hidden');
    });
    document.getElementById('cancelarEliminar').addEventListener('click', function() {
        document.getElementById('modalEliminar').classList.add('hidden');
    });
    document.getElementById('confirmarEliminar').addEventListener('click', function() {
        alert("esta seguro de continuar con la eliminación")
        const selectBiometrico = document.getElementById('id_biometrico');
        const selectedOption = selectBiometrico.options[selectBiometrico.selectedIndex];
        // Verificar si se seleccionó un biométrico
        if (!selectBiometrico.value) {
            alert('Por favor, seleccione un biométrico.');
            return;
        }
        // Obtener los datos adicionales del <option>
        const ipBiometrico = selectBiometrico.value;
        const BIOMETRIC_API_URL = "{{ env('BIOMETRIC_API_URL') }}";
        fetch(`${BIOMETRIC_API_URL}/eliminar-asistencias`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    ip_biometrico: ipBiometrico,
                }),
            })
            .then(async response => await response.json())
            .then(data => {
                alert(data.message);
                document.getElementById('modalEliminar').classList.add('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Ocurrió un error al eliminar.", error);
            });
    });
</script>
@endsection

@section('content')
@if ($errors->hasAny(['nombre_biometrico', 'ip_biometrico']))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const largeModalButton = document.getElementById(
            'ModalButton'); // Botón del modal de creación
        if (largeModalButton) {
            largeModalButton.click(); // Simular clic en el botón del modal de creación
        }
    })
</script>
@endif
<div
    class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-16">Conexión con biometrico</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li
                    class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="#!" class="text-slate-400 dark:text-zink-200">Conexión</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    USB-Red
                </li>
            </ul>
        </div>

        @can('crear_biometrico')
        <button data-modal-target="defaultModal2" id="ModalButton" type="button"
            class="text-white btn bg-slate-500 border-slate-500 hover:text-white hover:bg-slate-600 hover:border-slate-600 focus:text-white focus:bg-slate-600 focus:border-slate-600 focus:ring focus:ring-slate-100 active:text-white active:bg-slate-600 active:border-slate-600 active:ring active:ring-slate-100 dark:ring-slate-400/10 mb-2">Crear
            Biometrico</button>
        @endcan

           <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 w-full">
                    @can('biometrico_conexion_red')

            <div class="2xl:col-span-1">
                <div class="card shadow-lg border rounded-md">
                    <div class="card-body p-6">
                        <div
                            class="flex items-center justify-center bg-purple-100 rounded-md size-12 dark:bg-purple-500/20 float-right">
                            <i data-lucide="network" class="text-green-500 fill-green-200 dark:fill-green-500/30"></i>
                        </div>
                        <h5 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mt-4">Conexión en red con
                            Biometrico</h5>
                        <p class="text-sm text-gray-500 dark:text-gray-300 mt-2 mb-4">Asegúrate de mantener la conexión
                            con el dispositivo para una correcta sincronización de asistencia.</p>
                        <div class="flex gap-6 mt-6 justify-center">
                            <button type="button" id="sincronizarBtn"
                                class="bg-white text-slate-500 btn border-slate-500 hover:text-white hover:bg-slate-600 hover:border-slate-600 focus:text-white focus:bg-slate-600 focus:border-slate-600 focus:ring focus:ring-slate-100 active:text-white active:bg-slate-600 active:border-slate-600 active:ring active:ring-slate-100 dark:bg-zink-700 dark:hover:bg-slate-500 dark:ring-slate-400/20 dark:focus:bg-slate-500">
                                Sincronizar
                            </button>

                            <!-- Botón para abrir el modal -->
                            <button type="button" id="btnEliminar"
                                class="text-red-500 bg-white border-red-500 btn hover:text-white hover:bg-red-600 hover:border-red-600 focus:text-white focus:bg-red-600 focus:border-red-600 focus:ring focus:ring-red-100 active:text-white active:bg-red-600 active:border-red-600 active:ring active:ring-red-100 dark:bg-zink-700 dark:hover:bg-red-500 dark:ring-red-400/20 dark:focus:bg-red-500">
                                Eliminar Asistencias
                            </button>

                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-4">
                            <div class="flex-1">
                                <select id="id_biometrico" name="id_biometrico"
                                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 "
                                    data-choices data-choices-sorting-false>
                                    <option value="" selected disabled>Seleccione Biometrico</option>
                                    @foreach ($biometricos as $biometrico)
                                    <option value="{{ $biometrico->ip_biometrico }}"
                                        data-nombre-base-de-datos="{{ $biometrico->nombre_base_de_datos }}"
                                        data-nombre-biometrico="{{ $biometrico->nombre_biometrico }}">
                                        Biometrico {{ $biometrico->nombre_biometrico }}
                                        ({{ $biometrico->ip_biometrico }})
                                    </option>
                                    @endforeach

                                </select>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        @endcan
        @can('biometrico_conexion_usb')

            <div class="2xl:col-span-1">

                <div class="card">
                    <div class="card-body">
                        <div
                            class="flex items-center justify-center bg-purple-100 rounded-md size-12 dark:bg-purple-500/20 ltr:float-right rtl:float-left">
                            <i data-lucide="usb" class="text-green-500 fill-green-200 dark:fill-green-500/30"></i>
                        </div>
                        <h5>Por favor, selecciona solo archivos con extensión <strong>.DAT</strong>.</h5>
                        <form action="{{ route('conexion.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3 mt-3 flex items-center">
                                <div class="w-full justify-center">

                                    <div class="flex items-center justify-center w-full">
                                        <label for="dropzone-file"
                                            class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 20 16">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                        class="font-semibold">Haz clic para subir un archivo</span> DAT
                                                </p>

                                            </div>
                                            <input id="dropzone-file" type="file" accept=".dat" class="hidden"
                                                name="file" />
                                            @error('file')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-4">
                                <div class="flex-1">
                                    <button type="submit"
                                        class="bg-white text-sky-500 btn border-sky-500 hover:text-white hover:bg-sky-600 hover:border-sky-600 focus:text-white focus:bg-sky-600 focus:border-sky-600 focus:ring focus:ring-sky-100 active:text-white active:bg-sky-600 active:border-sky-600 active:ring active:ring-sky-100 dark:bg-zink-700 dark:hover:bg-sky-500 dark:ring-sky-400/20 dark:focus:bg-sky-500">
                                        Procesar
                                    </button>
                                </div>
                                <div class="flex-1">
                                    <select id="id_bio" name="id_bio" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                        disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                        dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 
                                        dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 
                                        @error('id_bio') border-red-500 dark:border-red-400 @enderror" data-choices data-choices-sorting-false>

                                        <option value="" selected disabled>Seleccione Biometrico</option>
                                        @foreach ($biometricos as $biom)
                                        <option value="{{ $biom->id }}"
                                            {{ old('id_bio') == $biom->id ? 'selected' : '' }}>
                                            Biometrico {{ $biom->nombre_biometrico }} ({{ $biom->ip_biometrico }})
                                        </option>
                                        @endforeach
                                    </select>

                                    @error('id_bio')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror

                                </div>

                            </div>
                        </form>
                    </div>
                </div>

            </div>
        @endcan

        </div> 
        
           <div class="card" id="file-content" style="display: none;">
            <div class="card-body">
                <h5>datos de archivo.dat</h5>
                <!-- Contenedor para el contenido del archivo, inicialmente oculto -->
                <div
                    class="mt-4 p-4 border rounded bg-gray-100 dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-300">
                    <table id="file-table" class="w-full text-center min-w-full table-auto">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">N°</th>
                                <th class="px-4 py-2">PIN</th>
                                <th class="px-4 py-2">Time</th>
                                <th class="px-4 py-2">DeviceId</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Verified</th>
                                <th class="px-4 py-2">WorkCode</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aquí se insertarán las filas del archivo -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div> 
        

    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-wrapper -->

<!-- Modal para seleccionar fecha -->
<div id="modalEliminar" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white dark:bg-zink-800 p-6 rounded-md shadow-lg w-96">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Eliminación de asistencias</h2>

        <label for="fechaEliminar" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
            "¡Cuidado! Esto borrará todas las asistencias registradas en el biometrico.
        </label>

        <div class="flex justify-end space-x-2 mt-4">
            <button id="cancelarEliminar"
                class="bg-white text-slate-500 btn border-slate-500 hover:text-white hover:bg-slate-600 hover:border-slate-600 focus:text-white focus:bg-slate-600 focus:border-slate-600 focus:ring focus:ring-slate-100 active:text-white active:bg-slate-600 active:border-slate-600 active:ring active:ring-slate-100 dark:bg-zink-700 dark:hover:bg-slate-500 dark:ring-slate-400/20 dark:focus:bg-slate-500">
                Cancelar
            </button>
            <button id="confirmarEliminar"
                class="bg-white text-sky-500 btn border-sky-500 hover:text-white hover:bg-sky-600 hover:border-sky-600 focus:text-white focus:bg-sky-600 focus:border-sky-600 focus:ring focus:ring-sky-100 active:text-white active:bg-sky-600 active:border-sky-600 active:ring active:ring-sky-100 dark:bg-zink-700 dark:hover:bg-sky-500 dark:ring-sky-400/20 dark:focus:bg-sky-500">
                Confirmar
            </button>
        </div>
    </div>
</div>

<div id="defaultModal2" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div class="w-screen md:w-[30rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full">
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-16">Crear Biometrico</h5>
            <button data-modal-close="defaultModal2"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500"><i
                    data-lucide="x" class="size-5"></i></button>
        </div>
        <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
            <form id="modalFormBiometrico" action="{{ route('crear-biometricos') }}" method="POST">
                @csrf
                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <div class="flex-1">
                        <label for="nombreBiometrico" class="inline-block mb-2 text-base font-medium">
                            Nombre Biometrico
                        </label>
                        <input type="text" id="nombreBiometrico" name="nombre_biometrico" class="form-input border-slate-200 dark:border-zink-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 dark:text-zink-200 disabled:text-slate-500 dark:bg-zink-700 dark:placeholder:text-zink-200 
                            @error('nombre_biometrico') border-red-500 @enderror"
                            placeholder="Ingrese solo nombre del biometrico" value="{{ old('nombre_biometrico') }}" />
                        @error('nombre_biometrico')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <div class="flex-1">
                        <label for="ipBiometrico" class="inline-block mb-2 text-base font-medium">
                            Ip Biometrico
                        </label>
                        <input type="text" id="ipBiometrico" name="ip_biometrico" class="form-input border-slate-200 dark:border-zink-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 dark:text-zink-200 disabled:text-slate-500 dark:bg-zink-700 dark:placeholder:text-zink-200 
                            @error('ip_biometrico') border-red-500 @enderror" placeholder="Ingrese IP del biometrico"
                            value="{{ old('ip_biometrico') }}" />
                        @error('ip_biometrico')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </form>

        </div>
        <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">
            <button type="reset" form="modalFormBiometrico" data-modal-close="defaultModal2"
                class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">
                <i data-lucide="x" class="inline-block size-4"></i> <span class="align-middle">Cancelar</span>
            </button>

            <button type="submit" form="modalFormBiometrico"
                class="ml-2 text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20"
                onclick="confirmar()">
                Guardar
            </button>
        </div>
    </div>
</div>
@endsection