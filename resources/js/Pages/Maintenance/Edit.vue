<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import MaterialsEngine from './MaterialsEngine.vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
  maintenance: Object,
  partida: Object,
  bill: Object,
  materials: Object,
  accesorios: Object,
  items: Array, // Dynamic items list
  statusLogs: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const isAdminOrSuper = computed(() => {
    const roles = user.value.roles || [];
    const roleNames = roles.map(r => r.name);
    return roleNames.includes('Administrador') || 
           roleNames.includes('Superusuario') || 
           user.value.rol === 'Administrador' || 
           user.value.rol === 'Superusuario';
});

const initialStatus = props.maintenance.status;
const currentStatus = ref(props.maintenance.status);
const statusPhotoFile = ref(null);
const photoPreview = ref(null);

const handleStatusChange = (e) => {
    currentStatus.value = e.target.value;
};

const mapStatus = (status) => {
    const statusMap = {
        'EN ESPERA': 'RECIBIDO',
        'EN PROCESO': 'ARMANDO',
        'TERMINADO': 'TERMINADO',
        'CANCELADO': 'CANCELADO',
    };
    return statusMap[status] || status;
};

const handleFileChange = (e) => {
    const file = e.target.files[0];
    statusPhotoFile.value = file;
    if (file) {
        photoPreview.value = URL.createObjectURL(file);
    } else {
        photoPreview.value = null;
    }
};

const submitForm = (e) => {
    const formElement = e.target;
    const data = {};
    for (const element of formElement.elements) {
        if (element.name && element.type !== 'file') {
            data[element.name] = element.value;
        }
    }
    // Handle the grouped commission
    if (data.grouped_commission) {
        data.cleaning = data.grouped_commission;
        data.consumables = data.grouped_commission;
        data.forklift = data.grouped_commission;
    }

    if (statusPhotoFile.value) {
        data.status_photo = statusPhotoFile.value;
    }
    
    router.post('/maintenance/update/' + props.maintenance.id, data);
};

// Módulo de repuestos y rectificadora states
const showAddModal = ref(false);
const showReturnModal = ref(false);
const activeReturnItemId = ref(null);
const showDetailModal = ref(false);
const activeDetailItem = ref(null);

const showDeleteConfirmModal = ref(false);
const itemToDeleteId = ref(null);

const suggestions = [
    { name: 'CONCHA DE BIELA', type: 'REPUESTO' },
    { name: 'CONCHA DE BANCADA', type: 'REPUESTO' },
    { name: 'ANILLOS', type: 'REPUESTO' },
    { name: 'EMPACADURA CÁMARA', type: 'REPUESTO' },
    { name: 'EMPACADURA DEL CARTER', type: 'REPUESTO' },
    { name: 'KIT DE EMPACADURAS', type: 'REPUESTO' },
    { name: 'TAPA VÁLVULA', type: 'REPUESTO' },
    { name: 'TAPA CADENA', type: 'REPUESTO' },
    { name: 'CARTER', type: 'REPUESTO' },
    { name: 'PESCADOR', type: 'REPUESTO' },
    { name: 'PISTÓN O BRAZO', type: 'REPUESTO' },
    { name: 'BAÑO QUÍMICO', type: 'SERVICIO' },
    { name: 'GOMA VÁLVULA', type: 'SERVICIO' },
    { name: 'PLANOS', type: 'SERVICIO' },
    { name: 'VÁLVULAS', type: 'SERVICIO' },
    { name: 'RECTIFICACIÓN', type: 'SERVICIO' },
    { name: 'ASIENTOS', type: 'SERVICIO' },
    { name: 'CAMISAS BLOQUES', type: 'SERVICIO' },
    { name: 'LEVA CAMISAS', type: 'SERVICIO' }
];

const selectSuggestion = (sug) => {
    addForm.description = sug.name;
    addForm.type = sug.type;
};

const addForm = useForm({
    description: '',
    type: 'REPUESTO',
    source: 'COMPRADO',
    cost: '',
    document_type: 'NINGUNO',
    invoice_number: '',
    base_imponible: '',
    status: 'COMPLETADO',
    notes: '',
    requires_outflow: false,
    invoice_file: null
});

const returnForm = useForm({
    cost: '',
    document_type: 'FACTURA',
    invoice_number: '',
    base_imponible: '',
    notes: '',
    invoice_file: null
});

// Edit item states
const showEditModal = ref(false);
const activeEditItem = ref(null);
const editForm = useForm({
    cost: '',
    document_type: 'FACTURA',
    invoice_number: '',
    base_imponible: '',
    notes: '',
    invoice_file: null
});

const handleSourceChange = () => {
    if (addForm.source === 'INVENTARIO') {
        addForm.cost = '';
        addForm.document_type = 'NINGUNO';
        addForm.invoice_number = '';
        addForm.base_imponible = 0;
        addForm.invoice_file = null;
    } else {
        addForm.cost = '';
        addForm.document_type = 'NINGUNO';
        addForm.invoice_number = '';
        addForm.base_imponible = '';
        addForm.invoice_file = null;
    }

    if (addForm.requires_outflow) {
        addForm.status = 'FUERA';
    } else {
        addForm.status = 'COMPLETADO';
    }
};

const handleRequiresOutflowChange = () => {
    if (addForm.requires_outflow) {
        addForm.status = 'FUERA';
        addForm.cost = '';
        addForm.document_type = 'NINGUNO';
        addForm.invoice_number = '';
        addForm.base_imponible = '';
        addForm.invoice_file = null;
    } else {
        addForm.status = 'COMPLETADO';
    }
};

const openAddModal = () => {
    addForm.reset();
    addForm.type = 'REPUESTO';
    addForm.source = 'COMPRADO';
    addForm.document_type = 'NINGUNO';
    addForm.status = 'COMPLETADO';
    addForm.requires_outflow = false;
    addForm.invoice_file = null;
    showAddModal.value = true;
};

