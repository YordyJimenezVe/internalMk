<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    cod: '',
    expediente: '',
    fecha: new Date().toISOString().split('T')[0],
    hora: new Date().toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit' }),
    motores: 0,
    cajas: 0,
    camaras: 0,
    accesorios: 0,
    costo_importacion_general: 0,
    aplicar_costos: false,
});

const submit = () => {
    form.post(route('storeContainer'));
};
</script>

<template>
    <AppLayout title="Registrar Contenedor">
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center">
                <i class="fa-solid fa-box-archive mr-3 text-indigo-500"></i>Registrar Nuevo Contenedor
            </h2>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50 p-8 md:p-12">
                    <form @submit.prevent="submit" class="space-y-10">
                        
                        <!-- Identification Section -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <h3 class="font-black text-[10px] uppercase tracking-[0.3em] text-gray-400 dark:text-gray-500 flex items-center">
                                    <i class="fa-solid fa-id-card mr-2"></i>Identificación
                                </h3>
                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase mb-2 ml-1" for="cod">Código de Contenedor</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-indigo-500">
                                                <i class="fa-solid fa-barcode"></i>
                                            </div>
                                            <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-4 pl-12 pr-4 leading-tight focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold" id="cod" type="text" v-model="form.cod" placeholder="Ej: CONT-2024-001" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase mb-2 ml-1" for="expediente">Nro. Expediente</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-indigo-500">
                                                <i class="fa-solid fa-folder-tree"></i>
                                            </div>
                                            <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-4 pl-12 pr-4 leading-tight focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold" id="expediente" type="text" v-model="form.expediente" placeholder="Ej: EXP-12345" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <h3 class="font-black text-[10px] uppercase tracking-[0.3em] text-gray-400 dark:text-gray-500 flex items-center">
                                    <i class="fa-solid fa-clock mr-2"></i>Logística
                                </h3>
                                <div class="grid grid-cols-1 gap-6">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase mb-2 ml-1" for="fecha">Fecha</label>
                                            <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-4 px-4 leading-tight focus:outline-none focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold" id="fecha" type="date" v-model="form.fecha">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase mb-2 ml-1" for="hora">Hora</label>
                                            <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-4 px-4 leading-tight focus:outline-none focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold" id="hora" type="time" v-model="form.hora">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quantities Section -->
                        <div class="p-8 bg-gray-50 dark:bg-gray-900/30 rounded-[2rem] border border-gray-100 dark:border-gray-700/50">
                            <h3 class="font-black text-[10px] uppercase tracking-[0.3em] text-gray-400 dark:text-gray-500 mb-6 flex items-center">
                                <i class="fa-solid fa-layer-group mr-2 text-indigo-500"></i>Cantidades Esperadas
                            </h3>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Motores</label>
                                    <input class="w-full bg-white dark:bg-gray-800 text-gray-800 dark:text-white text-lg font-black border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" type="number" v-model="form.motores">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Cajas</label>
                                    <input class="w-full bg-white dark:bg-gray-800 text-gray-800 dark:text-white text-lg font-black border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" type="number" v-model="form.cajas">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Cámaras</label>
                                    <input class="w-full bg-white dark:bg-gray-800 text-gray-800 dark:text-white text-lg font-black border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" type="number" v-model="form.camaras">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Accesorios</label>
                                    <input class="w-full bg-white dark:bg-gray-800 text-gray-800 dark:text-white text-lg font-black border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" type="number" v-model="form.accesorios">
                                </div>
                            </div>
                        </div>



                        <!-- Actions Section -->
                        <div class="pt-8 flex justify-center">
                            <button type="submit" :disabled="form.processing" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 px-20 rounded-[2rem] shadow-2xl shadow-indigo-600/30 transition-all transform hover:scale-[1.03] active:scale-95 disabled:opacity-50 flex items-center justify-center gap-3">
                                <i v-if="form.processing" class="fa-solid fa-circle-notch animate-spin text-xl"></i>
                                <i v-else class="fa-solid fa-plus text-xl"></i>
                                REGISTRAR CONTENEDOR
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>