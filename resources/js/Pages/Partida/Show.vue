<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  partida: Object,
  qrCode: String,
  barcode: String,
  barcodeData: String,
});

const form = useForm({
    partida_id: props.partida.id,
    price: props.partida.price_sale || 0, // Assuming price_sale exists, fallback to 0
    client_name: '',
    client_cedula: '',
    quantity: 1,
});

const submitBilling = () => {
    form.transform((data) => ({
        ...data,
        client_name: data.client_name ? data.client_name.toUpperCase() : '',
        client_cedula: data.client_cedula ? data.client_cedula.toUpperCase() : '',
        price: data.price.toString().replace(/\./g, '').replace(',', '.')
    })).post(route('billing.requests.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('client_name', 'quantity');
            form.defaults({ client_name: '', client_cedula: '' }); // Ensure clear state
        },
    });
};
</script>

<template>
    <AppLayout title="Detalle de Partida">
        <template #header>
            <h1 v-if="props.partida" class="text-center font-bold text-xl text-gray-800 dark:text-gray-200">
                Partida: {{ props.partida.marca }} {{ props.partida.modelo }}
            </h1>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    
                    <!-- Detail Form (Read Only) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Marca</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight bg-gray-200" type="text" disabled :value="props.partida.marca">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Modelo</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight bg-gray-200" type="text" disabled :value="props.partida.modelo">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Año</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight bg-gray-200" type="text" disabled :value="props.partida.año">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Inventario</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight bg-gray-200" type="text" disabled :value="props.partida.codInv">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Expediente</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight bg-gray-200" type="text" disabled :value="props.partida.expediente">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Condición</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight bg-gray-200" type="text" disabled :value="props.partida.condicion">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Estatus</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight bg-gray-200" type="text" disabled :value="props.partida.status">
                        </div>
                         <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Precio Ref</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight bg-gray-200" type="text" disabled :value="props.partida.price_sale">
                        </div>
                    </div>

                    <!-- QR & Barcode Section -->
                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="text-center">
                                <h4 class="font-bold mb-3 text-gray-700 dark:text-gray-300">Código QR</h4>
                                <div class="inline-block bg-white p-2 rounded" v-html="props.qrCode"></div>
                            </div>
                            <div class="text-center">
                                <h4 class="font-bold mb-3 text-gray-700 dark:text-gray-300">Código de Barras</h4>
                                <div class="inline-block bg-white p-2 rounded">
                                    <div v-html="props.barcode"></div>
                                    <p class="text-xs text-black mt-1">{{ props.barcodeData }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Request Form -->
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <div class="bg-gray-800 text-white p-6 rounded-lg shadow-lg">
                            <h3 class="text-xl font-bold mb-4">Enviar a Facturación (Solicitud)</h3>
                            <form @submit.prevent="submitBilling">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold mb-2">Precio Unitario ($)</label>
                                        <input 
                                            v-model="form.price" 
                                            type="text" 
                                            placeholder="Ej: 1.400" 
                                            class="shadow appearance-none border border-gray-600 rounded w-full py-2 px-3 text-white bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                            required
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold mb-2">Cantidad</label>
                                        <input 
                                            v-model="form.quantity" 
                                            type="number" 
                                            min="1" 
                                            class="shadow appearance-none border border-gray-600 rounded w-full py-2 px-3 text-white bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                            required
                                        >
                                    </div>
                                    <div class="md:col-span-1">
                                         <label class="block text-sm font-bold mb-2">Cédula / RIF (Opcional)</label>
                                        <input 
                                            v-model="form.client_cedula" 
                                            type="text" 
                                            placeholder="V-12345678"
                                            class="shadow appearance-none border border-gray-600 rounded w-full py-2 px-3 text-white bg-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        >
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-sm font-bold mb-2">Cliente (Opcional)</label>
                                        <input 
                                            v-model="form.client_name" 
                                            type="text" 
                                            placeholder="Nombre del cliente"
                                            class="shadow appearance-none border border-gray-600 rounded w-full py-2 px-3 text-white bg-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        >
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button 
                                        type="submit" 
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full transition duration-150 ease-in-out"
                                        :disabled="form.processing"
                                    >
                                        {{ form.processing ? 'Enviando...' : 'Enviar Solicitud' }}
                                    </button>
                                </div>
                                <div v-if="form.recentlySuccessful" class="mt-2 text-green-400 font-bold text-center">
                                    Solicitud enviada con éxito.
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>