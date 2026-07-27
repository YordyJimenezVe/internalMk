<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, router, Link } from '@inertiajs/vue3';
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { debounce } from 'lodash';
import axios from 'axios';

const props = defineProps({
    items: Object, // Paginated items object from backend
    filters: Object,
    totals: Object, // Backend global counts
    tasa_bcv: Number,
});

const search = ref(props.filters?.search || '');

// Debounce search update
const updateSearch = debounce((value) => {
    router.get(route('inventario.precio_pendiente'), { 
        search: value, 
        page: 1 
    }, { preserveState: true, replace: true });
}, 300);

watch(search, (value) => updateSearch(value));

const vehicleTypes = ref({});

const itemsList = computed(() => props.items?.data || props.items || []);

const fetchVehicleTypes = async () => {
    itemsList.value.forEach(async (item) => {
        // If already loading or loaded, skip
        if (vehicleTypes.value[item.id]) return;

        vehicleTypes.value[item.id] = { loading: true, type: '', example: '' };

        try {
            const res = await axios.get(route('inventario.vehicle_type'), {
                params: {
                    marca: item.marca,
                    modelo: item.modelo,
                    ano: item.año
                }
            });
            vehicleTypes.value[item.id] = {
                loading: false,
                type: res.data.tipo_vehiculo || 'Otro',
                example: res.data.ejemplo || ''
            };
        } catch (e) {
            console.error(e);
            vehicleTypes.value[item.id] = {
                loading: false,
                type: 'Otro',
                example: ''
            };
        }
    });
};

onMounted(() => {
    fetchVehicleTypes();
});

watch(itemsList, () => {
    fetchVehicleTypes();
}, { deep: true });

const isZeroOrEmpty = (val) => {
    if (val === null || val === undefined || val === '') return true;
    const num = parseFloat(val);
    return isNaN(num) || num === 0;
};

// Create forms dynamically for each item as plain reactive ref
const forms = ref({});

const initForms = () => {
    itemsList.value.forEach(item => {
        if (!forms.value[item.id]) {
            const hasValue = !isZeroOrEmpty(item.costo_importacion_unitario);
            forms.value[item.id] = {
                costo_importacion_unitario: hasValue ? item.costo_importacion_unitario : '',
                costo_usd: hasValue && props.tasa_bcv > 0 
                    ? (parseFloat(item.costo_importacion_unitario) / props.tasa_bcv).toFixed(2) 
                    : '',
                processing: false,
            };
        }
    });
};

// Initialize immediately for setup
initForms();

watch(itemsList, () => {
    initForms();
}, { deep: true });

const submitCost = (id) => {
    const f = forms.value[id];
    if (!f) return;
    
    router.post(route('inventario.precio_pendiente.update', id), {
        costo_importacion_unitario: f.costo_importacion_unitario,
    }, {
        preserveScroll: true,
        onStart: () => {
            f.processing = true;
        },
        onFinish: () => {
            f.processing = false;
        }
    });
};

const onUsdChange = (id) => {
    const f = forms.value[id];
    if (!f) return;
    
    // Clean and validate input
    let valStr = (f.costo_usd || '').toString().replace(/[^\d.,]/g, '').replace(',', '.');
    f.costo_usd = valStr;

    const usdVal = parseFloat(valStr);
    if (!isNaN(usdVal) && props.tasa_bcv > 0) {
        f.costo_importacion_unitario = (usdVal * props.tasa_bcv).toFixed(2);
    } else {
        f.costo_importacion_unitario = '';
    }
};

const onBsChange = (id) => {
    const f = forms.value[id];
    if (!f) return;
    
    // Clean and validate input
    let valStr = (f.costo_importacion_unitario || '').toString().replace(/[^\d.,]/g, '').replace(',', '.');
    f.costo_importacion_unitario = valStr;

    const bsVal = parseFloat(valStr);
    if (!isNaN(bsVal) && props.tasa_bcv > 0) {
        f.costo_usd = (bsVal / props.tasa_bcv).toFixed(2);
    } else {
        f.costo_usd = '';
    }
};

const summary = computed(() => {
    if (props.totals) {
        return props.totals;
    }
    let motores = 0;
    let cajas = 0;
    let camaras = 0;
    let autopartes = 0;
    let otros = 0;

    itemsList.value.forEach(item => {
        const t = (item.tipo || '').toUpperCase();
        if (t.includes('MOTOR')) {
            motores++;
        } else if (t.includes('CAJA')) {
            cajas++;
        } else if (t.includes('CÁMARA') || t.includes('CAMARA')) {
            camaras++;
        } else if (t.includes('AUTOPARTE')) {
            autopartes++;
        } else {
            otros++;
        }
    });

    return { motores, cajas, camaras, autopartes, otros };
});

