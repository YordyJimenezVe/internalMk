<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
const props = defineProps({
  containers: Object,
});

const currentUrl = window.location.pathname;
</script>

<template >
    <AppLayout title="Partidas">
        <template #header>
            <h1 class="text-center">
                Crear Partida 
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
                    <div class="w-full md:w-1/2 px-3 mb-3 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="container_id">
                            Container
                        </label>
                        <select class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="container_id" name="container_id" v-model="containerSelect" required>
                            <option v-for="container in containers"  :value="container.id" >Codigo {{ container.cod}} Expediente {{ container.expediente}}</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-wrap mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="tipo">
                        Tipo
                    </label>
                    <select class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="tipo" name="tipo" required>
                        <option v-if="currentUrl=='/partida/add'" value="MOTOR 3/4">Motor 3/4</option>
                        <option v-if="currentUrl=='/partida/add'" value="MOTOR 5/8">Motor 5/8</option>
                        <option v-if="currentUrl=='/partida/add'" value="MOTOR 7/8">Motor 7/8</option>
                        <option v-if="currentUrl=='/partida/add'" value="MOTOR COMPLETO">Motor COMPLETO</option>
                        <option v-if="currentUrl=='/partida/add'" value="CAJA AUTOMÁTICA">CAJA AUTOMÁTICA</option>
                        <option v-if="currentUrl=='/partida/add'" value="CAJA SINCRÓNICA">CAJA SINCRÓNICA</option>
                        <option value="CÁMARA">CÁMARA</option>
                        <option value="AUTOPARTE">AUTOPARTE</option>
                    </select>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="marca">
                        Marca
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="marca" type="text" placeholder="Marca" required>
                    </div>
                </div>
                <div class="flex flex-wrap mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="modelo">
                            Modelo
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="modelo" name="modelo" type="text" placeholder="Modelo" required>
                    </div>
                    
                    <div v-if="currentUrl=='/autopart/add'">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="categorie">
                            Categoria
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="categorie" name="categorie" type="text" placeholder="Ejemplo: Arranque" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="año">
                        Año
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="año" type="text" placeholder="Año" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="codInv">
                        Inventario
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="codInv" type="text" placeholder="Código Inventario"  required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="expediente">
                        Expediente
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" v-model="expedienteInput" name="expediente" type="text" readonly placeholder="Código Expediente" required>
                    </div>
                    <div v-if="currentUrl=='/autopart/add'">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="cantidad">
                            Cantidad
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="cantidad" name="cantidad" type="text" placeholder="Ejemplo: 10" required>
                    </div>
                    <div class="w-full md px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="price">
                        Precio en Divisa
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="price" type="text"  placeholder="Precio del producto en divisa" v-model="price" required>
                    </div>
                    <div class="w-full md px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="price_sale">
                        Precio De Venta
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="price_sale" type="text"  placeholder="Precio de Venta del Producto" v-model="price_sale" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="condicion">
                        Condición
                    </label>
                    <select class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="condicion" name="condicion" required>
                        <option value="APLICA">APLICA</option>
                        <option value="NO APLICA">NO APLICA</option>
                    </select>
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="status">
                            Estatus
                        </label>
                        <select class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="status" name="status" required>
                            <option value="DISPONIBLE">DISPONIBLE</option>
                            <option value="EN TALLER">EN TALLER</option>
                            <option value="VENDIDO">VENDIDO</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-wrap mx-3 mb-6">
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
      containerSelect: null,
      expedienteInput: '',
      price:'',
      price_sale:'',
    };
  },
  watch: {
    containerSelect(newValue) {
        var expediente=this.containers.filter(container => container.id == newValue);
        this.expedienteInput = expediente[0]['expediente'];
    },price(newValue){
        this.price=this.thousandsSeparatorDivisa(newValue)
    },price_sale(newValue){
        this.price_sale=this.thousandsSeparatorDivisa(newValue)
    },
  },
  methods: {
    submitForm() {
        const data = {};
        for (const element of this.$refs.form.elements) {
                data[element.name] = element.value;
        }
        this.$inertia.post('/partida/store', data);
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