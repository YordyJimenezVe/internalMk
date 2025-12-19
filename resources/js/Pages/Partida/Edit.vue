<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
const props = defineProps({
  partida: Object,
  containers: Object,
  tipos: Object,
});
const currentUrl = window.location.pathname;
const routeSegments = window.location.pathname.split('/');
const firstRange = routeSegments[1];
</script>
<template >
    
    <AppLayout title="Asignaciones">
        <template #header>
            <h1 v-if="props.partida && props.partida.marca" class="text-center">
                Editar Partida de: {{ props.partida.marca }}  {{ props.partida.modelo }}
            </h1>
        </template>

        <div class="bg-white">
            <form ref="form" @submit.prevent="submitForm" class="w-full max-w-lg mx-auto" method="post">
                <div class="flex flex-wrap mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="item">
                            ITem
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="item" type="text" placeholder="Item" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="container_id">
                            Container
                        </label>
                        <select class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="container_id" name="container_id" v-model="selectedContainerId" required>
                            <option v-for="container in props.containers" :value="container.id">Codigo {{ container.cod}} Expediente {{ container.expediente}}</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-wrap mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="marca">
                        Marca
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="marca" name="marca" type="text" placeholder="Marca" :value="props.partida.marca">
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="modelo">
                        Modelo
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="modelo" type="text" placeholder="Modelo" :value="props.partida.modelo">
                    </div>
                </div>
                <div class="flex flex-wrap mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0" v-if="firstRange === 'autopart'">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="categorie">
                        Categoría
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="categorie" name="categorie" type="text" placeholder="Categoria" :value="props.partida.categorie">
                    </div>
                    <div class="w-full md:w-1/2 px-3" v-if="firstRange=='autopart'">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="cantidad">
                        Cantidad
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="cantidad" type="text" placeholder="Modelo" :value="props.partida.cantidad">
                    </div>
                </div>
                <div class="flex flex-wrap mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="año">
                        Año
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="año" name="año" type="text" placeholder="Año" :value="props.partida.año">
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="codInv">
                        Inventario
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="codInv" type="text" placeholder="Código Inventario" :value="props.partida.codInv" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="expediente">
                        Expediente
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="expediente" type="text" placeholder="Código Expediente" v-model="expedienteInput" readonly required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="price">
                        Precio en Divisa
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="price" type="text" placeholder="Precio en Divisa"  v-model="precioInput"  required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="price_sale">
                        Precio de Venta
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="price_sale" type="text" placeholder="Precio De venta"  v-model="precioSaleInput"  required>
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="condicion">
                        Condición
                    </label>
                    <select class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="condicion" name="condicion" v-model="props.partida.condicion" required>
                        <option value="APLICA" :selected="props.partida.condicion === 'APLICA'">APLICA</option>
                        <option value="NO APLICA" :selected="props.partida.condicion === 'NO APLICA'">NO APLICA</option>
                    </select>
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="status">
                        Estatus
                    </label>
                    <select class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="status" name="status" v-model="props.partida.status" required>
                        <option value="DISPONIBLE" :selected="props.partida.status === 'APLICA'">DISPONIBLE</option>
                        <option value="EN TALLER" :selected="props.partida.status === 'EN TALLER'">EN TALLER</option>
                        <option value="VENDIDO" :selected="props.partida.status === 'VENDIDO'">VENDIDO</option>
                    </select>
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="tipo">
                        Tipo
                    </label>
                    <select class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="tipo" name="tipo" v-model="selectedTipo" required>
                        <option v-for="tipo in props.tipos" :value="tipo" :selected="props.partida.tipo">{{ tipo}} </option>
                    </select>
                    </div>
                </div>
                <div class="flex flex-wrap mx-3 mb-6">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-2 mx-auto">Actualizar</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
<script>
export default {
    data() {
        return {
            selectedContainerId: null,
            selectedTipo: null,
            expedienteInput: '',
            precioInput:'',
            precioSaleInput:'',
        };
    },
    mounted() {
        const containerEncontrado = this.containers.find(container => container.id == this.partida.container.id);
        if (containerEncontrado) {
            this.selectedContainerId = containerEncontrado.id;
            this.expedienteInput = containerEncontrado.expediente;
        }
        this.precioInput=this.partida.price
        this.selectedTipo=this.partida.tipo
        this.precioSaleInput=this.partida.price_sale
    },
    watch: {
        selectedContainerId(newValue) {
            var expediente=this.containers.filter(container => container.id == newValue);
            this.expedienteInput = expediente[0]['expediente'];
        },
        precioInput(newValue) {
            this.precioInput=this.thousandsSeparatorDivisa(newValue)
        },
        precioSaleInput(newValue) {
            this.precioSaleInput=this.thousandsSeparatorDivisa(newValue)
        },
    },
    methods: {
        submitForm() {
            const data = {};
            for (const element of this.$refs.form.elements) {
                    data[element.name] = element.value;
            }
            
            this.$inertia.post('/partida/update/' + this.partida.id, data);
            },
        thousandsSeparatorDivisa(param){
            return param
            .replace(/\D/g, "") // Remover caracteres no numéricos
            .replace(/([0-9])([0-9]{3})$/, "$1.$2") // agregar , luego de 2 digitos
            .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, "."); // Agegar decimal
            // actualizar la variable global con la variable local
            
        }
    }
};
</script>