const submitAdd = () => {
    // Validar que base_imponible no sea mayor al costo si es comprado y no va a rectificadora
    if (addForm.source === 'COMPRADO' && !addForm.requires_outflow) {
        const costVal = parseFloat(addForm.cost) || 0;
        const baseVal = parseFloat(addForm.base_imponible) || 0;
        if (baseVal > costVal) {
            addForm.setError('base_imponible', 'La Base Imponible (BIG) no puede ser mayor al Costo Real.');
            return;
        }
    }

    // Si no requiere salida, pero el documento es FACTURA, el estado debe ser RETORNADO para conciliación
    if (!addForm.requires_outflow) {
        if (addForm.source === 'COMPRADO' && addForm.document_type === 'FACTURA') {
            addForm.status = 'RETORNADO';
        } else {
            addForm.status = 'COMPLETADO';
        }
    } else {
        addForm.status = 'FUERA';
    }

    addForm.post(route('maintenance.store_item', props.maintenance.id), {
        onSuccess: () => {
            showAddModal.value = false;
            addForm.reset();
        }
    });
};

const openReturnModal = (itemId) => {
    activeReturnItemId.value = itemId;
    returnForm.reset();
    returnForm.document_type = 'FACTURA';
    returnForm.invoice_file = null;
    showReturnModal.value = true;
};

const submitReturn = () => {
    const costVal = parseFloat(returnForm.cost) || 0;
    const baseVal = parseFloat(returnForm.base_imponible) || 0;
    if (returnForm.document_type === 'FACTURA' && baseVal > costVal) {
        returnForm.setError('base_imponible', 'La Base Imponible (BIG) no puede ser mayor al Costo Real.');
        return;
    }

    returnForm.post(route('maintenance.register_return', activeReturnItemId.value), {
        onSuccess: () => {
            showReturnModal.value = false;
            returnForm.reset();
            activeReturnItemId.value = null;
        }
    });
};

const openEditModal = (item) => {
    activeEditItem.value = item;
    editForm.reset();
    editForm.cost = item.cost;
    editForm.document_type = item.document_type || 'NINGUNO';
    editForm.invoice_number = item.invoice_number || '';
    editForm.base_imponible = item.base_imponible || '';
    editForm.notes = item.notes || '';
    editForm.invoice_file = null;
    showEditModal.value = true;
};

const submitEdit = () => {
    const costVal = parseFloat(editForm.cost) || 0;
    const baseVal = parseFloat(editForm.base_imponible) || 0;
    if (editForm.document_type === 'FACTURA' && baseVal > costVal) {
        editForm.setError('base_imponible', 'La Base Imponible (BIG) no puede ser mayor al Costo Real.');
        return;
    }

    editForm.post(route('maintenance.update_item', activeEditItem.value.id), {
        onSuccess: () => {
            showEditModal.value = false;
            editForm.reset();
            activeEditItem.value = null;
        }
    });
};

const confirmDelete = (itemId) => {
    itemToDeleteId.value = itemId;
    showDeleteConfirmModal.value = true;
};

const executeDelete = () => {
    if (!itemToDeleteId.value) return;
    router.delete(route('maintenance.delete_item', itemToDeleteId.value), {
        onSuccess: () => {
            showDeleteConfirmModal.value = false;
            itemToDeleteId.value = null;
        }
    });
};

