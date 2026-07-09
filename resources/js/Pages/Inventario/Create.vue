<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted } from 'vue';

const props = defineProps({
  containers: Object,
});

const getDefaultTipo = () => {
    if (typeof window === 'undefined') return 'MOTOR 7/8';
    const path = window.location.pathname;
    if (path.includes('autopart')) return 'AUTOPARTE';
    if (path.includes('camara')) return 'CÁMARA';
    return 'MOTOR 7/8';
};

const form = useForm({
    container_id: '',
    tipo: getDefaultTipo(),
    origen: 'IMPORTADO', // Default
    marca: '',
    modelo: '',
    serial: '',
    año: '',
    codInv: '',
    expediente: '',
    cantidad: '',
    categorie: '', // For Autopartes name
    price: '0.00', // Venta default
    costo: '', // Costo (New)
    condicion: 'APLICA',
    status: 'DISPONIBLE',
    observation: '',
});

const tipoSelect = ref(null);

onMounted(() => {
    tipoSelect.value?.focus();
});

const isAutoparte = computed(() => form.tipo === 'AUTOPARTE');

// Watch container to auto-fill expediente
watch(() => form.container_id, (newVal) => {
    const container = props.containers.find(c => c.id == newVal);
    if (container) {
        form.expediente = container.expediente;
    }
});

// Format currency helpers
const formatCurrency = (value) => {
    if (value === null || value === undefined || value === '') return '';
    let strVal = String(value).trim();
    if (strVal.endsWith('.00')) {
        strVal = strVal.slice(0, -3);
    }
    if (strVal === '0' || strVal === '0.00') return '';
    return strVal
        .replace(/\D/g, "")
        .replace(/([0-9])([0-9]{3})$/, "$1.$2")
        .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
};

const handlePriceInput = (field) => {
    form[field] = formatCurrency(form[field]);
};

const submit = () => {
    // Clean currency before submit if needed, or backend handles it? 
    // Current controller just saves. Usually we should strip dots.
    // Assuming backend string column specific for this app format '1.000.000'
    form.marca = form.marca?.toUpperCase();
    form.modelo = form.modelo?.toUpperCase();
    form.serial = form.serial?.toUpperCase();
    form.post(route('storeInventario'));
};
</script>