const getTipoIcon = (tipo) => {
    const t = (tipo || '').toUpperCase();
    if (t.includes('MOTOR')) return 'fa-solid fa-gears';
    if (t.includes('CAJA')) return 'fa-solid fa-cube';
    if (t.includes('CÁMARA') || t.includes('CAMARA')) return 'fa-solid fa-wrench';
    if (t.includes('AUTOPARTE')) return 'fa-solid fa-box';
    return 'fa-solid fa-tag';
};

const getTipoBadgeClass = (tipo) => {
    const t = (tipo || '').toUpperCase();
    if (t.includes('MOTOR')) {
        return 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-100/50 dark:border-blue-900/30';
    }
    if (t.includes('CAJA')) {
        return 'bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 border border-purple-100/50 dark:border-purple-900/30';
    }
    if (t.includes('CÁMARA') || t.includes('CAMARA')) {
        return 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-100/50 dark:border-emerald-900/30';
    }
    if (t.includes('AUTOPARTE')) {
        return 'bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 border border-orange-100/50 dark:border-orange-900/30';
    }
    return 'bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700';
};
</script>

<template>
    <AppLayout title="Ítems con Precios Pendientes">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    <i class="fa-solid fa-hourglass-half mr-2 text-indigo-600 dark:text-indigo-400"></i> Ítems con Precios Pendientes
                </h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Info Alert Card -->
                <div class="mb-8 p-6 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-3xl shadow-xl text-white relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-black mb-1">Módulo de Asignación de Costos de Importación</h3>
                            <p class="text-white/80 text-sm max-w-2xl leading-relaxed">
                                Como rol de Facturación, aquí debes asignar el Costo de Importación de los nuevos registros. Una vez ingresado, el sistema calculará automáticamente el Precio/Costo final sumando los gastos del taller + porcentaje de utilidad, y cambiará el estatus del registro a <span class="font-bold bg-white/20 px-2 py-0.5 rounded">DISPONIBLE</span> (solo si originalmente estaba en PRECIO PENDIENTE).
                            </p>
                        </div>
                        <div class="text-3xl opacity-80 self-end md:self-center">
                            <i class="fa-solid fa-ship"></i>
                        </div>
                    </div>
                </div>

                <!-- Stats Summary Cards Grid -->
                <div v-if="summary.motores > 0 || summary.cajas > 0 || summary.camaras > 0 || summary.autopartes > 0 || summary.otros > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mb-8">
                    <!-- Motores Card -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-2xl p-4 shadow-md flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-gears"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Motores</p>
                            <p class="text-xl font-black text-gray-900 dark:text-white">{{ summary.motores }}</p>
                        </div>
                    </div>

                    <!-- Cajas Card -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-2xl p-4 shadow-md flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-cube"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Cajas</p>
                            <p class="text-xl font-black text-gray-900 dark:text-white">{{ summary.cajas }}</p>
                        </div>
                    </div>

                    <!-- Cámaras Card -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-2xl p-4 shadow-md flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-wrench"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Cámaras</p>
                            <p class="text-xl font-black text-gray-900 dark:text-white">{{ summary.camaras }}</p>
                        </div>
                    </div>

                    <!-- Autopartes Card -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-2xl p-4 shadow-md flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-box"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Autopartes</p>
                            <p class="text-xl font-black text-gray-900 dark:text-white">{{ summary.autopartes }}</p>
                        </div>
                    </div>

                    <!-- Otros Card -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-2xl p-4 shadow-md flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-700/20 text-gray-600 dark:text-gray-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-barcode"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Otros</p>
                            <p class="text-xl font-black text-gray-900 dark:text-white">{{ summary.otros }}</p>
                        </div>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-md">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Asignación de Costos</h3>
                    
                    <div class="relative w-full sm:w-80">
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Buscar expediente o contenedor..." 
                            class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm focus:outline-none"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="itemsList.length === 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-16 text-center">
                    <div class="h-20 w-20 bg-green-50 dark:bg-green-900/20 text-green-500 rounded-full flex items-center justify-center text-4xl mx-auto mb-6 shadow-inner">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">¡Todo al día!</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                        No hay ítems con precio pendiente que coincidan con la búsqueda.
                    </p>
                </div>

                <!-- Table View -->
                <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-3xl border border-gray-100 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 uppercase text-xs font-bold tracking-wider border-b border-gray-100 dark:border-gray-700">
                                    <th class="py-5 px-6">Código / Exp.</th>
                                    <th class="py-5 px-6">Descripción del Ítem</th>
                                    <th class="py-5 px-6">Serial</th>
                                    <th class="py-5 px-6">Origen</th>
                                    <th class="py-5 px-6 text-center">Costo de Importación (Bs. / $)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                <tr v-for="item in itemsList" :key="item.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-900/20 transition-all">
                                    <td class="py-5 px-6">
                                        <div class="flex flex-col">
                                            <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400 text-sm">#{{ item.codInv }}</span>
                                            <span class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Exp: {{ item.expediente || 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-900 dark:text-white">{{ item.item }}</span>
                                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                                <span class="text-xs text-gray-400 dark:text-gray-500 font-semibold">Modelo: {{ item.modelo }} | Año: {{ item.año || 'N/A' }}</span>
                                                <span v-if="item.tipo" :class="getTipoBadgeClass(item.tipo)" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase">
                                                    <i :class="getTipoIcon(item.tipo) + ' text-[9px]'"></i>
                                                    {{ item.tipo }}
                                                </span>
                                                <span v-if="vehicleTypes[item.id] && !vehicleTypes[item.id].loading && vehicleTypes[item.id].type" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 border border-indigo-100/50 dark:border-indigo-900/30">
                                                    <i class="fa-solid fa-truck-pickup text-[9px]"></i>
                                                    {{ vehicleTypes[item.id].example || vehicleTypes[item.id].type }}
                                                </span>
                                                <span v-else-if="!vehicleTypes[item.id] || vehicleTypes[item.id].loading" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-gray-50 dark:bg-gray-800 text-gray-400 border border-gray-100 dark:border-gray-700 animate-pulse">
                                                    <i class="fa-solid fa-circle-notch animate-spin text-[8px]"></i>
                                                    Cargando...
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6">
                                        <span class="font-mono text-sm text-gray-600 dark:text-gray-300 font-semibold">{{ item.serial || 'SIN SERIAL' }}</span>
                                    </td>
                                    <td class="py-5 px-6">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold tracking-wider"
                                              :class="item.origen === 'IMPORTADO' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'">
                                            {{ item.origen }}
                                        </span>
                                    </td>
                                    <td class="py-5 px-6">
                                        <form @submit.prevent="submitCost(item.id)" class="flex flex-col gap-2 items-center justify-center max-w-sm mx-auto">
                                            <div class="flex items-center gap-2">
                                                <!-- Dollar Helper Input -->
                                                <div class="relative rounded-xl shadow-sm w-28">
                                                    <div class="absolute inset-y-0 left-2.5 flex items-center pointer-events-none">
                                                        <span class="text-gray-400 dark:text-gray-500 font-bold text-xs">$</span>
                                                    </div>
                                                    <input 
                                                        v-model="forms[item.id].costo_usd" 
                                                        @input="onUsdChange(item.id)"
                                                        type="text" 
                                                        placeholder="USD"
                                                        class="block w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl py-1.5 pl-6 pr-2 text-right font-mono font-bold text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 text-xs"
                                                    >
                                                </div>

                                                <!-- Multiply/Convert Icon -->
                                                <span class="text-gray-400 dark:text-gray-600 text-xs">
                                                    <i class="fa-solid fa-right-left"></i>
                                                </span>

                                                <!-- Bolivares Input -->
                                                <div class="relative rounded-xl shadow-sm w-36">
                                                    <div class="absolute inset-y-0 left-2.5 flex items-center pointer-events-none">
                                                        <span class="text-gray-400 dark:text-gray-500 font-bold text-xs">Bs.</span>
                                                    </div>
                                                    <input 
                                                        v-model="forms[item.id].costo_importacion_unitario" 
                                                        @input="onBsChange(item.id)"
                                                        type="text" 
                                                        placeholder="0.00"
                                                        class="block w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl py-1.5 pl-8 pr-2 text-right font-mono font-bold text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs"
                                                        required
                                                    >
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between w-full px-2">
                                                <span class="text-[9px] text-gray-400 dark:text-gray-500 font-medium">
                                                    Tasa BCV: Bs. {{ props.tasa_bcv ? parseFloat(props.tasa_bcv).toFixed(2) : '0.00' }}
                                                </span>
                                                <button 
                                                    type="submit"
                                                    :disabled="forms[item.id]?.processing"
                                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1 px-4 rounded-xl shadow-md transition-all active:scale-95 text-xs flex items-center gap-1"
                                                >
                                                    <i class="fa-solid fa-floppy-disk"></i> Guardar
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="items?.links && items.links.length > 3" class="p-4 border-t border-gray-200 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center text-gray-500 dark:text-gray-400 font-medium">
                        <span class="text-sm">
                            Mostrando {{ items.from || 0 }} a {{ items.to || 0 }} de {{ items.total || 0 }} resultados
                        </span>
                        <div class="flex gap-1 mt-2 md:mt-0">
                            <component
                                :is="link.url ? Link : 'span'"
                                v-for="(link, index) in items.links"
                                :key="index"
                                :href="link.url"
                                class="px-3 py-1 rounded-md text-sm transition-colors"
                                :class="{
                                    'bg-indigo-600 text-white': link.active,
                                    'hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer': !link.active && link.url,
                                    'text-gray-300 dark:text-gray-600': !link.url
                                }"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
