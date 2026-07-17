<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted } from 'vue';

const props = defineProps({
    inventario: Object,
    containers: Object,
    tipos: Object,
});

const page = usePage();
const isContador = computed(() => {
    const user = page.props.auth.user;
    if (!user) return false;
    const directRol = (user.rol || '').toLowerCase();
    const spatieRoles = (user.roles || []).map(r => typeof r === 'string' ? r.toLowerCase() : (r.name || '').toLowerCase());
    return directRol.includes('contador') || 
           spatieRoles.includes('contador') || 
           directRol.includes('admin') || 
           directRol.includes('super') || 
           spatieRoles.includes('superusuario') || 
           spatieRoles.includes('administrador');
});

const form = useForm({
    id: props.inventario.id,
    container_id: props.inventario.container_id || '',
    tipo: props.inventario.tipo || 'MOTOR 3/4',
    origen: props.inventario.origen || 'IMPORTADO',
    marca: props.inventario.marca || '',
    modelo: props.inventario.modelo || '',
    serial: props.inventario.serial || '',
    año: props.inventario.año || '',
    codInv: props.inventario.codInv || '',
    expediente: props.inventario.expediente || '',
    categorie: props.inventario.categorie || '',
    cantidad: props.inventario.cantidad || '',
    price: props.inventario.price || '',
    price_sale: props.inventario.price_sale || '',
    costo_importacion_unitario: props.inventario.costo_importacion_unitario || '',
    condicion: props.inventario.condicion || 'APLICA',
    status: props.inventario.status || 'DISPONIBLE',
    observation: props.inventario.observation || '',
    serial_file: null,
});

const serialPreviewUrl = ref(props.inventario.serial_image_path ? `/storage/${props.inventario.serial_image_path}` : null);

const noSerial = ref(props.inventario.serial === 'NO APARENTA SERIAL' || props.inventario.serial === 'NO APARENTE SERIAL' || props.inventario.serial === 'S/S' || props.inventario.serial === 'SIN SERIAL');

const handleNoSerialChange = () => {
    if (noSerial.value) {
        form.serial = 'NO APARENTA SERIAL';
    } else {
        form.serial = '';
    }
};

const handleSerialFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            alert('La imagen no debe superar los 2MB');
            return;
        }
        form.serial_file = file;
        serialPreviewUrl.value = URL.createObjectURL(file);
    }
};

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
    form.marca = form.marca?.toUpperCase();
    form.modelo = form.modelo?.toUpperCase();
    form.serial = form.serial?.toUpperCase();
    form.post(route('updateInventario', props.inventario.id));
};

onMounted(() => {
    // Initial formatting if needed
    if (form.price) form.price = formatCurrency(form.price);
    if (form.price_sale) form.price_sale = formatCurrency(form.price_sale);
    if (form.costo_importacion_unitario) form.costo_importacion_unitario = formatCurrency(form.costo_importacion_unitario);
});
</script>

