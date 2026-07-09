<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  inventario: Object,
  qrCode: String,
  barcode: String,
  barcodeData: String,
  tasa_bcv: Number,
});

const vehicleType = ref('Cargando...');
const vehicleExample = ref('');
const loadingVehicleType = ref(true);

onMounted(() => {
    axios.get(route('inventario.vehicle_type'), {
        params: {
            marca: props.inventario.marca,
            modelo: props.inventario.modelo,
            ano: props.inventario.año
        }
    }).then(response => {
        vehicleType.value = response.data.tipo_vehiculo;
        vehicleExample.value = response.data.ejemplo;
    }).catch(error => {
        console.error('Error al obtener el tipo de vehículo:', error);
        vehicleType.value = 'N/A';
        vehicleExample.value = '';
    }).finally(() => {
        loadingVehicleType.value = false;
    });
});

const alreadyRequested = computed(() => {
    return (props.inventario.billing_requests && props.inventario.billing_requests.length > 0) || 
           (props.inventario.bill && props.inventario.bill.length > 0);
});

const baseImponible = computed(() => {
    return parseFloat(props.inventario.costo_importacion_unitario || 0) + parseFloat(props.inventario.costo_taller || 0);
});

const iva = computed(() => {
    return baseImponible.value * 0.16;
});

const totalConIva = computed(() => {
    return baseImponible.value * 1.16;
});

const form = useForm({
    partida_id: props.inventario.id,
    price: props.inventario.price_sale || 0,
    client_name: '',
    client_cedula: '',
    client_phone: '',
    client_address: '',
    quantity: 1,
    client_cedula_file: null,
});

const submitBilling = () => {
    form.transform((data) => ({
        ...data,
        client_name: data.client_name ? data.client_name.toUpperCase() : '',
        client_cedula: data.client_cedula ? data.client_cedula.toUpperCase() : '',
        price: data.price.toString().replace(/\./g, '').replace(',', '.')
    })).post(route('billing.requests.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset('client_name', 'client_cedula_file', 'quantity');
            router.visit(route('inventario'));
        },
    });
};
</script>

