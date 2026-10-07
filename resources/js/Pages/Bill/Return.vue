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

const todayDate = new Date().toISOString().split('T')[0];

const form = useForm({
    numero_factura: props.bill.numero_factura || 'S/N',
    numero_control: props.bill.numero_control || 'S/N',
    numero_nota_credito: props.bill.numero_nota_credito || '',
    numero_factura_afect: props.bill.numero_factura_afect || props.bill.numero_factura || 'S/N',
    return_type: 'TOTAL', // 'TOTAL', 'TEMPORAL', 'DESINCORPORACION', 'CAMBIO'
    nuevo_partida_id: '',
    fecha_cambio: todayDate,
    old_item_status: 'GARANTIA', // 'GARANTIA', 'DISPONIBLE', 'DESINCORPORADO'
    motivo_cambio: '',
});

// Search and selection state for CAMBIO
const searchQuery = ref('');
const isDropdownOpen = ref(false);
const selectedNewItem = ref(null);
const lookupLoading = ref(false);
const lookupError = ref('');

// Filter available items for quick selector
const filteredAvailableItems = computed(() => {
    if (!props.availableItems || props.availableItems.length === 0) return [];
    
    const query = searchQuery.value.trim().toLowerCase();
    const currentPartidaId = props.bill.partida_id;
    
    const available = props.availableItems.filter(item => item.id !== currentPartidaId);
    if (!query) return available.slice(0, 40);

    return available.filter(item => {
        const idMatch = String(item.id).includes(query);
        const codInvMatch = (item.codInv || '').toLowerCase().includes(query);
        const tipoMatch = (item.tipo || '').toLowerCase().includes(query);
        const marcaMatch = (item.marca || '').toLowerCase().includes(query);
        const modeloMatch = (item.modelo || '').toLowerCase().includes(query);
        const serialMatch = (item.serial || '').toLowerCase().includes(query);
        return idMatch || codInvMatch || tipoMatch || marcaMatch || modeloMatch || serialMatch;
    }).slice(0, 40);
});

// Select an item from the search list
const selectItem = (item) => {
    selectedNewItem.value = item;
    form.nuevo_partida_id = item.id;
    lookupError.value = '';
    isDropdownOpen.value = false;
    searchQuery.value = `#${item.id} - ${item.tipo} ${item.marca} ${item.modelo}`;
};

// Clear selected item
const clearSelectedItem = () => {
    selectedNewItem.value = null;
    form.nuevo_partida_id = '';
    searchQuery.value = '';
    lookupError.value = '';
};

// Real-time lookup when user types ID directly
let lookupTimeout = null;
const onIdInput = () => {
    clearTimeout(lookupTimeout);
    lookupTimeout = setTimeout(async () => {
        const rawId = String(form.nuevo_partida_id || '').trim();
        if (!rawId) {
            selectedNewItem.value = null;
            lookupError.value = '';
            return;
        }

        const idNum = parseInt(rawId, 10);
        if (isNaN(idNum) || idNum <= 0) {
            selectedNewItem.value = null;
            lookupError.value = 'Por favor ingrese un ID numérico válido.';
            return;
        }

        if (idNum === props.bill.partida_id) {
            selectedNewItem.value = null;
            lookupError.value = 'El nuevo elemento no puede ser el mismo elemento actual de la factura.';
            return;
        }

        // 1. Check local available items first (ultra-fast)
        const localMatch = props.availableItems.find(item => item.id === idNum);
        if (localMatch) {
            selectedNewItem.value = localMatch;
            lookupError.value = '';
            searchQuery.value = `#${localMatch.id} - ${localMatch.tipo} ${localMatch.marca} ${localMatch.modelo}`;
            return;
        }

        // 2. Query server for verification
        lookupLoading.value = true;
        lookupError.value = '';
        try {
            const response = await axios.get(route('billing.checkItem', { id: idNum }));
            if (response.data && response.data.success) {
                const itm = response.data.item;
                if (!itm.is_available) {
                    selectedNewItem.value = null;
                    lookupError.value = `El ítem #${itm.id} (${itm.tipo} ${itm.marca} ${itm.modelo}) no está DISPONIBLE (Estatus actual: ${itm.status}).`;
                } else {
                    selectedNewItem.value = itm;
                    lookupError.value = '';
                    searchQuery.value = `#${itm.id} - ${itm.tipo} ${itm.marca} ${itm.modelo}`;
                }
            }
        } catch (err) {
            selectedNewItem.value = null;
            if (err.response && err.response.status === 404) {
                lookupError.value = `No se encontró ningún elemento con el ID #${idNum}.`;
            } else {
                lookupError.value = 'Error al consultar el elemento en el inventario.';
            }
        } finally {
            lookupLoading.value = false;
        }
    }, 300);
};

