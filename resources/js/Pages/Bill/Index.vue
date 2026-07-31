<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref, computed, onMounted } from 'vue'; // Consolidated imports
/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'
 
/* import the fontawesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { fas } from '@fortawesome/free-solid-svg-icons';
import { router, usePage } from '@inertiajs/vue3';

library.add(fas)

const props = defineProps({
  Facturas: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const isReadOnly = computed(() => {
    const roles = (user.value?.roles || []).map(r => typeof r === 'string' ? r : r.name || '');
    const directRol = user.value?.rol || '';
    if (roles.includes('Administrador Consulta') || directRol === 'Administrador Consulta') return true;
    
    const permissions = (user.value?.permissions || []).map(p => typeof p === 'string' ? p : p.name || '');
    const hasWritePermission = permissions.some(p => ['manage billing', 'manage partida'].includes(p));
    const hasWriteRole = ['Superusuario', 'Administrador', 'Facturacion', 'Vendedor'].includes(directRol) || 
                         roles.some(name => ['Superusuario', 'Administrador', 'Facturacion', 'Vendedor'].includes(name));
    return !hasWritePermission && !hasWriteRole;
});

const deleteModal = ref({
    show: false,
    id: null,
    processing: false
});

const openDeleteModal = (id) => {
    deleteModal.value = { show: true, id, processing: false };
};

const closeDeleteModal = () => {
    deleteModal.value = { show: false, id: null, processing: false };
};

const confirmDelete = () => {
    deleteModal.value.processing = true;
    router.delete(`/billing/delete/${deleteModal.value.id}`, {
        onSuccess: () => closeDeleteModal(),
        onFinish: () => deleteModal.value.processing = false,
    });
};

const isGeneratingPdf = ref(false);

const searchQuery = ref(''); //Should really load it from the query string

onMounted(() => {
    const billingIds = page.props?.flash?.billing_ids || [];
    if (billingIds.length > 0) {
        billingIds.forEach(id => {
            window.open(route('billing.pdf', id), '_blank');
        });
    }
});

const filteredFacturas = computed(() => {
  const searchTerms = searchQuery.value.toLowerCase().trim();
  if (!searchTerms) return props.Facturas;

  return props.Facturas.filter((factura) => {
    // Search in main factura fields
    const mainFields = [
      factura.numero_factura,
      factura.numero_control,
      factura.client_name,
      factura.client_cedula,
      String(factura.partida_id),
      String(factura.id)
    ];

    if (mainFields.some(field => String(field).toLowerCase().includes(searchTerms))) {
      return true;
    }

    // Search in related item (partida) fields
    if (factura.partidas) {
        const itemFields = [
            factura.partidas.marca,
            factura.partidas.modelo,
            factura.partidas.tipo,
            factura.partidas.codInv
        ];
        if (itemFields.some(field => String(field).toLowerCase().includes(searchTerms))) {
            return true;
        }
    }

    return false;
  });
});

const editBilling = (id) => {
    // Realizar la solicitud a la ruta 'editCenterDist' usando Inertia.visit
    router.visit(route('editBilling', { id }));
};

const devolucionFactura = (id) => {
    // Realizar la solicitud a la ruta 'returnBilling' usando Inertia.visit para hacer una devolución
    router.visit(route('returnBilling', { id }));
};

const visualizeFact = (id) => {
    window.open(route('billing.pdf', { id }), '_blank');
};

const exportExcel = () => {
    const term = searchQuery.value || 'all';
    window.location.href = `/report/reporteExcel/facturas/${term}`;
};

const exportPdf = () => {
    const term = searchQuery.value || 'all';
    window.location.href = `/report/reportePdf/facturas/${term}`;
};
</script>


<template>
    <AppLayout title="Listado de Facturas">
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center transition-colors">
                    <i class="fa-solid fa-file-invoice-dollar mr-3 text-indigo-500"></i>Historial de Facturación
                </h2>
                
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                        </div>
                        <input 
                            type="search" 
                            v-model="searchQuery" 
                            class="block w-full md:w-64 pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all shadow-sm" 
                            placeholder="Buscar factura..."
                        >
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Quick Actions / Stats Banner -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Total Count Card -->
                    <div class="bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 p-6 rounded-[2rem] text-white shadow-xl shadow-indigo-500/20 transform hover:scale-[1.02] transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-12 w-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                                <i class="fa-solid fa-receipt text-xl"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest opacity-80 bg-black/20 px-3 py-1 rounded-full">Registro Global</span>
                        </div>
                        <div class="text-3xl font-black mb-1 line-clamp-1">{{ Facturas.length }}</div>
                        <div class="text-[10px] font-bold uppercase tracking-widest opacity-70">Facturas Procesadas</div>
                    </div>
                    
                    <!-- Export Excel -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between group hover:border-emerald-500 transition-all cursor-pointer transform hover:scale-[1.02]" @click="exportExcel">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 bg-emerald-50 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-file-excel text-2xl"></i>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Formato de Calculo</div>
                                <div class="text-lg font-bold text-gray-800 dark:text-white">Exportar Excel</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-300 group-hover:text-emerald-500 transition-colors"></i>
                    </div>

                    <!-- Export PDF -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between group hover:border-rose-500 transition-all cursor-pointer transform hover:scale-[1.02]" @click="exportPdf">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 bg-rose-50 dark:bg-rose-900/30 rounded-2xl flex items-center justify-center text-rose-600 dark:text-rose-400 group-hover:bg-rose-600 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-file-pdf text-2xl"></i>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Documento Oficial</div>
                                <div class="text-lg font-bold text-gray-800 dark:text-white">Exportar PDF</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-300 group-hover:text-rose-500 transition-colors"></i>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50 overflow-hidden transition-colors">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 transition-colors">
                                    <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500">Factura / Control</th>
                                    <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500">Ítem Vendido</th>
                                    <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500">Cliente</th>
                                    <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500">Monto Total</th>
                                    <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 text-center">Fecha</th>
                                    <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 text-right">Opciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                <tr v-for="factura in filteredFacturas" :key="factura.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors group">
                                    <td class="px-6 py-6 border-l-4 border-transparent hover:border-indigo-500 transition-all">
                                        <div class="flex items-center gap-4">
                                            <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-500 border border-indigo-100 dark:border-indigo-800 group-hover:shadow-lg group-hover:shadow-indigo-500/10 mb-[-1px]">
                                                <i class="fa-solid fa-file-invoice"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                                                    #{{ factura.numero_factura }}
                                                    <span v-if="factura.status === 'ANULADA'" class="px-1.5 py-0.5 text-[8px] font-black uppercase tracking-wider rounded-md bg-rose-100 dark:bg-rose-900/30 text-rose-500 border border-rose-200 dark:border-rose-800">
                                                        Anulada
                                                    </span>
                                                    <span v-else class="px-1.5 py-0.5 text-[8px] font-black uppercase tracking-wider rounded-md bg-emerald-100 dark:bg-emerald-900/30 text-emerald-500 border border-emerald-200 dark:border-emerald-800">
                                                        Activa
                                                    </span>
                                                </div>
                                                <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase">
                                                    Partida ID: {{ factura.partida_id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="space-y-1">
                                            <div class="text-xs font-black text-gray-700 dark:text-gray-300 uppercase truncate max-w-[200px]" v-if="factura.partidas">
                                                {{ factura.partidas.tipo }} {{ factura.partidas.marca }} {{ factura.partidas.modelo }}
                                            </div>
                                            <label class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-700 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-tighter">
                                                <i class="fa-solid fa-barcode text-[8px]"></i>
                                                {{ factura.id }}
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="space-y-1">
                                            <div class="text-xs font-black text-gray-800 dark:text-white uppercase leading-none">
                                                {{ factura.client_name || 'N/A' }}
                                            </div>
                                            <div v-if="factura.client_cedula" class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">
                                                <i class="fa-solid fa-id-card text-[8px] mr-1"></i>{{ factura.client_cedula }}
                                            </div>
                                            <div v-if="factura.client_email" class="text-[10px] text-indigo-500 dark:text-indigo-400 lowercase truncate max-w-[150px]" :title="factura.client_email">
                                                <i class="fa-solid fa-envelope text-[8px] mr-1"></i>{{ factura.client_email }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="text-sm font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 px-3 py-1 rounded-lg w-fit">
                                            {{ factura.precio_total }} <span class="text-[10px] opacity-70">Bs</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                                <i class="fa-solid fa-calendar-day text-[10px] opacity-50"></i>
                                                {{ factura.fecha }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 border-l border-gray-50 dark:border-gray-700/30">
                                        <div class="flex items-center justify-end gap-2.5">
                                            <button @click="visualizeFact(factura.id)" class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 hover:bg-white dark:hover:bg-gray-700 hover:text-indigo-500 hover:shadow-xl hover:shadow-indigo-500/20 transition-all transform hover:scale-110 active:scale-95" title="Ver Detalle">
                                                <i class="fa-solid fa-eye text-sm"></i>
                                            </button>
                                            <button v-if="!isReadOnly && factura.status !== 'ANULADA'" @click="editBilling(factura.id)" class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 hover:bg-white dark:hover:bg-gray-700 hover:text-blue-500 hover:shadow-xl hover:shadow-blue-500/20 transition-all transform hover:scale-110 active:scale-95" title="Editar">
                                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                                            </button>
                                            <button v-if="!isReadOnly && factura.status !== 'ANULADA'" @click="devolucionFactura(factura.id)" class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 hover:bg-white dark:hover:bg-gray-700 hover:text-emerald-500 hover:shadow-xl hover:shadow-emerald-500/20 transition-all transform hover:scale-110 active:scale-95" title="Devolución">
                                                <i class="fa-solid fa-repeat text-sm"></i>
                                            </button>
                                            <button v-if="!isReadOnly" @click="openDeleteModal(factura.id)" class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 hover:bg-white dark:hover:bg-gray-700 hover:text-rose-500 hover:shadow-xl hover:shadow-rose-500/20 transition-all transform hover:scale-110 active:scale-95" title="Eliminar">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredFacturas.length === 0">
                                    <td colspan="6" class="px-6 py-24 text-center">
                                        <div class="flex flex-col items-center gap-6">
                                            <div class="h-24 w-24 rounded-[2rem] bg-gray-50 dark:bg-gray-900/50 flex items-center justify-center border-2 border-dashed border-gray-100 dark:border-gray-800">
                                                <i class="fa-solid fa-file-circle-xmark text-5xl text-gray-200 dark:text-gray-800"></i>
                                            </div>
                                            <div class="space-y-2">
                                                <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Sin resultados</h3>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em]">No se encontraron facturas con el término "{{ searchQuery }}"</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

        <!-- Premium Delete Modal -->
        <div v-if="deleteModal.show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all animate-in fade-in duration-300">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] w-full max-w-md shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden transform animate-in zoom-in-95 duration-300">
                <div class="p-8 text-center">
                    <div class="h-20 w-20 bg-rose-50 dark:bg-rose-900/30 rounded-full flex items-center justify-center mx-auto mb-6 text-rose-500 border-4 border-rose-50 dark:border-rose-900/10 transition-transform scale-110">
                        <i class="fa-solid fa-trash-can text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight mb-2">¿Eliminar Factura?</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-8">Esta acción es permanente y no podrá recuperar el registro. ¿Desea continuar con la eliminación?</p>
                    
                    <div class="flex flex-col gap-3">
                        <button 
                            @click="confirmDelete" 
                            :disabled="deleteModal.processing"
                            class="w-full bg-rose-500 hover:bg-rose-600 text-white font-black py-4 rounded-2xl shadow-lg shadow-rose-500/20 transition-all transform active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2"
                        >
                            <i v-if="deleteModal.processing" class="fa-solid fa-circle-notch fa-spin"></i>
                            {{ deleteModal.processing ? 'ELIMINANDO...' : 'SÍ, ELIMINAR AHORA' }}
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

        <!-- Premium PDF Loading Overlay -->
        <div v-if="isGeneratingPdf" class="fixed inset-0 z-[100] flex items-center justify-center bg-indigo-900/40 backdrop-blur-md transition-all animate-in fade-in duration-500">
            <div class="flex flex-col items-center">
                <div class="relative">
                    <div class="h-24 w-24 rounded-full border-t-4 border-b-4 border-white animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fa-solid fa-file-pdf text-3xl text-white animate-pulse"></i>
                    </div>
                </div>
                <div class="mt-8 text-center">
                    <h3 class="text-2xl font-black text-white uppercase tracking-widest animate-pulse">Generando Factura</h3>
                    <p class="text-indigo-100 text-xs font-bold uppercase tracking-widest mt-2 opacity-70">Preparando documento digital...</p>
                </div>
            </div>
        </div>
    </div>
</div>
    </AppLayout>
</template>