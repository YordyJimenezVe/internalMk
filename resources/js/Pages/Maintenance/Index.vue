<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { fab } from '@fortawesome/free-brands-svg-icons';
import { router, Link, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';

library.add(fas, fab);

const props = defineProps({
  maintenances: Object,
  filters: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const isSuperUser = computed(() => {
    const roles = user.value.roles || [];
    const directRol = user.value.rol || '';
    const superRoles = ['Superusuario', 'SUPERUSUARIO', 'Administrador', 'ADMINISTRADOR'];
    return superRoles.includes(directRol) || roles.some(r => superRoles.includes(r.name));
});

const deleteModal = reactive({
    isOpen: false,
    maintenanceId: null,
    processing: false
});

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'partida.codInv', label: 'Cód. Inventario', sortable: true },
    { key: 'tipo', label: 'Tipo Mantenimiento', sortable: true },
    { key: 'partida.tipo', label: 'Categoría', sortable: true },
    { key: 'partida.marca', label: 'Marca', sortable: true },
    { key: 'partida.modelo', label: 'Modelo', sortable: true },
    { key: 'costo', label: 'Costo ($)', sortable: true },
    { key: 'status', label: 'Estado', sortable: true },
];

const openDeleteModal = (id) => {
    deleteModal.maintenanceId = id;
    deleteModal.isOpen = true;
};

const closeDeleteModal = () => {
    deleteModal.isOpen = false;
    deleteModal.maintenanceId = null;
};

const confirmDelete = () => {
    deleteModal.processing = true;
    router.delete(route('deleteMaintenance', deleteModal.maintenanceId), {
        onFinish: () => {
            deleteModal.processing = false;
            closeDeleteModal();
        }
    });
};

const editMaintenance = id => {
    router.visit(route('editMaintenance', { id }));
};

const showMaintenance = id => {
    router.visit(route('maintenance.show', { id }));
};

const filterByStatus = (status) => {
    router.get(route('maintenance'), { ...props.filters, status: status }, { preserveState: true });
};

const addMaintenance = () => {
    router.visit(route('createMaintenance'));
};

const getBrandSlug = (brand) => {
    if (!brand) return null;
    const b = brand.toLowerCase().trim();
    
    const slugMap = {
        'toyota': 'toyota',
        'totota': 'toyota',
        'hyundai': 'hyundai',
        'ford': 'ford',
        'nissan': 'nissan',
        'nissn': 'nissan',
        'nissa': 'nissan',
        'honda': 'honda',
        'chevrolet': 'chevrolet',
        'kia': 'kia',
        'mitsubishi': 'mitsubishi',
        'jeep': 'jeep',
        'jeepp': 'jeep',
        'volkswagen': 'volkswagen',
        'dodge': 'ram',
        'dosge': 'ram',
        'mazda': 'mazda',
        'suzuki': 'suzuki',
        'mercedes': 'mercedesbenz',
        'bmw': 'bmw',
        'fiat': 'fiat',
        'isuzu': 'isuzu',
        'hino': 'hino',
        'jmc': 'jmc',
        'lexus': 'lexus',
        'audi': 'audi',
        'volvo': 'volvo',
        'chrysler': 'chrysler',
        'renault': 'renault',
        'peugeot': 'peugeot',
        'cummins': 'cummins',
        'cumins': 'cummins',
    };

    for (const key in slugMap) {
        if (b.includes(key)) return slugMap[key];
    }
    
    return null;
};

const imageAttempts = ref({});

const getBrandIcon = (brand, id) => {
    const slug = getBrandSlug(brand);
    if (!slug) return null;
    
    if (slug === 'cummins') {
        return '/cummins-logo.svg';
    }
    
    const urls = [
        `https://cdn.simpleicons.org/${slug}/9ca3af`,
        `https://vl.imgix.net/img/${slug}-logo.png`,
        `https://logo.clearbit.com/${slug}.com`
    ];
    
    const attempt = imageAttempts.value[id] || 0;
    return attempt < urls.length ? urls[attempt] : null;
};const handleImageError = (id) => {
    imageAttempts.value[id] = (imageAttempts.value[id] || 0) + 1;
};

const getStatusLabel = (status) => {
    const statusMap = {
        'EN ESPERA': 'RECIBIDO',
        'EN PROCESO': 'ARMANDO',
        'TERMINADO': 'TERMINADO',
        'CANCELADO': 'CANCELADO',
    };
    return statusMap[status] || status;
};
</script>

<template>
    <AppLayout title="Mantenimiento">
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center transition-colors">
                <i class="fa-solid fa-screwdriver-wrench mr-3 text-indigo-500"></i>Gestión de Mantenimientos
            </h2>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Primary Action Toolbar -->
                <div class="mb-6 flex justify-end">
                    <button 
                        @click="addMaintenance" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-[2rem] font-black shadow-xl shadow-indigo-500/20 transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3 group border-b-4 border-indigo-800"
                    >
                        <i class="fa-solid fa-plus text-xl group-hover:rotate-90 transition-transform duration-300"></i>
                        <span class="tracking-widest uppercase text-sm">NUEVO TICKET</span>
                    </button>
                </div>
                
                <!-- Advanced Toolbar -->
                <div class="mb-8 grid grid-cols-1 lg:grid-cols-12 gap-6 bg-white dark:bg-gray-800 p-6 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700/50 backdrop-blur-sm">
                    
                    <!-- Scanner Section -->
                    <div class="lg:col-span-4 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-2">ESCÁNER DE CÓDIGO</label>
                        <div class="relative group">
                            <i class="fa-solid fa-barcode absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 transition-colors text-lg"></i>
                            <input 
                                type="text" 
                                placeholder="Escanear motor o caja..." 
                                class="w-full pl-14 pr-4 py-4 bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all font-bold shadow-inner outline-none placeholder-gray-400 shrink-0"
                                @keyup.enter="(e) => {
                                    const code = e.target.value.trim();
                                    if (code) {
                                        router.post(route('scan.process'), { code: code, redirect_to: 'maintenance' });
                                        e.target.value = '';
                                    }
                                }"
                            >
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="lg:col-span-8 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-2">FILTRAR POR ESTADO</label>
                        <div class="flex flex-wrap gap-3 bg-gray-50 dark:bg-gray-900/50 p-1.5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-inner">
                            <button 
                                @click="filterByStatus(null)" 
                                :class="[!props.filters.status ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow-md transform scale-[1.02]' : 'bg-transparent text-gray-500 dark:text-gray-400 hover:text-indigo-500']"
                                class="flex-1 px-4 py-3 rounded-[1.2rem] text-xs font-black uppercase tracking-widest transition-all duration-300"
                            >
                                TODOS
                            </button>
                            <button 
                                @click="filterByStatus('EN ESPERA')" 
                                :class="[props.filters.status === 'EN ESPERA' ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow-md transform scale-[1.02]' : 'bg-transparent text-gray-500 dark:text-gray-400 hover:text-indigo-500']"
                                class="flex-1 px-4 py-3 rounded-[1.2rem] text-xs font-black uppercase tracking-widest transition-all duration-300"
                            >
                                <i class="fa-solid fa-hourglass-start mr-2 opacity-50"></i>RECIBIDO
                            </button>
                            <button 
                                @click="filterByStatus('EN PROCESO')" 
                                :class="[props.filters.status === 'EN PROCESO' ? 'bg-white dark:bg-gray-800 text-amber-500 dark:text-amber-400 shadow-md transform scale-[1.02]' : 'bg-transparent text-gray-500 dark:text-gray-400 hover:text-amber-500']"
                                class="flex-1 px-4 py-3 rounded-[1.2rem] text-xs font-black uppercase tracking-widest transition-all duration-300"
                            >
                                <i class="fa-solid fa-spinner fa-spin-pulse mr-2 opacity-50"></i>ARMANDO
                            </button>
                            <button 
                                @click="filterByStatus('TERMINADO')" 
                                :class="[props.filters.status === 'TERMINADO' ? 'bg-white dark:bg-gray-800 text-indigo-500 dark:text-indigo-400 shadow-md transform scale-[1.02]' : 'bg-transparent text-gray-500 dark:text-gray-400 hover:text-indigo-500']"
                                class="flex-1 px-4 py-3 rounded-[1.2rem] text-xs font-black uppercase tracking-widest transition-all duration-300"
                            >
                                <i class="fa-solid fa-flag-checkered mr-2 opacity-50"></i>TERMINADO
                            </button>
                            <button 
                                @click="filterByStatus('CANCELADO')" 
                                :class="[props.filters.status === 'CANCELADO' ? 'bg-white dark:bg-gray-800 text-rose-500 dark:text-rose-450 shadow-md transform scale-[1.02]' : 'bg-transparent text-gray-500 dark:text-gray-400 hover:text-rose-500']"
                                class="flex-1 px-4 py-3 rounded-[1.2rem] text-xs font-black uppercase tracking-widest transition-all duration-300"
                            >
                                <i class="fa-solid fa-ban mr-2 opacity-50"></i>CANCELADO
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Premium DataTable -->
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50 overflow-hidden backdrop-blur-sm">
                    <DataTable 
                        :rows="maintenances" 
                        :columns="columns" 
                        :filters="filters"
                        routeName="maintenance"
                        title="REGISTROS DE TALLER"
                        exportType="maintenance"
                        class="p-4"
                    >
                        <!-- Custom Cells -->

                        <template #cell-costo="{ row }">
                            <span class="text-sm font-black text-indigo-600 dark:text-indigo-400">$ {{ row.costo || '0.00' }}</span>
                        </template>

                        <template #cell-status="{ row }">
                            <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border transition-all flex items-center justify-center w-fit gap-1.5 mx-auto"
                                :class="{
                                    'bg-indigo-50 text-indigo-700 border-indigo-100 dark:bg-indigo-900/20 dark:text-indigo-400 dark:border-indigo-800/50': row.status === 'TERMINADO',
                                    'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/50': row.status === 'EN PROCESO',
                                    'bg-rose-50 text-rose-700 border-rose-100 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-800/50': row.status === 'CANCELADO' || row.status === 'NO SE PUDO CONTINUAR',
                                    'bg-gray-50 text-gray-700 border-gray-100 dark:bg-gray-900/40 dark:text-gray-400 dark:border-gray-800/50': row.status === 'EN ESPERA'
                                }"
                            >
                                <i v-if="row.status === 'EN PROCESO'" class="fa-solid fa-spinner fa-spin-pulse"></i>
                                <i v-else-if="row.status === 'TERMINADO'" class="fa-solid fa-flag-checkered"></i>
                                <i v-else-if="row.status === 'CANCELADO'" class="fa-solid fa-ban"></i>
                                <i v-else-if="row.status === 'EN ESPERA'" class="fa-solid fa-hourglass-start"></i>
                                <span>{{ getStatusLabel(row.status) }}</span>
                            </span>span>
                        </template>

                        <template #cell-partida.tipo="{ row }">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-tight">{{ row.partida ? row.partida.tipo : 'N/A' }}</span>
                        </template>

                        <template #cell-partida.marca="{ row }">
                           <div class="flex items-center gap-3">
                               <div class="h-9 w-9 flex items-center justify-center bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-100 dark:border-gray-700 shrink-0 overflow-hidden p-1.5">
                                   <img v-if="getBrandIcon(row.partida?.marca, row.id)" :src="getBrandIcon(row.partida?.marca, row.id)" @error="handleImageError(row.id)" class="w-full h-full object-contain opacity-80" :alt="row.partida?.marca" />
                                   <FontAwesomeIcon v-else :icon="['fas', 'car']" class="text-gray-400 dark:text-gray-500 text-base" />
                               </div>
                               <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ row.partida ? row.partida.marca : 'N/A' }}</span>
                           </div>
                        </template>

                        <template #cell-partida.modelo="{ row }">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ row.partida ? row.partida.modelo : 'N/A' }}</span>
                        </template>

                        <!-- Actions (Premium Redesign) -->
                        <template #actions="{ row }">
                            <div class="flex justify-end gap-2">
                                <button @click="showMaintenance(row.id)" class="h-8 w-8 flex items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 hover:bg-gray-800 hover:text-white dark:hover:bg-gray-600 transition-all transform active:scale-90" title="Ver Detalles">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>

                                <template v-if="row.status !== 'TERMINADO' || isSuperUser">
                                    <button @click="editMaintenance(row.id)" class="h-8 w-8 flex items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 transition-all transform active:scale-90" title="Editar">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    <button @click="openDeleteModal(row.id)" class="h-8 w-8 flex items-center justify-center rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white dark:hover:bg-rose-500 transition-all transform active:scale-90" title="Eliminar">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- Custom Delete Modal -->
        <div v-if="deleteModal.isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-md transition-opacity" @click="closeDeleteModal"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-[2.5rem] max-w-sm w-full shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700 transform transition-all animate-in zoom-in-95 duration-200">
                <div class="p-8 text-center">
                    <div class="h-20 w-20 bg-rose-50 dark:bg-rose-900/30 rounded-full flex items-center justify-center mx-auto mb-6 text-rose-500 border-4 border-rose-50 dark:border-rose-900/10 scale-110">
                        <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight mb-2">¿Borrar Ticket?</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-8 font-medium">Esta acción eliminará el registro de mantenimiento permanentemente del sistema.</p>
                    
                    <div class="flex flex-col gap-3">
                        <button 
                            @click="confirmDelete" 
                            :disabled="deleteModal.processing"
                            class="w-full bg-rose-500 hover:bg-rose-600 text-white font-black py-4 rounded-2xl shadow-lg shadow-rose-500/20 transition-all transform active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2"
                        >
                            <i v-if="deleteModal.processing" class="fa-solid fa-circle-notch fa-spin"></i>
                            <span>{{ deleteModal.processing ? 'BORRANDO...' : 'SÍ, BORRAR REGISTRO' }}</span>
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