<template>
    <AppLayout title="Editar Registro">
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight text-center">
                <i class="fa-solid fa-pen-to-square mr-2 text-indigo-500"></i>Editar: {{ props.inventario.marca }} {{ props.inventario.modelo }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 p-8">
                    <form @submit.prevent="submit" class="space-y-8">
                        
                        <!-- Top Selector Section -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-6 border-b border-gray-100 dark:border-gray-700">
                            <!-- ID (Read-only) -->
                            <div>
                                <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                    <i class="fa-solid fa-hashtag mr-1"></i>ID
                                </label>
                                <input :value="form.id" class="block w-full bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-lg py-3 px-4 leading-tight focus:outline-none" type="text" readonly>
                            </div>

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
                                <select v-model="form.tipo" class="block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors">
                                    <option v-for="tipo in props.tipos" :key="tipo" :value="tipo">{{ tipo }}</option>
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
                                    <select v-model="form.container_id" class="block w-full bg-white dark:bg-gray-800 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-2 px-3 leading-tight focus:outline-none focus:border-indigo-500">
                                        <option value="">Seleccione un contenedor</option>
                                        <option v-for="container in props.containers" :key="container.id" :value="container.id">
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
                                </div>

                                <div>
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-folder-tree mr-1 text-indigo-500"></i>Cod. Inventario
                                    </label>
                                    <input v-model="form.codInv" class="appearance-none block w-full bg-white dark:bg-gray-800 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-2 px-3 leading-tight focus:outline-none focus:border-indigo-500" type="text" required>
                                    <InputError :message="form.errors.codInv" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Common Fields -->
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
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold">
                                            <i class="fa-solid fa-barcode mr-1 text-indigo-500"></i>Serial
                                        </label>
                                        <label class="flex items-center gap-1.5 text-xs text-indigo-600 dark:text-indigo-400 font-bold cursor-pointer">
                                            <input type="checkbox" v-model="noSerial" @change="handleNoSerialChange" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-3.5 w-3.5">
                                            No Aparenta Serial
                                        </label>
                                    </div>
                                    <input :disabled="noSerial" v-model="form.serial" class="appearance-none block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors uppercase disabled:opacity-60 disabled:cursor-not-allowed" type="text" required>
                                    <InputError :message="form.errors.serial" class="mt-2" />
                                </div>
                                
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-camera mr-1 text-indigo-500"></i>Foto de Serial
                                    </label>
                                    <div class="flex items-center gap-4">
                                        <label class="flex items-center justify-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 dark:bg-gray-700 dark:hover:bg-gray-600 text-indigo-700 dark:text-indigo-400 font-bold rounded-lg cursor-pointer border border-indigo-200 dark:border-gray-600 transition-colors text-sm">
                                            <i class="fa-solid fa-upload mr-2"></i> Subir Foto
                                            <input type="file" accept="image/*" class="hidden" @change="handleSerialFileChange">
                                        </label>
                                        <span class="text-xs text-gray-500 dark:text-gray-400" v-if="!serialPreviewUrl">Sin foto seleccionada</span>
                                    </div>
                                    <div v-if="serialPreviewUrl" class="mt-2 relative w-32 h-20 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-black flex items-center justify-center">
                                        <img :src="serialPreviewUrl" class="max-w-full max-h-full object-contain" alt="Vista previa del serial">
                                    </div>
                                    <InputError :message="form.errors.serial_file" class="mt-2" />
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
                                        <option value="GARANTIA">GARANTÍA</option>
                                        <option value="INOPERATIVO-DESARMADO">INOPERATIVO-DESARMADO</option>
                                    </select>
                                    <InputError :message="form.errors.status" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Additional fields for AUTOPARTES -->
                        <div v-if="isAutoparte" class="space-y-6">
                            <h3 class="font-bold text-gray-800 dark:text-white flex items-center pt-4 border-t border-gray-100 dark:border-gray-700">
                                <i class="fa-solid fa-puzzle-piece mr-2 text-indigo-500"></i>Especificaciones de la Pieza
                            </h3>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-rectangle-list mr-1 text-indigo-500"></i>Descripción / Categoría
                                    </label>
                                    <input v-model="form.categorie" class="appearance-none block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 text-sm font-bold dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors" type="text" placeholder="Ej: Arranque, Alternador...">
                                    <InputError :message="form.errors.categorie" class="mt-2" />
                                </div>

                                <div>
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-layer-group mr-1 text-indigo-500"></i>Cantidad
                                    </label>
                                    <input v-model="form.cantidad" class="appearance-none block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors" type="number">
                                    <InputError :message="form.errors.cantidad" class="mt-2" />
                                </div>
                             </div>
                        </div>

                        <!-- Observation Field -->
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

                        <!-- Pricing/Import Cost Section -->
                        <div class="bg-indigo-50/30 dark:bg-gray-900/30 p-8 rounded-2xl border border-indigo-100 dark:border-indigo-900/30">
                            <div v-if="isContador" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div>
                                    <label class="block uppercase tracking-wide text-indigo-700 dark:text-indigo-400 text-xs font-bold mb-3">
                                        <i class="fa-solid fa-ship mr-1"></i>Costo de Importación ($)
                                    </label>
                                    <input :value="form.costo_importacion_unitario" readonly class="appearance-none block w-full bg-gray-50 dark:bg-gray-700/50 text-indigo-600 dark:text-indigo-400 border border-gray-200 dark:border-gray-700 rounded-xl py-3 px-6 leading-tight focus:outline-none transition-all text-xl font-bold font-mono cursor-not-allowed" type="text" placeholder="0.00">
                                    <InputError :message="form.errors.costo_importacion_unitario" class="mt-2" />
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-indigo-700 dark:text-indigo-400 text-xs font-bold mb-3">
                                        <i class="fa-solid fa-money-bill-transfer mr-1"></i>Precio / Costo ($)
                                    </label>
                                    <input :value="form.price" readonly class="appearance-none block w-full bg-gray-50 dark:bg-gray-700/50 text-indigo-600 dark:text-indigo-400 border border-gray-200 dark:border-gray-700 rounded-xl py-3 px-6 leading-tight focus:outline-none transition-all text-xl font-bold font-mono cursor-not-allowed" type="text" placeholder="0.00">
                                    <InputError :message="form.errors.price" class="mt-2" />
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-indigo-700 dark:text-indigo-400 text-xs font-bold mb-3">
                                        <i class="fa-solid fa-tag mr-1"></i>Precio de Venta ($)
                                    </label>
                                    <input :value="form.price_sale" readonly class="appearance-none block w-full bg-gray-50 dark:bg-gray-700/50 text-indigo-600 dark:text-indigo-400 border border-gray-200 dark:border-gray-700 rounded-xl py-3 px-6 leading-tight focus:outline-none transition-all text-xl font-bold font-mono cursor-not-allowed" type="text" placeholder="0.00">
                                    <InputError :message="form.errors.price_sale" class="mt-2" />
                                </div>
                            </div>
                            <div class="flex justify-end pt-4">
                                <button type="submit" :disabled="form.processing" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-12 rounded-xl shadow-lg shadow-indigo-200 dark:shadow-none transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-floppy-disk text-xl"></i>
                                    Actualizar Registro
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>