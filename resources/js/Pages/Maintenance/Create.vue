<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  partidas: Object,
  datas: Object,
  employee: Object,
});

</script>

<template >
    <AppLayout title="Mantenimiento">
        <template #header>
            <h1 class="text-center" >
                Registar Mantenimiento Para <span v-if="partidas">{{ partidas.marca }} {{ partidas.modelo }}</span>
            </h1>
        </template>

        <div class="bg-white">
            <form ref="form" @submit.prevent="submitForm" class="w-full max-w-lg mx-auto" method="post">
                <div class="flex flex-wrap mx-3 mb-6">
                    <div class="w-full md:w px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="partida_id">
                        Partida
                    </label>
                    <select class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="partida_id" v-model="formData.partida"  @change="getPartida(formData)" name="partida_id"  required>
                        <option v-for="data in datas" :key="data.id" :value="data.id">{{data.id}}-{{data.tipo}}-{{data.marca}}-{{data.modelo}}-{{data.codInv}}</option>
                    </select>
                    </div>
                    <div class="w-full md:w-1/2 px-3" v-if="partidas">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="marca">
                        Marca
                    </label>
                    <input  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="marca" type="text" placeholder="Marca" :value="props.partidas.marca" readonly required>
                    </div>
                </div>
                <div class="flex flex-wrap mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0" v-if="partidas">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="modelo">
                        Modelo
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="modelo" name="modelo" type="text" placeholder="Modelo" :value="props.partidas.modelo" readonly required>
                    </div>
                    <div class="w-full md:w-1/2 px-3" v-if="partidas">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="año">
                        Año
                    </label>
                    <input  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="año" type="text" placeholder="Año" :value="props.partidas.año" readonly required>
                    </div>
                    <div class="w-full md:w-1/2 px-3" v-if="partidas">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="codInv">
                        Código de Inventario
                    </label>
                    <input  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="codInv" type="text" placeholder="Código Inventario" :value="props.partidas.codInv" readonly required>
                    </div>
                    <div class="w-full md:w-1/2 px-3" v-if="partidas">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="expediente">
                        Código de Expediente
                    </label>
                    <input  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="expediente" type="text" placeholder="Expediente" :value="props.partidas.expediente" readonly required>
                    </div>
                    <div class="w-full md:w px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="descripcion">
                        Descripción del Mantenimiento
                    </label>
                    <input  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="descripcion" type="text" placeholder="Descripción" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3" v-if="partidas">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="tipo">
                        Tipo de Mantenimiento
                    </label>
                    <input  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="tipo" type="text" placeholder="Preventivo, correctivo, Inspección"  required>
                    </div>
                    <div class="w-full md:w-1/2 px-3" v-if="partidas">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="estado">
                        estado
                    </label>
                    <select class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="estado" name="estado" required>
                        <option value="EN ESPERA">EN ESPERA</option>
                        <option value="EN PROCESO">EN PROCESO</option>
                        <option value="TERMINADO">TERMINADO</option>
                        <option value="NO SE PUDO CONTINUAR">NO SE PUDO CONTINUAR</option>
                    </select>
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="fecha">
                        Fecha de Mantenimiento
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="fecha" name="fecha" type="date" placeholder="Fecha"  required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="cedula_mecanico">
                        Cédula Responsable
                    </label>
                    <input v-model="formData.cedula_mecanico" @change="getEmployee(formData)" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="cedula_mecanico" type="text" placeholder="Cédula Responsable" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3" v-if="employee">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="nombre_mecanico">
                        Nombre Mecánico
                    </label>
                    <input  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="nombre_mecanico" type="text" :value="props.employee.nombre" placeholder="Nombre Mecánico" required>
                    </div>
                    
                    <div class="w-full md:w-1/2 px-3" v-if="employee">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="apellido_mecanico">
                        Apellido Mecánico
                    </label>
                    <input  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="apellido_mecanico" type="text" :value="props.employee.apellido" placeholder="Apellido Mecánico" required>
                    </div>
                </div>
                <div class="flex flex-wrap mx-3 mb-6" v-if="partidas">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-2 mx-auto">Registrar</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
<script>
export default {
    data() {
        return {
            formData: {
                partida: {
                    value: '0',
                    label: 'seleccione'
                },
                marca: '',
                modelo: '',
                año: '',
                descripcion: '',
                cedula_mecanico: '',
                nombre: '',
                apellido: '',
            },
        };
    },
    methods: {
        submitForm() {
            const data = {};
            for (const element of this.$refs.form.elements) {
                data[element.name] = element.value;
            }
            this.$inertia.post('/maintenance/store', data);
        },

        async getPartida(e){
            var partida='';
            if(this.formData.partida){
                    partida=this.formData.partida;
            }
            try {
                const employee = e.cedula_mecanico;
                const response=await this.$inertia.post('/maintenance/add', {
                    partida: partida,
                    employee: employee,
                    datas: this.datas,
                });
            } catch (error) {
            }
            
        },
        async getEmployee(e){
            
            var employee='';
            if(this.formData.cedula_mecanico){
                    employee=this.formData.cedula_mecanico;
            }else{
                employee='';
            }
            try {
                const partida = e.partida;
                const response=await this.$inertia.post('/maintenance/add', {
                    partida: partida,
                    employee: employee,
                });
            } catch (error) {
            }
        },
        
    }
};
</script>