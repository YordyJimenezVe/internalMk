<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, reactive, computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const isFacturacion = computed(() => {
    const rol = (user.value?.rol || '').toLowerCase();
    return rol.includes('fact') || (user.value?.roles || []).some(r => (r?.name || '').toLowerCase().includes('fact'));
});
const isAdminOrSuper = computed(() => {
    const rol = (user.value?.rol || '').toLowerCase();
    return rol.includes('admin') || rol.includes('super') || (user.value?.roles || []).some(r => ['superusuario', 'administrador'].includes((r?.name || '').toLowerCase()));
});

const props = defineProps({
    requests: Array,
});

const isProcessing = ref(false);

const deleteModal = reactive({
    isOpen: false,
    requestId: null,
    itemName: '',
    processing: false
});

const editingRequest = ref(null);
const editForm = useForm({
    quantity: 1,
    price: 0,
    client_name: '',
    client_cedula: '',
    client_phone: '',
    client_address: '',
    client_email: '',
    observation: '',
});

const startEdit = (req) => {
    editingRequest.value = req;
    editForm.quantity = req.quantity;
    editForm.price = req.price;
    editForm.client_name = req.client_name || '';
    editForm.client_cedula = req.client_cedula || '';
    editForm.client_phone = req.client_phone || '';
    editForm.client_address = req.client_address || '';
    editForm.client_email = req.client_email || '';
    editForm.observation = req.observation || '';
};

const cancelEdit = () => {
    editingRequest.value = null;
    editForm.reset();
};

const updateRequest = () => {
    editForm.put(route('billing.requests.update', editingRequest.value.id), {
        onSuccess: () => cancelEdit(),
    });
};

const openDeleteModal = (req) => {
    deleteModal.requestId = req.id;
    const item = req.inventario || req.partida;
    deleteModal.itemName = item ? `${item.marca} ${item.modelo}` : 'Producto Desconocido';
    deleteModal.isOpen = true;
};

const closeDeleteModal = () => {
    deleteModal.isOpen = false;
    deleteModal.requestId = null;
};

const confirmDelete = () => {
    deleteModal.processing = true;
    router.delete(route('billing.requests.destroy', deleteModal.requestId), {
        onFinish: () => {
            deleteModal.processing = false;
            closeDeleteModal();
        }
    });
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' }).toUpperCase();
};

const formatTime = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
};

const goToCreateBilling = (id, requestId) => {
    isProcessing.value = true;
    router.visit(route('createBilling', { id, request_id: requestId }), {
        onFinish: () => isProcessing.value = false
    });
};
</script>

