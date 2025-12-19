<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
const props = defineProps({
  container: Object,
});
</script>

<template >
    <AppLayout title="Asignaciones">
        <template #header>
            <h1 v-if="props.container && props.container.fecha" class="text-center">
                Editar Contenedor de Fecha: {{ props.container.fecha }}
            </h1>
        </template>

        <div class="bg-white">
            <form ref="form" @submit.prevent="submitForm" class="w-full max-w-lg mx-auto" method="post">
                <div class="flex flex-wrap mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="cod">
                        Código
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" name="cod" type="text" placeholder="Código"  :value="props.container.cod" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="expediente">
                        Expediente
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="expediente" type="text" placeholder="Expediente"  :value="props.container.expediente" required>
                    </div>
                </div>
                
                <div class="flex flex-wrap mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="fecha">
                        Fecha
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" name="fecha" type="text" placeholder="Fecha" :value="props.container.fecha">
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="hora">
                        Hora
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="hora" type="text" placeholder="Hora" :value="props.container.hora">
                    </div>
                </div>
                <div class="flex flex-wrap mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="motores">
                        Cantidad de Motores
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" name="motores" type="text" placeholder="Motores" :value="props.container.motores">
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="cajas">
                        Cantidad de Cajas
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="cajas" type="text" placeholder="Código Inventario" :value="props.container.cajas" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="camaras">
                        Cantidad de Cámaras
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="camaras" type="text" placeholder="Código Expediente" :value="props.container.camaras" required>
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="accesorios">
                        Cantidad de Accesorios
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="accesorios" type="text" placeholder="Código Expediente" :value="props.container.accesorios" required>
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
  methods: {
    submitForm() {
        const data = {};
        for (const element of this.$refs.form.elements) {
                data[element.name] = element.value;
        }
        this.$inertia.post('/container/update/' + this.container.id, data);
    }
}
};
</script>