<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    items: Array,
    finalizedItems: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const isReadOnly = computed(() => {
    const roles = user.value?.roles || [];
    const directRol = user.value?.rol || '';
    if (roles.includes('Administrador Consulta') || directRol === 'Administrador Consulta') return true;
    
    const permissions = user.value?.permissions || [];
    const hasWritePermission = permissions.some(p => ['manage billing', 'manage partida'].includes(p));
    const hasWriteRole = roles.some(r => {
        const name = typeof r === 'string' ? r : r.name;
        return ['Superusuario', 'Administrador', 'Facturacion', 'Vendedor'].includes(name);
    });
    return !hasWritePermission && !hasWriteRole;
});

const isProcessing = ref(false);
const showConfirmModal = ref(false);
const showRevertModal = ref(false);
const itemToConciliar = ref(null);
const itemToRevert = ref(null);
const activeTab = ref('pending'); // 'pending' or 'finalized'

// Pagination state for finalized items
const currentPage = ref(1);
const itemsPerPage = 10;

// Computed for paginated finalized items
const paginatedFinalizedItems = computed(() => {
    if (!props.finalizedItems) return [];
    const startIndex = (currentPage.value - 1) * itemsPerPage;
    return props.finalizedItems.slice(startIndex, startIndex + itemsPerPage);
});

// Computed for total pages
const totalPages = computed(() => {
    if (!props.finalizedItems) return 0;
    return Math.ceil(props.finalizedItems.length / itemsPerPage);
});

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

// Reset page on prop change or tab change
watch(() => props.finalizedItems, () => {
    currentPage.value = 1;
}, { deep: true });

watch(activeTab, () => {
    currentPage.value = 1;
});

const openConfirmModal = (itemId) => {
    itemToConciliar.value = itemId;
    showConfirmModal.value = true;
};

const confirmConciliar = () => {
    if (!itemToConciliar.value) return;
    isProcessing.value = true;
    router.post(route('maintenance.conciliar_item', itemToConciliar.value), {}, {
        onSuccess: () => {
            showConfirmModal.value = false;
            itemToConciliar.value = null;
        },
        onFinish: () => {
            isProcessing.value = false;
        }
    });
};

const openRevertModal = (itemId) => {
    itemToRevert.value = itemId;
    showRevertModal.value = true;
};

const confirmRevert = () => {
    if (!itemToRevert.value) return;
    isProcessing.value = true;
    router.post(route('maintenance.revert_conciliar_item', itemToRevert.value), {}, {
        onSuccess: () => {
            showRevertModal.value = false;
            itemToRevert.value = null;
        },
        onFinish: () => {
            isProcessing.value = false;
        }
    });
};

const formatPrice = (value) => {
    if (!value) return '$0.00';
    return '$' + parseFloat(value).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-VE', { day: '2-digit', month: '2-digit', year: 'numeric' });
};
</script>