<template>
    <AppLayout title="Solicitudes de Facturación">
        <!-- Loading Overlay -->
        <div v-if="isProcessing" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-md transition-all duration-500">
            <div class="bg-white dark:bg-gray-800 p-10 rounded-[3rem] shadow-2xl border border-gray-100 dark:border-gray-700 flex flex-col items-center gap-6 animate-in zoom-in-95">
                <div class="relative h-24 w-24">
                    <div class="absolute inset-0 rounded-full border-4 border-indigo-100 dark:border-indigo-900/30"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-indigo-500 border-t-transparent animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center text-indigo-500 text-3xl">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                </div>
                <div class="text-center">
                    <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Procesando Solicitud</h3>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1 italic">Generando documentos...</p>
                </div>
            </div>
        </div>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center transition-colors">
                    <i class="fa-solid fa-file-invoice-dollar mr-3 text-indigo-500"></i>Solicitudes de Facturación
                </h2>
                <div class="flex items-center gap-3">
                    <span class="px-4 py-2 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-xl text-[10px] font-black uppercase tracking-widest border border-amber-100 dark:border-amber-800/30 shadow-sm">
                        {{ requests.length }} PENDIENTES
                    </span>
                </div>
            </div>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-all duration-300">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div v-if="requests.length === 0" class="flex flex-col items-center justify-center p-20 bg-white dark:bg-gray-800 rounded-[3rem] shadow-xl border border-gray-100 dark:border-gray-700">
                    <div class="h-24 w-24 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center text-gray-300 dark:text-gray-600 mb-6 group hover:scale-110 transition-transform">
                        <i class="fa-solid fa-folder-open text-4xl"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 font-black uppercase tracking-[0.2em] text-sm">No hay solicitudes pendientes</p>
                    <p class="text-xs text-gray-400 mt-2 font-medium">Todo está al día por ahora.</p>
                </div>

                <div v-else class="bg-white dark:bg-gray-800 shadow-2xl rounded-[3rem] border border-gray-100 dark:border-gray-700/50 overflow-hidden backdrop-blur-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50/50 dark:bg-gray-700/30">
                                <tr>
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] w-32">
                                        <i class="fa-solid fa-calendar mr-2 text-indigo-500"></i>Registro
                                    </th>
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">
                                        <i class="fa-solid fa-box mr-2 text-indigo-400"></i>Producto / Partida
                                    </th>
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">
                                        <i class="fa-solid fa-user mr-2 text-emerald-400"></i>Cliente
                                    </th>
                                    <th v-if="!isFacturacion" class="px-8 py-5 text-center text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">
                                        Finanzas
                                    </th>
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">
                                        <i class="fa-solid fa-id-badge mr-2 text-amber-500"></i>Asesor
                                    </th>
                                    <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                <tr v-for="req in requests" :key="req.id" class="group hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-all duration-300">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-gray-800 dark:text-gray-200">{{ formatDate(req.created_at) }}</span>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ formatTime(req.created_at) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div v-if="req.inventario || req.partida" class="flex items-center">
                                            <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mr-4 font-black transition-transform group-hover:scale-110 border border-indigo-100/50 dark:border-indigo-700/30 shadow-inner">
                                                {{ (req.inventario || req.partida)?.codInv ? (req.inventario || req.partida).codInv.split('-')[0] : 'MK' }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black text-gray-800 dark:text-white uppercase leading-tight">{{ (req.inventario || req.partida)?.marca }} {{ (req.inventario || req.partida)?.modelo }}</span>
                                                <span class="text-[10px] font-bold text-gray-400 mt-0.5">ID: {{ (req.inventario || req.partida)?.codInv }} | {{ (req.inventario || req.partida)?.año }}</span>
                                            </div>
                                        </div>
                                        <div v-else class="flex flex-col">
                                            <span class="text-sm font-black text-rose-500 uppercase leading-tight italic">Producto Eliminado</span>
                                            <span class="text-[10px] font-bold text-gray-400 mt-0.5 italic">ID: N/A</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ req.client_name || 'COBRADOR PENDIENTE' }}</span>
                                            <span v-if="req.client_cedula" class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">
                                                <i class="fa-solid fa-id-card text-[8px] mr-1 text-indigo-400"></i>{{ req.client_cedula }}
                                            </span>
                                            <span v-if="req.client_phone" class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">
                                                <i class="fa-solid fa-phone text-[8px] mr-1 text-emerald-400"></i>{{ req.client_phone }}
                                            </span>
                                            <span v-if="req.client_email" class="text-[10px] text-indigo-500 dark:text-indigo-400 lowercase truncate max-w-[180px]" :title="req.client_email">
                                                <i class="fa-solid fa-envelope text-[8px] mr-1 text-indigo-400"></i>{{ req.client_email }}
                                            </span>
                                            <span v-if="req.client_address" class="text-[9px] text-gray-400 dark:text-gray-500 max-w-[200px] truncate" :title="req.client_address">
                                                <i class="fa-solid fa-location-dot text-[8px] mr-1 text-rose-400"></i>{{ req.client_address }}
                                            </span>
                                            <span v-if="req.observation" class="mt-1 text-[9px] bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 font-bold px-2 py-0.5 rounded-md border border-amber-100 dark:border-amber-800/30 uppercase tracking-wide inline-block max-w-[200px] truncate" :title="req.observation">
                                                <i class="fa-solid fa-comment-dots mr-1 text-amber-500"></i>{{ req.observation }}
                                            </span>
                                        </div>
                                    </td>
                                    <td v-if="!isFacturacion" class="px-8 py-6 whitespace-nowrap text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-xs font-black text-indigo-600 dark:text-indigo-400">${{ parseFloat(req.price).toFixed(2) }}</span>
                                            <div class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 rounded-full border border-indigo-100 dark:border-indigo-800/30 mt-1">
                                                <span class="text-[9px] font-black text-indigo-500 uppercase tracking-widest">{{ req.quantity }} UND</span>
                                            </div>
                                            <span class="text-[11px] font-black text-gray-800 dark:text-white mt-1 border-t border-gray-100 dark:border-gray-700 pt-1 w-20">Total: ${{ (req.price * req.quantity).toFixed(2) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <span class="text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest bg-amber-50 dark:bg-amber-900/20 px-3 py-1.5 rounded-lg border border-amber-100/50 dark:border-amber-800/30">
                                            {{ req.user?.name || 'Sistema' }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button 
                                                v-if="req.inventario || req.partida"
                                                @click="goToCreateBilling((req.inventario || req.partida).id, req.id)" 
                                                class="h-9 w-9 flex items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-500 transition-all transform active:scale-90 shadow-sm"
                                                title="Facturar"
                                            >
                                                <i class="fa-solid fa-file-circle-check"></i>
                                            </button>
                                            <button 
                                                @click="startEdit(req)" 
                                                class="h-9 w-9 flex items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-500 transition-all transform active:scale-90 shadow-sm"
                                                title="Ajustar Solicitud"
                                            >
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button 
                                                @click="openDeleteModal(req)" 
                                                class="h-9 w-9 flex items-center justify-center rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white dark:hover:bg-rose-500 transition-all transform active:scale-90 shadow-sm"
                                                title="Rechazar / Eliminar"
                                            >
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

        <!-- Premium Edit Modal -->
        <div v-if="editingRequest" class="fixed inset-0 z-50 overflow-y-auto px-4 py-12" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen">
                <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-md" @click="cancelEdit"></div>
                
                <div class="relative bg-white dark:bg-gray-800 rounded-[3rem] shadow-2xl transform transition-all sm:max-w-lg w-full overflow-hidden border border-gray-100 dark:border-gray-700">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                    
                    <form @submit.prevent="updateRequest">
                        <div class="p-8 sm:p-10">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="h-14 w-14 rounded-2xl bg-blue-50 dark:bg-blue-900/40 flex items-center justify-center text-blue-500 text-2xl shadow-inner border border-blue-100/50">
                                    <i class="fa-solid fa-edit"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Ajustar Solicitud</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest mt-1">Modificar detalles previos a factura</p>
                                </div>
                            </div>
                            
                            <div class="space-y-6">
                                <div class="grid gap-4" :class="isFacturacion ? 'grid-cols-1' : 'grid-cols-2'">
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Cantidad</label>
                                        <div class="relative group">
                                            <i class="fa-solid fa-hashtag absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                                            <input v-model="editForm.quantity" type="number" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold outline-none" min="1">
                                        </div>
                                    </div>
                                    <div v-if="!isFacturacion" class="space-y-2">
                                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Precio Unitario ($)</label>
                                        <div class="relative group">
                                            <i class="fa-solid fa-dollar-sign absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                                            <input v-model="editForm.price" type="number" step="0.01" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold outline-none">
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Nombre del Cliente</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                                        <input v-model="editForm.client_name" type="text" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold outline-none" placeholder="Nombre completo">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Cédula / Documento</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-address-card absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                                        <input v-model="editForm.client_cedula" type="text" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold outline-none" placeholder="V-00.000.000">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Teléfono</label>
                                        <div class="relative group">
                                            <i class="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                                            <input v-model="editForm.client_phone" type="text" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold outline-none" placeholder="0412-1234567">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Correo Electrónico</label>
                                        <div class="relative group">
                                            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                                            <input v-model="editForm.client_email" type="email" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold outline-none" placeholder="cliente@correo.com">
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Dirección</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-location-dot absolute left-4 top-[18px] text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                                        <textarea v-model="editForm.client_address" rows="2" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-[14px] focus:ring-2 focus:ring-blue-500 transition-all font-bold outline-none resize-none" placeholder="Dirección completa del cliente"></textarea>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Observación</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-comment-dots absolute left-4 top-[18px] text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                                        <textarea v-model="editForm.observation" @input="editForm.observation = editForm.observation.toUpperCase()" rows="2" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-[14px] focus:ring-2 focus:ring-blue-500 transition-all font-bold outline-none resize-none uppercase" placeholder="Ej: Motor para Captiva, etc."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/30 px-8 py-6 flex flex-col sm:flex-row-reverse gap-4">
                            <button type="submit" :disabled="editForm.processing" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 px-8 rounded-2xl shadow-xl shadow-blue-500/20 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                                <i v-if="editForm.processing" class="fa-solid fa-circle-notch fa-spin"></i>
                                <i v-else class="fa-solid fa-check-double text-lg"></i>
                                <span>GUARDAR AJUSTES</span>
                            </button>
                            <button type="button" @click="cancelEdit" class="w-full bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-bold py-4 px-8 rounded-2xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 transition-all">
                                CANCELAR
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Premium Delete Modal -->
        <div v-if="deleteModal.isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-md transition-opacity" @click="closeDeleteModal"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-[3rem] max-w-sm w-full shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700 transform transition-all animate-in zoom-in-95 duration-200">
                <div class="p-10 text-center">
                    <div class="h-24 w-24 bg-rose-50 dark:bg-rose-900/30 rounded-full flex items-center justify-center mx-auto mb-8 text-rose-500 border-4 border-rose-50 dark:border-rose-900/10 scale-110 shadow-inner">
                        <i class="fa-solid fa-file-circle-xmark text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight mb-3">¿Rechazar Solicitud?</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-10 font-medium px-4">Esta acción eliminará la solicitud de <span class="text-rose-500 font-black">{{ deleteModal.itemName }}</span> permanentemente.</p>
                    
                    <div class="flex flex-col gap-3">
                        <button 
                            @click="confirmDelete" 
                            :disabled="deleteModal.processing"
                            class="w-full bg-rose-500 hover:bg-rose-600 text-white font-black py-5 rounded-3xl shadow-lg shadow-rose-500/20 transition-all transform active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2"
                        >
                            <i v-if="deleteModal.processing" class="fa-solid fa-circle-notch fa-spin"></i>
                            <span>{{ deleteModal.processing ? 'ELIMINANDO...' : 'SÍ, RECHAZAR' }}</span>
                        </button>
                        <button 
                            @click="closeDeleteModal" 
                            class="w-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold py-5 rounded-3xl transition-all"
                        >
                            CANCELAR
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