const submit = () => {
    if (form.return_type === 'CAMBIO') {
        if (!form.nuevo_partida_id || !selectedNewItem.value) {
            lookupError.value = 'Debe indicar y verificar un nuevo elemento válido antes de confirmar el cambio.';
            return;
        }
    }
    form.post(route('billing.returnSubmit', props.bill.id));
};
</script>

<template>
    <AppLayout title="Devolución y Cambio de Factura">
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center transition-colors">
                    <i class="fa-solid fa-repeat mr-3 text-emerald-500"></i>Procesar Devolución / Cambio
                </h2>
                <div class="flex items-center gap-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                    <span>Factura Original #{{ props.bill.numero_factura }}</span>
                </div>
            </div>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                    
                    <!-- Header Card -->
                    <div class="bg-gradient-to-r from-emerald-500/10 via-indigo-500/10 to-amber-500/10 p-8 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="flex items-center gap-6">
                                <div class="h-16 w-16 bg-white dark:bg-gray-900 rounded-2xl flex items-center justify-center text-emerald-500 shadow-xl shadow-emerald-500/10 shrink-0">
                                    <i class="fa-solid fa-receipt text-3xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-tight">Detalles de la Factura Original</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest mt-1">
                                        Control: {{ props.bill.numero_control }} | Cliente: {{ props.bill.client_name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Current Item details card -->
                        <div class="mt-6 p-4 rounded-2xl bg-white/70 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/70 backdrop-blur-sm shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-lg bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                    <i class="fa-solid fa-box mr-1"></i>Elemento Actual en la Factura
                                </span>
                                <span class="text-xs font-black text-emerald-600 dark:text-emerald-400">
                                    {{ props.bill.total ? '$' + Number(props.bill.total).toLocaleString('es-VE', {minimumFractionDigits: 2}) : '' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                                <div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase block">ID / Tipo</span>
                                    <span class="font-black text-gray-800 dark:text-white">
                                        #{{ props.bill.partida?.id || props.bill.partida_id }} - {{ props.bill.partida?.tipo || 'Ítem' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase block">Marca y Modelo</span>
                                    <span class="font-bold text-gray-700 dark:text-gray-200">
                                        {{ props.bill.partida?.marca || 'N/A' }} {{ props.bill.partida?.modelo || '' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase block">Serial</span>
                                    <span class="font-bold text-gray-700 dark:text-gray-200">
                                        {{ props.bill.partida?.serial || 'S/N' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase block">Código Inv</span>
                                    <span class="font-bold text-gray-700 dark:text-gray-200">
                                        {{ props.bill.partida?.codInv || 'S/N' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="p-8 md:p-12 space-y-10">
                        
                        <!-- Section 1: Credit Note Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Número Nota de Crédito</label>
                                <div class="relative group">
                                    <i class="fa-solid fa-file-invoice absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition-colors"></i>
                                    <input v-model="form.numero_nota_credito" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-emerald-500 transition-all font-bold" type="text" placeholder="Ej: NC-001" required>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Factura Afectada</label>
                                <div class="relative group">
                                    <i class="fa-solid fa-link absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                    <input v-model="form.numero_factura_afect" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text" placeholder="Número Factura" required>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Return Type Radio Buttons -->
                        <div class="space-y-4">
                            <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Motivo / Tipo de Devolución</label>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Option 1: Total Return -->
                                <div 
                                    @click="form.return_type = 'TOTAL'"
                                    :class="form.return_type === 'TOTAL' ? 'border-emerald-500 bg-emerald-50/30 dark:bg-emerald-900/10 shadow-lg shadow-emerald-500/5' : 'border-gray-100 dark:border-gray-700 bg-transparent'"
                                    class="relative flex items-center p-5 border-2 rounded-3xl cursor-pointer transition-all hover:bg-emerald-50/20 group"
                                >
                                    <div class="flex items-center justify-center h-12 w-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 mr-4 group-hover:scale-110 transition-transform shrink-0">
                                        <i class="fa-solid fa-box-open text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0 pr-2">
                                        <h4 class="font-black text-gray-800 dark:text-white uppercase tracking-tight text-sm">Devolución Total</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-tight mt-0.5">El ítem regresará inmediatamente al stock disponible para la venta.</p>
                                    </div>
                                    <div v-if="form.return_type === 'TOTAL'" class="h-6 w-6 rounded-full bg-emerald-500 flex items-center justify-center text-white scale-110 shrink-0">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </div>
                                </div>

                                <!-- Option 2: Temporal Return -->
                                <div 
                                    @click="form.return_type = 'TEMPORAL'"
                                    :class="form.return_type === 'TEMPORAL' ? 'border-indigo-500 bg-indigo-50/30 dark:bg-indigo-900/10 shadow-lg shadow-indigo-500/5' : 'border-gray-100 dark:border-gray-700 bg-transparent'"
                                    class="relative flex items-center p-5 border-2 rounded-3xl cursor-pointer transition-all hover:bg-indigo-50/20 group"
                                >
                                    <div class="flex items-center justify-center h-12 w-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 mr-4 group-hover:scale-110 transition-transform shrink-0">
                                        <i class="fa-solid fa-screwdriver-wrench text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0 pr-2">
                                        <h4 class="font-black text-gray-800 dark:text-white uppercase tracking-tight text-sm">Devolución Temporal</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-tight mt-0.5">Permite mover el ítem a mantenimiento antes de reincorporarlo al stock.</p>
                                    </div>
                                    <div v-if="form.return_type === 'TEMPORAL'" class="h-6 w-6 rounded-full bg-indigo-500 flex items-center justify-center text-white scale-110 shrink-0">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </div>
                                </div>

                                <!-- Option 3: Desincorporacion -->
                                <div 
                                    @click="form.return_type = 'DESINCORPORACION'"
                                    :class="form.return_type === 'DESINCORPORACION' ? 'border-rose-500 bg-rose-50/30 dark:bg-rose-900/10 shadow-lg shadow-rose-500/5' : 'border-gray-100 dark:border-gray-700 bg-transparent'"
                                    class="relative flex items-center p-5 border-2 rounded-3xl cursor-pointer transition-all hover:bg-rose-50/20 group"
                                >
                                    <div class="flex items-center justify-center h-12 w-12 rounded-2xl bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 mr-4 group-hover:scale-110 transition-transform shrink-0">
                                        <i class="fa-solid fa-ban text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0 pr-2">
                                        <h4 class="font-black text-gray-800 dark:text-white uppercase tracking-tight text-sm">Desincorporación</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-tight mt-0.5">El ítem se marcará como dañado o inutilizable y saldrá del inventario activo.</p>
                                    </div>
                                    <div v-if="form.return_type === 'DESINCORPORACION'" class="h-6 w-6 rounded-full bg-rose-500 flex items-center justify-center text-white scale-110 shrink-0">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </div>
                                </div>

                                <!-- Option 4: Cambio de Elemento -->
                                <div 
                                    @click="form.return_type = 'CAMBIO'"
                                    :class="form.return_type === 'CAMBIO' ? 'border-amber-500 bg-amber-50/30 dark:bg-amber-900/10 shadow-lg shadow-amber-500/5' : 'border-gray-100 dark:border-gray-700 bg-transparent'"
                                    class="relative flex items-center p-5 border-2 rounded-3xl cursor-pointer transition-all hover:bg-amber-50/20 group"
                                >
                                    <div class="flex items-center justify-center h-12 w-12 rounded-2xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 mr-4 group-hover:scale-110 transition-transform shrink-0">
                                        <i class="fa-solid fa-arrows-rotate text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0 pr-2">
                                        <h4 class="font-black text-gray-800 dark:text-white uppercase tracking-tight text-sm">Cambio de Elemento</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-tight mt-0.5">Sustituye el ítem de la factura por otro motor, caja, cámara, etc. disponible.</p>
                                    </div>
                                    <div v-if="form.return_type === 'CAMBIO'" class="h-6 w-6 rounded-full bg-amber-500 flex items-center justify-center text-white scale-110 shrink-0">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: CAMBIO Panel (Only visible when return_type === 'CAMBIO') -->
                        <div v-if="form.return_type === 'CAMBIO'" class="p-6 md:p-8 rounded-[2rem] bg-amber-500/5 dark:bg-amber-950/20 border-2 border-amber-500/30 space-y-6">
                            
                            <div class="flex items-center justify-between border-b border-amber-200 dark:border-amber-800/50 pb-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/20">
                                        <i class="fa-solid fa-arrows-rotate text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-gray-800 dark:text-white uppercase tracking-tight text-base">
                                            Selección del Nuevo Elemento (Reemplazo)
                                        </h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Indique el ID del otro motor, caja, cámara, etc. que reemplazará al producto actual.
                                        </p>
                                    </div>
                                </div>
                                <span class="hidden sm:inline-block px-3 py-1 rounded-xl text-[10px] font-black uppercase bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-700">
                                    Cambio Activo
                                </span>
                            </div>

                            <!-- Fields for ID and Search Selection -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <!-- Direct ID Input -->
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase flex items-center gap-1.5">
                                        <i class="fa-solid fa-hashtag text-amber-500"></i>ID del Nuevo Elemento *
                                    </label>
                                    <div class="relative group">
                                        <input 
                                            v-model="form.nuevo_partida_id" 
                                            @input="onIdInput"
                                            type="number" 
                                            min="1"
                                            placeholder="Ej: 345" 
                                            class="appearance-none block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-white border-2 border-amber-200 dark:border-amber-800/60 rounded-2xl py-3.5 pl-4 pr-12 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 font-black text-lg transition-all"
                                            required
                                        >
                                        <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                                            <i v-if="lookupLoading" class="fa-solid fa-circle-notch fa-spin text-amber-500"></i>
                                            <i v-else-if="selectedNewItem" class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                                            <button v-if="selectedNewItem" type="button" @click="clearSelectedItem" class="text-gray-400 hover:text-rose-500 text-xs">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500">
                                        Escriba el número de ID exacto del nuevo motor, caja o cámara.
                                    </p>
                                </div>

                                <!-- Filterable Search Dropdown -->
                                <div class="space-y-2 relative">
                                    <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase flex items-center gap-1.5">
                                        <i class="fa-solid fa-magnifying-glass text-amber-500"></i>O Buscar en Stock Disponible
                                    </label>
                                    <div class="relative group">
                                        <input 
                                            v-model="searchQuery" 
                                            @focus="isDropdownOpen = true"
                                            type="text" 
                                            placeholder="Buscar por marca, modelo, serial, código..." 
                                            class="appearance-none block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-white border border-gray-200 dark:border-gray-700 rounded-2xl py-3.5 pl-4 pr-10 focus:ring-2 focus:ring-amber-500 transition-all text-xs font-semibold"
                                        >
                                        <button 
                                            type="button" 
                                            @click="isDropdownOpen = !isDropdownOpen" 
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-amber-500 transition-colors"
                                        >
                                            <i class="fa-solid" :class="isDropdownOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                        </button>
                                    </div>

                                    <!-- Dropdown list -->
                                    <div 
                                        v-if="isDropdownOpen" 
                                        class="absolute z-30 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-2 space-y-1 divide-y divide-gray-50 dark:divide-gray-700/50"
                                    >
                                        <div 
                                            v-if="filteredAvailableItems.length === 0" 
                                            class="p-4 text-center text-xs text-gray-400 font-bold"
                                        >
                                            No se encontraron elementos disponibles con esa búsqueda.
                                        </div>
                                        <div 
                                            v-for="item in filteredAvailableItems" 
                                            :key="item.id" 
                                            @click="selectItem(item)" 
                                            class="p-3 rounded-xl hover:bg-amber-50 dark:hover:bg-amber-950/40 cursor-pointer transition-all flex items-center justify-between group"
                                        >
                                            <div class="min-w-0 pr-2">
                                                <div class="font-black text-xs text-gray-800 dark:text-white group-hover:text-amber-600 transition-colors">
                                                    #{{ item.id }} - {{ item.tipo }} {{ item.marca }} {{ item.modelo }}
                                                </div>
                                                <div class="text-[10px] text-gray-400 mt-0.5">
                                                    Serial: {{ item.serial || 'S/N' }} | Cód: {{ item.codInv || 'S/N' }} {{ item.año ? '| Año: ' + item.año : '' }}
                                                </div>
                                            </div>
                                            <span class="text-[9px] font-black uppercase text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-md shrink-0">
                                                DISPONIBLE
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Validation Error Message -->
                            <div v-if="lookupError" class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/60 text-rose-600 dark:text-rose-400 text-xs font-bold flex items-center gap-3">
                                <i class="fa-solid fa-circle-exclamation text-lg shrink-0"></i>
                                <span>{{ lookupError }}</span>
                            </div>

                            <!-- Preview Card of Selected New Item -->
                            <div v-if="selectedNewItem" class="p-5 rounded-2xl bg-white dark:bg-gray-900 border-2 border-emerald-500/50 shadow-xl shadow-emerald-500/5 space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="h-7 w-7 rounded-lg bg-emerald-500 text-white flex items-center justify-center font-black text-xs shadow-md shadow-emerald-500/20">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        <span class="text-xs font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                                            Nuevo Ítem Confirmado para Asignar
                                        </span>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                        LISTO PARA VENTA
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs pt-1">
                                    <div>
                                        <span class="text-[10px] font-bold uppercase text-gray-400 block">ID / Tipo</span>
                                        <span class="font-black text-gray-800 dark:text-white">
                                            #{{ selectedNewItem.id }} - {{ selectedNewItem.tipo }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold uppercase text-gray-400 block">Marca y Modelo</span>
                                        <span class="font-bold text-gray-700 dark:text-gray-200">
                                            {{ selectedNewItem.marca }} {{ selectedNewItem.modelo }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold uppercase text-gray-400 block">Serial</span>
                                        <span class="font-bold text-gray-700 dark:text-gray-200">
                                            {{ selectedNewItem.serial || 'S/N' }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold uppercase text-gray-400 block">Código Inv</span>
                                        <span class="font-bold text-gray-700 dark:text-gray-200">
                                            {{ selectedNewItem.codInv || 'S/N' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Destination of the Old Item -->
                            <div class="space-y-3 pt-2">
                                <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase">
                                    Destino del Ítem Anterior (#{{ props.bill.partida_id }}) que retorna el cliente:
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div 
                                        @click="form.old_item_status = 'GARANTIA'" 
                                        :class="form.old_item_status === 'GARANTIA' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900'"
                                        class="p-3.5 border-2 rounded-2xl cursor-pointer transition-all flex flex-col justify-between"
                                    >
                                        <div class="font-bold text-xs text-gray-800 dark:text-white flex items-center gap-2">
                                            <i class="fa-solid fa-screwdriver-wrench text-indigo-500"></i>
                                            <span>Garantía / Taller</span>
                                        </div>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">Genera ticket para revisión técnica mecánica (Recomendado).</p>
                                    </div>

                                    <div 
                                        @click="form.old_item_status = 'DISPONIBLE'" 
                                        :class="form.old_item_status === 'DISPONIBLE' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900'"
                                        class="p-3.5 border-2 rounded-2xl cursor-pointer transition-all flex flex-col justify-between"
                                    >
                                        <div class="font-bold text-xs text-gray-800 dark:text-white flex items-center gap-2">
                                            <i class="fa-solid fa-box-open text-emerald-500"></i>
                                            <span>Stock Disponible</span>
                                        </div>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">Reingresa directo a inventario para venta (Si está operativo).</p>
                                    </div>

                                    <div 
                                        @click="form.old_item_status = 'DESINCORPORADO'" 
                                        :class="form.old_item_status === 'DESINCORPORADO' ? 'border-rose-500 bg-rose-50 dark:bg-rose-900/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900'"
                                        class="p-3.5 border-2 rounded-2xl cursor-pointer transition-all flex flex-col justify-between"
                                    >
                                        <div class="font-bold text-xs text-gray-800 dark:text-white flex items-center gap-2">
                                            <i class="fa-solid fa-ban text-rose-500"></i>
                                            <span>Desincorporar</span>
                                        </div>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">Marcar como dañado/inoperativo y dar de baja del stock.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- New Date of Billing and Optional Motivo -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold text-gray-700 dark:text-gray-300 uppercase ml-1 flex items-center gap-1.5">
                                        <i class="fa-solid fa-calendar-day text-amber-500"></i>Nueva Fecha de Facturación *
                                    </label>
                                    <input 
                                        v-model="form.fecha_cambio" 
                                        type="date" 
                                        required
                                        class="appearance-none block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-white border border-gray-200 dark:border-gray-700 rounded-2xl py-3 px-4 focus:ring-2 focus:ring-amber-500 text-xs font-bold"
                                    >
                                    <p class="text-[9px] text-gray-400 ml-1">La factura se actualizará con esta fecha para el nuevo elemento (día del cambio).</p>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">
                                        Motivo del Cambio (Opcional)
                                    </label>
                                    <input 
                                        v-model="form.motivo_cambio" 
                                        type="text" 
                                        placeholder="Ej: Falla en compresión de cilindro 3, cambio por garantía..." 
                                        class="appearance-none block w-full bg-white dark:bg-gray-900 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-700 rounded-2xl py-3 px-4 focus:ring-2 focus:ring-amber-500 text-xs font-semibold"
                                    >
                                </div>
                            </div>

                            <!-- Visual Comparison Summary -->
                            <div v-if="selectedNewItem" class="p-4 rounded-2xl bg-white dark:bg-gray-900 border border-amber-200 dark:border-amber-800/40 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold shrink-0">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[10px] font-black uppercase text-rose-500 tracking-wider block">Ítem Saliente:</span>
                                        <p class="text-xs font-bold text-gray-800 dark:text-white truncate">
                                            #{{ props.bill.partida?.id || props.bill.partida_id }} - {{ props.bill.partida?.tipo }} {{ props.bill.partida?.marca }} {{ props.bill.partida?.modelo }}
                                        </p>
                                        <span class="text-[10px] text-gray-400">Pasa a: {{ form.old_item_status }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold shrink-0">
                                        <i class="fa-solid fa-right-to-bracket"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[10px] font-black uppercase text-emerald-500 tracking-wider block">Nuevo Ítem Asignado:</span>
                                        <p class="text-xs font-bold text-gray-800 dark:text-white truncate">
                                            #{{ selectedNewItem.id }} - {{ selectedNewItem.tipo }} {{ selectedNewItem.marca }} {{ selectedNewItem.modelo }}
                                        </p>
                                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">
                                            Pasa a: VENDIDO (Fecha: {{ form.fecha_cambio }})
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Actions -->
                        <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-6 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 text-xs font-bold uppercase tracking-widest">
                                <i 
                                    class="fa-solid fa-circle-exclamation text-base" 
                                    :class="form.return_type === 'TEMPORAL' ? 'text-indigo-500' : (form.return_type === 'CAMBIO' ? 'text-amber-500' : 'text-rose-500')"
                                ></i>
                                <span v-if="form.return_type === 'CAMBIO'">
                                    Esta acción sustituirá el elemento en la factura #{{ props.bill.numero_factura }} por el nuevo ítem (#{{ form.nuevo_partida_id || '...' }}), actualizando la fecha al día del cambio y manteniendo la factura activa
                                </span>
                                <span v-else-if="form.return_type === 'TEMPORAL'">
                                    Esta acción generará una nota de crédito afectando a la factura #{{ props.bill.numero_factura }} sin eliminarla
                                </span>
                                <span v-else>
                                    Esta acción eliminará la factura #{{ props.bill.numero_factura }} permanentemente
                                </span>
                            </div>
                            
                            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                                <button type="button" @click="router.visit(route('billing'))" class="w-full md:w-auto bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold py-4 px-12 rounded-[2rem] transition-all transform active:scale-95">
                                    CANCELAR
                                </button>
                                <button 
                                    type="submit" 
                                    :disabled="form.processing || (form.return_type === 'CAMBIO' && (!form.nuevo_partida_id || !selectedNewItem))" 
                                    :class="form.return_type === 'CAMBIO' ? 'bg-amber-600 hover:bg-amber-700 shadow-amber-600/30' : 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/30'"
                                    class="w-full md:w-auto text-white font-black py-4 px-12 rounded-[2rem] shadow-2xl transition-all transform hover:scale-[1.03] active:scale-95 flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                                >
                                    <i v-if="form.processing" class="fa-solid fa-circle-notch fa-spin"></i>
                                    <i v-else :class="form.return_type === 'CAMBIO' ? 'fa-solid fa-arrows-rotate' : 'fa-solid fa-check-double'" class="text-xl"></i>
                                    {{ form.processing ? 'PROCESANDO...' : (form.return_type === 'CAMBIO' ? 'CONFIRMAR CAMBIO' : 'CONFIRMAR DEVOLUCIÓN') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