<template>
    <AppLayout title="Conciliación de Taller">
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center">
                    <i class="fa-solid fa-file-shield mr-3 text-indigo-500"></i>Conciliación de Facturas de Taller
                </h2>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-amber-500/10 text-amber-600 dark:text-amber-400 text-xs font-black uppercase rounded-full tracking-wider">
                        {{ props.items.length }} Pendientes
                    </span>
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-black uppercase rounded-full tracking-wider">
                        {{ props.finalizedItems ? props.finalizedItems.length : 0 }} Finiquitadas
                    </span>
                </div>
            </div>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Main Container Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl rounded-3xl border border-gray-100 dark:border-gray-700/50 p-6 md:p-8">
                    
                    <div class="mb-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Esta bandeja muestra los gastos y repuestos de talleres o rectificadoras externas registrados con **Factura**. Al conciliarlos, se sumarán de forma oficial a la **Base Imponible (BIG)** del motor para la venta final.
                        </p>
                    </div>

                    <!-- Tab Switcher -->
                    <div class="flex border-b border-gray-150 dark:border-gray-700/60 mb-6 gap-2">
                        <button 
                            @click="activeTab = 'pending'"
                            class="pb-3 px-4 text-xs font-black uppercase tracking-wider transition-all border-b-2"
                            :class="activeTab === 'pending' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                        >
                            Pendientes de Conciliar
                            <span class="ml-1.5 px-2 py-0.5 text-[9px] rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 font-extrabold">
                                {{ props.items.length }}
                            </span>
                        </button>
                        <button 
                            @click="activeTab = 'finalized'"
                            class="pb-3 px-4 text-xs font-black uppercase tracking-wider transition-all border-b-2"
                            :class="activeTab === 'finalized' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                        >
                            Conciliaciones Finiquitadas
                            <span class="ml-1.5 px-2 py-0.5 text-[9px] rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-extrabold">
                                {{ props.finalizedItems ? props.finalizedItems.length : 0 }}
                            </span>
                        </button>
                    </div>

                    <!-- Tab 1: Pending Invoices -->
                    <div v-if="activeTab === 'pending'">
                        <div v-if="props.items.length > 0" class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-700/50">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                                        <th class="py-4 px-6">Motor / Referencia</th>
                                        <th class="py-4 px-6">Ítem / Servicio Externo</th>
                                        <th class="py-4 px-6">Proveedor / Factura</th>
                                        <th class="py-4 px-6 text-center">Documento</th>
                                        <th class="py-4 px-6 text-right">Base Imponible (BIG)</th>
                                        <th class="py-4 px-6 text-right">Costo Total</th>
                                        <th class="py-4 px-6 text-center">Fechas</th>
                                        <th class="py-4 px-6 text-center">Estado</th>
                                        <th v-if="!isReadOnly" class="py-4 px-6 text-right">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/40 text-sm font-medium">
                                    <tr v-for="item in props.items" :key="item.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10 transition-all">
                                        
                                        <!-- Motor Reference -->
                                        <td class="py-4 px-6">
                                            <div v-if="item.maintenance?.partida" class="space-y-1">
                                                <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 text-[10px] font-black uppercase rounded-md">
                                                    {{ item.maintenance.partida.codInv }}
                                                </span>
                                                <div class="font-bold text-gray-900 dark:text-white uppercase text-xs">
                                                    {{ item.maintenance.partida.tipo }}
                                                </div>
                                                <div class="text-[10px] text-gray-400 font-bold uppercase">
                                                    {{ item.maintenance.partida.marca }} {{ item.maintenance.partida.modelo }}
                                                </div>
                                            </div>
                                            <span v-else class="text-xs text-gray-400 italic">Sin Motor</span>
                                        </td>

                                        <!-- Service Description -->
                                        <td class="py-4 px-6">
                                            <div class="space-y-1">
                                                <div class="text-gray-900 dark:text-white font-bold text-xs uppercase flex items-center gap-1.5">
                                                    <i class="fa-solid" :class="item.type === 'REPUESTO' ? 'fa-gears text-indigo-400' : 'fa-wrench text-amber-400'"></i>
                                                    {{ item.description }}
                                                </div>
                                                <span class="inline-flex items-center text-[9px] font-black tracking-widest px-1.5 py-0.5 rounded-full uppercase"
                                                    :class="item.type === 'REPUESTO' ? 'bg-purple-100 dark:bg-purple-950/30 text-purple-700 dark:text-purple-400' : 'bg-amber-100 dark:bg-amber-955/30 text-amber-700 dark:text-amber-400'">
                                                    {{ item.type }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Invoice Number -->
                                        <td class="py-4 px-6">
                                            <div class="space-y-1">
                                                <div class="font-black text-indigo-600 dark:text-indigo-400 text-xs flex items-center gap-1">
                                                    <i class="fa-solid fa-file-invoice"></i>
                                                    {{ item.invoice_number || 'S/N' }}
                                                </div>
                                                <div class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">
                                                    COMPRADO EXTE.
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Invoice Visualizer (Eye Button) -->
                                        <td class="py-4 px-6 text-center">
                                            <a 
                                                v-if="item.invoice_path" 
                                                :href="'/storage/' + item.invoice_path" 
                                                target="_blank"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-950/50 text-[10px] font-black uppercase rounded-lg transition-all shadow-sm border border-indigo-100 dark:border-indigo-900/30"
                                            >
                                                <i class="fa-solid fa-eye text-xs"></i>
                                                Ver Factura
                                            </a>
                                            <span v-else class="text-xs text-gray-400 italic">Sin Adjunto</span>
                                        </td>

                                        <!-- Base Imponible (BIG) -->
                                        <td class="py-4 px-6 text-right">
                                            <div class="font-extrabold text-emerald-600 dark:text-emerald-400 text-xs">
                                                {{ formatPrice(item.base_imponible) }}
                                            </div>
                                            <span class="text-[9px] text-gray-400 uppercase font-bold tracking-tight">BASE PARA IVA</span>
                                        </td>

                                        <!-- Costo Total -->
                                        <td class="py-4 px-6 text-right">
                                            <div class="font-bold text-gray-900 dark:text-white text-xs">
                                                {{ formatPrice(item.cost) }}
                                            </div>
                                            <span class="text-[9px] text-gray-400 uppercase font-bold tracking-tight">COSTO REAL</span>
                                        </td>

                                        <!-- Dates -->
                                        <td class="py-4 px-6 text-center text-xs">
                                            <div class="space-y-0.5 text-[10px] font-bold text-gray-500 dark:text-gray-400">
                                                <div><span class="text-gray-400 uppercase">SALIDA:</span> {{ formatDate(item.outflow_date) }}</div>
                                                <div><span class="text-gray-400 uppercase">RETORNO:</span> {{ formatDate(item.return_date) }}</div>
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="py-4 px-6 text-center">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-black uppercase rounded-full tracking-wider animate-pulse">
                                                <i class="fa-solid fa-clock text-[8px]"></i>
                                                Pendiente
                                            </span>
                                        </td>

                                        <!-- Action Button -->
                                        <td v-if="!isReadOnly" class="py-4 px-6 text-right">
                                            <button 
                                                @click="openConfirmModal(item.id)" 
                                                :disabled="isProcessing"
                                                class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 disabled:opacity-50 text-white text-xs font-black uppercase rounded-xl transition-all shadow-lg shadow-emerald-600/20 gap-1.5"
                                            >
                                                <i class="fa-solid fa-circle-check text-sm"></i>
                                                Conciliar
                                            </button>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="py-20 flex flex-col items-center justify-center text-center space-y-4">
                            <div class="w-24 h-24 rounded-full bg-emerald-50 dark:bg-emerald-950/20 flex items-center justify-center text-emerald-500 shadow-xl shadow-emerald-500/10">
                                <i class="fa-solid fa-circle-check text-5xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">¡Todo al día!</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">
                                No hay facturas de taller o de rectificadoras externas pendientes de conciliar en este momento.
                            </p>
                        </div>
                    </div>

                    <!-- Tab 2: Finalized Invoices -->
                    <div v-if="activeTab === 'finalized'">
                        <div v-if="props.finalizedItems && props.finalizedItems.length > 0" class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-700/50">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                                        <th class="py-4 px-6">Motor / Referencia</th>
                                        <th class="py-4 px-6">Ítem / Servicio Externo</th>
                                        <th class="py-4 px-6">Proveedor / Factura</th>
                                        <th class="py-4 px-6 text-center">Documento</th>
                                        <th class="py-4 px-6 text-right">Base Imponible (BIG)</th>
                                        <th class="py-4 px-6 text-right">Costo Total</th>
                                        <th class="py-4 px-6 text-center">Fechas</th>
                                        <th class="py-4 px-6 text-center">Estado</th>
                                        <th v-if="!isReadOnly" class="py-4 px-6 text-right">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/40 text-sm font-medium">
                                    <tr v-for="item in paginatedFinalizedItems" :key="item.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10 transition-all">
                                        
                                        <!-- Motor Reference -->
                                        <td class="py-4 px-6">
                                            <div v-if="item.maintenance?.partida" class="space-y-1">
                                                <span class="inline-flex items-center px-2 py-0.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 text-[10px] font-black uppercase rounded-md">
                                                    {{ item.maintenance.partida.codInv }}
                                                </span>
                                                <div class="font-bold text-gray-900 dark:text-white uppercase text-xs">
                                                    {{ item.maintenance.partida.tipo }}
                                                </div>
                                                <div class="text-[10px] text-gray-400 font-bold uppercase">
                                                    {{ item.maintenance.partida.marca }} {{ item.maintenance.partida.modelo }}
                                                </div>
                                            </div>
                                            <span v-else class="text-xs text-gray-400 italic">Sin Motor</span>
                                        </td>

                                        <!-- Service Description -->
                                        <td class="py-4 px-6">
                                            <div class="space-y-1">
                                                <div class="text-gray-900 dark:text-white font-bold text-xs uppercase flex items-center gap-1.5">
                                                    <i class="fa-solid" :class="item.type === 'REPUESTO' ? 'fa-gears text-emerald-400' : 'fa-wrench text-emerald-400'"></i>
                                                    {{ item.description }}
                                                </div>
                                                <span class="inline-flex items-center text-[9px] font-black tracking-widest px-1.5 py-0.5 rounded-full uppercase bg-emerald-100 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400">
                                                    {{ item.type }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Invoice Number -->
                                        <td class="py-4 px-6">
                                            <div class="space-y-1">
                                                <div class="font-black text-emerald-600 dark:text-emerald-400 text-xs flex items-center gap-1">
                                                    <i class="fa-solid fa-file-invoice"></i>
                                                    {{ item.invoice_number || 'S/N' }}
                                                </div>
                                                <div class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">
                                                    COMPRADO EXTE.
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Invoice Visualizer (Eye Button) -->
                                        <td class="py-4 px-6 text-center">
                                            <a 
                                                v-if="item.invoice_path" 
                                                :href="'/storage/' + item.invoice_path" 
                                                target="_blank"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-950/50 text-[10px] font-black uppercase rounded-lg transition-all shadow-sm border border-emerald-100 dark:border-emerald-900/30"
                                            >
                                                <i class="fa-solid fa-eye text-xs"></i>
                                                Ver Factura
                                            </a>
                                            <span v-else class="text-xs text-gray-400 italic">Sin Adjunto</span>
                                        </td>

                                        <!-- Base Imponible (BIG) -->
                                        <td class="py-4 px-6 text-right">
                                            <div class="font-extrabold text-emerald-600 dark:text-emerald-400 text-xs">
                                                {{ formatPrice(item.base_imponible) }}
                                            </div>
                                            <span class="text-[9px] text-gray-400 uppercase font-bold tracking-tight">BASE PARA IVA</span>
                                        </td>

                                        <!-- Costo Total -->
                                        <td class="py-4 px-6 text-right">
                                            <div class="font-bold text-gray-900 dark:text-white text-xs">
                                                {{ formatPrice(item.cost) }}
                                            </div>
                                            <span class="text-[9px] text-gray-400 uppercase font-bold tracking-tight">COSTO REAL</span>
                                        </td>

                                        <!-- Dates -->
                                        <td class="py-4 px-6 text-center text-xs">
                                            <div class="space-y-0.5 text-[10px] font-bold text-gray-500 dark:text-gray-400">
                                                <div><span class="text-gray-400 uppercase">SALIDA:</span> {{ formatDate(item.outflow_date) }}</div>
                                                <div><span class="text-gray-400 uppercase">RETORNO:</span> {{ formatDate(item.return_date) }}</div>
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="py-4 px-6 text-center">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase rounded-full tracking-wider">
                                                <i class="fa-solid fa-circle-check text-[8px]"></i>
                                                Conciliado
                                            </span>
                                        </td>

                                        <!-- Revert Button -->
                                        <td v-if="!isReadOnly" class="py-4 px-6 text-right">
                                            <button 
                                                @click="openRevertModal(item.id)" 
                                                :disabled="isProcessing"
                                                class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 disabled:opacity-50 text-white text-xs font-black uppercase rounded-xl transition-all shadow-lg shadow-amber-600/20 gap-1.5"
                                            >
                                                <i class="fa-solid fa-undo text-xs"></i>
                                                Revertir
                                            </button>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>

                            <!-- Pagination Controls -->
                            <div v-if="totalPages > 1" class="mt-4 flex items-center justify-between border-t border-gray-100 dark:border-gray-700/50 pt-4 px-4 pb-2">
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Mostrando <span class="font-bold text-gray-700 dark:text-gray-200">{{ (currentPage - 1) * itemsPerPage + 1 }}</span> a 
                                    <span class="font-bold text-gray-700 dark:text-gray-200">{{ Math.min(currentPage * itemsPerPage, props.finalizedItems.length) }}</span> de 
                                    <span class="font-bold text-gray-700 dark:text-gray-200">{{ props.finalizedItems.length }}</span> registros
                                </div>
                                <div class="flex items-center gap-1">
                                    <button 
                                        @click="prevPage" 
                                        :disabled="currentPage === 1"
                                        class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 dark:bg-gray-850 dark:hover:bg-gray-750 disabled:opacity-40 text-gray-700 dark:text-white text-xs font-black uppercase rounded-lg border border-gray-150 dark:border-gray-700/50 transition-all flex items-center gap-1"
                                    >
                                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                                        Anterior
                                    </button>
                                    <span class="px-3 text-xs font-bold text-gray-600 dark:text-gray-300">
                                        Pág {{ currentPage }} de {{ totalPages }}
                                    </span>
                                    <button 
                                        @click="nextPage" 
                                        :disabled="currentPage === totalPages"
                                        class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 dark:bg-gray-850 dark:hover:bg-gray-750 disabled:opacity-40 text-gray-700 dark:text-white text-xs font-black uppercase rounded-lg border border-gray-150 dark:border-gray-700/50 transition-all flex items-center gap-1"
                                    >
                                        Siguiente
                                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="py-20 flex flex-col items-center justify-center text-center space-y-4">
                            <div class="w-24 h-24 rounded-full bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400">
                                <i class="fa-solid fa-folder-open text-5xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Sin conciliaciones</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">
                                Aún no has finiquitado ninguna conciliación de factura en esta bandeja.
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- Custom Confirmation Modal (Conciliar) -->
        <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-in fade-in duration-200">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showConfirmModal = false"></div>
            <div class="relative bg-white dark:bg-slate-900 w-full max-w-md rounded-3xl shadow-2xl border border-gray-100 dark:border-slate-800 p-6 sm:p-8 animate-in zoom-in-95 duration-200">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 uppercase flex items-center gap-2">
                    <i class="fa-solid fa-circle-question text-indigo-500 text-xl"></i>Confirmar Conciliación
                </h3>
                
                <p class="text-sm text-gray-600 dark:text-slate-400 mb-6 leading-relaxed">
                    ¿Está seguro de conciliar y declarar esta factura de taller? Al confirmarlo, este costo se sumará formalmente a la base imponible del motor.
                </p>

                <div class="flex items-center justify-end gap-3">
                    <button 
                        type="button" 
                        @click="showConfirmModal = false" 
                        class="px-4 py-2 bg-gray-150 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-gray-700 dark:text-white text-xs font-bold uppercase rounded-xl transition-all"
                    >
                        Cancelar
                    </button>
                    <button 
                        type="button" 
                        @click="confirmConciliar" 
                        :disabled="isProcessing"
                        class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase rounded-xl transition-all shadow-md shadow-emerald-600/10 flex items-center gap-1.5"
                    >
                        <i class="fa-solid fa-circle-check"></i>
                        Sí, Conciliar
                    </button>
                </div>
            </div>
        </div>

        <!-- Custom Confirmation Modal (Revertir) -->
        <div v-if="showRevertModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-in fade-in duration-200">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showRevertModal = false"></div>
            <div class="relative bg-white dark:bg-slate-900 w-full max-w-md rounded-3xl shadow-2xl border border-gray-100 dark:border-slate-800 p-6 sm:p-8 animate-in zoom-in-95 duration-200">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 uppercase flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 text-xl"></i>Confirmar Reversión
                </h3>
                
                <p class="text-sm text-gray-600 dark:text-slate-400 mb-6 leading-relaxed">
                    ¿Está seguro de deshacer la conciliación de esta factura? Al confirmarlo, el ítem volverá a aparecer como pendiente en la bandeja de facturación y se eliminará de las conciliaciones finiquitadas.
                </p>

                <div class="flex items-center justify-end gap-3">
                    <button 
                        type="button" 
                        @click="showRevertModal = false" 
                        class="px-4 py-2 bg-gray-150 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-gray-700 dark:text-white text-xs font-bold uppercase rounded-xl transition-all"
                    >
                        Cancelar
                    </button>
                    <button 
                        type="button" 
                        @click="confirmRevert" 
                        :disabled="isProcessing"
                        class="px-6 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-black uppercase rounded-xl transition-all shadow-md shadow-amber-600/10 flex items-center gap-1.5"
                    >
                        <i class="fa-solid fa-undo"></i>
                        Sí, Revertir
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
