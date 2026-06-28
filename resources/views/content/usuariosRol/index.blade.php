@extends('layouts.master')

@section('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('#usuariosTable').DataTable({
            responsive: true,
            paging: true,
            pageLength: 10,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            language: {
                url: "{{ asset('assets/lang-datatables/Spanish.json') }}"
            },
            lengthMenu: [5, 10, 25, 50]
        });
    });
</script>

<script>
    function openRoleModal(element) {
        const userId = element.getAttribute('data-user-id');
        const currentRoles = element.getAttribute('data-current-roles');
        document.getElementById('modalUserId').value = userId;
        document.getElementById('currentRoles').textContent = currentRoles || 'Ningún rol asignado';
        // Opcional: Pre-seleccionar los roles actuales en el select
        const select = document.getElementById('newRoles');
        const currentRolesArray = currentRoles ? currentRoles.split(', ') : [];
        Array.from(select.options).forEach(option => {
            option.selected = currentRolesArray.includes(option.value);
        });
    }
    document.getElementById('roleChangeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch("{{ route('users.updateRoles') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('changeRoleModal').classList.add('hidden');
                    // Recarga la página o actualiza la UI según necesites
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast('error', data.message || 'Error al actualizar roles');
                }
            })
            .catch(error => {
                showToast('error', 'Error en la solicitud');
            });
    });
    // Función auxiliar para mostrar notificaciones
    function showToast(type, message) {
        // Implementa tu propio sistema de notificaciones o usa uno existente
        console.log(`${type}: ${message}`);
    }
