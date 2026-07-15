<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { Link } from '@inertiajs/vue3';
import MaterialsEngine from './MaterialsEngine.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

library.add(fas);

const props = defineProps({
  maintenance: Object,
  partida: Object,
  bill: Object,
  materials: Object,
  accesorios: Object,
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

const statusLabel = computed(() => {
    if (!props.maintenance || !props.maintenance.status) return '';
    const statusMap = {
        'EN ESPERA': 'RECIBIDO',
        'EN PROCESO': 'ARMANDO',
        'TERMINADO': 'TERMINADO',
        'CANCELADO': 'CANCELADO',
    };
    return statusMap[props.maintenance.status] || props.maintenance.status;
});
</script>

<template>
    <AppLayout title="Ver Detalle Mantenimiento">
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <Link :href="route('maintenance')" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                    <FontAwesomeIcon icon="fa-solid fa-arrow-left" class="mr-2" />
                    Volver
                </Link>
                <div class="h-8 w-px bg-gray-300 dark:bg-gray-600 hidden md:block"></div>
                <div class="flex gap-2">
                    <a :href="route('maintenance.pdf', props.maintenance.id)" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-md">
                        <FontAwesomeIcon icon="fa-solid fa-file-pdf" class="mr-2" />
                        Imprimir Ficha
                    </a>
                </div>
                <div class="h-8 w-px bg-gray-300 dark:bg-gray-600 hidden md:block"></div>
                <h1 v-if="props.maintenance" class="text-2xl font-bold text-gray-800 dark:text-white flex flex-wrap items-center gap-2">
                    <span>Mantenimiento</span>
                    <span class="bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 px-3 py-1 rounded-lg text-lg">
                        #{{ props.maintenance.id }}
                    </span>
                    <span v-if="props.partida" class="text-gray-500 dark:text-gray-400 font-medium text-xl ml-1">
                        &mdash; {{ props.partida.marca }} {{ props.partida.modelo }}
                    </span>
                </h1>
                <h1 v-else class="text-2xl font-bold text-gray-400 animate-pulse">
                    Cargando registro...
                </h1>
            </div>
        </template>

        <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8" v-if="props.maintenance">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Unit Info -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-indigo-600 mb-4 border-b pb-2 flex items-center">
                            <FontAwesomeIcon icon="fa-solid fa-car" class="mr-2" />
                            Datos de la Unidad
                        </h3>
                        <div class="space-y-3 text-sm" v-if="props.partida">
                            <div class="flex justify-between"><span class="font-bold text-gray-500">Marca:</span> <span class="text-gray-900 dark:text-gray-100">{{ props.partida.marca }}</span></div>
                            <div class="flex justify-between"><span class="font-bold text-gray-500">Modelo:</span> <span class="text-gray-900 dark:text-gray-100">{{ props.partida.modelo }}</span></div>
                            <div class="flex justify-between"><span class="font-bold text-gray-500">Año:</span> <span class="text-gray-900 dark:text-gray-100">{{ props.partida.año }}</span></div>
                            <div class="flex justify-between"><span class="font-bold text-gray-500">Expediente:</span> <span class="text-gray-900 dark:text-gray-100">{{ props.partida.expediente }}</span></div>
                        </div>
                        <div v-else class="text-gray-400 italic text-sm">No hay datos de unidad disponibles.</div>

                        <h3 class="text-lg font-bold text-indigo-600 mt-8 mb-4 border-b pb-2 flex items-center">
                            <FontAwesomeIcon icon="fa-solid fa-gears" class="mr-2" />
                            Accesorios
                        </h3>
                        <div class="space-y-3 text-sm" v-if="props.accesorios">
                            <div v-if="parseFloat(props.accesorios.valve_cover) > 0" class="flex justify-between border-b border-gray-100 dark:border-gray-700 pb-1">
                                <span class="font-bold text-gray-500">Tapa Válvula:</span> 
                                <span class="text-green-600 dark:text-green-400 font-black">
                                    SÍ
                                </span>
                            </div>
                            <div v-if="parseFloat(props.accesorios.chain_cover) > 0" class="flex justify-between border-b border-gray-100 dark:border-gray-700 pb-1">
                                <span class="font-bold text-gray-500">Tapa Cadena:</span> 
                                <span class="text-green-600 dark:text-green-400 font-black">
                                    SÍ
                                </span>
                            </div>
                            <div v-if="parseFloat(props.accesorios.carter) > 0" class="flex justify-between border-b border-gray-100 dark:border-gray-700 pb-1">
                                <span class="font-bold text-gray-500">Carter:</span> 
                                <span class="text-green-600 dark:text-green-400 font-black">
                                    SÍ
                                </span>
                            </div>
                            <div v-if="parseFloat(props.accesorios.pescador) > 0" class="flex justify-between border-b border-gray-100 dark:border-gray-700 pb-1">
                                <span class="font-bold text-gray-500">Pescador:</span> 
                                <span class="text-green-600 dark:text-green-400 font-black">
                                    SÍ
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Maintenance Basic Info -->
                    <div class="md:col-span-2 space-y-8">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-indigo-600 mb-4 border-b pb-2 flex items-center">
                                <FontAwesomeIcon icon="fa-solid fa-wrench" class="mr-2" />
                                Información del Trabajo Realizado
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                                <div>
                                    <p class="font-bold text-gray-500 mb-1">Fecha:</p>
                                    <p class="text-gray-900 dark:text-gray-100 p-2 bg-gray-50 dark:bg-gray-700 rounded">{{ props.maintenance.fecha }}</p>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-500 mb-1">Tipo de Mantenimiento:</p>
                                    <p class="text-gray-900 dark:text-gray-100 p-2 bg-gray-50 dark:bg-gray-700 rounded">{{ props.maintenance.tipo }}</p>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-500 mb-1">Estado:</p>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-green-100 text-green-800">
                                        {{ statusLabel }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-500 mb-1">Mecánico Responsable:</p>
                                    <p class="text-gray-900 dark:text-gray-100 p-2 bg-gray-50 dark:bg-gray-700 rounded">{{ props.maintenance.nombre_mecanico }} {{ props.maintenance.apellido_mecanico }} ({{ props.maintenance.cedula_mecanico }})</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="font-bold text-gray-500 mb-1">Descripción Detallada:</p>
                                    <div class="text-gray-900 dark:text-gray-100 p-3 bg-gray-50 dark:bg-gray-700 rounded min-h-[100px] whitespace-pre-wrap">
                                        {{ props.maintenance.descripcion }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Commisions & Tools -->
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700" v-if="props.bill">
                            <h3 class="text-lg font-bold text-indigo-600 mb-4 border-b pb-2 flex items-center">
                                <FontAwesomeIcon icon="fa-solid fa-percent" class="mr-2" />
                                Herramientas y Mano de Obra (%)
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                                <template v-if="isAdminOrSuper">
                                    <div v-if="parseFloat(props.bill.multi_tools) > 0" class="p-2 border rounded border-gray-100 dark:border-gray-600"><span class="block font-bold text-gray-400">Herramientas:</span> <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ props.bill.multi_tools }}%</span></div>
                                    <div v-if="parseFloat(props.bill.mechanic) > 0" class="p-2 border rounded border-gray-100 dark:border-gray-600"><span class="block font-bold text-gray-400">Mecánico:</span> <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ props.bill.mechanic }}%</span></div>
                                    <div v-if="parseFloat(props.bill.mechanic_assistant) > 0" class="p-2 border rounded border-gray-100 dark:border-gray-600"><span class="block font-bold text-gray-400">Ayudante Mec.:</span> <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ props.bill.mechanic_assistant }}%</span></div>
                                    <div v-if="parseFloat(props.bill.seller) > 0" class="p-2 border rounded border-gray-100 dark:border-gray-600"><span class="block font-bold text-gray-400">Vendedor:</span> <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ props.bill.seller }}%</span></div>
                                    <div v-if="parseFloat(props.bill.seller_assistant) > 0" class="p-2 border rounded border-gray-100 dark:border-gray-600"><span class="block font-bold text-gray-400">Ayudante Vent.:</span> <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ props.bill.seller_assistant }}%</span></div>
                                    <div v-if="parseFloat(props.bill.camera_technician) > 0" class="p-2 border rounded border-gray-100 dark:border-gray-600"><span class="block font-bold text-gray-400">Téc. Cámaras:</span> <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ props.bill.camera_technician }}%</span></div>
                                    <div v-if="parseFloat(props.bill.camera_technical_assistant) > 0" class="p-2 border rounded border-gray-100 dark:border-gray-600"><span class="block font-bold text-gray-400">Ayudante Téc.:</span> <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ props.bill.camera_technical_assistant }}%</span></div>
                                </template>
                                
                                <div v-if="parseFloat(props.bill.consumables) > 0" class="col-span-2 md:col-span-4 p-4 border rounded-xl border-orange-100 dark:border-orange-900/40 bg-orange-50/30 dark:bg-orange-900/10 flex justify-between items-center group">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-orange-100 dark:bg-orange-900/40 rounded-lg text-orange-600 dark:text-orange-400 group-hover:scale-110 transition-transform">
                                            <FontAwesomeIcon icon="fa-solid fa-percent" />
                                        </div>
                                        <span class="font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Limpieza, Consumibles y Montacarga:</span>
                                    </div>
                                    <span class="text-2xl font-black text-orange-600 dark:text-orange-400">{{ props.bill.consumables }}%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Materials Section -->
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700" v-if="props.materials">
                             <h3 class="text-lg font-bold text-indigo-600 mb-4 border-b pb-2 flex items-center">
                                <FontAwesomeIcon icon="fa-solid fa-box-open" class="mr-2" />
                                Materiales Utilizados
                            </h3>
                            <div class="pointer-events-none opacity-90">
                                <MaterialsEngine :materials="materials" :accesorios="accesorios" :isView="true" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 p-12 rounded-xl shadow-lg text-center">
                    <FontAwesomeIcon icon="fa-solid fa-circle-exclamation" class="text-red-500 text-5xl mb-4" />
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Mantenimiento no encontrado</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">El registro que intentas ver no existe o ha sido eliminado.</p>
                    <Link :href="route('maintenance')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                        Volver al Listado
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
