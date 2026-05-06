<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';

const props = defineProps({
    users: Object,
    roles: Array,
    availablePermissions: Array,
});

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isPermissionModalOpen = ref(false);

const deleteModal = reactive({
    isOpen: false,
    userId: null,
    userName: '',
    processing: false
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: '',
});

const updateForm = useForm({
    id: null,
    name: '',
    email: '',
    password: '', // Optional for updates
    role: '',
});

const permissionForm = useForm({
    userId: null,
    permission: '',
    minutes: 60,
});

const openCreateModal = () => {
    form.reset();
    isCreateModalOpen.value = true;
};

const openEditModal = (user) => {
    updateForm.id = user.id;
    updateForm.name = user.name;
    updateForm.email = user.email;
    updateForm.password = '';
    updateForm.role = user.roles[0]?.name || ''; // Assuming single role per user
    isEditModalOpen.value = true;
};

const openPermissionModal = (user) => {
    permissionForm.userId = user.id;
    permissionForm.permission = 'view bitacora'; // Default example
    permissionForm.minutes = 60;
    isPermissionModalOpen.value = true;
};

const openDeleteModal = (user) => {
    deleteModal.userId = user.id;
    deleteModal.userName = user.name;
    deleteModal.isOpen = true;
};

const closeDeleteModal = () => {
    deleteModal.isOpen = false;
    deleteModal.userId = null;
    deleteModal.userName = '';
};

const createUser = () => {
    form.post(route('users.store'), {
        onSuccess: () => isCreateModalOpen.value = false,
    });
};

const updateUser = () => {
    updateForm.put(route('users.update', updateForm.id), {
        onSuccess: () => isEditModalOpen.value = false,
    });
};