</script>
<script>
    function submitPermissionForm() {
        document.getElementById('permissionForm').submit();
    }

    function submitRoleForm() {
        const userId = $('#roleModal').data('user-id');
        // Asegúrate de que userId no sea undefined
        if (userId) {
            $('#roleForm').attr('action', '{{ url('
                usuarios ') }}/' + userId);
            $('#roleForm').submit();
        } else {
            alert('El ID del usuario no está disponible.');
        }
    }
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectRole = document.getElementById('selectRole');
    const roleNameContainer = document.getElementById('roleNameContainer');
    const roleNameInput = document.getElementById('roleName');
    const permissionsContainer = document.getElementById('permissionsContainer');
    const updateRoleBtn = document.getElementById('updateRoleBtn');
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    
    // Datos de roles con sus permisos (deberías pasarlos desde el controlador)
    const rolesData = @json($allRoles->keyBy('id')->map(function($role) {
        return [
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('id')->toArray()
        ];
    }));

    selectRole.addEventListener('change', function() {
        const roleId = this.value;
        
        if (roleId) {
            // Mostrar los contenedores
            roleNameContainer.classList.remove('hidden');
            permissionsContainer.classList.remove('hidden');
            updateRoleBtn.disabled = false;
            
            // Actualizar el nombre del rol
            roleNameInput.value = rolesData[roleId].name;
            
            // Desmarcar todos los checkboxes primero
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Marcar los checkboxes de los permisos del rol seleccionado
            rolesData[roleId].permissions.forEach(permissionId => {
                const checkbox = document.getElementById(`perm-edit-${permissionId}`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        } else {
            // Ocultar los contenedores si no hay rol seleccionado
            roleNameContainer.classList.add('hidden');
            permissionsContainer.classList.add('hidden');
            updateRoleBtn.disabled = true;
        }
    });
});
</script>
@endsection

@section('content')
<div
    class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-16">Usuarios y Roles</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li
                    class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="#" class="text-slate-400 dark:text-zink-200">Administración</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    Usuarios
                </li>
            </ul>
        </div>

        @can('crear_rol')
           <button data-modal-target="ModalCrearRol" type="button"
            class="mb-2 text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:focus:ring-custom-400/20">Crear
            Rol</button> 
        @endcan
        @can('editar_rol')
         <button data-modal-target="ModalEditarRol" type="button"
            class="mb-2 text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:focus:ring-custom-400/20">Editar
            Rol</button>   
        @endcan
        

        <div class="card">
            <div class="card-body">
                <div class="overflow-x-auto">
                    <table id="usuariosTable" class="display" style="width:100%">
                        <thead>
                            <tr class="bg-gray-100 text-left text-gray-600 text-sm font-medium uppercase">
                                <th class="py-3 px-4 border-b border-gray-300">ID</th>
                                <th class="py-3 px-4 border-b border-gray-300">Nombre de Usuario</th>
                                <th class="py-3 px-4 border-b border-gray-300">Roles</th>
                                <th class="py-3 px-4 border-b border-gray-300">Persona Asociada</th>
                                <th class="py-3 px-4 border-b border-gray-300">CI</th>

                                <th class="py-3 px-4 border-b border-gray-300">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usuarios as $usuario)
                            <tr>
                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                    {{ $usuario->id }}</td>
                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                    {{ $usuario->name }}</td>

                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($usuario->roles as $rol)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-700/50">
                                            {{ $rol->name }}
                                            <svg class="w-3 h-3 ml-1 -mr-0.5 text-blue-400 dark:text-blue-300"
                                                fill="currentColor" viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                    @if ($usuario->persona)
                                    {{ $usuario->persona->nombres }} {{ $usuario->persona->apellido_pat }}
                                    {{ $usuario->persona->apellido_mat }}
                                    @else
                                    <span class="text-gray-400">No asociado</span>
                                    @endif
                                </td>
                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                    @if ($usuario->persona)
                                    {{ $usuario->persona->ci }}
                                    @else
                                    <span class="text-gray-400">No asociado</span>
                                    @endif
                                </td>
                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                    <div class="relative dropdown">
                                        <button id="userAction{{ $usuario->id }}" data-bs-toggle="dropdown"
                                            class="flex items-center justify-center size-[30px] dropdown-toggle p-0 text-slate-500 btn bg-slate-100 hover:text-white hover:bg-slate-600 focus:text-white focus:bg-slate-600 focus:ring focus:ring-slate-100 active:text-white active:bg-slate-600 active:ring active:ring-slate-100 dark:bg-slate-500/20 dark:text-slate-400 dark:hover:bg-slate-500 dark:hover:text-white dark:focus:bg-slate-500 dark:focus:text-white dark:active:bg-slate-500 dark:active:text-white dark:ring-slate-400/20">
                                            <i data-lucide="more-horizontal" class="size-3"></i>
                                        </button>
                                        
                                        @can('cambiar_rol')
                                          <ul class="absolute z-50 hidden py-2 mt-1 ltr:text-left rtl:text-right list-none bg-white rounded-md shadow-md dropdown-menu min-w-[10rem] dark:bg-zink-600"
                                            aria-labelledby="userAction{{ $usuario->id }}">
                                            <li>
                                                <a href="#" data-modal-target="changeRoleModal"
                                                    data-user-id="{{ $usuario->id }}"
                                                    data-current-roles="{{ $usuario->roles->pluck('name')->join(', ') }}"
                                                    class="block px-4 py-1.5 text-base transition-all duration-200 ease-linear text-slate-600 dropdown-item hover:bg-slate-100 hover:text-slate-500 focus:bg-slate-100 focus:text-slate-500 dark:text-zink-100 dark:hover:bg-zink-500 dark:hover:text-zink-200 dark:focus:bg-zink-500 dark:focus:text-zink-200"
                                                    onclick="openRoleModal(this)">
                                                    <i data-lucide="shield"
                                                        class="inline-block size-3 ltr:mr-1 rtl:ml-1"></i>
                                                    <span class="align-middle">Cambiar Rol</span>
                                                </a>
                                            </li>
                                        </ul>  
                                        @endcan
                                        
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 text-left text-gray-600 text-sm font-medium uppercase">
                                <th class="py-3 px-4 border-b border-gray-300">ID</th>
                                <th class="py-3 px-4 border-b border-gray-300">Nombre de Usuario</th>

                                <th class="py-3 px-4 border-b border-gray-300">Roles</th>
                                <th class="py-3 px-4 border-b border-gray-300">Persona Asociada</th>
                                <th class="py-3 px-4 border-b border-gray-300">CI</th>
                                <th class="py-3 px-4 border-b border-gray-300">Acciones</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar roles -->
