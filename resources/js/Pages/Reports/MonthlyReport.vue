<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    initialStartMonth: Number,
    initialEndMonth: Number,
    initialYear: Number,
    initialGroupingMode: String,
    reportData: Object,
});

const selectedStartMonth = ref(props.initialStartMonth || new Date().getMonth() + 1);
const selectedEndMonth = ref(props.initialEndMonth || selectedStartMonth.value);
const selectedYear = ref(props.initialYear || new Date().getFullYear());
const selectedGroupingMode = ref(props.initialGroupingMode || 'base');
const searchQuery = ref('');
const isLoading = ref(false);
const currentReportData = ref(props.reportData);

const months = [
    { value: 1, label: 'Enero' },
    { value: 2, label: 'Febrero' },
    { value: 3, label: 'Marzo' },
    { value: 4, label: 'Abril' },
    { value: 5, label: 'Mayo' },
    { value: 6, label: 'Junio' },
    { value: 7, label: 'Julio' },
    { value: 8, label: 'Agosto' },
    { value: 9, label: 'Septiembre' },
    { value: 10, label: 'Octubre' },
    { value: 11, label: 'Noviembre' },
    { value: 12, label: 'Diciembre' },
];

const availableYears = computed(() => {
    const currentYr = new Date().getFullYear();
    const years = [];
    for (let y = currentYr - 2; y <= currentYr + 2; y++) {
        years.push(y);
    }
    return years;
});

const handleStartMonthChange = () => {
    if (selectedEndMonth.value < selectedStartMonth.value) {
        selectedEndMonth.value = selectedStartMonth.value;
    }
    fetchReportData();
};

const handleEndMonthChange = () => {
    if (selectedEndMonth.value < selectedStartMonth.value) {
        selectedStartMonth.value = selectedEndMonth.value;
    }
    fetchReportData();
};

const setPresetRange = (preset) => {
    const curMonth = new Date().getMonth() + 1;
    if (preset === 'single') {
        selectedStartMonth.value = curMonth;
        selectedEndMonth.value = curMonth;
    } else if (preset === 'bimonthly_mobile') {
        selectedStartMonth.value = Math.max(1, curMonth - 1);
        selectedEndMonth.value = curMonth;
    } else if (preset === 'bimonthly_aug_sep') {
        selectedStartMonth.value = 8; // Agosto
        selectedEndMonth.value = 9;   // Septiembre
    } else if (preset === 'quarterly') {
        selectedStartMonth.value = Math.max(1, curMonth - 2);
        selectedEndMonth.value = curMonth;
    }
    fetchReportData();
};

const fetchReportData = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(route('reports.monthly.data'), {
            params: {
                start_month: selectedStartMonth.value,
                end_month: selectedEndMonth.value,
                year: selectedYear.value,
                grouping_mode: selectedGroupingMode.value,
            }
        });
        currentReportData.value = response.data;
    } catch (error) {
        console.error('Error al cargar datos del reporte:', error);
    } finally {
        isLoading.value = false;
    }
};

