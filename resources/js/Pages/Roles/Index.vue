<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    roles: Array,
    availablePermissions: Array,
});

const isModalOpen = ref(false);
const isEditing = ref(false);

const form = useForm({
    id: null,
    name: '',
    permissions: [],
});

const openCreateModal = () => {
    isEditing.value = false;
    form.reset();
    form.permissions = [];
    isModalOpen.value = true;
};

const openEditModal = (role) => {
    isEditing.value = true;
    form.id = role.id;
    form.name = role.name;
    form.permissions = role.permissions.map(p => p.name);
    isModalOpen.value = true;
};

const translatePermission = (name) => {
    const translations = {
        'access scan': 'Acceso a Escáner',
        'create maintenance': 'Crear Mantenimiento',
        'delete bitacora': 'Eliminar Bitácora',
        'manage backups': 'Gestionar Respaldos',
        'manage billing': 'Gestionar Facturación',
        'manage partida': 'Gestionar Inventario',
        'manage roles': 'Gestionar Roles',
        'manage users': 'Gestionar Usuarios',
        'view billing': 'Ver Facturación',
        'view bitacora': 'Ver Bitácora',
        'view maintenance': 'Ver Mantenimiento',
        'view partida': 'Ver Inventario',
        'view reports': 'Ver Reportes'
    };
    return translations[name] || name;
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('roles.update', form.id), {
            onSuccess: () => isModalOpen.value = false,
        });
    } else {
        form.post(route('roles.store'), {
            onSuccess: () => isModalOpen.value = false,
        });
    }
};

const deleteRole = (id) => {
    if (confirm('¿Estás seguro de eliminar este rol?')) {
        form.delete(route('roles.destroy', id));
    }
};
</script>

<template>
    <AppLayout title="Gestión de Roles">
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center">
                <i class="fa-solid fa-user-shield mr-2 text-indigo-500"></i>Gestión de Roles y Permisos
            </h2>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Toolbar -->
                <div class="mb-8 flex justify-end">
                    <button @click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-indigo-200 dark:shadow-none transition-all transform hover:scale-[1.02] active:scale-95 flex items-center gap-2">
                        <i class="fa-solid fa-plus text-sm"></i>
                        Nuevo Rol
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <div v-for="role in roles" :key="role.id" class="group bg-white dark:bg-gray-800/80 backdrop-blur-md overflow-hidden shadow-2xl rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50 transition-all duration-300 hover:scale-[1.03] hover:shadow-indigo-500/10 flex flex-col">
                        <!-- Card Header with Gradient -->
                        <div class="h-2 w-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                        
                        <div class="p-8 flex-grow">
                            <div class="flex justify-between items-center mb-8">
                                <div class="flex items-center space-x-3">
                                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/40 rounded-2xl text-indigo-600 dark:text-indigo-400 group-hover:rotate-12 transition-transform duration-500">
                                        <i class="fa-solid fa-user-shield text-xl"></i>
                                    </div>
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight uppercase">
                                        {{ role.name }}
                                    </h3>
                                </div>
                                <div class="flex gap-2">
                                    <button v-if="role.name !== 'Superusuario'" @click="openEditModal(role)" class="p-2.5 text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-500/20 rounded-xl transition-all active:scale-90" title="Editar Rol">
                                        <i class="fa-solid fa-pen-to-square text-lg"></i>
                                    </button>
                                    <button v-if="role.name !== 'Superusuario'" @click="deleteRole(role.id)" class="p-2.5 text-rose-500 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-500/20 rounded-xl transition-all active:scale-90" title="Eliminar Rol">
                                        <i class="fa-solid fa-trash-can text-lg"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] flex items-center">
                                        <span class="w-8 h-[1px] bg-gray-200 dark:bg-gray-700 mr-2"></span>
                                        Permisos Asignados
                                    </h4>
                                    <span class="text-[10px] bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full text-gray-500 dark:text-gray-400 font-bold">
                                        {{ role.permissions.length }}
                                    </span>
                                </div>
                                
                                <div class="flex flex-wrap gap-2 min-h-[100px] content-start">
                                    <div v-for="perm in role.permissions" :key="perm.id" 
                                        class="px-3.5 py-1.5 text-[11px] font-extrabold rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-gray-300 border border-gray-100 dark:border-gray-700/50 transition-all hover:border-indigo-300 dark:hover:border-indigo-700 hover:text-indigo-600 dark:hover:text-indigo-400">
                                        {{ translatePermission(perm.name) }}
                                    </div>
                                    <div v-if="role.permissions.length === 0" class="w-full flex flex-col items-center justify-center py-4 bg-gray-50/50 dark:bg-gray-900/20 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                                        <i class="fa-solid fa-lock-open text-gray-300 dark:text-gray-600 mb-2"></i>
                                        <span class="text-xs text-gray-400 italic">Acceso restringido</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer Decoration -->
                        <div class="px-8 pb-4 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                            <div class="h-1 w-full bg-indigo-500/20 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 w-1/3 group-hover:translate-x-[200%] transition-transform duration-1000 ease-in-out"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/75 transition-opacity backdrop-blur-sm" @click="isModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100 dark:border-gray-700">
                    <form @submit.prevent="submit">
                        <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4 sm:p-8 sm:pb-4">
                            <h3 class="text-2xl leading-6 font-black text-gray-900 dark:text-white mb-8 flex items-center">
                                <i class="fa-solid fa-circle-user mr-2 text-indigo-500"></i>
                                {{ isEditing ? 'Editar Rol' : 'Crear Nuevo Rol' }}
                            </h3>
                            
                            <div class="mb-6">
                                <label class="block text-gray-700 dark:text-gray-300 text-xs font-bold mb-2 uppercase tracking-wide">Nombre del Rol</label>
                                <input v-model="form.name" type="text" 
                                    class="block w-full bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl py-3 px-4 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none font-bold" 
                                    :disabled="form.name === 'Superusuario' && isEditing"
                                    placeholder="Ej: Editor, Supervisor...">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 dark:text-gray-300 text-xs font-bold mb-4 uppercase tracking-wide flex items-center">
                                    <i class="fa-solid fa-list-check mr-2 text-indigo-500"></i>Seleccionar Permisos
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-72 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                                    <div v-for="perm in availablePermissions" :key="perm.id" 
                                        class="flex items-center p-2 rounded-lg hover:bg-white dark:hover:bg-gray-800 transition-colors shadow-sm hover:shadow border border-transparent hover:border-gray-100 dark:hover:border-gray-700 group">
                                        <input type="checkbox" :id="'perm-' + perm.id" :value="perm.name" v-model="form.permissions" 
                                            class="h-5 w-5 text-indigo-600 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-indigo-500 transition-all cursor-pointer">
                                        <label :for="'perm-' + perm.id" class="ml-3 block text-sm font-semibold text-gray-700 dark:text-gray-300 cursor-pointer group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                            {{ translatePermission(perm.name) }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-5 sm:px-8 sm:flex sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-xl px-8 py-3 bg-indigo-600 text-base font-black text-white hover:bg-indigo-700 sm:w-auto sm:text-sm transition-all transform hover:scale-[1.02] active:scale-95">
                                <i class="fa-solid fa-floppy-disk mr-2"></i>Guardar Rol
                            </button>
                            <button type="button" @click="isModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-8 py-3 bg-white dark:bg-gray-800 text-base font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto sm:text-sm transition-all">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
