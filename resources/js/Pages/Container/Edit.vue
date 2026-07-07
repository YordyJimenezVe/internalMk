<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  container: Object,
});

const form = useForm({
    cod: props.container.cod,
    expediente: props.container.expediente,
    fecha: props.container.fecha,
    hora: props.container.hora,
    motores: props.container.motores,
    cajas: props.container.cajas,
    camaras: props.container.camaras,
    accesorios: props.container.accesorios,
    costo_importacion_general: props.container.costo_importacion_general,
    aplicar_costos: props.container.aplicar_costos === 1 || props.container.aplicar_costos === true,
});

const submit = () => {
    form.post(route('updateContainer', props.container.id));
};
</script>

<template>
    <AppLayout title="Editar Contenedor">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center">
                    <i class="fa-solid fa-box-open mr-3 text-indigo-500"></i>Editar Contenedor: <span class="ml-2 text-indigo-600 dark:text-indigo-400 font-black">{{ container.cod }}</span>
                </h2>
                <div class="flex items-center gap-4">
                    <span v-if="form.isDirty" class="text-xs font-black text-amber-500 uppercase animate-pulse">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i>Cambios sin guardar
                    </span>
                    <a :href="route('container')" class="text-sm font-bold text-gray-500 hover:text-indigo-600 transition-colors">
                        Cancelar
                    </a>
                </div>
            </div>
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
                                            <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-4 pl-12 pr-4 leading-tight focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold" id="cod" type="text" v-model="form.cod" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase mb-2 ml-1" for="expediente">Nro. Expediente</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-indigo-500">
                                                <i class="fa-solid fa-folder-tree"></i>
                                            </div>
                                            <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-2xl py-4 pl-12 pr-4 leading-tight focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold" id="expediente" type="text" v-model="form.expediente" required>
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
                        <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-6">
                            <div class="text-gray-400 dark:text-gray-500 text-xs font-bold italic">
                                <i class="fa-solid fa-fingerprint mr-1"></i>Última actualización: {{ new Date(container.updated_at).toLocaleDateString() }}
                            </div>
                            <button type="submit" :disabled="form.processing" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 px-16 rounded-[2rem] shadow-2xl shadow-indigo-600/30 transition-all transform hover:scale-[1.03] active:scale-95 disabled:opacity-50 flex items-center justify-center gap-3">
                                <i v-if="form.processing" class="fa-solid fa-circle-notch animate-spin text-xl"></i>
                                <i v-else class="fa-solid fa-floppy-disk text-xl"></i>
                                GUARDAR CAMBIOS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>