<div id="changeRoleModal" modal-center=""
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4">
    <div class="w-screen lg:w-[55rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full">
        <!-- Encabezado del modal -->
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-16">Cambiar Roles de Usuario</h5>
            <button data-modal-close="changeRoleModal"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                <i data-lucide="x" class="size-5"></i>
            </button>
        </div>

        <!-- Contenido del modal -->
        <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
            <form id="roleChangeForm" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="modalUserId">

                <!-- Sección de roles actuales -->
                <div class="mb-6">
                    <label class="inline-block mb-2 text-base font-medium">Roles Actuales</label>
                    <div id="currentRoles" class="p-4 bg-gray-50 rounded-lg dark:bg-zink-700">
                        <!-- Se llenará dinámicamente con JavaScript -->
                        <div class="flex flex-wrap gap-2" id="currentRolesBadges"></div>
                    </div>
                </div>

                <!-- Selector de nuevos roles -->
                <div class="mb-6">
                    <label for="newRoles" class="inline-block mb-2 text-base font-medium">Asignar Nuevos Roles</label>
                    <select name="roles[]" id="newRoles" multiple
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                        @foreach($allRoles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-sm text-gray-500 dark:text-zink-300">Mantén presionado Ctrl (Windows) o Command
                        (Mac) para seleccionar múltiples roles</p>
                </div>

            </form>
        </div>

        <!-- Pie del modal -->
        <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">
            <button type="button" data-modal-close="changeRoleModal"
                class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">
                <i data-lucide="x" class="inline-block size-4"></i> Cancelar
            </button>
            <button type="submit" form="roleChangeForm"
                class="ml-2 text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                <i data-lucide="check" class="inline-block size-4"></i> Guardar Cambios
            </button>
        </div>
    </div>
</div>

<!-- Modal crear Rol -->
<div id="ModalCrearRol"
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 top-1/2 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div class="w-screen lg:w-[55rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-lg font-semibold text-gray-800 dark:text-zinc-100">Crear Rol</h5>
            <button data-modal-close="ModalCrearRol" type="reset" form="permissionForm"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                <i data-lucide="x" class="size-5"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="max-h-[calc(100vh-180px)] p-4 overflow-y-auto">
            <form id="permissionForm" method="POST" action="{{ route('users.createRoles') }}">
                @csrf

                <!-- Nombre del Rol -->
                <div class="mb-5">
                    <label for="roleName" class="block text-sm font-medium text-gray-700 dark:text-zinc-200 mb-1">
                        Nombre del Rol
                    </label>
                    <input type="text" id="roleName" name="name" value="{{ old('name') }}" required
                        placeholder="Escribe el nombre del rol"
                        class="form-input w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 @error('roleName') border-red-500 @enderror">
                    @error('name')
                    <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Permisos -->
                <div class="mb-4">
                    <h5 class="text-md font-semibold text-gray-800 dark:text-zinc-100 mb-2">Listado de Permisos</h5>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Columna 1: Menús -->
                        <div>
                            <h6 class="text-sm font-bold text-custom-500 mb-2">Menús</h6>
                            <div class="space-y-2">
                                @foreach ($menus as $permission)
                                <label for="perm-{{ $permission->id }}" class="flex items-center gap-2">
                                    <input id="perm-{{ $permission->id }}" name="permissions[]" type="checkbox"
                                        value="{{ $permission->id }}"
                                        class="border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-custom-500 checked:border-custom-500 dark:checked:bg-custom-400 dark:checked:border-custom-400"
                                        {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700 dark:text-zinc-200">
                                        {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Columna 2 -->
                        <div>
                            <h6 class="text-sm font-bold text-custom-500 mb-2">Acciones</h6>
                            <div class="space-y-2">
                                @foreach ($accionesCol2 as $permission)
                                <label for="perm-{{ $permission->id }}" class="flex items-center gap-2">
                                    <input id="perm-{{ $permission->id }}" name="permissions[]" type="checkbox"
                                        value="{{ $permission->id }}"
                                        class="border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-custom-500 checked:border-custom-500 dark:checked:bg-custom-400 dark:checked:border-custom-400"
                                        {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700 dark:text-zinc-200">
                                        {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Columna 3 -->
                        <div>
                            <h6 class="text-sm font-bold text-custom-500 mb-2">Más Acciones</h6>
                            <div class="space-y-2">
                                @foreach ($accionesCol3 as $permission)
                                <label for="perm-{{ $permission->id }}" class="flex items-center gap-2">
                                    <input id="perm-{{ $permission->id }}" name="permissions[]" type="checkbox"
                                        value="{{ $permission->id }}"
                                        class="border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-custom-500 checked:border-custom-500 dark:checked:bg-custom-400 dark:checked:border-custom-400"
                                        {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700 dark:text-zinc-200">
                                        {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="flex justify-end items-center gap-2 p-4 border-t border-slate-200 dark:border-zink-500">
            <button type="reset" form="permissionForm"
                class="px-4 py-2 rounded-md text-gray-700 dark:text-zinc-200 bg-white dark:bg-zink-600 border border-slate-300 dark:border-zink-500 hover:bg-gray-100 dark:hover:bg-zink-500 transition"
                data-modal-close="ModalCrearRol">
                Cancelar
            </button>
            <button type="submit" form="permissionForm"
                class="px-4 py-2 rounded-md bg-custom-500 text-white hover:bg-custom-600 transition">
                Crear Rol
            </button>
        </div>
    </div>
</div>

<!-- Modal Editar Rol -->
<div id="ModalEditarRol"
    class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 top-1/2 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div class="w-screen lg:w-[55rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
            <h5 class="text-lg font-semibold text-gray-800 dark:text-zinc-100">Editar Rol</h5>
            <button data-modal-close="ModalEditarRol" type="reset" form="editRoleForm"
                class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500">
                <i data-lucide="x" class="size-5"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="max-h-[calc(100vh-180px)] p-4 overflow-y-auto">
            <form id="editRoleForm" method="POST" action="{{ route('roles.update') }}">
                @csrf
                @method('PUT')

                <!-- Selector de Rol -->
                <div class="mb-5 flex flex-col md:flex-row gap-4">
                    <!-- Selector de Rol -->
                    <div class="flex-1">
                        <label for="selectRole" class="block text-sm font-medium text-gray-700 dark:text-zinc-200 mb-1">
                            Seleccionar Rol a Editar
                        </label>
                        <select id="selectRole" name="role_id" required
                            class="form-select w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                            <option value="">Seleccione un rol</option>
                            @foreach($allRoles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nombre del Rol (se actualizará con JS) -->
                    <div class="flex-1 hidden" id="roleNameContainer">
                        <label for="roleName" class="block text-sm font-medium text-gray-700 dark:text-zinc-200 mb-1">
                            Nuevo Nombre del Rol
                        </label>
                        <input type="text" id="roleName" name="name" placeholder="Escribe el nuevo nombre del rol"
                            class="form-input w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                    </div>
                </div>

                <!-- Permisos (se actualizará con JS) -->
                <div class="mb-4 hidden" id="permissionsContainer">
                    <h5 class="text-md font-semibold text-gray-800 dark:text-zinc-100 mb-2">Listado de Permisos</h5>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Columna 1: Menús -->
                        <div>
                            <h6 class="text-sm font-bold text-custom-500 mb-2">Menús</h6>
                            <div class="space-y-2" id="menusColumn">
                                @foreach ($menus as $permission)
                                <label for="perm-edit-{{ $permission->id }}" class="flex items-center gap-2">
                                    <input id="perm-edit-{{ $permission->id }}" name="permissions[]" type="checkbox"
                                        value="{{ $permission->id }}"
                                        class="permission-checkbox border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-custom-500 checked:border-custom-500 dark:checked:bg-custom-400 dark:checked:border-custom-400">
                                    <span class="text-sm text-gray-700 dark:text-zinc-200">
                                        {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Columna 2: Acciones -->
                        <div>
                            <h6 class="text-sm font-bold text-custom-500 mb-2">Acciones</h6>
                            <div class="space-y-2" id="actionsColumn1">
                                @foreach ($accionesCol2 as $permission)
                                <label for="perm-edit-{{ $permission->id }}" class="flex items-center gap-2">
                                    <input id="perm-edit-{{ $permission->id }}" name="permissions[]" type="checkbox"
                                        value="{{ $permission->id }}"
                                        class="permission-checkbox border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-custom-500 checked:border-custom-500 dark:checked:bg-custom-400 dark:checked:border-custom-400">
                                    <span class="text-sm text-gray-700 dark:text-zinc-200">
                                        {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Columna 3: Más Acciones -->
                        <div>
                            <h6 class="text-sm font-bold text-custom-500 mb-2">Más Acciones</h6>
                            <div class="space-y-2" id="actionsColumn2">
                                @foreach ($accionesCol3 as $permission)
                                <label for="perm-edit-{{ $permission->id }}" class="flex items-center gap-2">
                                    <input id="perm-edit-{{ $permission->id }}" name="permissions[]" type="checkbox"
                                        value="{{ $permission->id }}"
                                        class="permission-checkbox border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-custom-500 checked:border-custom-500 dark:checked:bg-custom-400 dark:checked:border-custom-400">
                                    <span class="text-sm text-gray-700 dark:text-zinc-200">
                                        {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="flex justify-end items-center gap-2 p-4 border-t border-slate-200 dark:border-zink-500">
            <button type="reset" form="editRoleForm"
                class="px-4 py-2 rounded-md text-gray-700 dark:text-zinc-200 bg-white dark:bg-zink-600 border border-slate-300 dark:border-zink-500 hover:bg-gray-100 dark:hover:bg-zink-500 transition"
                data-modal-close="ModalEditarRol">
                Cancelar
            </button>
            <button type="submit" form="editRoleForm" id="updateRoleBtn" disabled
                class="px-4 py-2 rounded-md bg-custom-500 text-white hover:bg-custom-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
                Actualizar Rol
            </button>
        </div>
    </div>
</div>



@endsection