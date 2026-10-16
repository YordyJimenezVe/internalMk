<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  bill: Object,
  availableItems: {
    type: Array,
    default: () => [],
  },
});

const originalItem = ref(props.bill.inventario || null);
const selectedItem = ref(props.bill.inventario || null);

// State for searching new item
const isSearchOpen = ref(false);
const searchQuery = ref('');
const isDropdownOpen = ref(false);
const lookupLoading = ref(false);
const lookupError = ref('');
const lookupResult = ref(null);
let searchDebounceTimeout = null;

const hasItemChanged = computed(() => {
    if (!originalItem.value && !selectedItem.value) return false;
    if (!originalItem.value && selectedItem.value) return true;
    if (originalItem.value && !selectedItem.value) return true;
    return originalItem.value.id !== selectedItem.value.id;
});

// Filter local available items for fast dropdown suggestions
const filteredAvailableItems = computed(() => {
    if (!props.availableItems || props.availableItems.length === 0) return [];
    const query = searchQuery.value.trim().toLowerCase();
    const currentId = originalItem.value ? originalItem.value.id : null;
    const available = props.availableItems.filter(item => item.id !== currentId);
    if (!query) return available.slice(0, 20);

    return available.filter(item => {
        const idMatch = String(item.id).includes(query);
        const codInvMatch = (item.codInv || '').toLowerCase().includes(query);
        const tipoMatch = (item.tipo || '').toLowerCase().includes(query);
        const marcaMatch = (item.marca || '').toLowerCase().includes(query);
        const modeloMatch = (item.modelo || '').toLowerCase().includes(query);
        const serialMatch = (item.serial || '').toLowerCase().includes(query);
        return idMatch || codInvMatch || tipoMatch || marcaMatch || modeloMatch || serialMatch;
    }).slice(0, 25);
});

// Search / Verify item via server endpoint
const verifyItem = async (queryInput) => {
    const raw = (queryInput !== undefined ? queryInput : searchQuery.value).trim();
    if (!raw) {
        lookupError.value = 'Ingresa un número de inventario o ID para consultar.';
        lookupResult.value = null;
        return;
    }

    lookupLoading.value = true;
    lookupError.value = '';
    lookupResult.value = null;

    try {
        const response = await axios.get(route('billing.checkItem', {
            id: raw,
            exclude_bill_id: props.bill.id,
        }));

        if (response.data && response.data.success) {
            const itm = response.data.item;

            // Check if it is the current item
            if (originalItem.value && itm.id === originalItem.value.id) {
                lookupError.value = `El ítem #${itm.id} [${itm.codInv || 'S/N'}] ya es el ítem actualmente asignado a esta factura.`;
                lookupResult.value = itm;
                return;
            }

            // Check availability
            if (!itm.is_available) {
                lookupError.value = `El ítem #${itm.id} [${itm.codInv || 'S/N'}] (${itm.tipo} ${itm.marca} ${itm.modelo}) NO está DISPONIBLE o ya fue vendido. (Estatus actual: ${itm.status}).`;
                lookupResult.value = itm;
            } else {
                lookupResult.value = itm;
                lookupError.value = '';
            }
        }
    } catch (err) {
        lookupResult.value = null;
        if (err.response && err.response.status === 404) {
            lookupError.value = `No se encontró ningún elemento con el número de inventario o ID "${raw}".`;
        } else {
            lookupError.value = 'Ocurrió un error al consultar el ítem en el inventario.';
        }
    } finally {
        lookupLoading.value = false;
    }
};

const onSearchInput = () => {
    lookupError.value = '';
    lookupResult.value = null;
    isDropdownOpen.value = true;

    if (searchDebounceTimeout) clearTimeout(searchDebounceTimeout);
    const q = searchQuery.value.trim();
    if (q.length >= 2) {
        searchDebounceTimeout = setTimeout(() => {
            verifyItem(q);
        }, 500);
    }
};