const filteredBrands = computed(() => {
    if (!currentReportData.value) return [];
    
    const brandsList = currentReportData.value.brands || [];
    if (brandsList.length === 0 && currentReportData.value.items) {
        return [{
            brand: 'GENERAL',
            items: currentReportData.value.items,
            totales: currentReportData.value.totales || {}
        }];
    }

    if (!searchQuery.value.trim()) {
        return brandsList;
    }

    const query = searchQuery.value.toLowerCase();

    return brandsList.map(bGroup => {
        const matchingItems = (bGroup.items || []).filter(item =>
            (item.code && item.code.toLowerCase().includes(query)) ||
            (item.marca && item.marca.toLowerCase().includes(query)) ||
            (item.modelo && item.modelo.toLowerCase().includes(query)) ||
            (item.description && item.description.toLowerCase().includes(query)) ||
            (item.containers_str && item.containers_str.toLowerCase().includes(query)) ||
            (bGroup.brand && bGroup.brand.toLowerCase().includes(query))
        );

        if (matchingItems.length === 0) return null;

        return {
            ...bGroup,
            items: matchingItems,
            totales: {
                unidades_inicial: matchingItems.reduce((acc, i) => acc + (i.unidades_inicial || 0), 0),
                unidades_entradas: matchingItems.reduce((acc, i) => acc + (i.unidades_entradas || 0), 0),
                unidades_salidas: matchingItems.reduce((acc, i) => acc + (i.unidades_salidas || 0), 0),
                unidades_retiros: matchingItems.reduce((acc, i) => acc + (i.unidades_retiros || 0), 0),
                unidades_autoconsumo: matchingItems.reduce((acc, i) => acc + (i.unidades_autoconsumo || 0), 0),
                unidades_final: matchingItems.reduce((acc, i) => acc + (i.unidades_final || 0), 0),
                valores_inicial: matchingItems.reduce((acc, i) => acc + (i.valores_inicial || 0), 0),
                valores_entradas: matchingItems.reduce((acc, i) => acc + (i.valores_entradas || 0), 0),
                valores_salidas: matchingItems.reduce((acc, i) => acc + (i.valores_salidas || 0), 0),
                valores_retiros: matchingItems.reduce((acc, i) => acc + (i.valores_retiros || 0), 0),
                valores_autoconsumo: matchingItems.reduce((acc, i) => acc + (i.valores_autoconsumo || 0), 0),
                valores_final: matchingItems.reduce((acc, i) => acc + (i.valores_final || 0), 0),
            }
        };
    }).filter(Boolean);
});

const failedLogos = ref({});

const getBrandSlug = (brandName) => {
    if (!brandName) return null;
    const b = brandName.toLowerCase();
    
    const slugMap = {
        'chevrolet': 'chevrolet',
        'chevy': 'chevrolet',
        'ford': 'ford',
        'toyota': 'toyota',
        'totota': 'toyota',
        'jeep': 'jeep',
        'hyundai': 'hyundai',
        'kia': 'kia',
        'nissan': 'nissan',
        'mitsubishi': 'mitsubishi',
        'dodge': 'dodge',
        'ram': 'ram',
        'chrysler': 'chrysler',
        'honda': 'honda',
        'mazda': 'mazda',
        'isuzu': 'isuzu',
        'volkswagen': 'volkswagen',
        'vw': 'volkswagen',
        'cummins': 'cummins',
        'mack': 'mack',
        'international': 'international',
        'daewoo': 'daewoo',
        'caterpillar': 'caterpillar',
        'cat': 'caterpillar',
        'fiat': 'fiat',
        'mercedes': 'mercedes',
        'mini': 'mini',
        'suzuki': 'suzuki',
        'bmw': 'bmw',
        'audi': 'audi',
        'volvo': 'volvo',
        'peugeot': 'peugeot',
        'renault': 'renault',
        'caribe': 'isuzu',
    };

    for (const key in slugMap) {
        if (b.includes(key)) return slugMap[key];
    }
    return null;
};

const getBrandLogoUrl = (brandName) => {
    const slug = getBrandSlug(brandName);
    if (!slug || failedLogos.value[brandName]) return null;

    if (slug === 'cummins') return '/cummins-logo.svg';
    if (slug === 'international') return '/international-logo.svg';
    if (slug === 'mack') return '/mack-logo.svg';
    if (slug === 'daewoo') return '/daewoo-logo.svg';

    if (slug === 'caterpillar') {
        return `https://cdn.simpleicons.org/${slug}/000000`;
    }

    return `https://cdn.simpleicons.org/${slug}/ffffff`;
};

const handleLogoError = (brandName) => {
    failedLogos.value[brandName] = true;
};

