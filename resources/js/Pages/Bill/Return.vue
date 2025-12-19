<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
const props = defineProps({
  bill: Object,
});
</script>

<template >
    <AppLayout title="Asignaciones">
        <template #header>
            <h1 v-if="props.bill && props.bill.fecha" class="text-center">
                Devolución de  Factura {{ props.bill.numero_factura }} de fecha {{ props.bill.fecha }}
            </h1>
        </template>

        <div class="bg-white">
            <form ref="form" @submit.prevent="submitForm" class="w-full max-w-lg mx-auto" method="post">
                <div class="flex flex-wrap mx-3 mb-6">
                    
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="numero_factura">
                        Número Factura
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="numero_factura" type="text" placeholder="Número Factura" :value="props.bill.numero_factura" readonly required>
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="numero_control">
                        Número Control
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="numero_control" type="text" placeholder="Número Control" :value="props.bill.numero_control" readonly required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="numero_nota_credito">
                        Número Nota de Crédito
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="numero_nota_credito" type="text" placeholder="Número Nota de Crédito" :value="props.bill.numero_nota_credito" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="numero_factura_afect">
                        Número Factura Afectada
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="numero_factura_afect" type="text" placeholder="Numero Factura Afectada" :value="props.bill.numero_factura_afect" required>
                    </div>
                </div>
                <div class="flex flex-wrap mx-3 mb-6">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-2 mx-auto">Enviar</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
<script>
export default {
  methods: {
    submitForm() {
        const data = {};
        for (const element of this.$refs.form.elements) {
                data[element.name] = element.value;
        }
        this.$inertia.post('/billing/returnSubmit/' + this.bill.id, data);
    }
}
};
</script>