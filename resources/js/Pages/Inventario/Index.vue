<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { fab } from '@fortawesome/free-brands-svg-icons';
import { router, Link, usePage } from '@inertiajs/vue3';
import { computed, ref, reactive, watch } from 'vue';

library.add(fas, fab);
const page = usePage();
const props = defineProps({
    Inventarios: Object,
    filters: Object,
    tipos: Object,
    marcas: Array,
    containersList: Array,
});

const getBrandSlug = (brand) => {
    if (!brand) return null;
    const b = brand.toLowerCase().trim();
    
    if (b === 'cat' || b.startsWith('cat ') || b.includes('caterpillar') || b.includes('caterpilar')) {
        return 'caterpillar';
    }
    
    if (b.includes('international') || b.includes('internacional')) {
        return 'international';
    }
    
    if (b.includes('mack')) {
        return 'mack';
    }
    
    if (b.includes('daewoo') || b.includes('daewo')) {
        return 'daewoo';
    }
    
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
        'daewoo': 'daewoo',
        'daewo': 'daewoo',
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
    
    if (slug === 'international') {
        return '/international-logo.svg';
    }
    
    if (slug === 'mack') {
        return '/mack-logo.svg';
    }
    
    if (slug === 'daewoo') {
        return '/daewoo-logo.svg';
    }
    
    const urls = [
        `https://cdn.simpleicons.org/${slug}/9ca3af`,
        `https://vl.imgix.net/img/${slug}-logo.png`,
        `https://logo.clearbit.com/${slug}.com`
    ];
    
    const attempt = imageAttempts.value[id] || 0;
    return attempt < urls.length ? urls[attempt] : null;
};

const handleImageError = (id) => {
    imageAttempts.value[id] = (imageAttempts.value[id] || 0) + 1;
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
        { key: 'container.cod', label: 'Contenedor', sortable: true },
        { key: 'codInv', label: 'Inventario', sortable: true },
        { key: 'expediente', label: 'Expediente', sortable: true },
        { key: 'tipo', label: 'Tipo', sortable: true },
        { key: 'serial', label: 'Serial', sortable: true },
        { key: 'model_display', label: 'Marca / Modelo', sortable: true },
    ];

    if (isAutopart.value) {
        cols.push({ key: 'categorie', label: 'Categoría', sortable: true });
        cols.push({ key: 'cantidad', label: 'Cantidad', sortable: true });
    } else {
        cols.push({ key: 'año', label: 'Año', sortable: true });
    }

    cols.push({ key: 'precio_factura', label: 'Factura Declarada', sortable: false });

    return cols;
});

const getBillingTotals = (row) => {
    const baseCosto = parseFloat(row.price || row.costo_importacion_unitario || row.costo || 0);
    if (!baseCosto || baseCosto <= 0) {
        return { hasCost: false };
    }
    const costoTaller = parseFloat(row.costo_taller || 0) || 0;
    
    let bigBsOriginal = parseFloat(row.price || 0);
    if (!bigBsOriginal || bigBsOriginal <= 0) {
        bigBsOriginal = baseCosto + costoTaller;
    }

    const rateItem = parseFloat(row.tasa_bcv || row.container?.tasa_bcv || 0);
    const rateToday = parseFloat(page.props.tasa_bcv_hoy || 0) || rateItem;

    let bigBs = bigBsOriginal;
    let bigUsd = 0;
    if (rateItem > 0) {
        bigUsd = bigBsOriginal / rateItem;
    }

    if (rateItem > 0 && rateToday > 0 && rateToday !== rateItem) {
        bigBs = bigUsd * rateToday;
    }

    const totalBs = bigBs * 1.16;
    const totalUsd = bigUsd > 0 ? (bigUsd * 1.16) : 0;

    return {
        hasCost: true,
        bigBs: bigBs.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
        totalBs: totalBs.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
        totalUsd: totalUsd > 0 ? totalUsd.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : null
    };
};

