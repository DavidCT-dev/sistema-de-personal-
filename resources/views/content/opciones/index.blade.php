@extends('layouts.master')
@section('script')
<script>
    $(document).ready(async function() {
        const response = await fetch("{{ asset('assets/lang-datatables/Spanish.json') }}");
        translations = await response.json();
        $('#tablaTiposContrato').DataTable({
            "pageLength": 5,
            lengthMenu: [5, 10, 25],
            language: translations,
        }); // Primera tabla
        $('#tablaLugaresTrabajo').DataTable({
            "pageLength": 5,
            lengthMenu: [5, 10, 25],
            language: translations,
        }); // Segunda tabla
        $('#tablaCargos').DataTable({
            "pageLength": 5,
            lengthMenu: [5, 10, 25],
            language: translations,
        }); // Última tabla
        $('#tablaMotiovoPermiso').DataTable({
            "pageLength": 5,
            lengthMenu: [5, 10, 25],
            language: translations,
        });
        $('#tablaMotiovoFeriado').DataTable({
            "pageLength": 5,
            lengthMenu: [5, 10, 25],
            language: translations,
        });
    });
</script>
<script>
    function fillModalTipoContrato(element) {
        document.getElementById('tipo_contratoUpdate').value = element.dataset.desc;
        // Establecer la acción del formulario con la URL correcta
        const modal = document.querySelector('#formTipoContrato');
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
        document.getElementById('crearTipoContrato').innerHTML = '<i class="ri-file-text-line mr-2"></i> Actualizar';
    }

    function fillModalLugarTrabajo(element) {
        document.getElementById('tipo_LugarTabajo').value = element.dataset.descripcion;
        // Establecer la acción del formulario con la URL correcta
        const modal = document.querySelector('#formLugarTrabajo');
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
        document.getElementById('crearLugarTrabajo').innerHTML = '<i class="ri-map-pin-line mr-2"></i> Actualizar';
    }

    function fillModalMotivoPermiso(element) {
        document.getElementById('tipo_motivoPermisoUpdate').value = element.dataset.descripcion;
        // Establecer la acción del formulario con la URL correcta
        const modal = document.querySelector('#formMotivoPermiso');
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
        document.getElementById('crearMotivoPermiso').innerHTML = '<i class="ri-briefcase-4-line mr-2"></i> Actualizar';
    }

    function fillModalCargo(element) {
        document.getElementById('tipo_Cargo').value = element.dataset.descripcion;
        // Establecer la acción del formulario con la URL correcta
        const modal = document.querySelector('#formCargo');
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
        document.getElementById('crearCargo').innerHTML = '<i class="ri-briefcase-4-line mr-2"></i> Actualizar';
    }

    function fillModalMotivoFeriado(element) {
        document.getElementById('motivo_feriado').value = element.dataset.descripcion;
        // Establecer la acción del formulario con la URL correcta
        const modal = document.querySelector('#formMotivoFeriado');
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
        document.getElementById('crearMotivoFeriado').innerHTML = '<i class="ri-briefcase-4-line mr-2"></i> Actualizar';
    }

    function deleteModalCargo(element) {
        document.getElementById('cargoId').value = element.dataset.id;
    }

    function deleteModalLugarTrabajo(element) {
        document.getElementById('lugarTrabajoId').value = element.dataset.id;
    }

    function deleteModalTipoContrato(element) {
        document.getElementById('tipoContratoId').value = element.dataset.id;
    }

    function deleteModalMotivoPermiso(element) {
        document.getElementById('motivoPermisioId').value = element.dataset.id;
    }

    function deleteModalMotivoFeriado(element) {
        document.getElementById('motivoFeriadoId').value = element.dataset.id;
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Botón de Cancelar para Tipo de Contrato
        const cancelarTipoContrato = document.getElementById("cancelarTipoContrato");
        if (cancelarTipoContrato) {
            cancelarTipoContrato.addEventListener("click", function() {
                const form = document.getElementById("formTipoContrato");
                const submitButton = document.getElementById("crearTipoContrato");
                if (form && submitButton) {
                    form.action = "{{ route('crear-tipoContrato') }}";
                    const methodInput = form.querySelector('input[name="_method"]');
                    if (methodInput) methodInput.remove();
                    submitButton.innerHTML = '<i class="ri-file-text-line mr-2"></i> Crear';
                }
            });
        }
        // Botón de Cancelar para Lugar de Trabajo
        const cancelarLugarTrabajo = document.getElementById("cancelarLugarTrabajo");
        if (cancelarLugarTrabajo) {
            cancelarLugarTrabajo.addEventListener("click", function() {
                const form = document.getElementById("formLugarTrabajo");
                const submitButton = document.getElementById("crearLugarTrabajo");
                if (form && submitButton) {
                    form.action = "{{ route('crear-lugarTrabajo') }}";
                    const methodInput = form.querySelector('input[name="_method"]');
                    if (methodInput) methodInput.remove();
                    submitButton.innerHTML = '<i class="ri-map-pin-line mr-2"></i> Crear';
                }
            });
        }
        // Botón de Cancelar para Cargo
        const cancelarCargo = document.getElementById("cancelarCargo");
        if (cancelarCargo) {
            cancelarCargo.addEventListener("click", function() {
                const form = document.getElementById("formCargo");
                const submitButton = document.getElementById("crearCargo");
                if (form && submitButton) {
                    form.action = "{{ route('crear-cargo') }}";
                    const methodInput = form.querySelector('input[name="_method"]');
                    if (methodInput) methodInput.remove();
                    submitButton.innerHTML = '<i class="ri-briefcase-4-line mr-2"></i> Crear';
                }
            });
        }
        const cancelarMotivoPermiso = document.getElementById("cancelarMotivoPermiso");
        if (cancelarMotivoPermiso) {
            cancelarMotivoPermiso.addEventListener("click", function() {
                const form = document.getElementById("formMotivoPermiso");
                const submitButton = document.getElementById("crearMotivoPermiso");
                if (form && submitButton) {
                    form.action = "{{ route('crear-motivoPermiso') }}";
                    const methodInput = form.querySelector('input[name="_method"]');
                    if (methodInput) methodInput.remove();
                    submitButton.innerHTML = '<i class="ri-file-text-line mr-2"></i> Crear';
                }
            });
        }
        const cancelarMotivoFe = document.getElementById("cancelarMotivoFeriado");
        if (cancelarMotivoFe) {
            cancelarMotivoFe.addEventListener("click", function() {
                const form = document.getElementById("formMotivoFeriado");
                const submitButton = document.getElementById("crearMotivoFeriado");
                if (form && submitButton) {
                    form.action = "{{ route('crear-motivoFeriado') }}";
                    const methodInput = form.querySelector('input[name="_method"]');
                    if (methodInput) methodInput.remove();
                    submitButton.innerHTML = '<i class="ri-file-text-line mr-2"></i> Crear';
                }
            });
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
                <h5 class="text-16">Aqui puede crear cargos, lugar de trabajos, tipos de contratos</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li
                    class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="#!" class="text-slate-400 dark:text-zink-200">Personal</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    Opciones
                </li>
            </ul>
        </div>

        <div class="p-6 dark:bg-zink-800 rounded-md ">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-5 gap-y-6">
                <!-- Formulario Tipo de Contrato -->
                <form id="formTipoContrato" action="{{ route('crear-tipoContrato') }}" method="POST"
                    class="p-4 bg-slate-50 dark:bg-zink-700 rounded-md shadow space-y-4 mb-3">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 mb-3">
                        <input type="text" id="tipo_contratoUpdate" name="tipo_contrato"
                            value="{{ old('tipo_contrato') }}"
                            class="w-full form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 placeholder:text-slate-400 dark:placeholder:text-zink-200 dark:text-zink-100 dark:bg-zink-800 rounded-md @error('tipo_contrato') border-red-500 @enderror"
                            placeholder="Ingrese tipo de contrato">

                        <!-- Mostrar error debajo del input -->
                        @error('tipo_contrato')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex space-x-4 justify-center p-1">
                        <!-- Botón de Crear Tipo de Contrato -->
                        @can('crear_tipo_contrato')
                        <button type="submit" id="crearTipoContrato"
                            class="m-1 w-1/2 bg-custom-500 text-white px-4 py-2 rounded-md hover:bg-custom-600 focus:outline-none focus:ring-2 focus:ring-custom-500 flex items-center justify-center">
                            <i class="ri-file-text-line mr-2"></i>Crear
                        </button>
                        @endcan
                        <!-- Botón de Cancelar -->
                        <button type="reset" id="cancelarTipoContrato"
                            class="m-1 w-1/2 bg-red-100 text-red-500 px-4 py-2 rounded-md hover:bg-red-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-500 flex items-center justify-center transition-all duration-200 ease-linear dark:bg-red-500/20 dark:hover:bg-red-500 dark:text-red-200 dark:hover:text-white">
                            <i class="ri-close-line mr-2"></i> Cancelar
                        </button>
                    </div>

                </form>

                <!-- Formulario Lugar de Trabajo -->
                <form id="formLugarTrabajo" action="{{ route('crear-lugarTrabajo') }}"
                    class="p-4 bg-slate-50 dark:bg-zink-700 rounded-md shadow space-y-4 mb-3" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 mb-3">
                        <input type="text" name="lugar_trabajo" id="tipo_LugarTabajo" value="{{ old('lugar_trabajo') }}"
                            class="w-full form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 placeholder:text-slate-400 dark:placeholder:text-zink-200 dark:text-zink-100 dark:bg-zink-800 rounded-md @error('lugar_trabajo') border-red-500 @enderror"
                            placeholder="Ingrese lugar de trabajo">

                        <!-- Mostrar error debajo del input -->
                        @error('lugar_trabajo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex space-x-4 justify-center p-1">
                        <!-- Botón de Crear Lugar de Trabajo -->
                        <button type="submit" id="crearLugarTrabajo"
                            class="m-1 w-1/2 bg-custom-500 text-white px-4 py-2 rounded-md hover:bg-custom-600 focus:outline-none focus:ring-2 focus:ring-custom-500 flex items-center justify-center">
                            <i class="ri-map-pin-line mr-2"></i>Crear
                        </button>
                        <!-- Botón de Cancelar -->
                        @can('crear_lugar_trabajo')
                        <button type="reset" id="cancelarLugarTrabajo"
                            class="m-1 w-1/2 bg-red-100 text-red-500 px-4 py-2 rounded-md hover:bg-red-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-500 flex items-center justify-center transition-all duration-200 ease-linear dark:bg-red-500/20 dark:hover:bg-red-500 dark:text-red-200 dark:hover:text-white">
                            <i class="ri-close-line mr-2"></i> Cancelar
                        </button>
                        @endcan

                    </div>
                </form>

                <!-- Formulario Cargo -->
                <form id="formCargo" action="{{ route('crear-cargo') }}" method="POST"
                    class="p-4 bg-slate-50 dark:bg-zink-700 rounded-md shadow space-y-4 mb-3">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 mb-3">
                        <input type="text" name="cargo" value="{{ old('cargo') }}" id="tipo_Cargo"
                            class="w-full form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 placeholder:text-slate-400 dark:placeholder:text-zink-200 dark:text-zink-100 dark:bg-zink-800 rounded-md @error('cargo') border-red-500 @enderror"
                            placeholder="Ingrese cargo">

                        <!-- Mostrar error debajo del input -->
                        @error('cargo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex space-x-4 justify-center p-1">
                        @can('crear_cargo')
                        <button type="submit" id="crearCargo"
                            class="m-1 w-1/2 bg-custom-500 text-white px-4 py-2 rounded-md hover:bg-custom-600 focus:outline-none focus:ring-2 focus:ring-custom-500 flex items-center justify-center">
                            <i class="ri-briefcase-4-line mr-2"></i>Crear
                        </button>
                        @endcan

                        <!-- Botón de Cancelar -->
                        <button type="reset" id="cancelarCargo"
                            class="m-1 w-1/2 bg-red-100 text-red-500 px-4 py-2 rounded-md hover:bg-red-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-500 flex items-center justify-center transition-all duration-200 ease-linear dark:bg-red-500/20 dark:hover:bg-red-500 dark:text-red-200 dark:hover:text-white">
                            <i class="ri-close-line mr-2"></i> Cancelar
                        </button>
                    </div>
                </form>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-6">
                <!-- Formulario motivo-permiso -->
                <form id="formMotivoPermiso" action="{{ route('crear-motivoPermiso') }}" method="POST"
                    class="p-4 bg-slate-50 dark:bg-zink-700 rounded-md shadow space-y-4 mb-3">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 mb-3">
                        <input type="text" id="tipo_motivoPermisoUpdate" name="motivo_permiso"
                            value="{{ old('motivo_permiso') }}"
                            class="w-full form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 placeholder:text-slate-400 dark:placeholder:text-zink-200 dark:text-zink-100 dark:bg-zink-800 rounded-md @error('tipo_contrato') border-red-500 @enderror"
                            placeholder="Ingrese motivo del permiso">

                        <!-- Mostrar error debajo del input -->
                        @error('motivo_permiso')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex space-x-4 justify-center p-1">
                        <!-- Botón de Crear Tipo de Contrato -->
                        <button type="submit" id="crearMotivoPermiso"
                            class="m-1 w-1/2 bg-custom-500 text-white px-4 py-2 rounded-md hover:bg-custom-600 focus:outline-none focus:ring-2 focus:ring-custom-500 flex items-center justify-center">
                            <i class="ri-file-text-line mr-2"></i>Crear
                        </button>

                        <!-- Botón de Cancelar -->
                        @can('crear_motivo_permiso')
                        <button type="reset" id="cancelarMotivoPermiso"
                            class="m-1 w-1/2 bg-red-100 text-red-500 px-4 py-2 rounded-md hover:bg-red-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-500 flex items-center justify-center transition-all duration-200 ease-linear dark:bg-red-500/20 dark:hover:bg-red-500 dark:text-red-200 dark:hover:text-white">
                            <i class="ri-close-line mr-2"></i> Cancelar
                        </button>
                        @endcan

                    </div>

                </form>

                <!-- Formulario motivo-feriado -->
                <form id="formMotivoFeriado" action="{{ route('crear-motivoFeriado') }}"
                    class="p-4 bg-slate-50 dark:bg-zink-700 rounded-md shadow space-y-4 mb-3" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 mb-3">
                        <input type="text" name="motivo_feriado" id="motivo_feriado" value="{{ old('motivo_Feriado') }}"
                            class="w-full form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 placeholder:text-slate-400 dark:placeholder:text-zink-200 dark:text-zink-100 dark:bg-zink-800 rounded-md @error('lugar_trabajo') border-red-500 @enderror"
                            placeholder="Ingrese Motivo de Feriado">

                        <!-- Mostrar error debajo del input -->
                        @error('motivo_feriado')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex space-x-4 justify-center p-1">
                        <!-- Botón de Crear Lugar de Trabajo -->
                        @can('crear_motivo_feriado')
                        <button type="submit" id="crearMotivoFeriado"
                            class="m-1 w-1/2 bg-custom-500 text-white px-4 py-2 rounded-md hover:bg-custom-600 focus:outline-none focus:ring-2 focus:ring-custom-500 flex items-center justify-center">
                            <i class="ri-map-pin-line mr-2"></i>Crear
                        </button>
                        @endcan

                        <!-- Botón de Cancelar -->
                        <button type="reset" id="cancelarMotivoFeriado"
                            class="m-1 w-1/2 bg-red-100 text-red-500 px-4 py-2 rounded-md hover:bg-red-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-500 flex items-center justify-center transition-all duration-200 ease-linear dark:bg-red-500/20 dark:hover:bg-red-500 dark:text-red-200 dark:hover:text-white">
                            <i class="ri-close-line mr-2"></i> Cancelar
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
        <div class="grid grid-cols-1 gap-x-5 xl:grid-cols-2">

            <!-- Primera tabla -->
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-4 text-15">Tipos de Contrato</h6>

                    <div class="overflow-x-auto">
                        <table id="tablaTiposContrato" class="hover group" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descripción</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tiposContrato as $tipoContrato)
                                <tr id="row-{{ $tipoContrato->id }}">
                                    <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                        {{ $tipoContrato->id }}</td>
                                    <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                        {{ $tipoContrato->descripcion }}</td>

                                    <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                        <!-- Botón Editar -->
                                        @can('editar_tipo_contrato')
                                        <a href="#!" {{-- data-modal-target="updateEmpleadoModal" --}}
                                            data-id="{{ $tipoContrato->id }}"
                                            data-desc="{{ $tipoContrato->descripcion }}"
                                            data-url="{{ route('actualizar-tipoContrato', ['id' => $tipoContrato->id]) }}"
                                            class="transition-all duration-150 ease-linear text-custom-500 hover:text-custom-600"
                                            onclick="fillModalTipoContrato(this)" title="Editar Tipo de Contrato"
                                            aria-label="Editar Tipo de Contrato">
                                            <i class="ri-edit-2-line text-lg"></i>
                                        </a>
                                        @endcan

                                        @can('eliminar_tipo_contrato')
                                        <!-- Botón Eliminar -->
                                        <a href="#!" data-id="{{ $tipoContrato->id }}" data-modal-target="deleteModal"
                                            class="transition-all duration-150 ease-linear text-custom-500 hover:text-custom-600"
                                            onclick="deleteModalTipoContrato(this)" title="Eliminar Tipo de Contrato"
                                            aria-label="Eliminar Tipo de Contrato">
                                            <i class="ri-delete-bin-2-line text-lg"></i>
                                        </a>
                                        @endcan

                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Segunda tabla -->
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-4 text-15">Lugar de Trabajo</h6>

                    <div class="overflow-x-auto">
                        <table id="tablaLugaresTrabajo" class="hover group" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descripción</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lugaresTrabajo as $lugar)
                                <tr>
                                    <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                        {{ $lugar->id }}</td>
                                    <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                        {{ $lugar->descripcion }}</td>
                                    <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                        @can('editar_lugar_trabajo')
                                        <a href="#!"
                                            class="transition-all duration-150 ease-linear text-custom-500 hover:text-custom-600"
                                            data-id="{{ $lugar->id }}" data-descripcion="{{ $lugar->descripcion }}"
                                            data-url="{{ route('actualizar-lugarTrabajo', ['id' => $lugar->id]) }}"
                                            onclick="fillModalLugarTrabajo(this)" title="Editar Lugar de Trabajo"
                                            aria-label="Editar Lugar de Trabajo">
                                            <i class="ri-edit-2-line text-lg"></i>
                                        </a>
                                        @endcan

                                        @can('eliminar_lugar_trabajo')
                                        <a href="#!" data-id="{{ $lugar->id }}"
                                            class="transition-all duration-150 ease-linear text-custom-500 hover:text-custom-600"
                                            data-modal-target="deleteModalLugarTrabajo"
                                            onclick="deleteModalLugarTrabajo(this)" title="Eliminar Lugar de Trabajo"
                                            aria-label="Eliminar Lugar de Trabajo">
                                            <i class="ri-delete-bin-2-line text-lg"></i>
                                        </a>
                                        @endcan

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- tabla cargos -->
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-4 text-15">Cargos</h6>

                    <div class="overflow-x-auto">
                        <table id="tablaCargos" class="hover group" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descripción</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cargos as $cargo)
                                <tr>
                                    <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                        {{ $cargo->id }}</td>
                                    <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                        {{ $cargo->descripcion }}</td>
                                    <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                        @can('editar_cargo')
                                        <a href="#!"
                                            class="transition-all duration-150 ease-linear text-custom-500 hover:text-custom-600"
                                            data-descripcion="{{ $cargo->descripcion }}"
                                            data-url="{{ route('actualizar-cargo', ['id' => $cargo->id]) }}"
                                            onclick="fillModalCargo(this)">
                                            <i class="ri-edit-2-line text-lg"></i>
                                        </a>
                                        @endcan

                                        @can('eliminar_cargo')
                                        <a href="#!"
                                            class="transition-all duration-150 ease-linear text-custom-500 hover:text-custom-600"
                                            data-id="{{ $cargo->id }}" data-modal-target="deleteModalCargo"
                                            onclick="deleteModalCargo(this)">
                                            <i class="ri-delete-bin-2-line text-lg"></i>
                                        </a>
                                        @endcan

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- tabla motivo permisos -->
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-4 text-15">Motivos Permisos</h6>

                    <div class="overflow-x-auto">
                        <table id="tablaMotiovoPermiso" class="hover group" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descripción</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($motivoPermisos as $motivop)
                                <tr>
                                    <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                        {{ $motivop->id }}</td>
                                    <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                        {{ $motivop->descripcion }}</td>
                                    <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                        @can('editar_motivo_permiso')
                                        <a href="#!"
                                            class="transition-all duration-150 ease-linear text-custom-500 hover:text-custom-600"
                                            data-descripcion="{{ $motivop->descripcion }}"
                                            data-url="{{ route('actualizar-motivoPermiso', ['id' => $motivop->id]) }}"
                                            onclick="fillModalMotivoPermiso(this)">
                                            <i class="ri-edit-2-line text-lg"></i>
                                        </a>
                                        @endcan

                                        @can('eliminar_motivo_permiso')
                                        <a href="#!"
                                            class="transition-all duration-150 ease-linear text-custom-500 hover:text-custom-600"
                                            data-id="{{ $motivop->id }}" data-modal-target="deleteModalMotivoPermiso"
                                            onclick="deleteModalMotivoPermiso(this)">
                                            <i class="ri-delete-bin-2-line text-lg"></i>
                                        </a>
                                        @endcan

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- tabla motivo feriados -->
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-4 text-15">Motivos feriados</h6>

                    <div class="overflow-x-auto">
                        <table id="tablaMotiovoFeriado" class="hover group" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descripción</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($motivoFeriados as $motivof)
                                <tr>
                                    <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                        {{ $motivof->id }}</td>
                                    <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                        {{ $motivof->descripcion }}</td>
                                    <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                        @can('editar_motivo_feriado')
                                        <a href="#!"
                                            class="transition-all duration-150 ease-linear text-custom-500 hover:text-custom-600"
                                            data-descripcion="{{ $motivof->descripcion }}"
                                            data-url="{{ route('actualizar-motivoFeriado', ['id' => $motivof->id]) }}"
                                            onclick="fillModalMotivoFeriado(this)">
                                            <i class="ri-edit-2-line text-lg"></i>
                                        </a>
                                        @endcan

                                        @can('eliminar_motivo_feriado')
                                        <a href="#!"
                                            class="transition-all duration-150 ease-linear text-custom-500 hover:text-custom-600"
                                            data-id="{{ $motivof->id }}" data-modal-target="deleteModalMotivoFeriado"
                                            onclick="deleteModalMotivoFeriado(this)">
                                            <i class="ri-delete-bin-2-line text-lg"></i>
                                        </a>
                                        @endcan

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="deleteModal" modal-center=""
        class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
        <div class="w-screen md:w-[25rem] bg-white shadow rounded-md dark:bg-zink-600">
            <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto px-6 py-8">
                <div class="float-right">
                    <button data-modal-close="deleteModal"
                        class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500"><i
                            data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <img src="data:image/png;base64,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"
                    alt="" class="block h-12 mx-auto">
                <div class="mt-5 text-center">
                    <h5 class="mb-1">Estas Seguro?</h5>
                    <p class="text-slate-500 dark:text-zink-200">Estas seguro de Eliminar tipo de contrato?</p>
                    <div class="flex justify-center gap-2 mt-6">

                        @if (isset($tipoContrato->id))
                        <form action="{{ route('eliminar-tipoContrato', $tipoContrato->id) }}" method="POST"
                            class="inline mt-6">
                            @csrf
                            @method('DELETE')
                            <button type="reset" data-modal-close="deleteModal"
                                class="bg-white text-slate-500 btn hover:text-slate-500 hover:bg-slate-100 focus:text-slate-500 focus:bg-slate-100 active:text-slate-500 active:bg-slate-100 dark:bg-zink-600 dark:hover:bg-slate-500/10 dark:focus:bg-slate-500/10 dark:active:bg-slate-500/10">Cancelar</button>

                            <input type="hidden" id="tipoContratoId" name="tipoContratoId">

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

    <div id="deleteModalLugarTrabajo" modal-center=""
        class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
        <div class="w-screen md:w-[25rem] bg-white shadow rounded-md dark:bg-zink-600">
            <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto px-6 py-8">
                <div class="float-right">
                    <button data-modal-close="deleteModalLugarTrabajo"
                        class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500"><i
                            data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <img src="data:image/png;base64,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"
                    alt="" class="block h-12 mx-auto">
                <div class="mt-5 text-center">
                    <h5 class="mb-1">Estas Seguro?</h5>
                    <p class="text-slate-500 dark:text-zink-200">Estas seguro de Eliminar lugar de trabajo?</p>
                    <div class="flex justify-center gap-2 mt-6">

                        @if (isset($lugar->id))
                        <form action="{{ route('eliminar-lugarTrabajo', $lugar->id) }}" method="POST"
                            class="inline mt-6">
                            @csrf
                            @method('DELETE')
                            <button type="reset" data-modal-close="deleteModalLugarTrabajo"
                                class="bg-white text-slate-500 btn hover:text-slate-500 hover:bg-slate-100 focus:text-slate-500 focus:bg-slate-100 active:text-slate-500 active:bg-slate-100 dark:bg-zink-600 dark:hover:bg-slate-500/10 dark:focus:bg-slate-500/10 dark:active:bg-slate-500/10">Cancelar</button>

                            <input type="hidden" id="lugarTrabajoId" name="lugarTrabajoId">

                            <button type="submit" id="deleteRecord" data-modal-close="deleteModalLugarTrabajo"
                                class="text-white bg-red-500 border-red-500 btn hover:text-white hover:bg-red-600 hover:border-red-600 focus:text-white focus:bg-red-600 focus:border-red-600 focus:ring focus:ring-red-100 active:text-white active:bg-red-600 active:border-red-600 active:ring active:ring-red-100 dark:ring-custom-400/20">Si,
                                Eliminalo!</button>
                        </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModalCargo" modal-center=""
        class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
        <div class="w-screen md:w-[25rem] bg-white shadow rounded-md dark:bg-zink-600">
            <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto px-6 py-8">
                <div class="float-right">
                    <button data-modal-close="deleteModalCargo"
                        class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500"><i
                            data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <img src="data:image/png;base64,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"
                    alt="" class="block h-12 mx-auto">
                <div class="mt-5 text-center">
                    <h5 class="mb-1">Estas Seguro?</h5>
                    <p class="text-slate-500 dark:text-zink-200">Estas seguro de Eliminar cargo?</p>
                    <div class="flex justify-center gap-2 mt-6">

                        @if (isset($cargo->id))
                        <form action="{{ route('eliminar-cargo', $lugar->id) }}" method="POST" class="inline mt-6">
                            @csrf
                            @method('DELETE')
                            <button type="reset" data-modal-close="deleteModalCargo"
                                class="bg-white text-slate-500 btn hover:text-slate-500 hover:bg-slate-100 focus:text-slate-500 focus:bg-slate-100 active:text-slate-500 active:bg-slate-100 dark:bg-zink-600 dark:hover:bg-slate-500/10 dark:focus:bg-slate-500/10 dark:active:bg-slate-500/10">Cancelar</button>

                            <input type="hidden" id="cargoId" name="cargoId">

                            <button type="submit" id="deleteRecord" data-modal-close="deleteModalCargo"
                                class="text-white bg-red-500 border-red-500 btn hover:text-white hover:bg-red-600 hover:border-red-600 focus:text-white focus:bg-red-600 focus:border-red-600 focus:ring focus:ring-red-100 active:text-white active:bg-red-600 active:border-red-600 active:ring active:ring-red-100 dark:ring-custom-400/20">Si,
                                Eliminalo!</button>
                        </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModalMotivoPermiso" modal-center=""
        class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
        <div class="w-screen md:w-[25rem] bg-white shadow rounded-md dark:bg-zink-600">
            <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto px-6 py-8">
                <div class="float-right">
                    <button data-modal-close="deleteModalMotivoPermiso"
                        class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500"><i
                            data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <img src="data:image/png;base64,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"
                    alt="" class="block h-12 mx-auto">
                <div class="mt-5 text-center">
                    <h5 class="mb-1">Estas Seguro?</h5>
                    <p class="text-slate-500 dark:text-zink-200">Estas seguro de Eliminar motivo de permiso?</p>
                    <div class="flex justify-center gap-2 mt-6">

                        @if (isset($cargo->id))
                        <form action="{{ route('eliminar-motivo-permiso', $lugar->id) }}" method="POST"
                            class="inline mt-6">
                            @csrf
                            @method('DELETE')
                            <button type="reset" data-modal-close="deleteModalMotivoPermiso"
                                class="bg-white text-slate-500 btn hover:text-slate-500 hover:bg-slate-100 focus:text-slate-500 focus:bg-slate-100 active:text-slate-500 active:bg-slate-100 dark:bg-zink-600 dark:hover:bg-slate-500/10 dark:focus:bg-slate-500/10 dark:active:bg-slate-500/10">Cancelar</button>

                            <input type="hidden" id="motivoPermisioId" name="motivoPermisioId">

                            <button type="submit" id="deleteRecord" data-modal-close="deleteModalMotivoPermiso"
                                class="text-white bg-red-500 border-red-500 btn hover:text-white hover:bg-red-600 hover:border-red-600 focus:text-white focus:bg-red-600 focus:border-red-600 focus:ring focus:ring-red-100 active:text-white active:bg-red-600 active:border-red-600 active:ring active:ring-red-100 dark:ring-custom-400/20">Si,
                                Eliminalo!</button>
                        </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModalMotivoFeriado" modal-center=""
        class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
        <div class="w-screen md:w-[25rem] bg-white shadow rounded-md dark:bg-zink-600">
            <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto px-6 py-8">
                <div class="float-right">
                    <button data-modal-close="deleteModalMotivoFeriado"
                        class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500"><i
                            data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <img src="data:image/png;base64,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"
                    alt="" class="block h-12 mx-auto">
                <div class="mt-5 text-center">
                    <h5 class="mb-1">Estas Seguro?</h5>
                    <p class="text-slate-500 dark:text-zink-200">Estas seguro de Eliminar motivo de Feriado?</p>
                    <div class="flex justify-center gap-2 mt-6">

                        @if (isset($cargo->id))
                        <form action="{{ route('eliminar-motivo-feriado', $lugar->id) }}" method="POST"
                            class="inline mt-6">
                            @csrf
                            @method('DELETE')
                            <button type="reset" data-modal-close="deleteModalMotivoFeriado"
                                class="bg-white text-slate-500 btn hover:text-slate-500 hover:bg-slate-100 focus:text-slate-500 focus:bg-slate-100 active:text-slate-500 active:bg-slate-100 dark:bg-zink-600 dark:hover:bg-slate-500/10 dark:focus:bg-slate-500/10 dark:active:bg-slate-500/10">Cancelar</button>

                            <input type="hidden" id="motivoFeriadoId" name="motivoFeriadoId">

                            <button type="submit" id="deleteRecord" data-modal-close="deleteModalMotivoFeriado"
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