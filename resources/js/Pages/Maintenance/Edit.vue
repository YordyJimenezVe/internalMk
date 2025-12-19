<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  maintenance: Object,
  partida: Object,
  bill: Object,
  materials: Object,
  accesorios: Object,
});
</script>

<template >
    <AppLayout title="Asignaciones">
        <template #header>
            <h1 v-if="props.maintenance && props.maintenance.marca" class="text-center">
                Editar Partida de: {{ props.maintenance.marca }}  {{ props.maintenance.modelo }}
            </h1>
        </template>

        <div class="bg-white">
            <div class="row flex justify-between">
                <div class="md:w-1/3 ">
                    <h3 class="text-center mb-3 font-semibold text-xl text-gray-800 leading-tight">Datos de la Partida</h3>
                    <h3 class="text-center mb-3 font-semibold text-xl text-gray-800 leading-tight">Sólo para Efectos de Lectura</h3>
                    <div class="flex flex-wrap mx-3 mb-3">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="marca">
                            Marca
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="marca" name="marca" type="text" readonly placeholder="Marca" :value="props.partida.marca">
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="modelo">
                            Modelo
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="modelo" type="text" readonly placeholder="Modelo" :value="props.partida.modelo">
                        </div>
                    </div>
                    <div class="flex flex-wrap mx-3 mb-6">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="año">
                            Año
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="año" name="año" type="text" readonly placeholder="Año" :value="props.partida.año">
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="expediente">
                            Expediente
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="expediente" type="text" readonly placeholder="Expediente" :value="props.partida.expediente">
                        </div>
                    </div>
                    <h3 class="text-center mb-3 font-semibold text-xl text-gray-800 leading-tight">Accesorios</h3>
                    <form ref="accesoriosForm" @submit.prevent="UpdateAccesorios" method="post">
                        <div class="flex flex-wrap mx-3 mb-3">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="valve_cover">
                                Tapa Válvula
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="valve_cover" name="valve_cover" type="text" placeholder="Tapa Valvula" required :value="props.accesorios.valve_cover">
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="chain_cover">
                                Tapa Cadena
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="chain_cover" type="text" placeholder="Tapa Cadena" required :value="props.accesorios.chain_cover">
                            </div>
                        </div>
                        <div class="flex flex-wrap mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="carter">
                                Carte
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="carter" name="carter" type="text" placeholder="Carter" required :value="props.accesorios.carter">
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="pescador">
                                Pescador
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="pescador" type="text" placeholder="Pescador" required :value="props.accesorios.pescador">
                            </div>
                        </div>
                        <div class="flex flex-wrap mx-3 mb-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-2 mx-auto">Actualizar</button>
                        </div>
                    </form>
                </div>

                <div class="md:w-2/3 ">
                    <form ref="maintenanceForm" @submit.prevent="submitForm" method="post">
                    <h3 class="text-center mb-6 font-semibold text-xl text-gray-800 leading-tight">Datos del Mantenimiento</h3>
                    <h4 class="text-center mb-6 font-semibold text-gray-800 leading-tight">Información Común</h4>
                        
                    <div class="flex flex-wrap mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="fecha">
                                Fecha
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="fecha" name="fecha" type="date"  placeholder="Fecha" :value="props.maintenance.fecha">
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="tipo">
                                Tipo
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="tipo" type="text"  placeholder="Modelo" :value="props.maintenance.tipo">
                            </div>
                        </div>
                        <div class="flex flex-wrap mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="estado">
                                Estado
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="estado" name="estado" type="text"  placeholder="Estado" :value="props.maintenance.estado">
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="cedula_mecanico">
                                Cédula del Mecánico
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="cedula_mecanico" type="text"  placeholder="Cédula del Mecánico" :value="props.maintenance.cedula_mecanico">
                            </div>
                        </div>
                        <div class="flex flex-wrap mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="nombre_mecanico">
                                Nombre Mecánico
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="nombre_mecanico" name="nombre_mecanico" type="text"  placeholder="Nombre del Mecánico" :value="props.maintenance.nombre_mecanico">
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="apellido_mecanico">
                                Apellido
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="apellido_mecanico" type="text"  placeholder="Apellido del Mecánico" :value="props.maintenance.apellido_mecanico">
                            </div>
                        </div>
                        <div class="flex flex-wrap mx-3 mb-6">
                            <div class="w-full md:w px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="descripcion">
                                Descripción
                            </label>
                            <textarea class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="descripcion" name="descripcion" type="text" placeholder="Descripción del trabajo Realizado" :value="props.maintenance.descripcion"></textarea> 
                            </div>
                        </div>
                        <h4 class="text-center mb-6 font-semibold text-gray-800 leading-tight">Información De Herramientas y Mano de Obra</h4>
                        <div class="flex flex-wrap mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="multi_tools">
                                % Herramientas Multiples
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="multi_tools" name="multi_tools" type="text" placeholder="% por uso" :value="props.bill.multi_tools" required>
                            </div>
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="multi_equipament">
                                % Equipos Multiples
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="multi_equipament" name="multi_equipament" type="text" placeholder="% por uso" :value="props.bill.multi_equipament" required>
                            </div>
                        </div>
                        <div class="flex flex-wrap mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="mechanic">
                                % Comisión de Mecánico
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="mechanic" name="mechanic" type="text" placeholder="% Comisión del Mecánico" :value="props.bill.mechanic">
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="mechanic_assistant">
                                % Comisión del Ayudante Mecánico
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="mechanic_assistant" type="text" placeholder="% Comisión del Ayudante Mecánico" :value="props.bill.mechanic_assistant">
                            </div>
                        </div>
                        <div class="flex flex-wrap mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="seller">
                                % Comisión del Vendedor
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="seller" name="seller" type="text" placeholder="% Comisión del Vendedor" :value="props.bill.seller">
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="seller_assistant">
                                % Comisión del Ayudante de Ventas
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="seller_assistant" type="text" placeholder="% Comisión del ayudante de Ventas" :value="props.bill.seller_assistant">
                            </div>
                        </div>
                        <div class="flex flex-wrap mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="cleaning">
                                % Productos de Limpieza (Jabón, Desengrasantes, etc)
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="cleaning" name="cleaning" type="text" placeholder="Productos de Limpieza" :value="props.bill.cleaning">
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="drinking_water">
                                % Agua Potable
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="drinking_water" type="text" placeholder="% Comisión Agua Potable" :value="props.bill.drinking_water">
                            </div>
                        </div>
                        <div class="flex flex-wrap mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="consumables">
                                % Uso de Consumibles
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="consumables" name="consumables" type="text" placeholder="Uso de Consumibles" :value="props.bill.consumables">
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="forklift_driver">
                                % Comisión Montacarguista
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="forklift_driver" type="text" placeholder="% Comisión Montacarguista" :value="props.bill.forklift_driver">
                            </div>
                        </div>
                        <div class="flex flex-wrap mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="camera_technician">
                                % Técnico en Cámaras
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="camera_technician" name="camera_technician" type="text" placeholder="Técnico en Cámaras" :value="props.bill.camera_technician">
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="camera_technical_assistant">
                                % Ayudante Técnico en Cámaras
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="camera_technical_assistant" type="text" placeholder="% Comisión ayudante Técnico en Cámaras" :value="props.bill.camera_technical_assistant">
                            </div>
                        </div>
                            <h4 class="text-center mb-6 font-semibold text-gray-800 leading-tight">Información De Materiales</h4>
                            <MaterialsEngine v-bind:materials="materials" />
                        <div class="flex flex-wrap mx-3 mb-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-2 mx-auto">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<script>
import MaterialsEngine from './MaterialsEngine.vue'; // Import MaterialsEngine
export default {
   
  methods: {
        submitForm() {
            const data = {};
            for (const element of this.$refs.maintenanceForm.elements) {
                    data[element.name] = element.value;
            }
            this.$inertia.post('/maintenance/update/' + this.maintenance.id, data);
        },
        async UpdateAccesorios() {
            try {
                const url = route('updateAccesorios', this.maintenance.id);
                const data = {};
                for (const element of this.$refs.accesoriosForm.elements) {
                        data[element.name] = element.value;
                }
                const response = await axios.post(url, data);
                if (response.status === 200) {
                // Redireccionamos luego de que nuestra respuesta fue exitosa
                //return this.$inertia.route('/maintenance/edit/'+ this.maintenance.id);
                } else {
                // En caso de error durante la consulta imprimimos un mensaje
                console.error('Error Al actualizar Accesorios:', response.data);
                }
            } catch (error) {
                // Capturamos el Error Global
                console.error('Un error ocurrió:', error);
            }
        },  
    },
};
</script>