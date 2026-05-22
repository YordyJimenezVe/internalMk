<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import MaterialsEngine from './MaterialsEngine.vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';

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

const submitForm = (e) => {
    const formElement = e.target;
    const data = {};
    for (const element of formElement.elements) {
        if (element.name) {
            data[element.name] = element.value;
        }
    }
    // Handle the grouped commission
    if (data.grouped_commission) {
        data.cleaning = data.grouped_commission;
        data.consumables = data.grouped_commission;
        data.forklift = data.grouped_commission;
    }
    
    router.post('/maintenance/update/' + props.maintenance.id, data);
};

// Note: The previous UpdateAccesorios was redundant as it's now handled by the main update
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
                                <select class="block w-full bg-slate-900/50 text-white border border-slate-700 rounded-xl py-2.5 px-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 transition-all appearance-none cursor-pointer" id="status" name="status">
                                    <option value="EN ESPERA" :selected="props.maintenance.status === 'EN ESPERA'">En Espera</option>
                                    <option value="EN PROCESO" :selected="props.maintenance.status === 'EN PROCESO'">En Proceso</option>
                                    <option value="TERMINADO" :selected="props.maintenance.status === 'TERMINADO'">Terminado</option>
                                    <option value="CANCELADO" :selected="props.maintenance.status === 'CANCELADO'">Cancelado</option>
                                </select>
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

                        <h4 class="text-center mb-6 font-bold text-lg text-blue-600 dark:text-blue-400 tracking-tight uppercase">Información De Materiales y Accesorios</h4>
                        <div class="bg-white dark:bg-slate-800/40 p-5 rounded-xl border border-gray-100 dark:border-slate-700/50 mb-8 shadow-sm transition-colors">
                            <MaterialsEngine v-bind:materials="materials" v-bind:accesorios="accesorios" />
                        </div>
                        
                        <div class="pb-6">
                            <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 text-white font-bold py-4 px-6 rounded-2xl shadow-xl transform active:scale-[0.99] transition-all uppercase tracking-widest text-sm">Guardar Cambios del Mantenimiento</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>