// Filter states
const statusFilter = ref(props.filters?.status || 'ALL');
const typeFilter = ref(props.filters?.type_filter || '');
const modelFilter = ref(props.filters?.model_filter || '');
const brandFilter = ref(props.filters?.brand_filter || '');
const containerFilter = ref(props.filters?.container_filter || '');
const priceFilter = ref(props.filters?.price_filter || '');

// Keep filters synchronized with inertia props
watch(() => props.filters, (newFilters) => {
    if (newFilters) {
        if (newFilters.status !== undefined) statusFilter.value = newFilters.status || 'ALL';
        if (newFilters.type_filter !== undefined) typeFilter.value = newFilters.type_filter || '';
        if (newFilters.model_filter !== undefined) modelFilter.value = newFilters.model_filter || '';
        if (newFilters.brand_filter !== undefined) brandFilter.value = newFilters.brand_filter || '';
        if (newFilters.container_filter !== undefined) containerFilter.value = newFilters.container_filter || '';
        if (newFilters.price_filter !== undefined) priceFilter.value = newFilters.price_filter || '';
    }
}, { deep: true });

const activeFiltersCount = computed(() => {
    let count = 0;
    if (statusFilter.value && statusFilter.value !== 'ALL') count++;
    if (typeFilter.value) count++;
    if (modelFilter.value) count++;
    if (brandFilter.value) count++;
    if (containerFilter.value) count++;
    if (priceFilter.value) count++;
    if (props.filters?.search) count++;
    return count;
});

const fetchInventarios = () => {
    const routeName = isAutopart.value ? 'autopart' : (isCamara.value ? 'camara' : 'inventario');
    router.get(route(routeName), { 
        ...props.filters,
        status: statusFilter.value,
        type_filter: typeFilter.value,
        model_filter: modelFilter.value,
        brand_filter: brandFilter.value,
        container_filter: containerFilter.value,
        price_filter: priceFilter.value,
        page: 1
    }, { preserveState: true, replace: true });
};

