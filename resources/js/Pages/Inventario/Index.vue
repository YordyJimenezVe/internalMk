<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { fab } from '@fortawesome/free-brands-svg-icons';
import { router, Link } from '@inertiajs/vue3';
import { computed, ref, reactive, watch, onMounted } from 'vue';

library.add(fas, fab);
const props = defineProps({
    Inventarios: Object,
    filters: Object,
    tipos: Object,
});

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
        'dodge': 'dodge',
        'dosge': 'dodge',
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
    };

    for (const key in slugMap) {
        if (b.includes(key)) return slugMap[key];
    }
    
    return null;
};

const getBrandIcon = (brand) => {
    const slug = getBrandSlug(brand);
    if (slug) return `https://cdn.simpleicons.org/${slug}/9ca3af`;
    return null;
};

const currentUrl = window.location.pathname;
const isAutopart = computed(() => currentUrl.includes('autopart'));
const isCamara = computed(() => currentUrl.includes('camara'));

const deleteModal = reactive({
    isOpen: false,
    itemId: null,
    processing: false
});

// Computed Columns based on route
const columns = computed(() => {
    let cols = [
        { key: 'id', label: 'ID', sortable: true },
        { key: 'container.cod', label: 'Contenedor', sortable: true }, // Nested sort might need backend tweak, but key is for display
        { key: 'codInv', label: 'Inventario', sortable: true },
        { key: 'expediente', label: 'Expediente', sortable: true },
        { key: 'tipo', label: 'Tipo', sortable: true },
        { key: 'serial', label: 'Serial', sortable: true },
        { key: 'model_display', label: 'Marca / Modelo' }, // Composite
    ];

    if (isAutopart.value) {
        cols.push({ key: 'categorie', label: 'Categoría', sortable: true });
        cols.push({ key: 'cantidad', label: 'Cantidad', sortable: true });
    } else {
        cols.push({ key: 'año', label: 'Año', sortable: true });
    }

    return cols;
});

const statusFilter = ref(props.filters?.status || 'DISPONIBLE');
const typeFilter = ref(props.filters?.type_filter || ''); // New

const fetchInventarios = () => {
    const routeName = isAutopart.value ? 'autopart' : (isCamara.value ? 'camara' : 'inventario');
    router.get(route(routeName), { 
        ...props.filters,
        status: statusFilter.value,
        type_filter: typeFilter.value // Add this
    }, { preserveState: true });
};

const registrarInventario = () => {
    if (isAutopart.value) router.visit(route('createAutopart'));
    else if (isCamara.value) router.visit(route('createCamara'));
    else router.visit(route('createInventario'));
};

const editarInventario = id => {
    if (isAutopart.value) router.visit(route('editAutopart', { id }));
    else if (isCamara.value) router.visit(route('editCamara', { id }));
    else router.visit(route('editInventario', { id }));
};

const openDeleteModal = id => {
    deleteModal.itemId = id;
    deleteModal.isOpen = true;
};

const closeDeleteModal = () => {
    deleteModal.isOpen = false;
    deleteModal.itemId = null;
};

const confirmDelete = () => {
    deleteModal.processing = true;
    router.delete(route('deleteInventario', deleteModal.itemId), {
        onFinish: () => {
            deleteModal.processing = false;
            closeDeleteModal();
        }
    });
};

</script>

