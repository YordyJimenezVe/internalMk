<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
  bill: Object,
});

const form = useForm({
    numero_factura: props.bill.numero_factura || 'S/N',
    numero_control: props.bill.numero_control || 'S/N',
    numero_nota_credito: props.bill.numero_nota_credito || '',
    numero_factura_afect: props.bill.numero_factura_afect || props.bill.numero_factura || 'S/N',
    return_type: 'TOTAL', // Default to total return
});

const submit = () => {
    form.post(route('billing.returnSubmit', props.bill.id));
};
</script>

<template>
    <AppLayout title="Devolución de Factura">
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center transition-colors">
                    <i class="fa-solid fa-repeat mr-3 text-emerald-500"></i>Procesar Devolución
                </h2>
                <div class="flex items-center gap-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                    <span>Factura Original #{{ props.bill.numero_factura }}</span>
                </div>
            </div>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                    
                    <div class="bg-gradient-to-r from-emerald-500/10 to-indigo-500/10 p-8 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-6">
                            <div class="h-16 w-16 bg-white dark:bg-gray-900 rounded-2xl flex items-center justify-center text-emerald-500 shadow-xl shadow-emerald-500/10">
                                <i class="fa-solid fa-receipt text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-tight">Detalles de la Factura Original</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest mt-1">Control: {{ props.bill.numero_control }} | Cliente: {{ props.bill.client_name }}</p>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="p-8 md:p-12 space-y-10">
                        
                        <!-- Section 1: Credit Note Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Número Nota de Crédito</label>
                                <div class="relative group">
                                    <i class="fa-solid fa-file-invoice absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition-colors"></i>
                                    <input v-model="form.numero_nota_credito" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-emerald-500 transition-all font-bold" type="text" placeholder="Ej: NC-001" required>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Factura Afectada</label>
                                <div class="relative group">
                                    <i class="fa-solid fa-link absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                    <input v-model="form.numero_factura_afect" class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-3.5 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" type="text" placeholder="Número Factura" required>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Return Type Radio Buttons -->
                        <div class="space-y-4">
                            <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase ml-1">Motivo / Tipo de Devolución</label>
                            
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Option 1: Total Return -->
                                <div 
                                    @click="form.return_type = 'TOTAL'"
                                    :class="form.return_type === 'TOTAL' ? 'border-emerald-500 bg-emerald-50/30 dark:bg-emerald-900/10' : 'border-gray-100 dark:border-gray-700 bg-transparent'"
                                    class="relative flex items-center p-6 border-2 rounded-3xl cursor-pointer transition-all hover:bg-emerald-50/20 group"
                                >
                                    <div class="flex items-center justify-center h-12 w-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 mr-4 group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-box-open text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-black text-gray-800 dark:text-white uppercase tracking-tight text-sm">Devolución Total</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-tight mt-0.5">El ítem regresará inmediatamente al stock disponible para la venta.</p>
                                    </div>
                                    <div v-if="form.return_type === 'TOTAL'" class="h-6 w-6 rounded-full bg-emerald-500 flex items-center justify-center text-white scale-110">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </div>
                                </div>

                                <!-- Option 2: Temporal Return -->
                                <div 
                                    @click="form.return_type = 'TEMPORAL'"
                                    :class="form.return_type === 'TEMPORAL' ? 'border-indigo-500 bg-indigo-50/30 dark:bg-indigo-900/10' : 'border-gray-100 dark:border-gray-700 bg-transparent'"
                                    class="relative flex items-center p-6 border-2 rounded-3xl cursor-pointer transition-all hover:bg-indigo-50/20 group"
                                >
                                    <div class="flex items-center justify-center h-12 w-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 mr-4 group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-screwdriver-wrench text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-black text-gray-800 dark:text-white uppercase tracking-tight text-sm">Devolución Temporal</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-tight mt-0.5">Permite mover el ítem a mantenimiento antes de reincorporarlo al stock.</p>
                                    </div>
                                    <div v-if="form.return_type === 'TEMPORAL'" class="h-6 w-6 rounded-full bg-indigo-500 flex items-center justify-center text-white scale-110">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </div>
                                </div>

                                <!-- Option 3: Desincorporacion -->
                                <div 
                                    @click="form.return_type = 'DESINCORPORACION'"
                                    :class="form.return_type === 'DESINCORPORACION' ? 'border-rose-500 bg-rose-50/30 dark:bg-rose-900/10' : 'border-gray-100 dark:border-gray-700 bg-transparent'"
                                    class="relative flex items-center p-6 border-2 rounded-3xl cursor-pointer transition-all hover:bg-rose-50/20 group"
                                >
                                    <div class="flex items-center justify-center h-12 w-12 rounded-2xl bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 mr-4 group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-ban text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-black text-gray-800 dark:text-white uppercase tracking-tight text-sm">Desincorporación</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-tight mt-0.5">El ítem se marcará como dañado o inutilizable y saldrá del inventario activo.</p>
                                    </div>
                                    <div v-if="form.return_type === 'DESINCORPORACION'" class="h-6 w-6 rounded-full bg-rose-500 flex items-center justify-center text-white scale-110">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-6 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 text-xs font-bold uppercase tracking-widest">
                                <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                                <span>Esta acción eliminará la factura permanentemente</span>
                            </div>
                            
                            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                                <button type="button" @click="router.visit(route('billing'))" class="w-full md:w-auto bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-200 font-bold py-4 px-12 rounded-[2rem] transition-all transform active:scale-95">
                                    CANCELAR
                                </button>
                                <button type="submit" :disabled="form.processing" class="w-full md:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 px-12 rounded-[2rem] shadow-2xl shadow-emerald-600/30 transition-all transform hover:scale-[1.03] active:scale-95 flex items-center justify-center gap-3">
                                    <i v-if="form.processing" class="fa-solid fa-circle-notch fa-spin"></i>
                                    <i v-else class="fa-solid fa-check-double text-xl"></i>
                                    {{ form.processing ? 'PROCESANDO...' : 'CONFIRMAR DEVOLUCIÓN' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>