const clearFilters = () => {
    statusFilter.value = 'ALL';
    typeFilter.value = '';
    modelFilter.value = '';
    brandFilter.value = '';
    containerFilter.value = '';
    priceFilter.value = '';
    const routeName = isAutopart.value ? 'autopart' : (isCamara.value ? 'camara' : 'inventario');
    router.get(route(routeName), {
        status: 'ALL',
        page: 1
    }, { preserveState: true, replace: true });
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

const getCreateUrl = () => {
    if (isAutopart.value) return route('createAutopart');
    if (isCamara.value) return route('createCamara');
    return route('createInventario');
};

const getEditUrl = (id) => {
    if (isAutopart.value) return route('editAutopart', { id });
    if (isCamara.value) return route('editCamara', { id });
    return route('editInventario', { id });
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
            <div v-if="$page.props.auth.user.permissions?.includes('manage partida') || $page.props.auth.user.roles?.includes('Superusuario')" class="mb-6 flex justify-end">
                <Link 
                    :href="getCreateUrl()" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-[2rem] font-black shadow-xl shadow-indigo-500/20 transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3 group border-b-4 border-indigo-800"
                >
                    <i class="fa-solid fa-plus text-xl group-hover:rotate-90 transition-transform duration-300"></i>
                    <span class="tracking-widest uppercase text-sm">REGISTRAR NUEVO</span>
                </Link>
            </div>
            
            <!-- Professional Advanced Toolbar -->
            <div class="mb-8 bg-white dark:bg-gray-800 p-6 md:p-8 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700/50 backdrop-blur-sm transition-all duration-300">
                
                <!-- Toolbar Header with Title and Reset Button -->
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-100 dark:border-gray-700/60">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <i class="fa-solid fa-sliders text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                Filtros Profesionales
                                <span v-if="activeFiltersCount > 0" class="px-2.5 py-0.5 text-[10px] font-black rounded-full bg-indigo-500 text-white shadow-sm shadow-indigo-500/30">
                                    {{ activeFiltersCount }} activo{{ activeFiltersCount > 1 ? 's' : '' }}
                                </span>
                            </h3>
                            <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">Filtra por contenedor, marca, categoría, estado y modelo con actualización en tiempo real</p>
                        </div>
                    </div>

                    <!-- Clear Filters Button -->
                    <button 
                        v-if="activeFiltersCount > 0" 
                        @click="clearFilters" 
                        class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-rose-50 text-gray-600 hover:text-rose-600 dark:bg-gray-700/50 dark:hover:bg-rose-900/20 dark:text-gray-300 dark:hover:text-rose-400 text-xs font-black transition-all flex items-center gap-2 group active:scale-95 shadow-sm"
                    >
                        <i class="fa-solid fa-filter-circle-xmark text-sm group-hover:rotate-12 transition-transform"></i>
                        <span>LIMPIAR FILTROS</span>
                    </button>
                </div>

                <!-- Filters Grid (6 Professional Filters) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                    
                    <!-- 1. Category/Type Filter -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-1">CATEGORÍA</label>
                        <div class="relative group">
                            <i class="fa-solid fa-layer-group absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 text-xs transition-colors pointer-events-none"></i>
                            <select v-model="typeFilter" @change="fetchInventarios" class="w-full pl-9 pr-6 py-3 bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-xs outline-none appearance-none cursor-pointer">
                                <option value="">TODOS LOS TIPOS</option>
                                <option value="motores">MOTORES</option>
                                <option value="cajas">CAJAS</option>
                                <option value="camaras">CÁMARAS</option>
                                <option value="autopartes">AUTOPARTES</option>
                            </select>
                        </div>
                    </div>

                    <!-- 2. Brand Filter -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-1">MARCA</label>
                        <div class="relative group">
                            <i class="fa-solid fa-shield-halved absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 text-xs transition-colors pointer-events-none"></i>
                            <select v-model="brandFilter" @change="fetchInventarios" class="w-full pl-9 pr-6 py-3 bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-xs outline-none appearance-none cursor-pointer">
                                <option value="">TODAS LAS MARCAS</option>
                                <option v-for="b in (marcas || [])" :key="b" :value="b">{{ b }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- 3. Container Filter -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-1">CONTENEDOR / EXP.</label>
                        <div class="relative group">
                            <i class="fa-solid fa-box-archive absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 text-xs transition-colors pointer-events-none"></i>
                            <select v-model="containerFilter" @change="fetchInventarios" class="w-full pl-9 pr-6 py-3 bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-xs outline-none appearance-none cursor-pointer truncate">
                                <option value="">TODOS CONTENEDORES</option>
                                <option v-for="c in (containersList || [])" :key="c.id" :value="c.id">
                                    {{ c.cod }} ({{ c.expediente || 'S/E' }})
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- 4. Model Filter -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-1">MODELO</label>
                        <div class="relative group">
                            <i class="fa-solid fa-car-side absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 text-xs transition-colors pointer-events-none"></i>
                            <input 
                                v-model="modelFilter" 
                                @keyup.enter="fetchInventarios"
                                @blur="fetchInventarios"
                                type="text" 
                                placeholder="Ej: COROLLA, HILUX..." 
                                class="w-full pl-9 pr-7 py-3 bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all font-bold outline-none uppercase text-xs"
                            />
                            <button v-if="modelFilter" @click="modelFilter = ''; fetchInventarios();" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>

                    <!-- 5. Status Filter -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-1">ESTADO</label>
                        <div class="relative group">
                            <i class="fa-solid fa-tag absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 text-xs transition-colors pointer-events-none"></i>
                            <select v-model="statusFilter" @change="fetchInventarios" class="w-full pl-9 pr-6 py-3 bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 transition-all font-bold text-xs outline-none appearance-none cursor-pointer">
                                <option value="ALL">LISTADO COMPLETO</option>
                                <option value="DISPONIBLE">DISPONIBLES</option>
                                <option value="VENDIDO">VENDIDOS</option>
                                <option value="GARANTIA">GARANTÍAS</option>
                                <option value="PRECIO PENDIENTE">PRECIO PENDIENTE</option>
                                <option value="INOPERATIVO-DESARMADO">INOPERATIVO-DESARMADO</option>
                                <option value="USO INTERNO">USO INTERNO</option>
                            </select>
                        </div>
                    </div>

                    <!-- 6. Costo / Factura Filter -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-1">COSTO / FACTURA</label>
                        <div class="relative group">
                            <i class="fa-solid fa-file-invoice-dollar absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 text-xs transition-colors pointer-events-none"></i>
                            <select v-model="priceFilter" @change="fetchInventarios" class="w-full pl-9 pr-6 py-3 bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 transition-all font-bold text-xs outline-none appearance-none cursor-pointer">
                                <option value="">TODOS LOS COSTOS</option>
                                <option value="CON_PRECIO">CON PRECIO REGISTRADO</option>
                                <option value="PENDIENTE">PRECIO PENDIENTE</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Premium DataTable -->
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50 overflow-hidden backdrop-blur-sm">
                <DataTable 
                    :rows="Inventarios" 
                    :columns="columns" 
                    :filters="{ 
                        ...filters, 
                        status: statusFilter, 
                        type_filter: typeFilter, 
                        model_filter: modelFilter,
                        brand_filter: brandFilter,
                        container_filter: containerFilter,
                        price_filter: priceFilter
                    }"
                    :routeName="isAutopart ? 'autopart' : (isCamara ? 'camara' : 'inventario')"
                    :title="isAutopart ? 'LISTADO DE AUTOPARTES' : (isCamara ? 'CONTROL DE CÁMARAS' : 'REGISTROS DE INVENTARIO')"
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
                                <img v-if="getBrandIcon(row.marca, row.id)" :src="getBrandIcon(row.marca, row.id)" @error="handleImageError(row.id)" class="w-full h-full object-contain opacity-80" :alt="row.marca" />
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

                    <template #cell-precio_factura="{ row }">
                        <div class="flex flex-col text-[11px] leading-snug">
                            <template v-if="getBillingTotals(row).hasCost">
                                <span class="font-bold text-gray-700 dark:text-gray-300 font-mono">
                                    Base: Bs. {{ getBillingTotals(row).bigBs }}
                                </span>
                                <span class="font-black text-emerald-600 dark:text-emerald-400 font-mono">
                                    Total c/IVA: Bs. {{ getBillingTotals(row).totalBs }}
                                </span>
                                <span v-if="getBillingTotals(row).totalUsd" class="text-[10px] font-extrabold text-indigo-600 dark:text-indigo-400 font-mono">
                                    ($ {{ getBillingTotals(row).totalUsd }} USD)
                                </span>
                            </template>
                            <template v-else>
                                <span class="text-amber-500 font-bold italic text-[10px] flex items-center gap-1">
                                    <i class="fa-solid fa-hourglass-half"></i> Pendiente
                                </span>
                            </template>
                        </div>
                    </template>

                    <!-- Actions (Premium Redesign) -->
                    <template #actions="{ row }">
                        <div class="flex justify-end gap-2">
                             <Link :href="route('showInventario', row.id)"
                                class="h-8 w-8 flex items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-600 transition-all transform active:scale-90" title="Ver Detalles">
                                <i class="fa-solid fa-eye text-xs"></i>
                             </Link>
                             <Link v-if="$page.props.auth.user.permissions?.includes('manage partida') || $page.props.auth.user.roles?.includes('Superusuario')" :href="getEditUrl(row.id)"
                                class="h-8 w-8 flex items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 transition-all transform active:scale-90" title="Editar">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                             </Link>
                             <button v-if="$page.props.auth.user.permissions?.includes('manage partida') || $page.props.auth.user.roles?.includes('Superusuario')" @click="openDeleteModal(row.id)"
                                class="h-8 w-8 flex items-center justify-center rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white dark:hover:bg-rose-500 transition-all transform active:scale-90" title="Eliminar">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                             </button>
                        </div>
                    </template>
                </DataTable>
            </div>

            <!-- Enhanced Summary Footer with Live Counters (Dynamically updates with Search and Filters) -->
            <div class="mt-8 p-6 md:p-8 bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700/60 backdrop-blur-sm transition-all duration-300">
                <div class="flex flex-col sm:flex-row items-center justify-between pb-4 mb-6 border-b border-gray-100 dark:border-gray-700/60 gap-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">
                            Resumen Dinámico del Inventario
                        </span>
                        <span v-if="activeFiltersCount > 0" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-2.5 py-0.5 rounded-full border border-indigo-100 dark:border-indigo-800/40">
                            Filtrado activo ({{ activeFiltersCount }})
                        </span>
                    </div>
                    <div class="text-xs text-gray-400 dark:text-gray-500 font-medium">
                        Total de coincidencias: <strong class="text-gray-800 dark:text-white font-black text-sm">{{ tipos?.[0]?.['total_filtrados'] ?? 0 }}</strong>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <!-- Total Coincidencias Card -->
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50/80 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/40">
                        <div class="h-12 w-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-500 shrink-0">
                            <i class="fa-solid fa-boxes-stacked text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Registros</p>
                            <p class="text-xl font-black text-gray-800 dark:text-white">{{ tipos?.[0]?.['total_filtrados'] ?? 0 }}</p>
                        </div>
                    </div>

                    <!-- Motores Card -->
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50/80 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/40">
                        <div class="h-12 w-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-500 shrink-0">
                            <i class="fa-solid fa-wrench text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Motores</p>
                            <p class="text-xl font-black text-gray-800 dark:text-white">{{ tipos?.[0]?.['motores'] || 0 }}</p>
                        </div>
                    </div>

                    <!-- Cajas Card -->
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50/80 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/40">
                        <div class="h-12 w-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-500 shrink-0">
                            <i class="fa-solid fa-gears text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Cajas</p>
                            <p class="text-xl font-black text-gray-800 dark:text-white">{{ tipos?.[0]?.['cajas_automaticas'] || 0 }}</p>
                        </div>
                    </div>

                    <!-- Autopartes Card -->
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50/80 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/40">
                        <div class="h-12 w-12 rounded-2xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-500 shrink-0">
                            <i class="fa-solid fa-oil-can text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Autopartes</p>
                            <p class="text-xl font-black text-gray-800 dark:text-white">{{ tipos?.[0]?.['autopartes'] || 0 }}</p>
                        </div>
                    </div>

                    <!-- Cámaras Card -->
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50/80 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/40 col-span-2 sm:col-span-1">
                        <div class="h-12 w-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-500 shrink-0">
                            <i class="fa-solid fa-camera text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Cámaras</p>
                            <p class="text-xl font-black text-gray-800 dark:text-white">{{ tipos?.[0]?.['camaras'] || 0 }}</p>
                        </div>
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
                
                <div class="flex gap-3">
                    <button @click="closeDeleteModal" class="flex-1 py-3.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold rounded-xl transition-all">
                        Cancelar
                    </button>
                    <button @click="confirmDelete" :disabled="deleteModal.processing" class="flex-1 py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-lg shadow-rose-600/30 transition-all flex items-center justify-center gap-2">
                        <i v-if="deleteModal.processing" class="fa-solid fa-spinner fa-spin text-sm"></i>
                        <span>Eliminar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
  </AppLayout>
</template>