<template>
  <AppLayout title="Inventario">
    <template #header>
        <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center transition-colors">
            <i :class="isAutopart ? 'fa-solid fa-gears text-indigo-500' : (isCamara ? 'fa-solid fa-camera text-indigo-500' : 'fa-solid fa-boxes-stacked text-indigo-500')" class="mr-3"></i>
            {{ isAutopart ? 'Gestión de Autopartes' : (isCamara ? 'Control de Cámaras' : 'Inventario General') }}
        </h2>
    </template>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Primary Action Toolbar -->
            <div class="mb-6 flex justify-end">
                <button 
                    @click="registrarInventario()" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-[2rem] font-black shadow-xl shadow-indigo-500/20 transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3 group border-b-4 border-indigo-800"
                >
                    <i class="fa-solid fa-plus text-xl group-hover:rotate-90 transition-transform duration-300"></i>
                    <span class="tracking-widest uppercase text-sm">REGISTRAR NUEVO</span>
                </button>
            </div>
            
            <!-- Advanced Toolbar -->
            <div class="mb-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bg-white dark:bg-gray-800 p-6 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700/50 backdrop-blur-sm">
                
                <!-- Category/Type Filter -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-2">CATEGORÍA</label>
                    <div class="relative group">
                        <i class="fa-solid fa-filter absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                        <select v-model="typeFilter" @change="fetchInventarios" class="w-full pl-12 pr-4 py-3.5 bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all font-bold outline-none appearance-none cursor-pointer">
                            <option value="">TODOS LOS TIPOS</option>
                            <option value="motores">MOTORES</option>
                            <option value="cajas">CAJAS</option>
                            <option value="camaras">CÁMARAS</option>
                            <option value="autopartes">AUTOPARTES</option>
                        </select>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-2">ESTADO</label>
                    <div class="relative group">
                        <i class="fa-solid fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition-colors"></i>
                        <select v-model="statusFilter" @change="fetchInventarios" class="w-full pl-12 pr-4 py-3.5 bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-emerald-500 transition-all font-bold outline-none appearance-none cursor-pointer">
                            <option value="DISPONIBLE">DISPONIBLES</option>
                            <option value="VENDIDO">VENDIDOS</option>
                            <option value="GARANTIA">GARANTÍAS</option>
                            <option value="ALL">LISTADO COMPLETO</option>
                        </select>
                    </div>
                </div>

                <!-- Summary Stats Card (Quick View) -->
                <div class="hidden lg:flex items-center justify-around bg-indigo-50/50 dark:bg-indigo-900/10 rounded-2xl border border-indigo-100/50 dark:border-indigo-800/30 px-4 py-3">
                    <div class="text-center">
                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Total</p>
                        <p class="text-xl font-black text-indigo-600 dark:text-indigo-400">{{ Inventarios.total }}</p>
                    </div>
                    <div class="h-8 w-[1px] bg-indigo-200 dark:bg-indigo-800"></div>
                    <div class="text-center">
                        <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">Página</p>
                        <p class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{ Inventarios.current_page }}</p>
                    </div>
                </div>
            </div>

            <!-- Premium DataTable -->
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50 overflow-hidden backdrop-blur-sm">
                <DataTable 
                    :rows="Inventarios" 
                    :columns="columns" 
                    :filters="{ ...filters, status: statusFilter, type_filter: typeFilter }"
                    :routeName="isAutopart ? 'autopart' : (isCamara ? 'camara' : 'inventario')"
                    :title="isAutopart ? 'LISTADO DE AUTOPARTES' : 'REGISTROS DE INVENTARIO'"
                    exportType="Inventarios"
                    class="p-4"
                >
                    <!-- Custom Cells -->
                    <template #cell-id="{ row }">
                         <span class="text-xs font-black text-gray-400">#{{ row.id }}</span>
                    </template>
                    
                    <template #cell-container.cod="{ row }">
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 text-[10px] font-black border border-indigo-100 dark:border-indigo-800/30">
                            {{ row.container ? row.container.cod : 'N/A' }}
                        </span>
                    </template>

                    <template #cell-model_display="{ row }">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 flex items-center justify-center bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden p-1.5">
                                <img v-if="getBrandIcon(row.marca)" :src="getBrandIcon(row.marca)" class="w-full h-full object-contain opacity-80" :alt="row.marca" />
                                <FontAwesomeIcon v-else :icon="['fas', 'car']" class="text-gray-400 dark:text-gray-500 text-xl" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-gray-800 dark:text-white uppercase leading-tight">{{ row.marca }}</span>
                                <span class="text-[11px] font-medium text-gray-400 dark:text-gray-500">{{ row.modelo }}</span>
                            </div>
                        </div>
                    </template>

                    <template #cell-tipo="{ row }">
                        <span class="text-xs font-bold text-indigo-500/80 dark:text-indigo-400/80 uppercase tracking-tight">{{ row.tipo }}</span>
                    </template>

                    <!-- Actions (Premium Redesign) -->
                    <template #actions="{ row }">
                        <div class="flex justify-end gap-2">
                             <button @click="router.visit(route('showInventario', row.id))"
                                class="h-8 w-8 flex items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-600 transition-all transform active:scale-90" title="Ver Detalles">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>
                            <button @click="editarInventario(row.id)"
                                class="h-8 w-8 flex items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 transition-all transform active:scale-90" title="Editar">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </button>
                            <button @click="openDeleteModal(row.id)"
                                class="h-8 w-8 flex items-center justify-center rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white dark:hover:bg-rose-500 transition-all transform active:scale-90" title="Eliminar">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </template>
                </DataTable>
            </div>

            <!-- Enhanced Summary Footer -->
             <div class="mt-8 p-8 bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl border border-gray-100 dark:border-gray-700 flex flex-wrap items-center justify-center gap-12 sm:justify-start">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-500">
                        <i class="fa-solid fa-wrench text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Motores</p>
                        <p class="text-lg font-black text-gray-700 dark:text-white">{{ tipos?.[0]?.['motores'] || 0 }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-500">
                        <i class="fa-solid fa-gears text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Cajas</p>
                        <p class="text-lg font-black text-gray-700 dark:text-white">{{ tipos?.[0]?.['cajas_automaticas'] || 0 }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-500">
                        <i class="fa-solid fa-oil-can text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Autopartes</p>
                        <p class="text-lg font-black text-gray-700 dark:text-white">{{ tipos?.[0]?.['autopartes'] || 0 }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-500">
                        <i class="fa-solid fa-camera text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Cámaras</p>
                        <p class="text-lg font-black text-gray-700 dark:text-white">{{ tipos?.[0]?.['camaras'] || 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Delete Modal -->
    <div v-if="deleteModal.isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 min-h-screen">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-md transition-opacity" @click="closeDeleteModal"></div>
        
        <div class="relative bg-white dark:bg-gray-800 rounded-[2.5rem] max-w-sm w-full shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700 transform transition-all animate-in zoom-in-95 duration-200">
            <div class="p-8 text-center">
                <div class="h-20 w-20 bg-rose-50 dark:bg-rose-900/30 rounded-full flex items-center justify-center mx-auto mb-6 text-rose-500 border-4 border-rose-50 dark:border-rose-900/10 scale-110">
                    <i class="fa-solid fa-box-archive text-3xl"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight mb-2">¿Eliminar Item?</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-8 font-medium">Esta acción eliminará permanentemente este registro del inventario.</p>
                
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