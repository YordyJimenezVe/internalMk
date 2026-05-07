<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, nextTick, computed } from 'vue';

const searchTerm = ref('');
const inputRef = ref(null);
const page = usePage();

const userRoles = computed(() => page.props.auth.user?.roles || []);
const isTecnico = computed(() => userRoles.value.includes('Tecnico'));
const isFacturacion = computed(() => userRoles.value.includes('Facturacion'));

const handleSearch = () => {
    const term = searchTerm.value.trim();
    if (!term) return;

    // Use backend logic for lookup and redirect
    router.post(route('scan.process'), { code: term }, {
        onFinish: () => {
            searchTerm.value = '';
            nextTick(() => {
                if (inputRef.value) inputRef.value.focus();
            });
        }
    });
};

onMounted(() => {
    // Auto-focus input
    nextTick(() => {
        if (inputRef.value) inputRef.value.focus();
    });
});
</script>

<template>
    <AppLayout title="Escáner">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Módulo de Escaneo
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 flex flex-col items-center justify-center min-h-[450px] transition-colors border border-transparent dark:border-gray-700">
                    
                    <!-- Contexto del Rol -->
                    <div class="mb-6 flex gap-2">
                        <span v-if="isTecnico" class="px-3 py-1 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 rounded-full text-sm font-medium border border-amber-200 dark:border-amber-800">
                            🛠️ Modo Remanufacturación
                        </span>
                        <span v-if="isFacturacion" class="px-3 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full text-sm font-medium border border-emerald-200 dark:border-emerald-800">
                            💰 Modo Ventas
                        </span>
                        <span v-if="!isTecnico && !isFacturacion" class="px-3 py-1 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-full text-sm font-medium border border-gray-200 dark:border-gray-600">
                            🔍 Modo Consulta General
                        </span>
                    </div>

                    <div class="mb-8 text-center">
                        <div class="relative inline-block">
                            <svg class="w-24 h-24 text-indigo-500 dark:text-indigo-400 mx-auto mb-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 17h.01M4.5 20h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 20z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300">Listo para Escanear</h3>
                        <p class="text-gray-500 dark:text-gray-500 max-w-sm mx-auto">
                            <span v-if="isTecnico">Escanea un motor para iniciar o continuar su mantenimiento.</span>
                            <span v-else-if="isFacturacion">Escanea una factura para ver detalles o un motor para consultar stock.</span>
                            <span v-else>Utiliza tu lector de código QR o de barras para buscar cualquier item o factura.</span>
                        </p>
                    </div>

                    <div class="w-full max-w-lg">
                        <form @submit.prevent="handleSearch" class="relative">
                            <input 
                                ref="inputRef"
                                v-model="searchTerm"
                                type="text" 
                                class="w-full px-6 py-5 text-3xl border-2 border-indigo-500 dark:border-indigo-600 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:focus:ring-indigo-900/50 shadow-2xl text-center font-mono bg-white dark:bg-gray-700 text-gray-800 dark:text-white transition-all placeholder-gray-300 dark:placeholder-gray-500"
                                placeholder="Esperando código..."
                                autocomplete="off"
                                autofocus
                            >
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 flex items-center pointer-events-none">
                                <div class="w-2 h-2 rounded-full bg-green-500 animate-ping"></div>
                            </div>
                        </form>
                        <div class="mt-6 flex items-center justify-center gap-4 text-sm text-gray-400 dark:text-gray-500">
                            <div class="flex items-center gap-1">
                                <kbd class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600 font-sans">Enter</svg>
                                <span>para procesar</span>
                            </div>
                            <span>•</span>
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11H9v-2h2v2zm0-4H9V7h2v2z"/></svg>
                                <span>Foco automático activo</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
