<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    requests: Array,
});

const form = useForm({
    request_ids: [],
});

const processRequest = (id) => {
    form.request_ids = [id];
    form.post(route('billing.requests.process'), {
        preserveScroll: true,
        onSuccess: (page) => {
            form.reset();
            // Check for billing_ids in flash messages and open PDFs
            const billingIds = page.props.flash.billing_ids || [];
            if (billingIds.length > 0) {
                billingIds.forEach(id => {
                     window.open(route('billing.pdf', id), '_blank');
                });
            }
        },
    });
};

const editingRequest = ref(null);
const editForm = useForm({
    quantity: 1,
    price: 0,
    client_name: '',
    client_cedula: '',
});

const startEdit = (req) => {
    editingRequest.value = req;
    editForm.quantity = req.quantity;
    editForm.price = req.price;
    editForm.client_name = req.client_name;
    editForm.client_cedula = req.client_cedula;
};

const cancelEdit = () => {
    editingRequest.value = null;
    editForm.reset();
};

const updateRequest = () => {
    editForm.put(route('billing.requests.update', editingRequest.value.id), {
        onSuccess: () => cancelEdit(),
    });
};

const deleteRequest = (id) => {
    if (confirm('¿Estás seguro de eliminar esta solicitud?')) {
        router.delete(route('billing.requests.destroy', id));
    }
};

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('es-ES', options);
};
</script>

<template>
    <AppLayout title="Solicitudes de Facturación">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Solicitudes de Facturación Pendientes
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    
                    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
                        
                        <div v-if="requests.length === 0" class="text-center text-gray-500 dark:text-gray-400">
                            No hay solicitudes pendientes.
                        </div>

                        <div v-else class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Fecha</th>
                                        <th scope="col" class="px-6 py-3">Partida</th>
                                        <th scope="col" class="px-6 py-3">Cliente</th>
                                        <th scope="col" class="px-6 py-3">Cant.</th>
                                        <th scope="col" class="px-6 py-3">Precio</th>
                                        <th scope="col" class="px-6 py-3">Total</th>
                                        <th scope="col" class="px-6 py-3">Usuario</th>
                                        <th scope="col" class="px-6 py-3">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="req in requests" :key="req.id" class="bg-white border-b dark:bg-gray-900 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4">
                                            {{ formatDate(req.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ req.partida.marca }} {{ req.partida.modelo }} ({{ req.partida.año }})
                                            <br>
                                            <span class="text-xs text-gray-400">Inv: {{ req.partida.codInv }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ req.client_name || 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ req.quantity }}
                                        </td>
                                        <td class="px-6 py-4">
                                            ${{ req.price }}
                                        </td>
                                         <td class="px-6 py-4 font-bold">
                                            ${{ (req.price * req.quantity).toFixed(2) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ req.user.name }}
                                        </td>
                                        <td class="px-6 py-4 flex items-center space-x-2">
                                            <button 
                                                @click="processRequest(req.id)" 
                                                class="text-green-600 hover:text-green-900 font-bold"
                                                title="Procesar Venta"
                                            >
                                                ✅
                                            </button>
                                            <button 
                                                @click="startEdit(req)" 
                                                class="text-blue-600 hover:text-blue-900 font-bold"
                                                title="Editar"
                                            >
                                                ✏️
                                            </button>
                                            <button 
                                                @click="deleteRequest(req.id)" 
                                                class="text-red-600 hover:text-red-900 font-bold"
                                                title="Eliminar"
                                            >
                                                🗑️
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Edit Modal -->
                        <div v-if="editingRequest" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
                            <div class="bg-white p-5 rounded-md shadow-xl w-96">
                                <h3 class="text-lg font-bold mb-4">Editar Solicitud</h3>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Cantidad</label>
                                    <input v-model="editForm.quantity" type="number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Precio ($)</label>
                                    <input v-model="editForm.price" type="number" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Cliente</label>
                                    <input v-model="editForm.client_name" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Cédula</label>
                                    <input v-model="editForm.client_cedula" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="flex justify-end space-x-2">
                                    <button @click="cancelEdit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Cancelar</button>
                                    <button @click="updateRequest" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Guardar</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