const formatPrice = (value) => {
    if (value === null || value === undefined || value === '') return '$0.00';
    return '$' + parseFloat(value).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const openDetailModal = (item) => {
    activeDetailItem.value = item;
    showDetailModal.value = true;
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
};
</script>

<template >
    <AppLayout title="Asignaciones">
        <template #header>
            <h1 v-if="props.maintenance && props.maintenance.marca" class="text-center">
                Editar Partida de: {{ props.maintenance.marca }}  {{ props.maintenance.modelo }}
            </h1>
        </template>

        <div class="bg-white dark:bg-slate-900/50 backdrop-blur-sm rounded-3xl p-6 shadow-2xl border border-gray-100 dark:border-slate-800 transition-colors duration-300">
            <div class="flex flex-col lg:flex-row gap-8">
                <div class="lg:w-1/4 bg-gray-50 dark:bg-slate-800/40 p-6 rounded-2xl border border-gray-100 dark:border-slate-700/50">
                    <h3 class="text-center mb-1 font-bold text-xl text-gray-800 dark:text-white tracking-tight uppercase">Datos de la Partida</h3>
                    <p class="text-center mb-6 text-xs font-medium text-gray-400 dark:text-slate-500 uppercase tracking-widest">(Solo lectura)</p>
                    
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="marca">
                                <i class="fas fa-tag mr-2 text-blue-500"></i>Marca
                            </label>
                            <input class="block w-full bg-gray-100 dark:bg-slate-900/40 text-gray-700 dark:text-slate-300 border border-gray-200 dark:border-slate-700/50 rounded-xl py-2.5 px-4 leading-tight focus:outline-none cursor-not-allowed italic" id="marca" name="marca" type="text" readonly :value="props.partida.marca">
                        </div>
                        <div>
                            <label class="block uppercase tracking-wider text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="modelo">
                                <i class="fas fa-car-side mr-2 text-blue-400"></i>Modelo
                            </label>
                            <input class="block w-full bg-slate-900/40 text-slate-300 border border-slate-700/50 rounded-xl py-2.5 px-4 leading-tight focus:outline-none cursor-not-allowed italic" name="modelo" type="text" readonly :value="props.partida.modelo">
                        </div>
                        <div>
                            <label class="block uppercase tracking-wider text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="año">
                                <i class="fas fa-calendar mr-2 text-blue-400"></i>Año
                            </label>
                            <input class="block w-full bg-slate-900/40 text-slate-300 border border-slate-700/50 rounded-xl py-2.5 px-4 leading-tight focus:outline-none cursor-not-allowed italic" id="año" name="año" type="text" readonly :value="props.partida.año">
                        </div>
                        <div>
                            <label class="block uppercase tracking-wider text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="expediente">
                                <i class="fas fa-id-card-clip mr-2 text-blue-400"></i>Expediente
                            </label>
                            <input class="block w-full bg-slate-900/40 text-slate-300 border border-slate-700/50 rounded-xl py-2.5 px-4 leading-tight focus:outline-none cursor-not-allowed italic" name="expediente" type="text" readonly :value="props.partida.expediente">
                        </div>
                    </div>
                </div>

                <div class="lg:w-3/4 bg-gray-50/50 dark:bg-slate-800/20 p-6 rounded-2xl border border-gray-100 dark:border-slate-700/30">
                    <form ref="maintenanceForm" @submit.prevent="submitForm" method="post">
                        <h3 class="text-center mb-1 font-bold text-xl text-gray-800 dark:text-white tracking-tight uppercase">Datos del Mantenimiento</h3>
                        <p class="text-center mb-8 text-xs font-medium text-gray-400 dark:text-slate-500 uppercase tracking-widest">Actualización de Registro</p>
                            
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 group-input-section bg-white dark:bg-slate-800/40 p-5 rounded-xl border border-gray-100 dark:border-slate-700/50 shadow-sm transition-colors">
                            <div>
                                <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="fecha">
                                    <i class="fas fa-calendar-days mr-2 text-blue-500"></i>Fecha
                                </label>
                                <input class="block w-full bg-gray-100 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2.5 px-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 transition-all font-medium" id="fecha" name="fecha" type="date" :value="props.maintenance.fecha">
                            </div>
                            <div>
                                <label class="block uppercase tracking-wider text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="tipo">
                                    <i class="fas fa-wrench mr-2 text-blue-400"></i>Tipo
                                </label>
                                <input class="block w-full bg-slate-900/50 text-white border border-slate-700 rounded-xl py-2.5 px-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 transition-all" name="tipo" type="text" placeholder="Tipo" :value="props.maintenance.tipo">
                            </div>
                            <div>
                                <label class="block uppercase tracking-wider text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="status">
                                    <i class="fas fa-signal mr-2 text-blue-400"></i>Estado
                                </label>
                                <select class="block w-full bg-slate-900/50 text-white border border-slate-700 rounded-xl py-2.5 px-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 transition-all appearance-none cursor-pointer" id="status" name="status" @change="handleStatusChange">
                                    <option value="EN ESPERA" :selected="props.maintenance.status === 'EN ESPERA'">RECIBIDO</option>
                                    <option value="EN PROCESO" :selected="props.maintenance.status === 'EN PROCESO'">ARMANDO</option>
                                    <option value="TERMINADO" :selected="props.maintenance.status === 'TERMINADO'">TERMINADO</option>
                                    <option value="CANCELADO" :selected="props.maintenance.status === 'CANCELADO'">CANCELADO</option>
                                </select>
                            </div>
                            <div v-if="currentStatus !== initialStatus" class="md:col-span-2 bg-slate-900/40 p-4 rounded-xl border border-dashed border-slate-700 flex flex-col items-center justify-center gap-3">
                                <span class="text-xs font-bold text-amber-400 flex items-center gap-2">
                                    <i class="fas fa-triangle-exclamation"></i>
                                    Has modificado el estado. Debe adjuntar una foto de respaldo
                                    <span v-if="props.maintenance.tipo === 'GARANTÍA' || props.maintenance.tipo === 'GARANTIA'" class="text-red-500 font-extrabold">* (Obligatorio para Garantías)</span>
                                </span>
                                <div class="flex items-center gap-4 w-full justify-center">
                                    <label class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center gap-2 transition-colors">
                                        <i class="fas fa-camera"></i>
                                        Seleccionar Foto
                                        <input type="file" class="hidden" accept="image/*" @change="handleFileChange" />
                                    </label>
                                    <span class="text-xs text-slate-400 truncate max-w-[200px]" v-if="statusPhotoFile">
                                        {{ statusPhotoFile.name }}
                                    </span>
                                    <span class="text-xs text-slate-500" v-else>
                                        Ningún archivo seleccionado
                                    </span>
                                </div>
                                <span v-if="page.props.errors && page.props.errors.status_photo" class="text-xs text-rose-500 font-extrabold block mt-1 uppercase tracking-tight">
                                    {{ page.props.errors.status_photo }}
                                </span>
                                <div v-if="photoPreview" class="mt-2 w-full max-w-[150px] aspect-video rounded-lg overflow-hidden border border-slate-700 shadow-lg">
                                    <img :src="photoPreview" class="w-full h-full object-cover" />
                                </div>
                            </div>
                            <div>
                                <label class="block uppercase tracking-wider text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="cedula_mecanico">
                                    <i class="fas fa-address-card mr-2 text-blue-400"></i>Cédula del Mecánico
                                </label>
                                <input class="block w-full bg-slate-900/50 text-white border border-slate-700 rounded-xl py-2.5 px-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 transition-all" name="cedula_mecanico" type="text" placeholder="Cédula" :value="props.maintenance.cedula_mecanico">
                            </div>
                            <div>
                                <label class="block uppercase tracking-wider text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="nombre_mecanico">
                                    <i class="fas fa-user-gear mr-2 text-blue-400"></i>Nombre Mecánico
                                </label>
                                <input class="block w-full bg-slate-900/50 text-white border border-slate-700 rounded-xl py-2.5 px-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 transition-all" id="nombre_mecanico" name="nombre_mecanico" type="text" placeholder="Nombre" :value="props.maintenance.nombre_mecanico">
                            </div>
                            <div>
                                <label class="block uppercase tracking-wider text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="apellido_mecanico">
                                    <i class="fas fa-user-tag mr-2 text-blue-400"></i>Apellido
                                </label>
                                <input class="block w-full bg-slate-900/50 text-white border border-slate-700 rounded-xl py-2.5 px-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 transition-all" name="apellido_mecanico" type="text" placeholder="Apellido" :value="props.maintenance.apellido_mecanico">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block uppercase tracking-wider text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="descripcion">
                                    <i class="fas fa-file-signature mr-2 text-blue-400"></i>Descripción
                                </label>
                                <textarea class="block w-full bg-slate-900/50 text-white border border-slate-700 rounded-xl py-2.5 px-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 transition-all min-h-[100px]" id="descripcion" name="descripcion" placeholder="Descripción del trabajo realizado" :value="props.maintenance.descripcion"></textarea> 
                            </div>
                        </div>

                        <h4 class="text-center mb-6 font-bold text-lg text-blue-600 dark:text-blue-400 tracking-tight uppercase">Herramientas y Mano de Obra</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-8 bg-white dark:bg-slate-800/40 p-5 rounded-xl border border-gray-100 dark:border-slate-700/50 shadow-sm transition-colors">
                            <!-- Grouped Commissions for all users -->
                            <div class="md:col-span-2 mb-4 bg-gray-50 dark:bg-slate-900/30 p-4 rounded-xl border border-gray-100 dark:border-slate-700/50">
                                <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="grouped_commission">
                                    <i class="fas fa-percent mr-2 text-orange-500"></i>% Limpieza, Consumibles y Montacarga
                                </label>
                                <input class="block w-full bg-white dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2.5 px-4 focus:outline-none focus:border-blue-500 transition-all font-bold text-center" id="grouped_commission" name="grouped_commission" type="text" placeholder="%" :value="props.bill.consumables">
                            </div>

                            <template v-if="isAdminOrSuper">
                                <div>
                                    <label class="block uppercase tracking-wider text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="mechanic">
                                        <i class="fas fa-user-gear mr-2 text-blue-400"></i>% Mecánico
                                    </label>
                                    <input class="block w-full bg-slate-900/50 text-white border border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all" id="mechanic" name="mechanic" type="text" placeholder="%" :value="props.bill.mechanic">
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wider text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="mechanic_assistant">
                                        <i class="fas fa-user-plus mr-2 text-blue-400"></i>% Ayudante Mec.
                                    </label>
                                    <input class="block w-full bg-slate-900/50 text-white border border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all" name="mechanic_assistant" type="text" placeholder="%" :value="props.bill.mechanic_assistant">
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wider text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="seller">
                                        <i class="fas fa-user-tie mr-2 text-blue-400"></i>% Vendedor
                                    </label>
                                    <input class="block w-full bg-slate-900/50 text-white border border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all" id="seller" name="seller" type="text" placeholder="%" :value="props.bill.seller">
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wider text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="seller_assistant">
                                        <i class="fas fa-user-tag mr-2 text-blue-400"></i>% Ayudante Vent.
                                    </label>
                                    <input class="block w-full bg-slate-900/50 text-white border border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all" name="seller_assistant" type="text" placeholder="%" :value="props.bill.seller_assistant">
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wider text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="camera_technician">
                                        <i class="fas fa-camera-retro mr-2 text-blue-400"></i>% Técnico en Cámaras
                                    </label>
                                    <input class="block w-full bg-slate-900/50 text-white border border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all" id="camera_technician" name="camera_technician" type="text" placeholder="%" :value="props.bill.camera_technician">
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wider text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="camera_technical_assistant">
                                        <i class="fas fa-video mr-2 text-blue-400"></i>% Ayudante Técnico Cámaras
                                    </label>
                                    <input class="block w-full bg-slate-900/50 text-white border border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all" name="camera_technical_assistant" type="text" placeholder="%" :value="props.bill.camera_technical_assistant">
                                </div>
                            </template>
                        </div>



                        <!-- Panel de Repuestos y Trabajos Externos -->
                        <div class="mb-8">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                                <h4 class="font-bold text-lg text-blue-600 dark:text-blue-400 tracking-tight uppercase flex items-center gap-2">
                                    <i class="fa-solid fa-gears text-indigo-500"></i>Gestión de Repuestos y Trabajos Externos
                                </h4>
                                <button type="button" @click="openAddModal" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-black uppercase rounded-xl transition-all shadow-md shadow-indigo-600/20 gap-1.5 self-start sm:self-auto">
                                    <i class="fa-solid fa-circle-plus"></i>Agregar Repuesto / Servicio
                                </button>
                            </div>

                            <div class="bg-white dark:bg-slate-800/45 rounded-2xl border border-gray-100 dark:border-slate-700/50 p-6 shadow-sm transition-colors">
                                <div v-if="props.items && props.items.length > 0" class="divide-y divide-gray-100 dark:divide-slate-700/50">
                                    <div v-for="item in props.items" :key="item.id" class="py-4 first:pt-0 last:pb-0 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-extrabold text-sm text-gray-800 dark:text-white uppercase">{{ item.description }}</span>
                                                <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md"
                                                    :class="item.type === 'REPUESTO' ? 'bg-purple-100 dark:bg-purple-950/30 text-purple-700 dark:text-purple-400' : 'bg-amber-100 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400'">
                                                    {{ item.type }}
                                                </span>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                                <!-- Origen Badge -->
                                                <span class="inline-flex items-center gap-1 font-bold text-gray-500 dark:text-slate-400">
                                                    <i class="fa-solid" :class="item.source === 'INVENTARIO' ? 'fa-warehouse' : 'fa-basket-shopping'"></i>
                                                    {{ item.source === 'INVENTARIO' ? 'Taller (Existente)' : 'Compra Externa' }}
                                                </span>
                                                <span class="text-gray-300 dark:text-slate-600">•</span>
                                                <!-- Documento Badge -->
                                                <span class="font-bold text-gray-500 dark:text-slate-400">
                                                    Soporte: 
                                                    <span class="font-black" :class="item.document_type === 'FACTURA' ? 'text-indigo-600 dark:text-indigo-400' : item.document_type === 'RECIBO' ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400'">
                                                        {{ item.document_type }}
                                                    </span>
                                                </span>
                                                <span v-if="item.invoice_number" class="text-gray-300 dark:text-slate-600">•</span>
                                                <span v-if="item.invoice_number" class="font-black text-indigo-500 dark:text-indigo-400">
                                                    Fac: {{ item.invoice_number }}
                                                </span>
                                                <span v-if="item.outflow_date" class="text-gray-300 dark:text-slate-600">•</span>
                                                <span v-if="item.outflow_date" class="inline-flex items-center gap-1 font-bold text-rose-500 dark:text-rose-450 animate-fade-in" title="Fecha de salida a rectificadora">
                                                    <i class="fa-solid fa-calendar-minus"></i>
                                                    Salida: {{ formatDate(item.outflow_date) }}
                                                </span>
                                                <span v-if="item.return_date" class="text-gray-300 dark:text-slate-600">•</span>
                                                <span v-if="item.return_date" class="inline-flex items-center gap-1 font-bold text-emerald-600 dark:text-emerald-400 animate-fade-in" title="Fecha de entrada/recepción">
                                                    <i class="fa-solid fa-calendar-check"></i>
                                                    Entrada: {{ formatDate(item.return_date) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-6 justify-between md:justify-end">
                                            <!-- Cost / BIG info -->
                                            <div class="text-right">
                                                <div class="font-black text-sm text-gray-800 dark:text-white">{{ formatPrice(item.cost) }}</div>
                                                <div v-if="item.base_imponible > 0" class="text-[10px] text-emerald-600 dark:text-emerald-400 font-extrabold">
                                                    BIG: {{ formatPrice(item.base_imponible) }}
                                                </div>
                                            </div>

                                            <!-- Status & Action Buttons -->
                                            <div class="flex items-center gap-3">
                                                <!-- Status Badge -->
                                                <span class="px-2.5 py-1 text-[10px] font-black uppercase rounded-full tracking-wider inline-flex items-center gap-1"
                                                    :class="item.status === 'FUERA' ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400' :
                                                            item.status === 'RETORNADO' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 animate-pulse' :
                                                            item.status === 'CONCILIADO' ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400' :
                                                            'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'">
                                                    <i class="fa-solid" :class="item.status === 'FUERA' ? 'fa-truck-arrow-right animate-bounce' : item.status === 'RETORNADO' ? 'fa-clock' : 'fa-circle-check'"></i>
                                                    {{ item.status === 'FUERA' ? 'En Rectificadora' : item.status === 'RETORNADO' ? 'Pendiente' : item.status === 'CONCILIADO' ? 'Conciliado' : 'Completado' }}
                                                </span>

                                                <!-- View Details Button -->
                                                <button type="button" @click="openDetailModal(item)" class="p-2 text-indigo-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/20 rounded-lg transition-all" title="Ver detalles y notas">
                                                    <i class="fa-solid fa-eye text-sm"></i>
                                                </button>

                                                <!-- Return Button -->
                                                <button v-if="item.status === 'FUERA'" type="button" @click="openReturnModal(item.id)" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase rounded-lg transition-all shadow-md shadow-emerald-600/10 flex items-center gap-1">
                                                    <i class="fa-solid fa-right-to-bracket"></i>Entrada
                                                </button>

                                                <!-- Edit Button -->
                                                <button v-if="item.status !== 'CONCILIADO' && item.source !== 'INVENTARIO'" type="button" @click="openEditModal(item)" class="p-2 text-amber-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/20 rounded-lg transition-all" title="Editar detalles de factura/costo">
                                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                                </button>

                                                <!-- Delete Button -->
                                                <button v-if="item.status !== 'CONCILIADO'" type="button" @click="confirmDelete(item.id)" class="p-2 text-rose-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/20 rounded-lg transition-all" title="Eliminar ítem">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center py-6 text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">
                                    No hay repuestos o servicios dinámicos cargados a esta orden.
                                </div>
                            </div>
                        </div>
                        
                        <!-- Registered Status / Photo History logs -->
                        <div class="mb-8 bg-white dark:bg-slate-800/40 p-5 rounded-xl border border-gray-100 dark:border-slate-700/50 shadow-sm" v-if="props.statusLogs && props.statusLogs.length > 0">
                            <h4 class="text-left mb-4 font-bold text-sm text-blue-600 dark:text-blue-400 tracking-tight uppercase flex items-center gap-2">
                                <i class="fas fa-camera"></i>
                                Historial de Fotos y Estatus Registrados
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                <div v-for="log in props.statusLogs" :key="log.id" class="border border-gray-100 dark:border-slate-700/50 rounded-xl p-3 bg-gray-50 dark:bg-slate-900/30 flex flex-col gap-2">
                                    <div class="flex justify-between items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                            {{ mapStatus(log.status) }}
                                        </span>
                                        <span class="text-[9px] text-gray-400 dark:text-slate-500 font-medium">
                                            {{ new Date(log.created_at).toLocaleString() }}
                                        </span>
                                    </div>
                                    <div v-if="log.photo_url" class="aspect-video w-full rounded-lg overflow-hidden border border-gray-200 dark:border-slate-800 shadow-sm">
                                        <a :href="log.photo_url" target="_blank" class="block w-full h-full relative group">
                                            <img :src="log.photo_url" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" />
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-[10px] font-bold gap-1">
                                                <i class="fas fa-expand"></i> Ampliar
                                            </div>
                                        </a>
                                    </div>
                                    <div v-else class="text-[10px] italic text-gray-400 dark:text-slate-500 py-4 text-center">
                                        Sin imagen.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pb-6">
                            <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 text-white font-bold py-4 px-6 rounded-2xl shadow-xl transform active:scale-[0.99] transition-all uppercase tracking-widest text-sm">Guardar Cambios del Mantenimiento</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Item Modal -->
        <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showAddModal = false"></div>
            <div class="relative bg-white dark:bg-slate-900 w-full max-w-lg rounded-3xl shadow-2xl border border-gray-100 dark:border-slate-800 p-6 sm:p-8 animate-in zoom-in-95 duration-200">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 uppercase flex items-center gap-2">
                    <i class="fa-solid fa-circle-plus text-indigo-500"></i>Añadir Repuesto / Servicio Externo
                </h3>
                
                <form @submit.prevent="submitAdd" class="space-y-4 text-left">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                            <i class="fa-solid fa-wrench text-indigo-500 mr-1.5"></i>Descripción / Pieza
                        </label>
                        <input type="text" v-model="addForm.description" required class="block w-full bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:ring-2 focus:ring-indigo-500 uppercase font-bold" placeholder="EJ: ANILLOS, PISTONES, REVISIÓN CIGÜEÑAL">
                    </div>

                    <!-- Sugerencias de repuestos y servicios -->
                    <div class="space-y-1.5">
                        <label class="block text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-wider">Sugerencias rápidas:</label>
                        <div class="flex flex-wrap gap-1.5 max-h-28 overflow-y-auto p-2 bg-gray-50 dark:bg-slate-800/40 rounded-xl border border-gray-150 dark:border-slate-800/70">
                            <button v-for="sug in suggestions" :key="sug.name" type="button" @click="selectSuggestion(sug)" class="px-2 py-1 bg-white dark:bg-slate-850 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 text-gray-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 text-[10px] font-extrabold uppercase rounded-lg border border-gray-200 dark:border-slate-700/60 shadow-sm transition-all">
                                {{ sug.name }}
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                                <i class="fa-solid fa-tag text-indigo-500 mr-1.5"></i>Tipo
                            </label>
                            <select v-model="addForm.type" class="block w-full bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-indigo-500 rounded-xl">
                                <option value="REPUESTO">REPUESTO</option>
                                <option value="SERVICIO">SERVICIO</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                                <i class="fa-solid fa-store text-indigo-500 mr-1.5"></i>Origen
                            </label>
                            <select v-model="addForm.source" @change="handleSourceChange" class="block w-full bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-indigo-500 rounded-xl">
                                <option value="COMPRADO">COMPRADO EXTE.</option>
                                <option value="INVENTARIO">TALLER (STOCK)</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="requires_outflow" v-model="addForm.requires_outflow" @change="handleRequiresOutflowChange" class="rounded border-gray-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500">
                        <label for="requires_outflow" class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase cursor-pointer select-none">¿Requiere salida a rectificadora / taller externo?</label>
                    </div>

                    <!-- Datos de Compra Directa o Referencia de Stock (si no va a rectificadora) -->
                    <div v-if="(addForm.source === 'COMPRADO' || addForm.source === 'INVENTARIO') && !addForm.requires_outflow" class="p-4 bg-gray-50 dark:bg-slate-800/40 border border-gray-100 dark:border-slate-800 rounded-2xl space-y-4 animate-in slide-in-from-top-4 duration-200">
                        <div class="text-[10px] font-black text-indigo-500 dark:text-indigo-400 uppercase tracking-widest flex items-center gap-1.5">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                            {{ addForm.source === 'COMPRADO' ? 'Datos del Soporte de Compra' : 'Costo de Referencia (Stock)' }}
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div :class="addForm.source === 'INVENTARIO' ? 'col-span-2' : ''">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                                    <i class="fa-solid fa-hand-holding-dollar text-indigo-500 mr-1.5"></i>Costo ($ USD)
                                </label>
                                <input type="number" step="0.01" v-model="addForm.cost" required class="block w-full bg-white dark:bg-slate-900 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:ring-2 focus:ring-indigo-500 text-xs font-bold" placeholder="0.00">
                                <span v-if="addForm.errors.cost" class="text-[9px] text-rose-500 font-extrabold mt-1 block uppercase tracking-tight">{{ addForm.errors.cost }}</span>
                            </div>
                            <div v-if="addForm.source === 'COMPRADO'">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                                    <i class="fa-solid fa-file-invoice text-indigo-500 mr-1.5"></i>Soporte Fiscal
                                </label>
                                <select v-model="addForm.document_type" class="block w-full bg-white dark:bg-slate-900 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-indigo-500 rounded-xl text-xs font-bold">
                                    <option value="FACTURA">FACTURA</option>
                                    <option value="RECIBO">RECIBO</option>
                                    <option value="NINGUNO">NINGUNO</option>
                                </select>
                            </div>
                        </div>

                        <div v-if="addForm.source === 'COMPRADO' && addForm.document_type !== 'NINGUNO'" class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                                    <i class="fa-solid fa-hashtag text-indigo-500 mr-1.5"></i>Nro. Factura/Recibo
                                </label>
                                <input type="text" v-model="addForm.invoice_number" required class="block w-full bg-white dark:bg-slate-900 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:ring-2 focus:ring-indigo-500 uppercase text-xs font-bold" placeholder="EJ: 12345">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                                    <i class="fa-solid fa-calculator text-indigo-500 mr-1.5"></i>Base Imponible (BIG)
                                </label>
                                <input type="number" step="0.01" v-model="addForm.base_imponible" class="block w-full bg-white dark:bg-slate-900 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:ring-2 focus:ring-indigo-500 text-xs font-bold" placeholder="Opcional">
                                <span v-if="addForm.errors.base_imponible" class="text-[9px] text-rose-500 font-extrabold mt-1 block uppercase tracking-tight">{{ addForm.errors.base_imponible }}</span>
                            </div>
                        </div>

                        <div v-if="addForm.source === 'COMPRADO' && addForm.document_type !== 'NINGUNO'">
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                                <i class="fa-solid fa-image text-indigo-500 mr-1.5"></i>Imagen de la Factura o Recibo
                            </label>
                            <input @input="addForm.invoice_file = $event.target.files[0]" type="file" accept="image/*" class="block w-full text-xs text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-indigo-50 dark:file:bg-slate-800 file:text-indigo-700 dark:file:text-indigo-400 hover:file:bg-indigo-100 dark:hover:file:bg-slate-750 border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-2 bg-white dark:bg-slate-900">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                            <i class="fa-solid fa-comment-dots text-indigo-500 mr-1.5"></i>Notas / Observaciones
                        </label>
                        <textarea v-model="addForm.notes" class="block w-full bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 min-h-[60px] focus:ring-2 focus:ring-indigo-500" placeholder="Opcional"></textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-gray-150 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-gray-700 dark:text-white text-xs font-bold uppercase rounded-xl transition-all">Cancelar</button>
                        <button type="submit" :disabled="addForm.processing" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase rounded-xl transition-all shadow-md shadow-indigo-600/10">Agregar Ítem</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Return Item Modal -->
        <div v-if="showReturnModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showReturnModal = false"></div>
            <div class="relative bg-white dark:bg-slate-900 w-full max-w-md rounded-3xl shadow-2xl border border-gray-100 dark:border-slate-800 p-6 sm:p-8 animate-in zoom-in-95 duration-200">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 uppercase flex items-center gap-2">
                    <i class="fa-solid fa-right-to-bracket text-emerald-500"></i>Registrar Entrada de Rectificadora
                </h3>
                
                <form @submit.prevent="submitReturn" class="space-y-4 text-left">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                                <i class="fa-solid fa-dollar-sign text-emerald-500 mr-1.5"></i>Costo ($ USD)
                            </label>
                            <input type="number" step="0.01" v-model="returnForm.cost" required class="block w-full bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:ring-2 focus:ring-emerald-500 rounded-xl">
                            <span v-if="returnForm.errors.cost" class="text-[9px] text-rose-500 font-extrabold mt-1 block uppercase tracking-tight">{{ returnForm.errors.cost }}</span>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                                <i class="fa-solid fa-file-invoice text-emerald-500 mr-1.5"></i>Soporte
                            </label>
                            <select v-model="returnForm.document_type" class="block w-full bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-emerald-500 rounded-xl">
                                <option value="FACTURA">FACTURA</option>
                                <option value="RECIBO">RECIBO / NOTA</option>
                            </select>
                        </div>
                    </div>

                    <div v-if="returnForm.document_type === 'FACTURA'" class="grid grid-cols-2 gap-4 animate-in fade-in duration-200">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                                <i class="fa-solid fa-hashtag text-emerald-500 mr-1.5"></i>Nro. de Factura
                            </label>
                            <input type="text" v-model="returnForm.invoice_number" required class="block w-full bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:ring-2 focus:ring-emerald-500 rounded-xl uppercase font-bold" placeholder="F-0001">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                                <i class="fa-solid fa-calculator text-emerald-500 mr-1.5"></i>Base Imponible (BIG)
                            </label>
                            <input type="number" step="0.01" v-model="returnForm.base_imponible" required class="block w-full bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:ring-2 focus:ring-emerald-500 rounded-xl">
                            <span v-if="returnForm.errors.base_imponible" class="text-[9px] text-rose-500 font-extrabold mt-1 block uppercase tracking-tight">{{ returnForm.errors.base_imponible }}</span>
                        </div>
                    </div>

                    <!-- Imagen de Soporte (Factura / Recibo) -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                            <i class="fa-solid fa-image text-emerald-500 mr-1.5"></i>Imagen de la Factura o Recibo
                        </label>
                        <input @input="returnForm.invoice_file = $event.target.files[0]" type="file" accept="image/*" class="block w-full text-xs text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-emerald-50 dark:file:bg-slate-850 file:text-emerald-700 dark:file:text-emerald-400 hover:file:bg-emerald-100 dark:hover:file:bg-slate-750 border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-2 bg-gray-50 dark:bg-slate-800">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                            <i class="fa-solid fa-comment-dots text-emerald-500 mr-1.5"></i>Notas / Observaciones de Entrada
                        </label>
                        <textarea v-model="returnForm.notes" class="block w-full bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 min-h-[60px] focus:ring-2 focus:ring-emerald-500" placeholder="Opcional"></textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3">
                        <button type="button" @click="showReturnModal = false" class="px-4 py-2 bg-gray-150 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-gray-700 dark:text-white text-xs font-bold uppercase rounded-xl transition-all">Cancelar</button>
                        <button type="submit" :disabled="returnForm.processing" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase rounded-xl transition-all shadow-md shadow-emerald-600/10">Registrar Entrada</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Item Modal -->
        <div v-if="showEditModal && activeEditItem" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showEditModal = false"></div>
            <div class="relative bg-white dark:bg-slate-900 w-full max-w-lg rounded-3xl shadow-2xl border border-gray-100 dark:border-slate-800 p-6 sm:p-8 animate-in zoom-in-95 duration-200">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 uppercase flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-amber-500"></i>Editar Factura / Recibo de Ítem
                </h3>
                
                <form @submit.prevent="submitEdit" class="space-y-4 text-left">
                    <div class="p-3 bg-indigo-50/50 dark:bg-slate-800/30 rounded-xl border border-indigo-100 dark:border-slate-700/50">
                        <div class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">Ítem / Pieza</div>
                        <div class="text-sm font-black text-gray-800 dark:text-white uppercase">{{ activeEditItem.description }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                                <i class="fa-solid fa-dollar-sign text-amber-500 mr-1.5"></i>Costo ($ USD)
                            </label>
                            <input type="number" step="0.01" v-model="editForm.cost" required class="block w-full bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:ring-2 focus:ring-amber-500 rounded-xl">
                            <span v-if="editForm.errors.cost" class="text-[9px] text-rose-500 font-extrabold mt-1 block uppercase tracking-tight">{{ editForm.errors.cost }}</span>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                                <i class="fa-solid fa-file-invoice text-amber-500 mr-1.5"></i>Soporte
                            </label>
                            <select v-model="editForm.document_type" class="block w-full bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-amber-500 rounded-xl">
                                <option value="FACTURA">FACTURA</option>
                                <option value="RECIBO">RECIBO / NOTA</option>
                                <option value="NINGUNO">NINGUNO</option>
                            </select>
                        </div>
                    </div>

                    <div v-if="editForm.document_type !== 'NINGUNO'" class="grid grid-cols-2 gap-4 animate-in fade-in duration-200">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                                <i class="fa-solid fa-hashtag text-amber-500 mr-1.5"></i>Nro. de Documento
                            </label>
                            <input type="text" v-model="editForm.invoice_number" required class="block w-full bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:ring-2 focus:ring-amber-500 rounded-xl uppercase font-bold" placeholder="EJ: 12345">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                                <i class="fa-solid fa-calculator text-amber-500 mr-1.5"></i>Base Imponible (BIG)
                            </label>
                            <input type="number" step="0.01" v-model="editForm.base_imponible" class="block w-full bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:ring-2 focus:ring-amber-500 rounded-xl">
                            <span v-if="editForm.errors.base_imponible" class="text-[9px] text-rose-500 font-extrabold mt-1 block uppercase tracking-tight">{{ editForm.errors.base_imponible }}</span>
                        </div>
                    </div>

                    <!-- Imagen de Soporte (Factura / Recibo) -->
                    <div v-if="editForm.document_type !== 'NINGUNO'">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1 flex justify-between">
                            <span><i class="fa-solid fa-image text-amber-500 mr-1.5"></i>Actualizar Imagen de Soporte</span>
                            <span v-if="activeEditItem.invoice_path" class="text-indigo-500 dark:text-indigo-400 font-extrabold text-[8px] lowercase italic">(ya cuenta con archivo adjunto)</span>
                        </label>
                        <input @input="editForm.invoice_file = $event.target.files[0]" type="file" accept="image/*" class="block w-full text-xs text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-amber-50 dark:file:bg-slate-855 file:text-amber-700 dark:file:text-amber-400 hover:file:bg-amber-100 dark:hover:file:bg-slate-750 border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-2 bg-gray-50 dark:bg-slate-800">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">
                            <i class="fa-solid fa-comment-dots text-amber-500 mr-1.5"></i>Notas / Observaciones
                        </label>
                        <textarea v-model="editForm.notes" class="block w-full bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 min-h-[60px] focus:ring-2 focus:ring-amber-500" placeholder="Opcional"></textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-150 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-gray-700 dark:text-white text-xs font-bold uppercase rounded-xl transition-all">Cancelar</button>
                        <button type="submit" :disabled="editForm.processing" class="px-6 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-black uppercase rounded-xl transition-all shadow-md shadow-amber-600/10">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Detail Modal -->
        <div v-if="showDetailModal && activeDetailItem" class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-in fade-in duration-200">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showDetailModal = false"></div>
            <div class="relative bg-white dark:bg-slate-900 w-full max-w-lg rounded-3xl shadow-2xl border border-gray-100 dark:border-slate-800 p-6 sm:p-8 animate-in zoom-in-95 duration-200">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 uppercase flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-indigo-500"></i>Detalles del Repuesto / Servicio
                </h3>
                
                <div class="space-y-4 text-left max-h-[70vh] overflow-y-auto pr-1">
                    <div class="bg-gray-50 dark:bg-slate-800/50 rounded-2xl p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-gray-400 uppercase flex items-center gap-1.5">
                                <i class="fa-solid fa-wrench text-indigo-500 mr-0.5"></i>Descripción
                            </span>
                            <span class="text-sm font-black text-gray-800 dark:text-white uppercase">{{ activeDetailItem.description }}</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-100 dark:border-slate-700/50 pt-2.5">
                            <span class="text-[10px] font-black text-gray-400 uppercase flex items-center gap-1.5">
                                <i class="fa-solid fa-tag text-indigo-500 mr-0.5"></i>Tipo
                            </span>
                            <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md bg-purple-100 dark:bg-purple-950/30 text-purple-700 dark:text-purple-400">
                                {{ activeDetailItem.type }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-100 dark:border-slate-700/50 pt-2.5">
                            <span class="text-[10px] font-black text-gray-400 uppercase flex items-center gap-1.5">
                                <i class="fa-solid fa-warehouse text-indigo-500 mr-0.5"></i>Origen
                            </span>
                            <span class="text-xs font-bold text-gray-700 dark:text-slate-300">
                                {{ activeDetailItem.source === 'INVENTARIO' ? 'Taller (Existente)' : 'Compra Externa' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-100 dark:border-slate-700/50 pt-2.5">
                            <span class="text-[10px] font-black text-gray-400 uppercase flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-notch text-indigo-500 mr-0.5"></i>Estado
                            </span>
                            <span class="px-2.5 py-1 text-[9px] font-black uppercase rounded-full tracking-wider bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                                {{ activeDetailItem.status === 'FUERA' ? 'En Rectificadora' : activeDetailItem.status === 'RETORNADO' ? 'Pendiente' : activeDetailItem.status === 'CONCILIADO' ? 'Conciliado' : 'Completado' }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-slate-800/50 rounded-2xl p-4 space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-[10px] font-black text-gray-400 uppercase mb-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-hand-holding-dollar text-emerald-500 mr-0.5"></i>Costo ($ USD)
                                </span>
                                <span class="text-sm font-black text-gray-800 dark:text-white">{{ formatPrice(activeDetailItem.cost) }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-black text-gray-400 uppercase mb-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-file-invoice text-emerald-500 mr-0.5"></i>Soporte Fiscal
                                </span>
                                <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase">{{ activeDetailItem.document_type || 'NINGUNO' }}</span>
                            </div>
                        </div>

                        <div v-if="activeDetailItem.invoice_number" class="grid grid-cols-2 gap-4 border-t border-gray-100 dark:border-slate-700/50 pt-2.5">
                            <div>
                                <span class="block text-[10px] font-black text-gray-400 uppercase mb-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-hashtag text-emerald-500 mr-0.5"></i>Nro. de Factura
                                </span>
                                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase font-black">{{ activeDetailItem.invoice_number }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-black text-gray-400 uppercase mb-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-calculator text-emerald-500 mr-0.5"></i>Base Imponible (BIG)
                                </span>
                                <span class="text-xs font-extrabold text-emerald-600 dark:text-emerald-450">{{ formatPrice(activeDetailItem.base_imponible) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-slate-800/50 rounded-2xl p-4 space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-[10px] font-black text-gray-400 uppercase mb-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-calendar-minus text-rose-500 mr-0.5"></i>Fecha de Salida
                                </span>
                                <span class="text-xs font-bold text-gray-700 dark:text-slate-300">
                                    {{ activeDetailItem.outflow_date ? formatDate(activeDetailItem.outflow_date) : 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-black text-gray-400 uppercase mb-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-calendar-check text-emerald-500 mr-0.5"></i>Fecha de Entrada
                                </span>
                                <span class="text-xs font-bold text-gray-700 dark:text-slate-300">
                                    {{ activeDetailItem.return_date ? formatDate(activeDetailItem.return_date) : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeDetailItem.notes" class="bg-gray-50 dark:bg-slate-800/50 rounded-2xl p-4">
                        <span class="block text-[10px] font-black text-gray-400 uppercase mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-comment-dots text-indigo-500 mr-0.5"></i>Notas / Observaciones
                        </span>
                        <p class="text-xs font-semibold text-gray-600 dark:text-slate-400 leading-relaxed uppercase">{{ activeDetailItem.notes }}</p>
                    </div>

                    <!-- Imagen de Soporte (Factura / Recibo) -->
                    <div v-if="activeDetailItem.invoice_path" class="bg-gray-50 dark:bg-slate-800/50 rounded-2xl p-4 space-y-2">
                        <span class="block text-[10px] font-black text-gray-400 uppercase flex items-center gap-1.5">
                            <i class="fa-solid fa-image text-indigo-500 mr-0.5"></i>Imagen de Soporte (Factura / Recibo)
                        </span>
                        <div class="relative group overflow-hidden rounded-xl border border-gray-200 dark:border-slate-700 bg-black/5 dark:bg-black/25 flex justify-center items-center p-2">
                            <img :src="'/storage/' + activeDetailItem.invoice_path" class="max-h-[250px] w-auto object-contain rounded-lg shadow-md transition-transform duration-300 group-hover:scale-[1.02]" alt="Imagen de Soporte">
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex items-center justify-end">
                    <button type="button" @click="showDetailModal = false" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase rounded-xl transition-all shadow-md shadow-indigo-600/10">Cerrar</button>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-in fade-in duration-200">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showDeleteConfirmModal = false"></div>
            <div class="relative bg-white dark:bg-slate-900 w-full max-w-sm rounded-3xl shadow-2xl border border-gray-100 dark:border-slate-800 p-6 sm:p-8 text-center animate-in zoom-in-95 duration-200">
                <div class="w-16 h-16 bg-rose-50 dark:bg-rose-950/30 rounded-full flex items-center justify-center mx-auto mb-4 text-rose-500">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                </div>
                <h3 class="text-base font-black text-gray-900 dark:text-white uppercase mb-2">¿Eliminar Repuesto / Servicio?</h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 font-semibold mb-6 uppercase">Esta acción es permanente y el costo se recalculará automáticamente.</p>
                
                <div class="flex items-center justify-center gap-3">
                    <button type="button" @click="showDeleteConfirmModal = false" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-gray-700 dark:text-white text-xs font-black uppercase rounded-xl transition-all">Cancelar</button>
                    <button type="button" @click="executeDelete" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black uppercase rounded-xl transition-all shadow-md shadow-rose-600/10">Sí, Eliminar</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>