<template>
    <AppLayout title="Detalle de Registro">
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center justify-center">
                <i class="fa-solid fa-box mr-2 text-indigo-500"></i>Registro: {{ props.inventario.marca }} {{ props.inventario.modelo }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 p-8">
                    
                    <!-- Detail Layout -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        
                        <!-- Left: Info -->
                        <div class="lg:col-span-2">
                             <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6 flex items-center">
                                <i class="fa-solid fa-circle-info mr-2 text-indigo-500"></i>Información General
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-copyright mr-1"></i>Marca
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-white rounded-xl py-3 px-4 font-semibold border border-gray-100 dark:border-gray-700">
                                        {{ props.inventario.marca }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-car mr-1"></i>Modelo
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-white rounded-xl py-3 px-4 font-semibold border border-gray-100 dark:border-gray-700">
                                        {{ props.inventario.modelo }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-barcode mr-1"></i>Serial
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-white rounded-xl py-3 px-4 font-semibold border border-gray-100 dark:border-gray-700">
                                        {{ props.inventario.serial || '-' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-calendar-check mr-1"></i>Año
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-white rounded-xl py-3 px-4 font-semibold border border-gray-100 dark:border-gray-700">
                                        {{ props.inventario.año }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-truck-pickup mr-1"></i>Tipo de Vehículo
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-white rounded-xl py-3 px-4 font-semibold border border-gray-100 dark:border-gray-700 min-h-[46px] flex flex-col justify-center">
                                        <span v-if="loadingVehicleType" class="text-xs text-gray-400 flex items-center">
                                            <i class="fa-solid fa-circle-notch fa-spin mr-2"></i>Consultando API...
                                        </span>
                                        <div v-else>
                                            <span class="font-bold">{{ vehicleType }}</span>
                                            <span v-if="vehicleExample" class="text-xs text-gray-400 block mt-0.5 font-normal">
                                                Ejemplo: {{ vehicleExample }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-barcode mr-1"></i>Inventario
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-indigo-600 dark:text-indigo-400 rounded-xl py-3 px-4 font-bold border border-gray-100 dark:border-gray-700">
                                        {{ props.inventario.codInv }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-file-invoice mr-1"></i>Expediente
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-white rounded-xl py-3 px-4 border border-gray-100 dark:border-gray-700">
                                        {{ props.inventario.expediente }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-check-to-slot mr-1"></i>Condición
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-white rounded-xl py-3 px-4 border border-gray-100 dark:border-gray-700">
                                        {{ props.inventario.condicion }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-signal mr-1"></i>Estatus
                                    </label>
                                    <div class="flex">
                                        <span :class="{
                                            'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300': props.inventario.status === 'DISPONIBLE',
                                            'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300': props.inventario.status === 'VENDIDO',
                                            'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300': props.inventario.status === 'EN TALLER',
                                            'bg-slate-100 text-slate-800 dark:bg-slate-900/40 dark:text-slate-300': props.inventario.status === 'INOPERATIVO-DESARMADO',
                                        }" class="px-4 py-2 rounded-xl text-sm font-bold uppercase border border-transparent">
                                            {{ props.inventario.status }}
                                        </span>
                                    </div>
                                </div>
                                <div v-if="props.inventario.origen === 'IMPORTADO' || props.inventario.costo_importacion_unitario > 0">
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-money-bill-transfer mr-1"></i>Costo de Importación + Mantenimiento
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-300 rounded-xl py-3.5 px-5 border border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                        <div class="flex flex-col">
                                            <span class="text-[9px] text-gray-400 uppercase tracking-wide">Total + IVA 16% (Bs.)</span>
                                            <span class="text-xl font-black text-indigo-600 dark:text-indigo-400">Bs. {{ totalConIva.toLocaleString('es-VE', {minimumFractionDigits: 2}) }}</span>
                                        </div>
                                        
                                        <div class="flex items-center gap-6">
                                            <!-- Detailed Breakdown -->
                                            <div class="flex flex-col text-right text-[11px] text-gray-500 font-semibold gap-0.5">
                                                <span>Base Imponible: Bs. {{ baseImponible.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</span>
                                                <span class="text-xs">IVA (16%): Bs. {{ iva.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</span>
                                                <span v-if="props.inventario.costo_taller > 0" class="text-[9px] text-gray-400 font-normal">
                                                    (Imp: Bs. {{ parseFloat(props.inventario.costo_importacion_unitario || 0).toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }} 
                                                    + Taller: Bs. {{ parseFloat(props.inventario.costo_taller || 0).toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }})
                                                </span>
                                            </div>

                                            <!-- Conversion to Dolar -->
                                            <div v-if="props.tasa_bcv > 0" class="flex flex-col items-end border-l border-gray-200 dark:border-gray-700 pl-4">
                                                <span class="text-[9px] text-indigo-500 dark:text-indigo-400 uppercase font-black">Conversión BCV (Con IVA)</span>
                                                <span class="text-xs font-black text-indigo-600 dark:text-indigo-400">
                                                    $ {{ (totalConIva / parseFloat(props.tasa_bcv)).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}
                                                </span>
                                                <span class="text-[8px] text-gray-400 font-medium">Tasa: Bs. {{ parseFloat(props.tasa_bcv).toFixed(2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-money-bill-transfer mr-1"></i>Costo de Adquisición
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-300 rounded-xl py-3 px-4 font-bold border border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                        <span>${{ parseFloat(props.inventario.costo || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-tag mr-1"></i>Precio de Venta Sugerido
                                    </label>
                                    <div class="w-full bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 rounded-xl py-3 px-4 text-xl font-black border border-indigo-100 dark:border-indigo-900/30">
                                        ${{ parseFloat(props.inventario.price_sale || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Observation Display -->
                            <div v-if="props.inventario.observation" class="mt-6 p-6 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700">
                                <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                    <i class="fa-solid fa-comment-dots mr-1 text-indigo-500"></i>Observaciones
                                </label>
                                <div class="text-sm font-semibold text-gray-800 dark:text-white leading-relaxed whitespace-pre-line">
                                    {{ props.inventario.observation }}
                                </div>
                            </div>

                            <!-- Codes Row -->
                            <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-8 p-6 bg-gray-50 dark:bg-gray-900/30 rounded-2xl border border-gray-100 dark:border-gray-700">
                                <div class="text-center">
                                    <h4 class="font-bold mb-4 text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400">Código QR</h4>
                                    <div class="inline-block bg-white p-3 rounded-xl shadow-inner" v-html="props.qrCode"></div>
                                </div>
                                <div class="text-center">
                                    <h4 class="font-bold mb-4 text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400">Código de Barras</h4>
                                    <div class="inline-block bg-white p-4 rounded-xl shadow-inner">
                                        <div v-html="props.barcode"></div>
                                        <p class="text-[10px] font-mono text-black mt-2 tracking-[0.2em]">{{ props.barcodeData }}</p>
                                    </div>
                                    <div class="mt-6">
                                        <a :href="route('printInventario', props.inventario.id)" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg hover:shadow-indigo-500/30">
                                            <i class="fa-solid fa-print mr-2"></i>Imprimir Etiqueta
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Actions/Billing -->
                        <div class="lg:col-span-1">
                             <div class="bg-indigo-600 dark:bg-indigo-900/40 p-8 rounded-3xl shadow-2xl dark:shadow-none text-white sticky top-8 border border-indigo-400 dark:border-indigo-800">
                                <h3 class="text-xl font-bold mb-6 flex items-center">
                                    <i class="fa-solid fa-paper-plane mr-2"></i>Facturación
                                </h3>
                                
                                <div v-if="props.inventario.status === 'INOPERATIVO-DESARMADO'" class="bg-white/10 dark:bg-black/20 p-6 rounded-2xl border border-white/20 dark:border-indigo-800/50 flex flex-col items-center text-center space-y-4">
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-ban text-3xl opacity-55"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-lg">Inoperativo / Desarmado</p>
                                        <p class="text-sm opacity-80 mt-1">Este ítem se encuentra inoperativo o desarmado. No está disponible para la venta o facturación.</p>
                                    </div>
                                </div>

                                <div v-else-if="alreadyRequested || props.inventario.status === 'VENDIDO'" class="bg-white/10 dark:bg-black/20 p-6 rounded-2xl border border-white/20 dark:border-indigo-800/50 flex flex-col items-center text-center space-y-4">
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-lock text-3xl opacity-50"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-lg">Vendido o Solicitado</p>
                                        <p class="text-sm opacity-80 mt-1">Este ítem ya ha sido procesado o tiene una solicitud pendiente de revisión.</p>
                                    </div>
                                </div>

                                <form v-else @submit.prevent="submitBilling" class="space-y-5">
                                    <div>
                                        <label class="block text-xs font-bold mb-2 uppercase opacity-80">Precio Final ($)</label>
                                        <input v-model="form.price" type="text" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white placeholder-white/40 focus:ring-2 focus:ring-white outline-none" placeholder="0.00" required>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold mb-2 uppercase opacity-80">Cédula / RIF</label>
                                            <input v-model="form.client_cedula" type="text" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white placeholder-white/40 focus:ring-2 focus:ring-white outline-none" placeholder="V-12345678">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold mb-2 uppercase opacity-80">Razón Social / Nombre</label>
                                            <input v-model="form.client_name" type="text" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white placeholder-white/40 focus:ring-2 focus:ring-white outline-none" placeholder="Nombre completo">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold mb-2 uppercase opacity-80">Capture de Cédula / RIF (Opcional)</label>
                                        <input @input="form.client_cedula_file = $event.target.files[0]" type="file" accept="image/*" class="block w-full text-sm text-white/80 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-white/20 file:text-white hover:file:bg-white/30 border border-white/20 rounded-xl py-2 px-2 focus:outline-none bg-white/10">
                                        <p class="mt-1 text-xs text-white/50">Formatos permitidos: JPG, PNG (Máx 2MB)</p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold mb-2 uppercase opacity-80">Teléfono (Opcional)</label>
                                            <input v-model="form.client_phone" type="text" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white placeholder-white/40 focus:ring-2 focus:ring-white outline-none" placeholder="0412-1234567">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold mb-2 uppercase opacity-80">Dirección (Opcional)</label>
                                            <textarea v-model="form.client_address" rows="2" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white placeholder-white/40 focus:ring-2 focus:ring-white outline-none resize-none" placeholder="Dirección del cliente"></textarea>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" :disabled="form.processing" class="w-full bg-white text-indigo-700 font-bold py-4 px-6 rounded-2xl shadow-xl transition-all transform hover:scale-[1.05] active:scale-95 disabled:opacity-50 flex items-center justify-center text-lg">
                                        <i class="fa-solid fa-circle-check mr-2"></i>{{ form.processing ? 'Enviando...' : 'Solicitar Venta' }}
                                    </button>
                                </form>
                             </div>
                        </div>
                    </div>

                    <!-- Maintenance History (Full width bottom) -->
                    <div class="mt-16 border-t border-gray-100 dark:border-gray-700 pt-12">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-2xl font-black text-gray-800 dark:text-white flex items-center">
                                <i class="fa-solid fa-wrench mr-3 text-indigo-500"></i>Bitácora de Mantenimiento
                            </h3>
                        </div>

                        <div v-if="props.inventario.maintenances && props.inventario.maintenances.length > 0" class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Fecha</th>
                                        <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Servicio</th>
                                        <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Estado</th>
                                        <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Técnico</th>
                                        <th class="px-6 py-4 text-right text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                    <tr v-for="maintenance in props.inventario.maintenances" :key="maintenance.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ maintenance.fecha }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 dark:text-indigo-400 font-black">{{ maintenance.tipo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 text-xs font-black rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                                {{ maintenance.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ maintenance.nombre_mecanico }} {{ maintenance.apellido_mecanico }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a :href="route('maintenance.show', maintenance.id)" class="bg-gray-100 dark:bg-gray-700 px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 font-bold hover:bg-indigo-500 hover:text-white transition-all">Ver Detalle</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-16 bg-gray-50 dark:bg-gray-900/30 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                             <div class="bg-gray-100 dark:bg-gray-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-folder-open text-gray-400 text-2xl"></i>
                             </div>
                             <p class="text-gray-500 dark:text-gray-400 font-bold">Sin registros de mantenimiento</p>
                             <p class="text-sm text-gray-400 mt-1">Este equipo no ha sido ingresado al taller hasta el momento.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>