const confirmDelete = () => {
    deleteModal.processing = true;
    router.delete(route('users.destroy', deleteModal.userId), {
        onFinish: () => {
            deleteModal.processing = false;
            closeDeleteModal();
        }
    });
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

const assignPermission = () => {
    permissionForm.post(route('users.temp_permission', permissionForm.userId), {
        onSuccess: () => isPermissionModalOpen.value = false,
    });
};
</script>

<template>
    <AppLayout title="Gestión de Usuarios">
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center transition-colors">
                <i class="fa-solid fa-users-gear mr-3 text-indigo-500"></i>Gestión de Usuarios
            </h2>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Toolbar -->
                <div class="mb-8 flex justify-end">
                    <button @click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-indigo-200 dark:shadow-none transition-all transform hover:scale-[1.02] active:scale-95 flex items-center gap-2">
                        <i class="fa-solid fa-plus text-sm"></i>
                        Nuevo Usuario
                    </button>
                </div>
                
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50 overflow-hidden backdrop-blur-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50/50 dark:bg-gray-700/30">
                                <tr>
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">
                                        <i class="fa-solid fa-user mr-2 text-indigo-500"></i>Identidad
                                    </th>
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">
                                        <i class="fa-solid fa-envelope mr-2 text-indigo-400"></i>Contacto
                                    </th>
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">
                                        <i class="fa-solid fa-shield-halved mr-2 text-emerald-400"></i>Rol & Nivel
                                    </th>
                                    <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                <tr v-for="user in users.data" :key="user.id" class="group hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-all duration-300">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mr-4 font-black text-lg border border-indigo-200/50 dark:border-indigo-700/30 group-hover:scale-110 transition-transform">
                                                {{ user.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div class="text-sm font-bold text-gray-800 dark:text-white">{{ user.name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                            <span class="hover:text-indigo-500 transition-colors">{{ user.email }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex gap-2">
                                            <span v-for="role in user.roles" :key="role.id" class="px-3 py-1 inline-flex text-[10px] leading-5 font-black rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800/50 uppercase tracking-widest">
                                                {{ role.name.toUpperCase() }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="openEditModal(user)" class="h-9 w-9 flex items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 transition-all transform active:scale-90" title="Editar Usuario">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button @click="openPermissionModal(user)" class="h-9 w-9 flex items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 hover:bg-amber-500 hover:text-white dark:hover:bg-amber-500 transition-all transform active:scale-90" title="Permiso Temporal">
                                                <i class="fa-solid fa-key"></i>
                                            </button>
                                            <button @click="openDeleteModal(user)" class="h-9 w-9 flex items-center justify-center rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 hover:bg-rose-500 hover:text-white dark:hover:bg-rose-500 transition-all transform active:scale-90" title="Eliminar Usuario">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal (Premium Redesign) -->
        <div v-if="isCreateModalOpen || isEditModalOpen" class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:py-12" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen">
                <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-md" @click="isCreateModalOpen = false; isEditModalOpen = false"></div>
                
                <div class="relative bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl transform transition-all sm:max-w-lg w-full overflow-hidden border border-gray-100 dark:border-gray-700">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                    
                    <form @submit.prevent="isCreateModalOpen ? createUser() : updateUser()">
                        <div class="p-8 sm:p-10">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="h-14 w-14 rounded-2xl bg-indigo-50 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-500 text-2xl shadow-inner">
                                    <i :class="isCreateModalOpen ? 'fa-solid fa-user-plus' : 'fa-solid fa-user-pen'"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tight">
                                        {{ isCreateModalOpen ? 'Crear Usuario' : 'Editar Usuario' }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest mt-1">
                                        {{ isCreateModalOpen ? 'Registrar nuevo acceso' : 'Actualizar información' }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Nombre Completo</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-signature absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                        <input v-if="isCreateModalOpen" v-model="form.name" type="text" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold outline-none" placeholder="Juan Pérez">
                                        <input v-else v-model="updateForm.name" type="text" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold outline-none" placeholder="Juan Pérez">
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Correo Electrónico</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-at absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                        <input v-if="isCreateModalOpen" v-model="form.email" type="email" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold outline-none" placeholder="juan@ejemplo.com">
                                        <input v-else v-model="updateForm.email" type="email" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold outline-none" placeholder="juan@ejemplo.com">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Contraseña {{ !isCreateModalOpen ? '(Opcional)' : '' }}</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                        <input v-if="isCreateModalOpen" v-model="form.password" type="password" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold outline-none" placeholder="••••••••">
                                        <input v-else v-model="updateForm.password" type="password" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold outline-none" placeholder="••••••••">
                                    </div>
                                    <span v-if="isCreateModalOpen && form.errors.password" class="text-rose-500 text-[10px] font-bold mt-1 px-1 uppercase tracking-wider">{{ form.errors.password }}</span>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Rol de Acceso</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-briefcase absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                        <select v-if="isCreateModalOpen" v-model="form.role" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold outline-none appearance-none">
                                            <option value="" disabled>Seleccione un rol</option>
                                            <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name.toUpperCase() }}</option>
                                        </select>
                                        <select v-else v-model="updateForm.role" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold outline-none appearance-none">
                                            <option value="" disabled>Seleccione un rol</option>
                                            <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name.toUpperCase() }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/30 px-8 py-6 flex flex-col sm:flex-row-reverse gap-4">
                            <button type="submit" :disabled="form.processing || updateForm.processing" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 px-8 rounded-2xl shadow-xl shadow-indigo-500/20 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                                <i v-if="form.processing || updateForm.processing" class="fa-solid fa-circle-notch fa-spin"></i>
                                <i v-else class="fa-solid fa-floppy-disk text-lg"></i>
                                <span>{{ isCreateModalOpen ? 'CREAR USUARIO' : 'GUARDAR CAMBIOS' }}</span>
                            </button>
                            <button type="button" @click="isCreateModalOpen = false; isEditModalOpen = false" class="w-full bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-bold py-4 px-8 rounded-2xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 transition-all transform active:scale-95">
                                CANCELAR
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Permission Modal (Premium Redesign) -->
        <div v-if="isPermissionModalOpen" class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:py-12" aria-labelledby="modal-title" role="dialog" aria-modal="true">
             <div class="flex items-center justify-center min-h-screen">
                <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-md" @click="isPermissionModalOpen = false"></div>
                
                <div class="relative bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl transform transition-all sm:max-w-md w-full overflow-hidden border border-gray-100 dark:border-gray-700">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-amber-500 to-orange-500"></div>
                    
                    <form @submit.prevent="assignPermission">
                        <div class="p-8 sm:p-10">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="h-14 w-14 rounded-2xl bg-amber-50 dark:bg-amber-900/40 flex items-center justify-center text-amber-500 text-2xl shadow-inner">
                                    <i class="fa-solid fa-key"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Acceso Temporal</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest mt-1">Conceder permiso extra</p>
                                </div>
                            </div>
                            
                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Elegir Permiso</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-shield-halved absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-amber-500 transition-colors"></i>
                                        <select v-model="permissionForm.permission" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-amber-500 transition-all font-bold outline-none appearance-none">
                                            <option v-for="perm in availablePermissions" :key="perm.id" :value="perm.name">{{ translatePermission(perm.name).toUpperCase() }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Duración (Minutos)</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-amber-500 transition-colors"></i>
                                        <input v-model="permissionForm.minutes" type="number" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-amber-500 transition-all font-bold outline-none" min="1">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/30 px-8 py-6 flex flex-col gap-3">
                            <button type="submit" :disabled="permissionForm.processing" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-black py-4 px-8 rounded-2xl shadow-xl shadow-amber-500/20 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                                <i v-if="permissionForm.processing" class="fa-solid fa-circle-notch fa-spin"></i>
                                <i v-else class="fa-solid fa-clock-rotate-left text-lg"></i>
                                <span>CONCEDER ACCESO</span>
                            </button>
                            <button type="button" @click="isPermissionModalOpen = false" class="w-full bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-bold py-4 px-8 rounded-2xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 transition-all">
                                CANCELAR
                            </button>
                        </div>
                    </form>
                </div>
             </div>
        </div>

        <!-- Premium Delete Confirmation Modal -->
        <div v-if="deleteModal.isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 min-h-screen">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-md transition-opacity" @click="closeDeleteModal"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-[2.5rem] max-w-sm w-full shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700 transform transition-all animate-in zoom-in-95 duration-200">
                <div class="p-8 text-center">
                    <div class="h-20 w-20 bg-rose-50 dark:bg-rose-900/30 rounded-full flex items-center justify-center mx-auto mb-6 text-rose-500 border-4 border-rose-50 dark:border-rose-900/10 scale-110">
                        <i class="fa-solid fa-user-xmark text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight mb-2">¿Eliminar Usuario?</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-8 font-medium">Estás a punto de eliminar a <span class="text-rose-500 font-black">{{ deleteModal.userName }}</span>. Esta acción no se puede deshacer.</p>
                    
                    <div class="flex flex-col gap-3">
                        <button 
                            @click="confirmDelete" 
                            :disabled="deleteModal.processing"
                            class="w-full bg-rose-500 hover:bg-rose-600 text-white font-black py-4 rounded-2xl shadow-lg shadow-rose-500/20 transition-all transform active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2"
                        >
                            <i v-if="deleteModal.processing" class="fa-solid fa-circle-notch fa-spin"></i>
                            <span>{{ deleteModal.processing ? 'ELIMINANDO...' : 'SÍ, ELIMINAR AHORA' }}</span>
                        </button>
                        <button 
                            @click="closeDeleteModal" 
                            class="w-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold py-4 rounded-2xl transition-all"
                        >
                            CANCELAR
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
