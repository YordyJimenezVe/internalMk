<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import _ from 'lodash'; // Assuming lodash is installed
const props = defineProps({
  partidas: Object,
  data: Object,
  employee: Object,
  tasa_bcv: Number,
});
</script>

<template >
    <AppLayout title="Facturación">
        <template #header>
            <h1 class="text-center">
                Registrar Facturación 
            </h1>
        </template>

        <div class="bg-white">
            <form ref="form" @submit.prevent="submitForm" class="w-full max-w-lg mx-auto" method="post">
                <div class="flex flex-wrap mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="fecha">
                            Fecha
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="fecha" type="date" placeholder="Fecha" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="hora">
                            Hora
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="hora" type="time"  placeholder="Hora" required>
                    </div>
                </div>
                <div class="flex flex-wrap mx-3 mb-6">
                    <div class="w-full md:w px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="partida_id">
                            Partida
                        </label>
                        <select class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="partida_id" v-model="partida" name="partida_id"  required>
                            <option :key="data.id" :value="data.id">{{data.id}}-{{data.tipo}}-{{data.marca}}-{{data.modelo}}-{{data.codInv}}</option>
                        </select>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="priceDivisa">
                            Precio del Producto en Divisas
                        </label>
                        <input class="bsAmount appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="priceDivisa" type="text" placeholder="Precio en Divisas" v-model="priceDivisa" readonly required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="bs">
                            Pago en Bs
                        </label>
                        <input class="bsAmount appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="bs" type="text" placeholder="Bs" v-model="bsAmount" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="value_divisa">
                            Valor Divisa BCV
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="value_divisa" type="text" v-model="valueDivisa" placeholder="Valor Divisa BCV" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="divisa">
                            Pago en Divisas
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="divisa" type="text" v-model="pagoDivisa" placeholder="Pago en Divisas" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="big">
                            Big
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="big" type="text" v-model="big" placeholder="Big" readonly required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="iva">
                            Iva
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="iva" type="text" v-model="iva" placeholder="Iva" readonly required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="igtf">
                            IGTF
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="igtf" type="text" v-model="igtf" placeholder="IGTF" readonly required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="precio_total">
                            Precio Total
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="precio_total" type="text" v-model="totalAmount" placeholder="Precio Total" readonly required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="monto_cancelado">
                            Monto Cancelado
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="monto_cancelado" type="text" v-model="montoCancelado" placeholder="Monto Cancelado" readonly required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="numero_factura">
                            Número de Factura
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="numero_factura" type="text" placeholder="Número de Factura" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="numero_control">
                            Número de Control
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="numero_control" type="text" placeholder="Número de Control" required>
                    </div>
                    <!-- <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="numero_nota_credito">
                            Número Nota de Crédito
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="numero_nota_credito" type="text" placeholder="Número Nota de Crédito" required>
                    </div> -->
                    <input type="hidden" v-model="pagoDivisaHidden">
                    <input type="hidden" v-model="pagoBsHidden">
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
            formatValue: '',
            bsAmount:'',
            priceDivisa:'',
            pagoDivisaHidden:'',
            pagoBsHidden:'',
            valueDivisa:'',
            pagoDivisa: '',
            montoCancelado:'',
            igtf: '',
            iva: '',
            partida:'',
            big: '',
            totalAmount: '',
            formData: {
                partida: {
                    value: '0',
                    label: 'seleccione'
                },
                big: '',
                igtf: '',
                iva: '',
                numero_factura: '',
                numero_control: '',
                precio_total: '',
                // numero_nota_credito: '',
                // numero_factura_afect: '',
            },
        };
    },
    watch: {
        bsAmount(newValue){
            this.bsAmount=this.thousandsSeparator(newValue)
        },
        valueDivisa(newValue){
            const value = (String(newValue)).replace(/[,.]/g, "");
            this.valueDivisa=this.thousandsSeparator(value)
        },
        priceDivisa(newValue) {
            var valor=newValue
        },
        pagoDivisa(newValue) {
            //Sacar Igtf
            var valueDivisaSinCaracter = (String(this.valueDivisa)).replace(/[,.]/g, "");
            var newValueSinCaracter=newValue.replace(/[,.]/g, "");
            var pre=valueDivisaSinCaracter*newValueSinCaracter
            var igtf=3*pre
            var igtf1=parseInt(igtf/100)
            this.igtf=this.thousandsSeparator(String(igtf1))

            
            //Monto Cancelado
            var precioTotal = (String(this.totalAmount)).replace(/[,.]/g, "");
            var total=parseInt(precioTotal)+parseInt(igtf1)
            this.montoCancelado=this.thousandsSeparator(String(total))
        },
    },
    mounted() {
            this.valueDivisa=this.tasa_bcv
        
            this.priceDivisa = this.data['price'];
            var priceDivisaSinCracter=(String(this.priceDivisa)).replace(/[,.]/g, "");

            //Big
            const valueDivisaSinCaracter = (String(this.valueDivisa)).replace(/[,.]/g, "");
            const general=priceDivisaSinCracter*valueDivisaSinCaracter
            const big=general/1.16
            const bigInt=parseInt(big)
            this.big=this.thousandsSeparator(String(bigInt))

            //Iva
            const iva=priceDivisaSinCracter*valueDivisaSinCaracter
            const iva1=iva*16
            const iva2=iva1/100
            this.iva=this.thousandsSeparator(String(iva2))

            //Precio Total
            const total=bigInt+iva2 
            this.totalAmount=this.thousandsSeparator(String(total))
    },
    methods: {
        submitForm() {
            const data = {};
            for (const element of this.$refs.form.elements) {
                data[element.name] = element.value;
            }

            this.$inertia.post('/billing/store', data);
        },
        thousandsSeparator(param){
            return param
            .replace(/\D/g, "") // Remover caracteres no numéricos
            .replace(/([0-9])([0-9]{2})$/, "$1,$2") // agregar , luego de 2 digitos
            .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, "."); // Agegar decimal
            // actualizar la variable global con la variable local
            
        }
       
        
    }
};
</script>