const assignItem = (item) => {
    if (!item) return;
    if (originalItem.value && item.id === originalItem.value.id) {
        lookupError.value = 'Este ítem ya está asignado actualmente a la factura.';
        return;
    }
    if (!item.is_available && item.status !== 'DISPONIBLE') {
        lookupError.value = `El ítem #${item.id} no puede asignarse porque su estatus es "${item.status}".`;
        return;
    }

    selectedItem.value = item;
    form.partida_id = item.id;
    searchQuery.value = '';
    lookupResult.value = null;
    lookupError.value = '';
    isDropdownOpen.value = false;
    isSearchOpen.value = false;
};

const revertItem = () => {
    selectedItem.value = originalItem.value;
    form.partida_id = originalItem.value ? originalItem.value.id : null;
    lookupError.value = '';
    lookupResult.value = null;
    searchQuery.value = '';
};

const form = useForm({
    partida_id: props.bill.partida_id,
    second_partida_id: props.bill.second_partida_id,
    fecha: props.bill.fecha,
    hora: props.bill.hora,
    big: props.bill.big,
    igtf: props.bill.igtf,
    iva: props.bill.iva,
    bs: props.bill.bs,
    divisa: props.bill.divisa,
    total: props.bill.total,
    numero_factura: props.bill.numero_factura,
    numero_control: props.bill.numero_control,
    numero_nota_credito: props.bill.numero_nota_credito,
    numero_factura_afect: props.bill.numero_factura_afect,
    client_name: props.bill.client_name,
    client_cedula: props.bill.client_cedula,
    client_phone: props.bill.client_phone,
    client_address: props.bill.client_address,
    client_email: props.bill.client_email,
    observaciones: props.bill.observaciones,
});

const submitForm = () => {
    form.post(route('updateBilling', props.bill.id), {
        onSuccess: () => {
            // Success handled by Inertia redirect
        }
    });
};
</script>