const getBrandBadge = (brandName) => {
    const b = (brandName || '').toUpperCase();
    if (b.includes('CHEVROLET')) {
        return { color: 'bg-amber-500 text-white border-amber-400', icon: 'fa-solid fa-car', logoText: 'CHEVROLET' };
    } else if (b.includes('FORD')) {
        return { color: 'bg-blue-600 text-white border-blue-500', icon: 'fa-solid fa-truck-pickup', logoText: 'FORD' };
    } else if (b.includes('TOYOTA')) {
        return { color: 'bg-red-600 text-white border-red-500', icon: 'fa-solid fa-car-side', logoText: 'TOYOTA' };
    } else if (b.includes('JEEP')) {
        return { color: 'bg-emerald-600 text-white border-emerald-500', icon: 'fa-solid fa-truck-monster', logoText: 'JEEP' };
    } else if (b.includes('HYUNDAI/KIA')) {
        return { color: 'bg-sky-600 text-white border-sky-500', icon: 'fa-solid fa-car-side', logoText: 'HYUNDAI/KIA' };
    } else if (b.includes('HYUNDAI')) {
        return { color: 'bg-sky-500 text-white border-sky-400', icon: 'fa-solid fa-car-rear', logoText: 'HYUNDAI' };
    } else if (b.includes('KIA')) {
        return { color: 'bg-rose-700 text-white border-rose-600', icon: 'fa-solid fa-car', logoText: 'KIA' };
    } else if (b.includes('NISSAN')) {
        return { color: 'bg-slate-600 text-white border-slate-500', icon: 'fa-solid fa-car', logoText: 'NISSAN' };
    } else if (b.includes('MITSUBISHI')) {
        return { color: 'bg-rose-600 text-white border-rose-500', icon: 'fa-solid fa-gem', logoText: 'MITSUBISHI' };
    } else if (b.includes('DODGE') || b.includes('RAM')) {
        return { color: 'bg-red-700 text-white border-red-600', icon: 'fa-solid fa-shield-halved', logoText: 'DODGE' };
    } else if (b.includes('CHRYSLER')) {
        return { color: 'bg-slate-700 text-white border-slate-600', icon: 'fa-solid fa-car', logoText: 'CHRYSLER' };
    } else if (b.includes('HONDA')) {
        return { color: 'bg-red-800 text-white border-red-700', icon: 'fa-solid fa-h', logoText: 'HONDA' };
    } else if (b.includes('MAZDA')) {
        return { color: 'bg-indigo-600 text-white border-indigo-500', icon: 'fa-solid fa-car', logoText: 'MAZDA' };
    } else if (b.includes('ISUZU')) {
        return { color: 'bg-red-600 text-white border-red-500', icon: 'fa-solid fa-truck', logoText: 'ISUZU' };
    } else if (b.includes('VOLKSWAGEN') || b.includes('VW')) {
        return { color: 'bg-cyan-700 text-white border-cyan-600', icon: 'fa-solid fa-car', logoText: 'VOLKSWAGEN' };
    } else if (b.includes('CUMMINS')) {
        return { color: 'bg-black text-red-500 border border-red-600', icon: 'fa-solid fa-gears', logoText: 'CUMMINS' };
    } else if (b.includes('MACK')) {
        return { color: 'bg-slate-800 text-amber-400 border border-amber-500', icon: 'fa-solid fa-truck-front', logoText: 'MACK' };
    } else if (b.includes('INTERNATIONAL')) {
        return { color: 'bg-orange-700 text-white border-orange-600', icon: 'fa-solid fa-truck-moving', logoText: 'INTERNATIONAL' };
    } else if (b.includes('DAEWOO')) {
        return { color: 'bg-blue-800 text-white border-blue-700', icon: 'fa-solid fa-car', logoText: 'DAEWOO' };
    } else if (b.includes('CARIBE')) {
        return { color: 'bg-amber-700 text-white border-amber-600', icon: 'fa-solid fa-truck-pickup', logoText: 'CARIBE' };
    } else if (b.includes('CATERPILLAR') || b.includes('CAT')) {
        return { color: 'bg-yellow-500 text-black font-bold border-black', icon: 'fa-solid fa-tractor', logoText: 'CATERPILLAR' };
    } else if (b.includes('FIAT')) {
        return { color: 'bg-red-600 text-white border-red-500', icon: 'fa-solid fa-car', logoText: 'FIAT' };
    } else if (b.includes('MERCEDES')) {
        return { color: 'bg-zinc-700 text-white border-zinc-600', icon: 'fa-solid fa-star', logoText: 'MERCEDES-BENZ' };
    } else if (b.includes('MINI')) {
        return { color: 'bg-zinc-800 text-white border-zinc-700', icon: 'fa-solid fa-car', logoText: 'MINI' };
    }
    return { color: 'bg-slate-700 text-white border-slate-600', icon: 'fa-solid fa-tag', logoText: b || 'MARCA' };
};

