<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({
  bill: Object,
});

const form = useForm({
    fecha: props.bill.fecha,
    hora: props.bill.hora,
    big: props.bill.big,
    igtf: props.bill.igtf,
    iva: props.bill.iva,
    bs: props.bill.bs,
    divisa: props.bill.divisa,
    total: props.bill.total,
    numero_factura: props.bill.numero_factura,
    numero_control: props.bill.numero_control,
    numero_nota_credito: props.bill.numero_nota_credito,
    numero_factura_afect: props.bill.numero_factura_afect,
    client_name: props.bill.client_name,
    client_cedula: props.bill.client_cedula,
    client_phone: props.bill.client_phone,
    client_address: props.bill.client_address,
    client_email: props.bill.client_email,
});

const submitForm = () => {
    form.post(route('updateBilling', props.bill.id), {
        onSuccess: () => {
            // Success logic if needed
        }
    });
};
</script>

<template>
    <AppLayout title="Editar Factura">
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center transition-colors">
                    <i class="fa-solid fa-file-pen mr-3 text-indigo-500"></i>Editar Factura #{{ props.bill.numero_factura }}
                </h2>
                <div class="flex items-center gap-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                    <span>Fecha: {{ props.bill.fecha }}</span>
                    <span class="mx-2">•</span>
                    <span>Hora: {{ props.bill.hora }}</span>
                </div>
            </div>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                    
                    <form @submit.prevent="submitForm" class="p-8 md:p-12 space-y-12">
                        
                        <!-- Section 1: Billing Metadata -->
                        <div class="space-y-6">
                            <h3 class="font-black text-xs uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-6 flex items-center">
                                <i class="fa-solid fa-rectangle-list mr-2 text-indigo-500"></i>Información de Control
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1" for="fecha">Fecha</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-calendar-day absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                        <input v-model="form.fecha" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="date">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1" for="hora">Hora</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                        <input v-model="form.hora" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="time">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-indigo-400 uppercase ml-1" for="numero_factura">Nro. Factura</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-hashtag absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400 transition-colors"></i>
                                        <input v-model="form.numero_factura" class="appearance-none block w-full bg-indigo-50/30 dark:bg-indigo-900/10 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/30 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-black" type="text" required>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1" for="numero_control">Nro. Control</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-fingerprint absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 transition-colors"></i>
                                        <input v-model="form.numero_control" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text" required>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Nro. Nota Crédito</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-file-invoice absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 transition-colors"></i>
                                        <input v-model="form.numero_nota_credito" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-sm" type="text">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Factura Afectada</label>
                                    <div class="relative group">
                                        <i class="fa-solid fa-link absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 transition-colors"></i>
                                        <input v-model="form.numero_factura_afect" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-sm" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Financial Details -->
                        <div class="space-y-6 pt-12 border-t border-gray-100 dark:border-gray-700">
                             <h3 class="font-black text-xs uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-6 flex items-center">
                                <i class="fa-solid fa-sack-dollar mr-2 text-emerald-500"></i>Detalles Financieros
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Base Imponible</label>
                                    <input v-model="form.big" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">IVA (16%)</label>
                                    <input v-model="form.iva" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-rose-400 uppercase ml-1">IGTF (3%)</label>
                                    <input v-model="form.igtf" class="appearance-none block w-full bg-rose-50/20 dark:bg-rose-900/10 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-rose-500 transition-all font-black" type="text" required>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Pago en Bs</label>
                                    <input v-model="form.bs" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Valor Divisa ($)</label>
                                    <input v-model="form.divisa" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Customer Information -->
                        <div class="space-y-6 pt-12 border-t border-gray-100 dark:border-gray-700">
                             <h3 class="font-black text-xs uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-6 flex items-center">
                                <i class="fa-solid fa-address-card mr-2 text-blue-500"></i>Información del Cliente
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Nombre</label>
                                        <input v-model="form.client_name" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Cédula / RIF</label>
                                        <input v-model="form.client_cedula" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Teléfono</label>
                                        <input v-model="form.client_phone" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Correo Electrónico</label>
                                        <input v-model="form.client_email" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="email">
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Dirección</label>
                                <input v-model="form.client_address" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text">
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pt-10 flex flex-col md:flex-row items-center justify-between gap-6 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 text-xs">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>Los cambios se registrarán en la bitácora del sistema</span>
                            </div>
                            
                            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                                <button type="button" @click="router.visit(route('billing'))" class="w-full md:w-auto bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-200 font-bold py-4 px-12 rounded-[2rem] transition-all transform active:scale-95">
                                    CANCELAR
                                </button>
                                <button type="submit" :disabled="form.processing" class="w-full md:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 px-12 rounded-[2rem] shadow-2xl shadow-emerald-600/30 transition-all transform hover:scale-[1.03] active:scale-95 flex items-center justify-center gap-3">
                                    <i v-if="form.processing" class="fa-solid fa-circle-notch fa-spin"></i>
                                    <i v-else class="fa-solid fa-floppy-disk text-xl"></i>
                                    {{ form.processing ? 'GUARDANDO...' : 'ACTUALIZAR FACTURA' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>