<template>
    <AppLayout title="Editar Factura">
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center transition-colors">
                    <i class="fa-solid fa-file-pen mr-3 text-indigo-500"></i>Editar Factura #{{ props.bill.numero_factura }}
                </h2>
                <div class="flex items-center gap-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                    <span>Fecha: {{ props.bill.fecha }}</span>
                    <span class="mx-2">•</span>
                    <span>Hora: {{ props.bill.hora }}</span>
                </div>
            </div>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                    
                    <form @submit.prevent="submitForm" class="p-8 md:p-12 space-y-12">
                        
                        <!-- Section 1: Billing Metadata -->
                        <div class="space-y-6">
                            <h3 class="font-black text-xs uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-6 flex items-center">
                                <i class="fa-solid fa-rectangle-list mr-2 text-indigo-500"></i>Información de Control
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1" for="fecha">Fecha</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-calendar-day absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                        <input v-model="form.fecha" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="date">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1" for="hora">Hora</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                        <input v-model="form.hora" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="time">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-indigo-400 uppercase ml-1" for="numero_factura">Nro. Factura</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-hashtag absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400 transition-colors"></i>
                                        <input v-model="form.numero_factura" class="appearance-none block w-full bg-indigo-50/30 dark:bg-indigo-900/10 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/30 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-black" type="text" required>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1" for="numero_control">Nro. Control</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-fingerprint absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 transition-colors"></i>
                                        <input v-model="form.numero_control" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text" required>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Nro. Nota Crédito</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-file-invoice absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 transition-colors"></i>
                                        <input v-model="form.numero_nota_credito" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-sm" type="text">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Factura Afectada</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-link absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 transition-colors"></i>
                                        <input v-model="form.numero_factura_afect" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-sm" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Billed Item (Partida de Inventario) -->
                        <div class="space-y-6 pt-12 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <h3 class="font-black text-xs uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 flex items-center">
                                    <i class="fa-solid fa-box-open mr-2 text-amber-500"></i>Ítem Facturado (Inventario)
                                </h3>

                                <div class="flex items-center gap-3">
                                    <span v-if="hasItemChanged" class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-amber-500/10 text-amber-500 border border-amber-500/20 flex items-center gap-1.5 animate-pulse">
                                        <i class="fa-solid fa-arrows-rotate"></i> Modificación Pendiente
                                    </span>
                                    <button 
                                        type="button" 
                                        @click="isSearchOpen = !isSearchOpen"
                                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2"
                                        :class="isSearchOpen ? 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200' : 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 border border-indigo-200 dark:border-indigo-800/40'"
                                    >
                                        <i class="fa-solid" :class="isSearchOpen ? 'fa-xmark' : 'fa-pen-to-square'"></i>
                                        <span>{{ isSearchOpen ? 'Cerrar Buscador' : 'Cambiar Ítem / Nro. Inventario' }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Server validation error for partida_id -->
                            <div v-if="form.errors.partida_id" class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/50 text-rose-700 dark:text-rose-300 text-sm flex items-start gap-3">
                                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-lg mt-0.5"></i>
                                <span class="font-bold">{{ form.errors.partida_id }}</span>
                            </div>

                            <!-- Item Comparison Banner if changed -->
                            <div v-if="hasItemChanged && selectedItem" class="p-5 rounded-2xl bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/40 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black uppercase tracking-wider text-amber-700 dark:text-amber-400 flex items-center gap-2">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        Reemplazo de Ítem Configurado
                                    </span>
                                    <button 
                                        type="button" 
                                        @click="revertItem" 
                                        class="text-xs font-bold text-gray-500 hover:text-gray-800 dark:hover:text-white underline flex items-center gap-1"
                                    >
                                        <i class="fa-solid fa-rotate-left"></i> Revertir al ítem original
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                    <div class="p-3 bg-white/80 dark:bg-gray-800/80 rounded-xl border border-rose-100 dark:border-rose-900/30">
                                        <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest block mb-1">
                                            🔴 Ítem Saliente (Pasará a DISPONIBLE)
                                        </span>
                                        <div class="font-black text-gray-800 dark:text-white">
                                            #{{ originalItem?.id }} [Nro. Inv: {{ originalItem?.codInv || 'S/N' }}]
                                        </div>
                                        <div class="text-gray-600 dark:text-gray-400">
                                            {{ originalItem?.tipo }} {{ originalItem?.marca }} {{ originalItem?.modelo }}
                                        </div>
                                    </div>
                                    <div class="p-3 bg-white/80 dark:bg-gray-800/80 rounded-xl border border-emerald-100 dark:border-emerald-900/30">
                                        <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest block mb-1">
                                            🟢 Nuevo Ítem Entrante (Pasará a VENDIDO)
                                        </span>
                                        <div class="font-black text-gray-800 dark:text-white">
                                            #{{ selectedItem.id }} [Nro. Inv: {{ selectedItem.codInv || 'S/N' }}]
                                        </div>
                                        <div class="text-gray-600 dark:text-gray-400">
                                            {{ selectedItem.tipo }} {{ selectedItem.marca }} {{ selectedItem.modelo }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Current / Selected Item Card -->
                            <div v-if="selectedItem" class="p-6 rounded-2xl border transition-all" :class="hasItemChanged ? 'bg-emerald-50/30 dark:bg-emerald-950/10 border-emerald-200 dark:border-emerald-800/40' : 'bg-gray-50/70 dark:bg-gray-900/40 border-gray-200 dark:border-gray-700/60'">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-gray-200/60 dark:border-gray-700/40">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <span class="px-3 py-1 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 font-black text-xs">
                                            Nro. Inv: {{ selectedItem.codInv || '#' + selectedItem.id }}
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-[11px]">
                                            ID #{{ selectedItem.id }}
                                        </span>
                                        <span v-if="selectedItem.container?.cod || selectedItem.container_cod" class="px-2.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 font-bold text-[11px] border border-amber-200 dark:border-amber-800/30">
                                            Contenedor: {{ selectedItem.container?.cod || selectedItem.container_cod }}
                                        </span>
                                        <span v-if="selectedItem.camera_side_label" class="px-2.5 py-0.5 rounded-md bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-bold text-[11px]">
                                            Lado: {{ selectedItem.camera_side_label }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase" :class="hasItemChanged ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/30' : 'bg-blue-500/10 text-blue-500 border border-blue-500/30'">
                                            {{ hasItemChanged ? 'NUEVO ASIGNADO' : 'FACTURADO ACTUALMENTE' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                                    <div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Descripción del Producto</span>
                                        <span class="text-base font-black text-gray-800 dark:text-white uppercase block mt-0.5">
                                            {{ selectedItem.tipo }} {{ selectedItem.marca }} {{ selectedItem.modelo }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Serial / Referencia</span>
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300 block mt-0.5">
                                            {{ selectedItem.serial || 'NO APLICA / S/N' }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Expediente de Importación</span>
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300 block mt-0.5">
                                            {{ selectedItem.container?.expediente || selectedItem.expediente || 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Second Item Card if Camera Pair -->
                            <div v-if="props.bill.second_inventario || props.bill.second_partida_id" class="p-4 rounded-xl bg-purple-50/30 dark:bg-purple-950/10 border border-purple-200 dark:border-purple-800/40 text-xs">
                                <span class="text-[10px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-widest block mb-1">
                                    <i class="fa-solid fa-layer-group mr-1"></i> Par de Cámaras (Segunda Cámara Asociada)
                                </span>
                                <div class="font-bold text-gray-800 dark:text-white">
                                    #{{ props.bill.second_inventario?.id }} [Nro. Inv: {{ props.bill.second_inventario?.codInv || 'S/N' }}] - {{ props.bill.second_inventario?.tipo }} {{ props.bill.second_inventario?.marca }} {{ props.bill.second_inventario?.modelo }}
                                </div>
                            </div>

                            <!-- Search & Selection Panel -->
                            <div v-if="isSearchOpen" class="p-6 rounded-2xl bg-indigo-50/40 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-800/40 space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-black text-indigo-900 dark:text-indigo-300 uppercase tracking-wider">
                                        <i class="fa-solid fa-magnifying-glass mr-1.5"></i>Buscar Ítem Disponible en Inventario
                                    </label>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 font-bold">
                                        Escribe el Nro. de Inventario (codInv) o ID
                                    </span>
                                </div>

                                <div class="relative">
                                    <div class="flex gap-2">
                                        <div class="relative flex-1">
                                            <i class="fa-solid fa-barcode absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                            <input 
                                                v-model="searchQuery"
                                                @input="onSearchInput"
                                                @focus="isDropdownOpen = true"
                                                @keydown.enter.prevent="verifyItem()"
                                                type="text"
                                                placeholder="Ejemplo: 627, TRHU-128, o busca 'Toyota 2RZ'..."
                                                class="appearance-none block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-white border border-indigo-200 dark:border-indigo-800 rounded-2xl py-3.5 pl-12 pr-4 font-bold text-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                                            />
                                        </div>
                                        <button 
                                            type="button" 
                                            @click="verifyItem()"
                                            :disabled="lookupLoading"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3.5 rounded-2xl text-xs uppercase tracking-wider transition-all flex items-center gap-2 disabled:opacity-50"
                                        >
                                            <i v-if="lookupLoading" class="fa-solid fa-circle-notch fa-spin"></i>
                                            <i v-else class="fa-solid fa-check"></i>
                                            <span>Verificar</span>
                                        </button>
                                    </div>

                                    <!-- Autocomplete Dropdown List -->
                                    <div 
                                        v-if="isDropdownOpen && filteredAvailableItems.length > 0 && searchQuery.trim().length > 0"
                                        class="absolute z-20 left-0 right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-2xl max-h-64 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700"
                                    >
                                        <div 
                                            v-for="item in filteredAvailableItems" 
                                            :key="item.id"
                                            @click="assignItem(item)"
                                            class="p-3.5 hover:bg-indigo-50/60 dark:hover:bg-indigo-950/40 cursor-pointer transition-colors flex items-center justify-between text-xs"
                                        >
                                            <div class="space-y-0.5">
                                                <div class="font-black text-gray-800 dark:text-white flex items-center gap-2">
                                                    <span class="px-2 py-0.5 rounded bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 font-bold text-[10px]">
                                                        Inv: {{ item.codInv || '#' + item.id }}
                                                    </span>
                                                    <span>{{ item.tipo }} {{ item.marca }} {{ item.modelo }}</span>
                                                </div>
                                                <div class="text-[11px] text-gray-400">
                                                    Serial: {{ item.serial || 'S/N' }} • Contenedor: {{ item.container?.cod || 'N/A' }}
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-1 rounded-full text-[9px] font-black uppercase bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                                    DISPONIBLE
                                                </span>
                                                <i class="fa-solid fa-chevron-right text-gray-300 dark:text-gray-600"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lookup Feedback Status -->
                                <div v-if="lookupError" class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/50 text-rose-700 dark:text-rose-300 text-xs flex items-start gap-3 animate-headShake">
                                    <i class="fa-solid fa-circle-xmark text-rose-500 text-base mt-0.5"></i>
                                    <div>
                                        <span class="font-bold block">No disponible para facturación</span>
                                        <span>{{ lookupError }}</span>
                                    </div>
                                </div>

                                <!-- Verified Available Item Preview Card -->
                                <div v-if="lookupResult && lookupResult.is_available && (!originalItem || lookupResult.id !== originalItem.id)" class="p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                            Ítem Disponible Encontrado
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500 text-white">
                                            Listo para asignar
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                                        <div>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase">Nro. Inv / ID:</span>
                                            <div class="font-black text-gray-800 dark:text-white">
                                                {{ lookupResult.codInv || '#' + lookupResult.id }} (ID: {{ lookupResult.id }})
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase">Producto:</span>
                                            <div class="font-bold text-gray-800 dark:text-white uppercase">
                                                {{ lookupResult.tipo }} {{ lookupResult.marca }} {{ lookupResult.modelo }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase">Serial / Contenedor:</span>
                                            <div class="font-bold text-gray-700 dark:text-gray-300">
                                                {{ lookupResult.serial || 'S/N' }} • {{ lookupResult.container_cod || 'N/A' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-2 flex justify-end">
                                        <button 
                                            type="button" 
                                            @click="assignItem(lookupResult)"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-black px-6 py-2.5 rounded-xl text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2"
                                        >
                                            <i class="fa-solid fa-plus"></i>
                                            <span>Asignar este Ítem a la Factura</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Financial Details -->
                        <div class="space-y-6 pt-12 border-t border-gray-100 dark:border-gray-700">
                             <h3 class="font-black text-xs uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-6 flex items-center">
                                <i class="fa-solid fa-sack-dollar mr-2 text-emerald-500"></i>Detalles Financieros
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Base Imponible</label>
                                    <input v-model="form.big" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">IVA (16%)</label>
                                    <input v-model="form.iva" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-rose-400 uppercase ml-1">IGTF (3%)</label>
                                    <input v-model="form.igtf" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Pago en BS</label>
                                    <input v-model="form.bs" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Valor Divisa ($)</label>
                                    <input v-model="form.divisa" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Customer Information -->
                        <div class="space-y-6 pt-12 border-t border-gray-100 dark:border-gray-700">
                             <h3 class="font-black text-xs uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-6 flex items-center">
                                <i class="fa-solid fa-address-card mr-2 text-blue-500"></i>Información del Cliente
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Nombre</label>
                                        <input v-model="form.client_name" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Cédula / RIF</label>
                                        <input v-model="form.client_cedula" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Teléfono</label>
                                        <input v-model="form.client_phone" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Correo Electrónico</label>
                                        <input v-model="form.client_email" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="email">
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Dirección</label>
                                <input v-model="form.client_address" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                            </div>
                            <div v-if="selectedItem && selectedItem.tipo && (selectedItem.tipo.toUpperCase().includes('MOTOR') || selectedItem.tipo.toUpperCase().includes('CAJA') || selectedItem.tipo.toUpperCase().includes('CÁMARA') || selectedItem.tipo.toUpperCase().includes('CAMARA') || selectedItem.tipo.toUpperCase().includes('CULATA'))" class="space-y-2">
                                <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Detalles de Despacho (Cómo sale)</label>
                                <select v-model="form.observaciones" class="block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-sm">
                                    <option value="" disabled>SELECCIONE UNA OPCIÓN</option>
                                    <template v-if="selectedItem.tipo.toUpperCase().includes('MOTOR')">
                                        <option value="MOTOR COMPLETO">MOTOR COMPLETO</option>
                                        <option value="MOTOR 7/8">MOTOR 7/8</option>
                                        <option value="MOTOR 3/4">MOTOR 3/4</option>
                                    </template>
                                    <template v-else-if="selectedItem.tipo.toUpperCase().includes('CAJA')">
                                        <option value="CAJA COMPLETA">CAJA COMPLETA</option>
                                        <option value="CAJA SIN TURBINA">CAJA SIN TURBINA</option>
                                    </template>
                                    <template v-else-if="selectedItem.tipo.toUpperCase().includes('CÁMARA') || selectedItem.tipo.toUpperCase().includes('CAMARA') || selectedItem.tipo.toUpperCase().includes('CULATA')">
                                        <option value="CÁMARA COMPLETA - LADO DERECHO (R)">CÁMARA COMPLETA - LADO DERECHO (R)</option>
                                        <option value="CÁMARA COMPLETA - LADO IZQUIERDO (L)">CÁMARA COMPLETA - LADO IZQUIERDO (L)</option>
                                        <option value="SOLO CAPARAZÓN - LADO DERECHO (R)">SOLO CAPARAZÓN - LADO DERECHO (R)</option>
                                        <option value="SOLO CAPARAZÓN - LADO IZQUIERDO (L)">SOLO CAPARAZÓN - LADO IZQUIERDO (L)</option>
                                        <option value="CÁMARA COMPLETA">CÁMARA COMPLETA (UN SOLO LADO)</option>
                                        <option value="SOLO CAPARAZÓN">SOLO CAPARAZÓN (UN SOLO LADO)</option>
                                        <option v-if="form.observaciones && !['CÁMARA COMPLETA - LADO IZQUIERDO (L)', 'CÁMARA COMPLETA - LADO DERECHO (R)', 'SOLO CAPARAZÓN - LADO IZQUIERDO (L)', 'SOLO CAPARAZÓN - LADO DERECHO (R)', 'CÁMARA COMPLETA', 'SOLO CAPARAZÓN'].includes(form.observaciones)" :value="form.observaciones">
                                            {{ form.observaciones }}
                                        </option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pt-10 flex flex-col md:flex-row items-center justify-between gap-6 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 text-xs">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>Los cambios se registrarán en la bitácora del sistema</span>
                            </div>
                            
                            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                                <button type="button" @click="router.visit(route('billing'))" class="w-full md:w-auto bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-200 font-bold py-4 px-12 rounded-[2rem] transition-all transform active:scale-95">
                                    CANCELAR
                                </button>
                                <button type="submit" :disabled="form.processing" class="w-full md:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 px-12 rounded-[2rem] shadow-2xl shadow-emerald-600/30 transition-all transform hover:scale-[1.03] active:scale-95 flex items-center justify-center gap-3">
                                    <i v-if="form.processing" class="fa-solid fa-circle-notch fa-spin"></i>
                                    <i v-else class="fa-solid fa-floppy-disk text-xl"></i>
                                    {{ form.processing ? 'GUARDANDO...' : 'ACTUALIZAR FACTURA' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