const formatBs = (val) => {
    return new Intl.NumberFormat('es-VE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(val || 0);
};

const formatNum = (val) => {
    return new Intl.NumberFormat('es-VE').format(val || 0);
};

const exportPdf = () => {
    const url = route('reports.monthly.pdf', {
        start_month: selectedStartMonth.value,
        end_month: selectedEndMonth.value,
        year: selectedYear.value,
        grouping_mode: selectedGroupingMode.value,
    });
    window.open(url, '_blank');
};

const exportExcel = () => {
    const url = route('reports.monthly.excel', {
        start_month: selectedStartMonth.value,
        end_month: selectedEndMonth.value,
        year: selectedYear.value,
        grouping_mode: selectedGroupingMode.value,
    });
    window.open(url, '_blank');
};

const goBack = () => {
    router.visit(route('reports.index'));
};
</script>

<template>
    <AppLayout title="Reporte de Inventario por Período">
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center">
                        <i class="fa-solid fa-book-bookmark mr-3 text-indigo-600 dark:text-indigo-400"></i>
                        Reporte de Inventario {{ selectedStartMonth === selectedEndMonth ? 'Mensual' : 'por Período' }} (Libro SENIAT)
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-semibold">
                        Selecciona libremente cualquier mes o rango de meses (ej: Agosto - Septiembre)
                    </p>
                </div>
                <button 
                    @click="goBack" 
                    class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold py-2 px-4 rounded-xl text-sm transition-all flex items-center gap-2"
                >
                    <i class="fa-solid fa-arrow-left"></i> Volver a Reportes
                </button>
            </div>
        </template>

        <div class="py-6">
            <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Control Panel / Selectors -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                        
                        <!-- Month Range, Year & Grouping Mode Selectors -->
                        <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto">
                            
                            <!-- Selector Mes Desde -->
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 dark:text-gray-400 mb-1">
                                    <i class="fa-solid fa-calendar-days mr-1 text-indigo-500"></i>Mes Desde
                                </label>
                                <select 
                                    v-model="selectedStartMonth" 
                                    @change="handleStartMonthChange"
                                    class="bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white font-semibold text-sm rounded-xl border border-gray-200 dark:border-gray-600 py-2.5 px-4 focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                                >
                                    <option v-for="m in months" :key="'start-' + m.value" :value="m.value">{{ m.label }}</option>
                                </select>
                            </div>

                            <!-- Selector Mes Hasta -->
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 dark:text-gray-400 mb-1">
                                    <i class="fa-solid fa-calendar-week mr-1 text-indigo-500"></i>Mes Hasta
                                </label>
                                <select 
                                    v-model="selectedEndMonth" 
                                    @change="handleEndMonthChange"
                                    class="bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white font-semibold text-sm rounded-xl border border-gray-200 dark:border-gray-600 py-2.5 px-4 focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                                >
                                    <option v-for="m in months" :key="'end-' + m.value" :value="m.value">{{ m.label }}</option>
                                </select>
                            </div>

                            <!-- Selector de Año -->
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 dark:text-gray-400 mb-1">
                                    <i class="fa-solid fa-calendar mr-1 text-indigo-500"></i>Año
                                </label>
                                <select 
                                    v-model="selectedYear" 
                                    @change="fetchReportData"
                                    class="bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white font-semibold text-sm rounded-xl border border-gray-200 dark:border-gray-600 py-2.5 px-4 focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                                >
                                    <option v-for="y in availableYears" :key="y" :value="y">{{ y }}</option>
                                </select>
                            </div>

                            <!-- Selector de Agrupación -->
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 dark:text-gray-400 mb-1">
                                    <i class="fa-solid fa-layer-group mr-1 text-indigo-500"></i>Agrupación
                                </label>
                                <select 
                                    v-model="selectedGroupingMode" 
                                    @change="fetchReportData"
                                    class="bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white font-semibold text-sm rounded-xl border border-gray-200 dark:border-gray-600 py-2.5 px-4 focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                                >
                                    <option value="base">Por Modelo General (Ej: CHEVROLET 5.3L)</option>
                                    <option value="exact">Por Modelo Exacto (Con variantes L83, IV GEN)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Action Export Buttons -->
                        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                            <button 
                                @click="exportPdf"
                                class="flex-1 sm:flex-none bg-rose-600 hover:bg-rose-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-md shadow-rose-100 dark:shadow-none transition-all flex items-center justify-center gap-2 text-sm"
                            >
                                <i class="fa-solid fa-file-pdf"></i> PDF Horizontal
                            </button>

                            <button 
                                @click="exportExcel"
                                class="flex-1 sm:flex-none bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-md shadow-emerald-100 dark:shadow-none transition-all flex items-center justify-center gap-2 text-sm"
                            >
                                <i class="fa-solid fa-file-excel"></i> Excel (.xlsx)
                            </button>
                        </div>
                    </div>

                    <!-- Preset Period Range Shortcuts -->
                    <div class="flex items-center gap-2 pt-2 border-t border-gray-100 dark:border-gray-700/60 overflow-x-auto">
                        <span class="text-[10px] font-black uppercase text-gray-400 dark:text-gray-500 tracking-wider mr-1">Rápido:</span>
                        <button @click="setPresetRange('single')" class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-[11px] font-bold hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-400 transition-all">
                            Mes Único
                        </button>
                        <button @click="setPresetRange('bimonthly_aug_sep')" class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-300 text-[11px] font-bold hover:bg-indigo-100 transition-all">
                            Agosto - Septiembre
                        </button>
                        <button @click="setPresetRange('bimonthly_mobile')" class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-[11px] font-bold hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-400 transition-all">
                            Últimos 2 Meses
                        </button>
                        <button @click="setPresetRange('quarterly')" class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-[11px] font-bold hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/40 dark:hover:text-indigo-400 transition-all">
                            Últimos 3 Meses
                        </button>
                    </div>
                </div>

                <!-- KPI Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-xs uppercase tracking-wider font-bold text-gray-400 mb-1">Existencia Final (Unidades)</div>
                        <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400">
                            {{ formatNum(currentReportData?.totales?.unidades_final) }}
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-xs uppercase tracking-wider font-bold text-gray-400 mb-1">Existencia Final (Valores Bs.)</div>
                        <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                            Bs. {{ formatBs(currentReportData?.totales?.valores_final) }}
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-xs uppercase tracking-wider font-bold text-gray-400 mb-1">Entradas del Período (Unid.)</div>
                        <div class="text-2xl font-black text-blue-600 dark:text-blue-400">
                            +{{ formatNum(currentReportData?.totales?.unidades_entradas) }}
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="text-xs uppercase tracking-wider font-bold text-gray-400 mb-1">Salidas del Período (Ventas Unid.)</div>
                        <div class="text-2xl font-black text-amber-600 dark:text-amber-400">
                            -{{ formatNum(currentReportData?.totales?.unidades_salidas) }}
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    
                    <!-- Search Bar Header -->
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h3 class="font-bold text-lg text-gray-800 dark:text-white">
                                    Detalle del Inventario Agrupado por Marcas
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Movimientos de {{ currentReportData?.periodName || currentReportData?.monthName }} {{ currentReportData?.year }} (Marca &rarr; Tipo &rarr; Modelo &rarr; Alfabético)
                                </p>
                            </div>

                            <div class="w-full sm:w-80 relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-gray-400"></i>
                                <input 
                                    v-model="searchQuery" 
                                    type="text" 
                                    placeholder="Buscar por marca, modelo o contenedor..."
                                    class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white text-xs rounded-xl border border-gray-200 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 outline-none"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div v-if="isLoading" class="p-12 text-center text-gray-500">
                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-indigo-600 mb-3"></i>
                        <p class="text-sm font-semibold">Procesando y agrupando movimientos del inventario por marcas...</p>
                    </div>

                    <!-- Main Data Table -->
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <!-- Top Headers Row -->
                                <tr class="bg-gray-100 dark:bg-gray-900/80 text-gray-700 dark:text-gray-300 font-bold uppercase tracking-wider text-[11px] border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-3 px-3 border-r border-gray-200 dark:border-gray-700 min-w-[110px]">Marca</th>
                                    <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 min-w-[160px]">Tipo de Producto</th>
                                    <th class="py-3 px-3 border-r border-gray-200 dark:border-gray-700 min-w-[120px]">Modelo</th>
                                    <th colspan="6" class="py-3 px-3 text-center bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 border-r border-gray-200 dark:border-gray-700">Unidades (Físicas)</th>
                                    <th colspan="6" class="py-3 px-3 text-center bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300">Valores en Bolívares (Bs.)</th>
                                </tr>

                                <!-- Sub-Headers Row -->
                                <tr class="bg-gray-50 dark:bg-gray-800 text-[10px] uppercase font-bold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-2.5 px-3 border-r border-gray-200 dark:border-gray-700"></th>
                                    <th class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700"></th>
                                    <th class="py-2.5 px-3 border-r border-gray-200 dark:border-gray-700"></th>

                                    <!-- Unidades Subheaders -->
                                    <th class="py-2.5 px-2 text-right bg-sky-50/50 dark:bg-sky-950/20 text-sky-800 dark:text-sky-300">Inicial</th>
                                    <th class="py-2.5 px-2 text-right bg-sky-50/50 dark:bg-sky-950/20 text-sky-800 dark:text-sky-300">Entradas</th>
                                    <th class="py-2.5 px-2 text-right bg-sky-50/50 dark:bg-sky-950/20 text-sky-800 dark:text-sky-300">Salidas</th>
                                    <th class="py-2.5 px-2 text-right bg-sky-50/50 dark:bg-sky-950/20 text-sky-800 dark:text-sky-300">Retiros</th>
                                    <th class="py-2.5 px-2 text-right bg-sky-50/50 dark:bg-sky-950/20 text-sky-800 dark:text-sky-300">Autocons.</th>
                                    <th class="py-2.5 px-2 text-right bg-sky-100/70 dark:bg-sky-900/50 text-sky-900 dark:text-sky-200 font-black border-r border-gray-200 dark:border-gray-700">Final</th>

                                    <!-- Valores Subheaders -->
                                    <th class="py-2.5 px-2 text-right bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300">Inicial</th>
                                    <th class="py-2.5 px-2 text-right bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300">Entradas</th>
                                    <th class="py-2.5 px-2 text-right bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300">Salidas</th>
                                    <th class="py-2.5 px-2 text-right bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300">Retiros</th>
                                    <th class="py-2.5 px-2 text-right bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300">Autocons.</th>
                                    <th class="py-2.5 px-2 text-right bg-emerald-100/70 dark:bg-emerald-900/50 text-emerald-900 dark:text-emerald-200 font-black">Final</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template v-for="(bGroup, bIdx) in filteredBrands" :key="bIdx">
                                    <!-- Brand Section Header Row -->
                                    <tr class="bg-slate-800 dark:bg-slate-950 text-white font-black text-xs tracking-wider uppercase border-t-2 border-slate-700">
                                        <td colspan="15" class="py-3 px-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <!-- Brand Badge with Official SVG Logo -->
                                                    <span :class="[getBrandBadge(bGroup.brand).color, 'px-3 py-1.5 rounded-lg text-xs font-black flex items-center gap-2 shadow-sm border']">
                                                        <img 
                                                            v-if="getBrandLogoUrl(bGroup.brand)" 
                                                            :src="getBrandLogoUrl(bGroup.brand)" 
                                                            :alt="bGroup.brand"
                                                            class="w-4 h-4 object-contain inline-block filter drop-shadow-sm" 
                                                            @error="handleLogoError(bGroup.brand)"
                                                        />
                                                        <i v-else :class="getBrandBadge(bGroup.brand).icon"></i>
                                                        <span>{{ bGroup.brand }}</span>
                                                    </span>
                                                    <span class="text-slate-300 text-xs font-semibold">
                                                        ({{ bGroup.items.length }} {{ bGroup.items.length === 1 ? 'modelo' : 'modelos' }})
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-4 text-[11px] font-semibold text-slate-300">
                                                    <span>Final Unid: <strong class="text-sky-300 font-mono">{{ formatNum(bGroup.totales.unidades_final) }}</strong></span>
                                                    <span>Total Bs: <strong class="text-emerald-300 font-mono">Bs. {{ formatBs(bGroup.totales.valores_final) }}</strong></span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Brand Items -->
                                    <tr v-for="(item, idx) in bGroup.items" :key="idx" class="hover:bg-gray-50/80 dark:hover:bg-gray-700/50 transition-colors align-top">
                                        <td class="py-3 px-3 font-semibold text-gray-900 dark:text-white border-r border-gray-100 dark:border-gray-700 text-center">{{ item.marca }}</td>
                                        <td class="py-3 px-4 font-medium text-gray-800 dark:text-gray-200 border-r border-gray-100 dark:border-gray-700">{{ item.description }}</td>
                                        <td class="py-3 px-3 font-mono font-bold text-gray-900 dark:text-white border-r border-gray-100 dark:border-gray-700 text-center">{{ item.modelo }}</td>

                                        <!-- Unidades -->
                                        <td class="py-3 px-2 text-right font-mono text-gray-600 dark:text-gray-300">{{ formatNum(item.unidades_inicial) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-blue-600 dark:text-blue-400 font-semibold">{{ formatNum(item.unidades_entradas) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-amber-600 dark:text-amber-400 font-semibold">{{ formatNum(item.unidades_salidas) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-rose-600 dark:text-rose-400">{{ formatNum(item.unidades_retiros) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-purple-600 dark:text-purple-400">{{ formatNum(item.unidades_autoconsumo) }}</td>
                                        <td class="py-3 px-2 text-right font-mono font-black text-gray-900 dark:text-white bg-sky-50/40 dark:bg-sky-950/20 border-r border-gray-100 dark:border-gray-700">{{ formatNum(item.unidades_final) }}</td>

                                        <!-- Valores Bs -->
                                        <td class="py-3 px-2 text-right font-mono text-gray-600 dark:text-gray-300">{{ formatBs(item.valores_inicial) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-blue-600 dark:text-blue-400">{{ formatBs(item.valores_entradas) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-amber-600 dark:text-amber-400">{{ formatBs(item.valores_salidas) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-rose-600 dark:text-rose-400">{{ formatBs(item.valores_retiros) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-purple-600 dark:text-purple-400">{{ formatBs(item.valores_autoconsumo) }}</td>
                                        <td class="py-3 px-2 text-right font-mono font-black text-emerald-700 dark:text-emerald-400 bg-emerald-50/40 dark:bg-emerald-950/20">{{ formatBs(item.valores_final) }}</td>
                                    </tr>

                                    <!-- Brand Subtotal Row -->
                                    <tr class="bg-gray-100/90 dark:bg-gray-800/90 font-bold text-gray-900 dark:text-white text-xs border-b border-gray-200 dark:border-gray-700">
                                        <td colspan="3" class="py-3 px-4 text-right font-black uppercase border-r border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                                            Subtotal {{ bGroup.brand }}
                                        </td>
                                        <td class="py-3 px-2 text-right font-mono">{{ formatNum(bGroup.totales.unidades_inicial) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-blue-600 dark:text-blue-400">{{ formatNum(bGroup.totales.unidades_entradas) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-amber-600 dark:text-amber-400">{{ formatNum(bGroup.totales.unidades_salidas) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-rose-600 dark:text-rose-400">{{ formatNum(bGroup.totales.unidades_retiros) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-purple-600 dark:text-purple-400">{{ formatNum(bGroup.totales.unidades_autoconsumo) }}</td>
                                        <td class="py-3 px-2 text-right font-mono font-black border-r border-gray-200 dark:border-gray-700 bg-sky-100/40 dark:bg-sky-900/30">{{ formatNum(bGroup.totales.unidades_final) }}</td>

                                        <td class="py-3 px-2 text-right font-mono">{{ formatBs(bGroup.totales.valores_inicial) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-blue-600 dark:text-blue-400">{{ formatBs(bGroup.totales.valores_entradas) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-amber-600 dark:text-amber-400">{{ formatBs(bGroup.totales.valores_salidas) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-rose-600 dark:text-rose-400">{{ formatBs(bGroup.totales.valores_retiros) }}</td>
                                        <td class="py-3 px-2 text-right font-mono text-purple-600 dark:text-purple-400">{{ formatBs(bGroup.totales.valores_autoconsumo) }}</td>
                                        <td class="py-3 px-2 text-right font-mono font-black text-emerald-600 dark:text-emerald-400 bg-emerald-100/40 dark:bg-emerald-900/30">{{ formatBs(bGroup.totales.valores_final) }}</td>
                                    </tr>
                                </template>

                                <tr v-if="filteredBrands.length === 0">
                                    <td colspan="15" class="p-8 text-center text-gray-400 dark:text-gray-500">
                                        No se encontraron marcas ni ítems para la búsqueda o el período seleccionado.
                                    </td>
                                </tr>
                            </tbody>

                            <!-- Totals Footer Row -->
                            <tfoot>
                                <tr class="bg-gray-100 dark:bg-gray-900 font-bold text-gray-900 dark:text-white text-xs border-t-2 border-gray-300 dark:border-gray-600">
                                    <td colspan="3" class="py-4 px-4 text-center font-black uppercase border-r border-gray-200 dark:border-gray-700">Totales Generales</td>
                                    
                                    <td class="py-4 px-2 text-right font-mono">{{ formatNum(currentReportData?.totales?.unidades_inicial) }}</td>
                                    <td class="py-4 px-2 text-right font-mono text-blue-600 dark:text-blue-400">{{ formatNum(currentReportData?.totales?.unidades_entradas) }}</td>
                                    <td class="py-4 px-2 text-right font-mono text-amber-600 dark:text-amber-400">{{ formatNum(currentReportData?.totales?.unidades_salidas) }}</td>
                                    <td class="py-4 px-2 text-right font-mono text-rose-600 dark:text-rose-400">{{ formatNum(currentReportData?.totales?.unidades_retiros) }}</td>
                                    <td class="py-4 px-2 text-right font-mono text-purple-600 dark:text-purple-400">{{ formatNum(currentReportData?.totales?.unidades_autoconsumo) }}</td>
                                    <td class="py-4 px-2 text-right font-mono font-black border-r border-gray-200 dark:border-gray-700 bg-sky-100/50 dark:bg-sky-900/40">{{ formatNum(currentReportData?.totales?.unidades_final) }}</td>

                                    <td class="py-4 px-2 text-right font-mono">{{ formatBs(currentReportData?.totales?.valores_inicial) }}</td>
                                    <td class="py-4 px-2 text-right font-mono text-blue-600 dark:text-blue-400">{{ formatBs(currentReportData?.totales?.valores_entradas) }}</td>
                                    <td class="py-4 px-2 text-right font-mono text-amber-600 dark:text-amber-400">{{ formatBs(currentReportData?.totales?.valores_salidas) }}</td>
                                    <td class="py-4 px-2 text-right font-mono text-rose-600 dark:text-rose-400">{{ formatBs(currentReportData?.totales?.valores_retiros) }}</td>
                                    <td class="py-4 px-2 text-right font-mono text-purple-600 dark:text-purple-400">{{ formatBs(currentReportData?.totales?.valores_autoconsumo) }}</td>
                                    <td class="py-4 px-2 text-right font-mono font-black text-emerald-600 dark:text-emerald-400 bg-emerald-100/50 dark:bg-emerald-900/40">{{ formatBs(currentReportData?.totales?.valores_final) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
