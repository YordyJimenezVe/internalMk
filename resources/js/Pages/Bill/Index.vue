<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref, computed, onMounted, watch } from 'vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { router, usePage } from '@inertiajs/vue3';

library.add(fas);

const props = defineProps({
  Facturas: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const isReadOnly = computed(() => {
    const roles = (user.value?.roles || []).map(r => (typeof r === 'string' ? r : r.name || '').toLowerCase());
    const directRol = (user.value?.rol || '').toLowerCase();
    if (roles.includes('administrador consulta') || directRol === 'administrador consulta') return true;
    
    const permissions = (user.value?.permissions || []).map(p => (typeof p === 'string' ? p : p.name || '').toLowerCase());
    const hasWritePermission = permissions.some(p => ['manage billing', 'manage partida'].includes(p));
    const hasWriteRole = ['superusuario', 'administrador', 'facturacion', 'vendedor'].includes(directRol) || 
                         roles.some(name => ['superusuario', 'administrador', 'facturacion', 'vendedor'].includes(name));
    return !hasWritePermission && !hasWriteRole;
});

const deleteModal = ref({
    show: false,
    id: null,
    processing: false
});

const docModal = ref({
    show: false,
    billingIds: [],
    warrantyIds: []
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

// Filter State
const searchQuery = ref('');
const statusFilter = ref('ALL'); // 'ALL', 'ACTIVA', 'ANULADA'
const productTypeFilter = ref('ALL');
const startDate = ref('');
const endDate = ref('');
const minAmount = ref('');
const maxAmount = ref('');
const sortBy = ref('fecha_desc'); // 'fecha_desc', 'fecha_asc', 'monto_desc', 'monto_asc', 'numero_desc', 'numero_asc'
const isAdvancedFiltersOpen = ref(false);

const closeDocModal = () => {
    docModal.value = { show: false, billingIds: [], warrantyIds: [] };
};

const openAllDocs = () => {
    if (docModal.value.billingIds.length > 0) {
        docModal.value.billingIds.forEach(id => {
            window.open(route('billing.pdf', id), '_blank');
        });
    }
    if (docModal.value.warrantyIds.length > 0) {
        setTimeout(() => {
            docModal.value.warrantyIds.forEach(id => {
                window.open(route('billing.warranty', id), '_blank');
            });
        }, 300);
    }
};

const checkAndOpenDocuments = () => {
    const billingIds = page.props?.flash?.billing_ids || [];
    const warrantyIds = page.props?.flash?.warranty_ids || [];

    if (billingIds.length > 0 || warrantyIds.length > 0) {
        docModal.value = {
            show: true,
            billingIds: [...billingIds],
            warrantyIds: [...warrantyIds]
        };
    }
};

onMounted(() => {
    checkAndOpenDocuments();
});

watch(() => page.props.flash, () => {
    checkAndOpenDocuments();
}, { deep: true });

// Helper to extract item details
const getItem = (factura) => {
    return factura.partidas || factura.partida || factura.inventario || factura.inventarios || {};
};

// Available product types list extracted from Facturas
const availableProductTypes = computed(() => {
    const typesMap = {};
    (props.Facturas || []).forEach(f => {
        const item = getItem(f);
        if (item && item.tipo) {
            const t = String(item.tipo).toUpperCase().trim();
            typesMap[t] = (typesMap[t] || 0) + 1;
        }
    });
    return Object.keys(typesMap).sort().map(tipo => ({
        name: tipo,
        count: typesMap[tipo]
    }));
});

// Helper for date parsing
const parseFacturaDate = (dateStr) => {
    if (!dateStr) return null;
    if (typeof dateStr === 'string' && dateStr.includes('/')) {
        const parts = dateStr.split('/');
        if (parts.length === 3) {
            return new Date(parts[2], parts[1] - 1, parts[0]);
        }
    }
    const d = new Date(dateStr);
    return isNaN(d.getTime()) ? null : d;
};

// Date Presets
const setDatePreset = (preset) => {
    const now = new Date();
    const formatDate = (dateObj) => {
        const y = dateObj.getFullYear();
        const m = String(dateObj.getMonth() + 1).padStart(2, '0');
        const d = String(dateObj.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    };

    if (preset === 'today') {
        startDate.value = formatDate(now);
        endDate.value = formatDate(now);
    } else if (preset === 'week') {
        const firstDayOfWeek = new Date(now);
        const day = now.getDay();
        const diff = now.getDate() - day + (day === 0 ? -6 : 1); // Monday start
        firstDayOfWeek.setDate(diff);
        startDate.value = formatDate(firstDayOfWeek);
        endDate.value = formatDate(now);
    } else if (preset === 'month') {
        const firstDayOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
        startDate.value = formatDate(firstDayOfMonth);
        endDate.value = formatDate(now);
    } else if (preset === 'year') {
        const firstDayOfYear = new Date(now.getFullYear(), 0, 1);
        startDate.value = formatDate(firstDayOfYear);
        endDate.value = formatDate(now);
    } else if (preset === 'clear') {
        startDate.value = '';
        endDate.value = '';
    }
};

// Active Advanced Filters Count
const activeAdvancedCount = computed(() => {
    let count = 0;
    if (productTypeFilter.value !== 'ALL') count++;
    if (startDate.value) count++;
    if (endDate.value) count++;
    if (minAmount.value !== '') count++;
    if (maxAmount.value !== '') count++;
    return count;
});

// Check if any filter is active
const hasActiveFilters = computed(() => {
    return (
        searchQuery.value.trim() !== '' ||
        statusFilter.value !== 'ALL' ||
        productTypeFilter.value !== 'ALL' ||
        startDate.value !== '' ||
        endDate.value !== '' ||
        minAmount.value !== '' ||
        maxAmount.value !== ''
    );
});

// Reset all filters
const resetFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'ALL';
    productTypeFilter.value = 'ALL';
    startDate.value = '';
    endDate.value = '';
    minAmount.value = '';
    maxAmount.value = '';
    sortBy.value = 'fecha_desc';
};

// Counts per status across total Facturas
const statusCounts = computed(() => {
    let all = props.Facturas?.length || 0;
    let activas = 0;
    let anuladas = 0;
    (props.Facturas || []).forEach(f => {
        if ((f.status || 'ACTIVA').toUpperCase() === 'ANULADA') {
            anuladas++;
        } else {
            activas++;
        }
    });
    return { all, activas, anuladas };
});

// Main Filtered & Sorted Facturas computed property
const filteredFacturas = computed(() => {
    let result = (props.Facturas || []).filter(factura => {
        // 1. Text Search Query
        const term = searchQuery.value.toLowerCase().trim();
        if (term) {
            const item = getItem(factura);
            const mainFields = [
                factura.numero_factura,
                factura.numero_control,
                factura.client_name,
                factura.client_cedula,
                factura.client_email,
                String(factura.partida_id || ''),
                String(factura.id || '')
            ];
            const itemFields = [
                item.marca,
                item.modelo,
                item.tipo,
                item.codInv
            ];

            const matchesMain = mainFields.some(field => field && String(field).toLowerCase().includes(term));
            const matchesItem = itemFields.some(field => field && String(field).toLowerCase().includes(term));

            if (!matchesMain && !matchesItem) return false;
        }

        // 2. Status Filter
        if (statusFilter.value !== 'ALL') {
            const factStatus = (factura.status || 'ACTIVA').toUpperCase();
            if (statusFilter.value === 'ACTIVA' && factStatus === 'ANULADA') return false;
            if (statusFilter.value === 'ANULADA' && factStatus !== 'ANULADA') return false;
        }

        // 3. Product Type Filter
        if (productTypeFilter.value !== 'ALL') {
            const item = getItem(factura);
            const itemTipo = String(item.tipo || '').toUpperCase().trim();
            if (itemTipo !== productTypeFilter.value) return false;
        }

        // 4. Date Range Filter
        if (startDate.value || endDate.value) {
            const factDate = parseFacturaDate(factura.fecha);
            if (factDate) {
                if (startDate.value) {
                    const start = new Date(startDate.value + 'T00:00:00');
                    if (factDate < start) return false;
                }
                if (endDate.value) {
                    const end = new Date(endDate.value + 'T23:59:59');
                    if (factDate > end) return false;
                }
            }
        }

        // 5. Amount Range Filter
        const amount = parseFloat(String(factura.precio_total || '0').replace(/[^0-9.-]+/g, '')) || 0;
        if (minAmount.value !== '' && !isNaN(parseFloat(minAmount.value))) {
            if (amount < parseFloat(minAmount.value)) return false;
        }
        if (maxAmount.value !== '' && !isNaN(parseFloat(maxAmount.value))) {
            if (amount > parseFloat(maxAmount.value)) return false;
        }

        return true;
    });

    // Sort Results
    result = [...result].sort((a, b) => {
        if (sortBy.value === 'fecha_desc') {
            const dateA = parseFacturaDate(a.fecha) || new Date(0);
            const dateB = parseFacturaDate(b.fecha) || new Date(0);
            return dateB - dateA || (b.id || 0) - (a.id || 0);
        } else if (sortBy.value === 'fecha_asc') {
            const dateA = parseFacturaDate(a.fecha) || new Date(0);
            const dateB = parseFacturaDate(b.fecha) || new Date(0);
            return dateA - dateB || (a.id || 0) - (b.id || 0);
        } else if (sortBy.value === 'monto_desc') {
            const amountA = parseFloat(String(a.precio_total || '0').replace(/[^0-9.-]+/g, '')) || 0;
            const amountB = parseFloat(String(b.precio_total || '0').replace(/[^0-9.-]+/g, '')) || 0;
            return amountB - amountA;
        } else if (sortBy.value === 'monto_asc') {
            const amountA = parseFloat(String(a.precio_total || '0').replace(/[^0-9.-]+/g, '')) || 0;
            const amountB = parseFloat(String(b.precio_total || '0').replace(/[^0-9.-]+/g, '')) || 0;
            return amountA - amountB;
        } else if (sortBy.value === 'numero_desc') {
            return (parseInt(b.numero_factura) || 0) - (parseInt(a.numero_factura) || 0);
        } else if (sortBy.value === 'numero_asc') {
            return (parseInt(a.numero_factura) || 0) - (parseInt(b.numero_factura) || 0);
        }
        return 0;
    });

    return result;
});

// Live Metric Stats for filtered set
const filteredStats = computed(() => {
    let totalAmount = 0;
    let activeCount = 0;
    let anuladaCount = 0;

    filteredFacturas.value.forEach(f => {
        const st = (f.status || 'ACTIVA').toUpperCase();
        if (st === 'ANULADA') {
            anuladaCount++;
        } else {
            activeCount++;
            const val = parseFloat(String(f.precio_total || '0').replace(/[^0-9.-]+/g, '')) || 0;
            totalAmount += val;
        }
    });

    return {
        totalCount: filteredFacturas.value.length,
        activeCount,
        anuladaCount,
        totalAmountFormatted: totalAmount.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
    };
});

const editBilling = (id) => {
    router.visit(route('editBilling', { id }));
};

const devolucionFactura = (id) => {
    router.visit(route('returnBilling', { id }));
};

const visualizeFact = (id) => {
    window.open(route('billing.pdf', { id }), '_blank');
};

const visualizeWarranty = (id) => {
    window.open(route('billing.warranty', { id }), '_blank');
};

const buildQueryString = () => {
    const params = new URLSearchParams();
    if (startDate.value) params.append('fecha_inicio', startDate.value);
    if (endDate.value) params.append('fecha_fin', endDate.value);
    if (statusFilter.value !== 'ALL') params.append('status', statusFilter.value);
    if (productTypeFilter.value !== 'ALL') params.append('tipo', productTypeFilter.value);
    return params.toString();
};

const exportExcel = () => {
    const term = searchQuery.value.trim() || 'all';
    const qs = buildQueryString();
    window.location.href = `/report/reporteExcel/facturas/${term}${qs ? '?' + qs : ''}`;
};

const exportPdf = () => {
    const term = searchQuery.value.trim() || 'all';
    const qs = buildQueryString();
    window.location.href = `/report/reportePdf/facturas/${term}${qs ? '?' + qs : ''}`;
};
</script>

<template>
    <AppLayout title="Listado de Facturas">
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="font-black text-2xl text-gray-800 dark:text-white leading-tight flex items-center transition-colors">
                        <i class="fa-solid fa-file-invoice-dollar mr-3 text-indigo-500"></i>Historial de Facturación
                    </h2>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1">
                        Búsqueda profesional, filtros por fecha, tipo, monto y estado contable
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Quick Actions / Live Stats Banner -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Dynamic Filtered Invoices Metric Card -->
                    <div class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 p-6 rounded-[2rem] text-white shadow-xl shadow-indigo-500/20 transform hover:scale-[1.02] transition-all duration-300 relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-12 w-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                                <i class="fa-solid fa-receipt text-xl"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest bg-black/20 px-3 py-1 rounded-full border border-white/10">
                                {{ hasActiveFilters ? 'Resultado Filtrado' : 'Registro Global' }}
                            </span>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black mb-1 leading-none">{{ filteredStats.totalCount }}</span>
                            <span class="text-xs opacity-75 font-semibold">de {{ Facturas.length }} facturas</span>
                        </div>
                        <div class="mt-3 pt-3 border-t border-white/15 flex items-center justify-between text-[11px] font-bold">
                            <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-300"></i> {{ filteredStats.activeCount }} Activas</span>
                            <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-xmark text-rose-300"></i> {{ filteredStats.anuladaCount }} Anuladas</span>
                        </div>
                    </div>
                    
                    <!-- Dynamic Revenue Metric Card -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex flex-col justify-between group hover:border-indigo-500 transition-all transform hover:scale-[1.02]">
                        <div class="flex items-center justify-between mb-2">
                            <div class="h-12 w-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-wallet text-xl"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-500 bg-indigo-50 dark:bg-indigo-950/60 px-2.5 py-1 rounded-full">
                                Total Activo
                            </span>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Monto Acumulado (Bs.)</div>
                            <div class="text-2xl font-black text-gray-800 dark:text-white tracking-tight">
                                {{ filteredStats.totalAmountFormatted }} <span class="text-xs text-indigo-500 font-bold">Bs.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Export Actions Group Card -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex flex-col justify-between gap-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Reportes Dinámicos</span>
                            <span class="text-[10px] font-bold text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-md">
                                Exporta {{ filteredStats.totalCount }} reg.
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <button @click="exportExcel" class="flex items-center justify-center gap-2 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-600 dark:hover:text-white font-bold text-xs transition-all border border-emerald-200 dark:border-emerald-800/50 shadow-sm">
                                <i class="fa-solid fa-file-excel text-base"></i>
                                <span>Excel</span>
                            </button>
                            <button @click="exportPdf" class="flex items-center justify-center gap-2 p-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300 hover:bg-rose-600 hover:text-white dark:hover:bg-rose-600 dark:hover:text-white font-bold text-xs transition-all border border-rose-200 dark:border-rose-800/50 shadow-sm">
                                <i class="fa-solid fa-file-pdf text-base"></i>
                                <span>PDF</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- PROFESSIONAL SEARCH & FILTER CONTROL CENTER -->
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-xl border border-gray-100 dark:border-gray-700/60 transition-colors space-y-4">
                    
                    <!-- Top Control Row: Search + Status Pills + Toggle Advanced + Sort -->
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                        
                        <!-- Search Bar -->
                        <div class="relative flex-1 group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-magnifying-glass text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            </div>
                            <input 
                                type="search" 
                                v-model="searchQuery" 
                                class="block w-full pl-11 pr-10 py-3 border border-gray-200 dark:border-gray-700 rounded-2xl bg-gray-50/50 dark:bg-gray-900/50 text-gray-800 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-gray-900 transition-all text-sm font-medium shadow-inner" 
                                placeholder="Buscar por N° factura, cliente, cédula, correo, marca, modelo, tipo..."
                            >
                            <button 
                                v-if="searchQuery" 
                                @click="searchQuery = ''"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                            >
                                <i class="fa-solid fa-circle-xmark"></i>
                            </button>
                        </div>

                        <!-- Status Quick Pills -->
                        <div class="flex items-center p-1 bg-gray-100 dark:bg-gray-900/80 rounded-2xl border border-gray-200/50 dark:border-gray-700/50 self-start lg:self-auto overflow-x-auto max-w-full">
                            <button 
                                @click="statusFilter = 'ALL'"
                                :class="[
                                    'px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap',
                                    statusFilter === 'ALL' 
                                        ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow-sm' 
                                        : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200'
                                ]"
                            >
                                <span>Todas</span>
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    {{ statusCounts.all }}
                                </span>
                            </button>

                            <button 
                                @click="statusFilter = 'ACTIVA'"
                                :class="[
                                    'px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap',
                                    statusFilter === 'ACTIVA' 
                                        ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' 
                                        : 'text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400'
                                ]"
                            >
                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                <span>Activas</span>
                                <span :class="[
                                    'px-1.5 py-0.5 rounded-full text-[10px]',
                                    statusFilter === 'ACTIVA' ? 'bg-emerald-600 text-white' : 'bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400'
                                ]">
                                    {{ statusCounts.activas }}
                                </span>
                            </button>

                            <button 
                                @click="statusFilter = 'ANULADA'"
                                :class="[
                                    'px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap',
                                    statusFilter === 'ANULADA' 
                                        ? 'bg-rose-500 text-white shadow-md shadow-rose-500/20' 
                                        : 'text-gray-500 dark:text-gray-400 hover:text-rose-600 dark:hover:text-rose-400'
                                ]"
                            >
                                <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                <span>Anuladas</span>
                                <span :class="[
                                    'px-1.5 py-0.5 rounded-full text-[10px]',
                                    statusFilter === 'ANULADA' ? 'bg-rose-600 text-white' : 'bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400'
                                ]">
                                    {{ statusCounts.anuladas }}
                                </span>
                            </button>
                        </div>

                        <!-- Right Actions: Advanced Toggle & Sort -->
                        <div class="flex items-center gap-3">
                            <!-- Toggle Advanced Filters Button -->
                            <button 
                                @click="isAdvancedFiltersOpen = !isAdvancedFiltersOpen"
                                :class="[
                                    'px-4 py-3 rounded-2xl border text-xs font-bold flex items-center gap-2.5 transition-all shadow-sm',
                                    isAdvancedFiltersOpen || activeAdvancedCount > 0
                                        ? 'bg-indigo-50 dark:bg-indigo-950/60 border-indigo-300 dark:border-indigo-700 text-indigo-600 dark:text-indigo-400'
                                        : 'bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'
                                ]"
                            >
                                <i class="fa-solid fa-sliders"></i>
                                <span>Filtros Avanzados</span>
                                <span v-if="activeAdvancedCount > 0" class="h-5 w-5 rounded-full bg-indigo-600 text-white text-[10px] font-black flex items-center justify-center">
                                    {{ activeAdvancedCount }}
                                </span>
                                <i :class="['fa-solid fa-chevron-down transition-transform duration-200 text-[10px]', isAdvancedFiltersOpen ? 'rotate-180' : '']"></i>
                            </button>

                            <!-- Sort Selector -->
                            <div class="relative">
                                <select 
                                    v-model="sortBy"
                                    class="appearance-none pl-3 pr-8 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl text-xs font-bold text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer shadow-sm"
                                >
                                    <option value="fecha_desc">Fecha: Más Recientes</option>
                                    <option value="fecha_asc">Fecha: Más Antiguas</option>
                                    <option value="monto_desc">Monto: Mayor a Menor</option>
                                    <option value="monto_asc">Monto: Menor a Mayor</option>
                                    <option value="numero_desc">N° Factura: Mayor a Menor</option>
                                    <option value="numero_asc">N° Factura: Menor a Mayor</option>
                                </select>
                                <i class="fa-solid fa-sort absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                            </div>
                        </div>

                    </div>

                    <!-- Collapsible Advanced Filters Drawer -->
                    <div v-show="isAdvancedFiltersOpen" class="pt-4 border-t border-gray-100 dark:border-gray-700/60 space-y-4 animate-in fade-in duration-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            
                            <!-- Product Type Selector -->
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1.5">
                                    <i class="fa-solid fa-tags text-indigo-500 mr-1"></i>Tipo de Producto
                                </label>
                                <select 
                                    v-model="productTypeFilter" 
                                    class="w-full py-2.5 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-semibold text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all"
                                >
                                    <option value="ALL">Todos los Tipos</option>
                                    <option v-for="pt in availableProductTypes" :key="pt.name" :value="pt.name">
                                        {{ pt.name }} ({{ pt.count }})
                                    </option>
                                </select>
                            </div>

                            <!-- Date Range: From -->
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1.5">
                                    <i class="fa-solid fa-calendar-day text-indigo-500 mr-1"></i>Fecha Desde
                                </label>
                                <input 
                                    type="date" 
                                    v-model="startDate"
                                    class="w-full py-2 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-semibold text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all"
                                >
                            </div>

                            <!-- Date Range: To -->
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1.5">
                                    <i class="fa-solid fa-calendar-check text-indigo-500 mr-1"></i>Fecha Hasta
                                </label>
                                <input 
                                    type="date" 
                                    v-model="endDate"
                                    class="w-full py-2 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-semibold text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all"
                                >
                            </div>

                            <!-- Amount Range (Min & Max) -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1.5">
                                        Monto Min (Bs.)
                                    </label>
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        min="0"
                                        placeholder="0.00"
                                        v-model="minAmount"
                                        class="w-full py-2 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-semibold text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all"
                                    >
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1.5">
                                        Monto Max (Bs.)
                                    </label>
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        min="0"
                                        placeholder="Máx"
                                        v-model="maxAmount"
                                        class="w-full py-2 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-semibold text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all"
                                    >
                                </div>
                            </div>

                        </div>

                        <!-- Date Quick Presets Toolbar -->
                        <div class="flex items-center gap-2 pt-2 overflow-x-auto">
                            <span class="text-[10px] font-black uppercase text-gray-400 dark:text-gray-500 tracking-wider mr-1">Rápido:</span>
                            <button @click="setDatePreset('today')" class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-[11px] font-bold hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-400 transition-all">
                                Hoy
                            </button>
                            <button @click="setDatePreset('week')" class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-[11px] font-bold hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-400 transition-all">
                                Esta Semana
                            </button>
                            <button @click="setDatePreset('month')" class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-[11px] font-bold hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-400 transition-all">
                                Este Mes
                            </button>
                            <button @click="setDatePreset('year')" class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-[11px] font-bold hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-400 transition-all">
                                Este Año
                            </button>
                            <button v-if="startDate || endDate" @click="setDatePreset('clear')" class="px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-[11px] font-bold hover:bg-rose-100 transition-all">
                                Limpiar Fechas
                            </button>
                        </div>
                    </div>

                    <!-- Active Filter Chips / Tags Bar -->
                    <div v-if="hasActiveFilters" class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700/60 text-xs">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-1">Filtros Activos:</span>
                            
                            <span v-if="searchQuery" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-medium">
                                Texto: "{{ searchQuery }}"
                                <button @click="searchQuery = ''" class="hover:text-rose-500"><i class="fa-solid fa-xmark"></i></button>
                            </span>

                            <span v-if="statusFilter !== 'ALL'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-medium">
                                Estado: {{ statusFilter }}
                                <button @click="statusFilter = 'ALL'" class="hover:text-rose-500"><i class="fa-solid fa-xmark"></i></button>
                            </span>

                            <span v-if="productTypeFilter !== 'ALL'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 font-medium">
                                Tipo: {{ productTypeFilter }}
                                <button @click="productTypeFilter = 'ALL'" class="hover:text-rose-500"><i class="fa-solid fa-xmark"></i></button>
                            </span>

                            <span v-if="startDate || endDate" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 font-medium">
                                Fecha: {{ startDate || '...' }} a {{ endDate || '...' }}
                                <button @click="startDate = ''; endDate = ''" class="hover:text-rose-500"><i class="fa-solid fa-xmark"></i></button>
                            </span>

                            <span v-if="minAmount !== '' || maxAmount !== ''" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-medium">
                                Monto: {{ minAmount || '0' }} - {{ maxAmount || '∞' }} Bs.
                                <button @click="minAmount = ''; maxAmount = ''" class="hover:text-rose-500"><i class="fa-solid fa-xmark"></i></button>
                            </span>
                        </div>

                        <button 
                            @click="resetFilters" 
                            class="text-xs font-bold text-rose-500 hover:text-rose-600 hover:underline flex items-center gap-1 shrink-0 ml-2"
                        >
                            <i class="fa-solid fa-rotate-left"></i> Limpiar Todo
                        </button>
                    </div>

                </div>

                <!-- Main Invoices Table Card -->
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
                                            <div class="text-xs font-black text-gray-700 dark:text-gray-300 uppercase truncate max-w-[200px]" v-if="getItem(factura).tipo">
                                                {{ getItem(factura).tipo }} {{ getItem(factura).marca }} {{ getItem(factura).modelo }}
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
                                            <button v-if="getItem(factura).tipo && String(getItem(factura).tipo).toUpperCase().includes('MOTOR')" @click="visualizeWarranty(factura.id)" class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 hover:bg-white dark:hover:bg-gray-700 hover:text-amber-500 hover:shadow-xl hover:shadow-amber-500/20 transition-all transform hover:scale-110 active:scale-95" title="Póliza de Garantía">
                                                <i class="fa-solid fa-shield-halved text-sm"></i>
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
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center gap-4 max-w-sm mx-auto">
                                            <div class="h-20 w-20 rounded-[2rem] bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center border-2 border-dashed border-indigo-200 dark:border-indigo-800 text-indigo-500">
                                                <i class="fa-solid fa-filter-circle-xmark text-4xl"></i>
                                            </div>
                                            <div class="space-y-1">
                                                <h3 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-tight">Sin coincidencias</h3>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                                    No se encontraron facturas con los filtros aplicados actualmente.
                                                </p>
                                            </div>
                                            <button 
                                                @click="resetFilters" 
                                                class="mt-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg shadow-indigo-500/20 transition-all"
                                            >
                                                <i class="fa-solid fa-rotate-left mr-2"></i>Restablecer Filtros
                                            </button>
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

                <!-- Document Success Modal -->
                <div v-if="docModal.show" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/70 backdrop-blur-md transition-all animate-in fade-in duration-300">
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] w-full max-w-lg shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden transform animate-in zoom-in-95 duration-300">
                        <div class="p-8 text-center">
                            <div class="h-20 w-20 bg-emerald-50 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-5 text-emerald-500 border-4 border-emerald-100 dark:border-emerald-900/30 transition-transform scale-110">
                                <i class="fa-solid fa-circle-check text-4xl"></i>
                            </div>
                            
                            <h3 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tight mb-2">
                                ¡Venta Registrada Exitosamente!
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider mb-6 font-semibold">
                                Los documentos oficiales están listos para ser visualizados o impresos:
                            </p>

                            <div class="space-y-3 mb-6">
                                <!-- Invoice Buttons -->
                                <div v-for="id in docModal.billingIds" :key="'bill-' + id" class="flex gap-2">
                                    <a 
                                        :href="route('billing.pdf', id)" 
                                        target="_blank"
                                        class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold py-3.5 px-4 rounded-2xl shadow-lg shadow-indigo-500/20 transition-all transform active:scale-98 text-sm"
                                    >
                                        <i class="fa-solid fa-file-invoice text-lg"></i>
                                        <span>Ver / Imprimir Factura #{{ String(id).padStart(6, '0') }}</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-xs opacity-70 ml-1"></i>
                                    </a>
                                </div>

                                <!-- Warranty Buttons -->
                                <div v-for="id in docModal.warrantyIds" :key="'warranty-' + id" class="flex gap-2">
                                    <a 
                                        :href="route('billing.warranty', id)" 
                                        target="_blank"
                                        class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-bold py-3.5 px-4 rounded-2xl shadow-lg shadow-amber-500/20 transition-all transform active:scale-98 text-sm"
                                    >
                                        <i class="fa-solid fa-shield-halved text-lg"></i>
                                        <span>Ver / Imprimir Póliza de Garantía</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-xs opacity-70 ml-1"></i>
                                    </a>
                                </div>

                                <!-- Open All Button if both exist -->
                                <button 
                                    v-if="docModal.billingIds.length > 0 && docModal.warrantyIds.length > 0"
                                    @click="openAllDocs"
                                    type="button"
                                    class="w-full flex items-center justify-center gap-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold py-2.5 px-4 rounded-xl transition-all text-xs uppercase tracking-wider"
                                >
                                    <i class="fa-solid fa-folder-open"></i>
                                    <span>Abrir Ambos Documentos (PDF)</span>
                                </button>
                            </div>

                            <button 
                                @click="closeDocModal" 
                                class="w-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold py-3.5 rounded-2xl transition-all text-sm"
                            >
                                Listo, Continuar
                            </button>
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