<template>
    <AppLayout title="Crear Registro">
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                <i class="fa-solid fa-plus-circle mr-2 text-indigo-500"></i>Registrar Item
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 p-8">
                    <form @submit.prevent="submit" class="space-y-8">
                        
                        <!-- Top Selector Section -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pb-6 border-b border-gray-100 dark:border-gray-700">
                            <!-- Origen Selection -->
                            <div>
                                <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                    <i class="fa-solid fa-earth-americas mr-1 text-blue-500"></i>Origen
                                </label>
                                <select v-model="form.origen" class="block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors">
                                    <option value="IMPORTADO">IMPORTADO</option>
                                    <option value="NACIONAL">NACIONAL</option>
                                </select>
                                <InputError :message="form.errors.origen" class="mt-2" />
                            </div>

                            <!-- Type Selection -->
                            <div>
                                <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                    <i class="fa-solid fa-list-check mr-1 text-indigo-500"></i>Tipo de Registro
                                </label>
                                <select ref="tipoSelect" autofocus v-model="form.tipo" class="block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors">
                                    <option value="MOTOR 3/4">Motor 3/4</option>
                                    <option value="MOTOR 5/8">Motor 5/8</option>
                                    <option value="MOTOR 7/8">Motor 7/8</option>
                                    <option value="MOTOR COMPLETO">Motor COMPLETO</option>
                                    <option value="CAJA AUTOMÁTICA">Caja Automática</option>
                                    <option value="CAJA SINCRÓNICA">Caja Sincrónica</option>
                                    <option value="CÁMARA">Cámara</option>
                                    <option value="AUTOPARTE">Autoparte (Pieza)</option>
                                </select>
                                <InputError :message="form.errors.tipo" class="mt-2" />
                            </div>
                        </div>

                        <!-- Import Details (Only if IMPORTADO) -->
                        <div v-if="form.origen === 'IMPORTADO'" class="bg-indigo-50 dark:bg-gray-900/50 rounded-2xl border border-indigo-100 dark:border-indigo-900/30 p-6 space-y-4">
                            <h3 class="font-bold text-indigo-700 dark:text-indigo-400 flex items-center">
                                <i class="fa-solid fa-ship mr-2"></i>Datos de Importación (Contenedor)
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-box-open mr-1 text-indigo-500"></i>Container
                                    </label>
                                    <select v-model="form.container_id" class="block w-full bg-white dark:bg-gray-800 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-2 px-3 leading-tight focus:outline-none focus:border-indigo-500" required>
                                        <option v-for="container in containers" :key="container.id" :value="container.id">
                                            {{ container.cod }} - {{ container.expediente }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.container_id" class="mt-2" />
                                </div>

                                <div>
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-file-invoice mr-1 text-indigo-500"></i>Expediente
                                    </label>
                                    <input v-model="form.expediente" class="appearance-none block w-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-600 rounded-lg py-2 px-3 leading-tight focus:outline-none" type="text" readonly>
                                    <InputError :message="form.errors.expediente" class="mt-2" />
                                </div>

                                <!-- Cod. Inventario HIDDEN as it is now automatic -->
                                <div class="hidden">
                                    <input v-model="form.codInv" type="hidden">
                                </div>
                            </div>
                        </div>

                        <!-- Common Fields for ALL (Motors, Cameras, Autoparts) -->
                        <div class="space-y-6">
                            <h3 class="font-bold text-gray-800 dark:text-white flex items-center">
                                <i class="fa-solid fa-cogs mr-2 text-indigo-500"></i>Detalles del Item
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-copyright mr-1 text-indigo-500"></i>Marca
                                    </label>
                                    <input v-model="form.marca" class="appearance-none block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors uppercase" type="text" required>
                                    <InputError :message="form.errors.marca" class="mt-2" />
                                </div>

                                <div>
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-car-side mr-1 text-indigo-500"></i>Modelo
                                    </label>
                                    <input v-model="form.modelo" class="appearance-none block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors uppercase" type="text" required>
                                    <InputError :message="form.errors.modelo" class="mt-2" />
                                </div>
                                
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-barcode mr-1 text-indigo-500"></i>Serial
                                    </label>
                                    <input v-model="form.serial" class="appearance-none block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors uppercase" type="text">
                                    <InputError :message="form.errors.serial" class="mt-2" />
                                </div>
                                
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-calendar-days mr-1 text-indigo-500"></i>Año
                                    </label>
                                    <input v-model="form.año" class="appearance-none block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors" type="text" required>
                                    <InputError :message="form.errors.año" class="mt-2" />
                                </div>

                                <div v-if="!isAutoparte">
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-check-double mr-1 text-indigo-500"></i>Condición
                                    </label>
                                    <select v-model="form.condicion" class="block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:border-indigo-500 transition-colors">
                                        <option value="APLICA">APLICA</option>
                                        <option value="NO APLICA">NO APLICA</option>
                                    </select>
                                    <InputError :message="form.errors.condicion" class="mt-2" />
                                </div>

                                <div>
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-signal mr-1 text-indigo-500"></i>Estatus
                                    </label>
                                    <select v-model="form.status" class="block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:border-indigo-500 transition-colors">
                                        <option value="PRECIO PENDIENTE">PRECIO PENDIENTE</option>
                                        <option value="DISPONIBLE">DISPONIBLE</option>
                                        <option value="EN TALLER">EN TALLER</option>
                                        <option value="VENDIDO">VENDIDO</option>
                                        <option value="INOPERATIVO-DESARMADO">INOPERATIVO-DESARMADO</option>
                                    </select>
                                    <InputError :message="form.errors.status" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Observation Field (sits inside main grid) -->
                        <div class="space-y-6">
                            <h3 class="font-bold text-gray-800 dark:text-white flex items-center pt-4 border-t border-gray-100 dark:border-gray-700">
                                <i class="fa-solid fa-comment-dots mr-2 text-indigo-500"></i>Observaciones
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-1">
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-comment-dots mr-1 text-indigo-500"></i>Observación
                                    </label>
                                    <textarea v-model="form.observation" @input="form.observation = form.observation.toUpperCase()" rows="3" class="appearance-none block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors resize-none uppercase" placeholder="OBSERVACIÓN O DETALLE ADICIONAL..."></textarea>
                                    <InputError :message="form.errors.observation" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Additional fields for AUTOPARTES -->
                        <div v-if="isAutoparte" class="space-y-6">
                            <h3 class="font-bold text-gray-800 dark:text-white flex items-center pt-4 border-t border-gray-100 dark:border-gray-700">
                                <i class="fa-solid fa-puzzle-piece mr-2 text-indigo-500"></i>Especificaciones de la Pieza
                            </h3>
                             <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-rectangle-list mr-1 text-indigo-500"></i>Descripción / Categoría
                                    </label>
                                    <input v-model="form.categorie" class="appearance-none block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 text-sm font-bold dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors" type="text" placeholder="Ej: Arranque, Alternador..." required>
                                    <InputError :message="form.errors.categorie" class="mt-2" />
                                </div>

                                <div>
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-layer-group mr-1 text-indigo-500"></i>Cantidad
                                    </label>
                                    <input v-model="form.cantidad" class="appearance-none block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors" type="number" required>
                                    <InputError :message="form.errors.cantidad" class="mt-2" />
                                </div>

                                 <div>
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-money-bill-transfer mr-1 text-emerald-500"></i>Costo (Adquisición) ($)
                                    </label>
                                    <input :value="form.costo" @input="e => { form.costo = e.target.value; handlePriceInput('costo') }" class="appearance-none block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors font-mono" type="text" placeholder="0.00" required>
                                    <InputError :message="form.errors.costo" class="mt-2" />
                                </div>
                             </div>
                        </div>

                        <!-- Common Fields (Submit) -->
                        <div class="bg-indigo-50/30 dark:bg-gray-900/30 p-8 rounded-2xl border border-indigo-100 dark:border-indigo-900/30 flex justify-end items-center">
                            <button type="submit" :disabled="form.processing" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-10 rounded-xl shadow-lg shadow-indigo-200 dark:shadow-none transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-floppy-disk text-xl"></i>
                                Guardar Registro
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
