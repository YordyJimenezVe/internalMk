<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Doughnut, Bar } from 'vue-chartjs';
import { 
    Chart as ChartJS, 
    Title, 
    Tooltip, 
    Legend, 
    ArcElement, 
    CategoryScale, 
    LinearScale, 
    BarElement 
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale, LinearScale, BarElement);

const props = defineProps({
    container: Object,
    stats: Object
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-VE', {
        style: 'currency',
        currency: 'USD'
    }).format(value);
};

const formatNumber = (value) => {
    return new Intl.NumberFormat('es-VE').format(value);
};

// Chart Data: Status (Doughnut)
const statusChartData = computed(() => ({
    labels: ['Disponible', 'Mantenimiento', 'Vendido', 'No Cargado'],
    datasets: [{
        data: [
            props.stats.available.count,
            props.stats.maintenance.count,
            props.stats.sold.count,
            props.stats.not_loaded.count
        ],
        backgroundColor: [
            '#10b981', // green-500
            '#f59e0b', // amber-500
            '#3b82f6', // blue-500
            '#ef4444'  // red-500
        ],
        borderWidth: 0,
        hoverOffset: 15
    }]
}));

const statusChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                color: '#94a3b8',
                font: { size: 12, weight: 'bold' },
                padding: 20,
                usePointStyle: true
            }
        },
        tooltip: {
            backgroundColor: '#1e293b',
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 14 },
            padding: 12,
            cornerRadius: 12,
            displayColors: true
        }
    },
    cutout: '70%'
};

// Chart Data: Categories (Bar)
const categoryChartData = computed(() => ({
    labels: ['Motores', 'Cajas', 'Cámaras', 'Autopartes'],
    datasets: [{
        label: 'Piezas Registradas',
        data: [
            props.stats.categories.motores,
            props.stats.categories.cajas,
            props.stats.categories.camaras,
            props.stats.categories.autopartes
        ],
        backgroundColor: [
            'rgba(99, 102, 241, 0.8)', // indigo-500
            'rgba(168, 85, 247, 0.8)', // purple-500
            'rgba(236, 72, 153, 0.8)', // pink-500
            'rgba(245, 158, 11, 0.8)'  // amber-500
        ],
        borderRadius: 8,
        barThickness: 30
    }]
}));

const categoryChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#1e293b',
            padding: 12,
            cornerRadius: 12
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: 'rgba(148, 163, 184, 0.1)' },
            ticks: { color: '#94a3b8', font: { weight: 'bold' } }
        },
        x: {
            grid: { display: false },
            ticks: { color: '#94a3b8', font: { weight: 'bold' } }
        }
    }
};

const exportReport = (format) => {
    const routeName = format === 'pdf' ? 'reportePdf' : 'reporteExcel';
    window.open(route(routeName, { 
        tipo: 'partidas', 
        caso: 'all', 
        termino: props.container.expediente 
    }), '_blank');
};
</script>

<template>
    <AppLayout title="Detalle del Contenedor">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                    Expediente: <span class="text-indigo-600 dark:text-indigo-400">{{ container.expediente }}</span>
                </h2>
                <Link :href="route('container')" class="flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al Listado
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- Main Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Available -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-2xl font-bold text-gray-800 dark:text-white">{{ stats.available.percentage }}%</span>
                        </div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">En Inventario</h3>
                        <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ stats.available.count }} Piezas</p>
                    </div>

                    <!-- Maintenance -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <span class="text-2xl font-bold text-gray-800 dark:text-white">{{ stats.maintenance.percentage }}%</span>
                        </div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">En Mantenimiento</h3>
                        <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ stats.maintenance.count }} Piezas</p>
                    </div>

                    <!-- Sold -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-2xl font-bold text-gray-800 dark:text-white">{{ stats.sold.percentage }}%</span>
                        </div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">Vendido</h3>
                        <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ stats.sold.count }} Piezas</p>
                    </div>

                    <!-- Not Loaded -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <span class="text-2xl font-bold text-gray-800 dark:text-white">{{ stats.not_loaded.percentage }}%</span>
                        </div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">No Cargado</h3>
                        <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ stats.not_loaded.count }} Piezas</p>
                    </div>
                </div>

                <!-- Financial & Category Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Categories Breakdown -->
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Resumen de Categorías</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Distribución de piezas registradas en el sistema</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl text-center">
                                    <span class="block text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ stats.categories.motores }}</span>
                                    <span class="text-xs uppercase tracking-wider font-bold text-gray-500 dark:text-gray-400">Motores</span>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl text-center">
                                    <span class="block text-2xl font-black text-purple-600 dark:text-purple-400">{{ stats.categories.cajas }}</span>
                                    <span class="text-xs uppercase tracking-wider font-bold text-gray-500 dark:text-gray-400">Cajas</span>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl text-center">
                                    <span class="block text-2xl font-black text-pink-600 dark:text-pink-400">{{ stats.categories.camaras }}</span>
                                    <span class="text-xs uppercase tracking-wider font-bold text-gray-500 dark:text-gray-400">Cámaras</span>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl text-center">
                                    <span class="block text-2xl font-black text-amber-600 dark:text-amber-400">{{ stats.categories.autopartes }}</span>
                                    <span class="text-xs uppercase tracking-wider font-bold text-gray-500 dark:text-gray-400">Autopartes</span>
                                </div>
                            </div>

                            <!-- Progress Breakdown -->
                            <div class="mt-8 space-y-4">
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600 dark:text-gray-400">Progreso de Carga ({{ stats.registered }} de {{ stats.total_expected }})</span>
                                        <span class="font-bold text-gray-800 dark:text-white">{{ Math.round((stats.registered / stats.total_expected) * 100) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5">
                                        <div class="bg-indigo-600 h-2.5 rounded-full" :style="{ width: (stats.registered / stats.total_expected * 100) + '%' }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Summary -->
                    <div class="bg-slate-900 dark:bg-gray-900 rounded-3xl shadow-2xl p-8 text-white flex flex-col justify-between relative overflow-hidden border border-slate-800 dark:border-indigo-500/20">
                        <!-- Decorative glow -->
                        <div class="absolute -right-10 -top-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl"></div>
                        <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl"></div>
                        
                        <div class="relative z-10">
                            <h3 class="text-lg font-bold mb-8 flex items-center text-indigo-400">
                                <div class="p-2 bg-indigo-500/10 rounded-lg mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                Análisis Financiero
                            </h3>
                            
                            <div class="space-y-8">
                                <div>
                                    <p class="text-slate-400 text-xs font-bold uppercase tracking-[0.2em] mb-2">Total Ventas</p>
                                    <p class="text-4xl font-black tracking-tight text-white">
                                        <span class="text-indigo-500 text-2xl mr-1 font-bold">USD</span>{{ formatNumber(stats.financials.total_revenue) }}
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-slate-400 text-xs font-bold uppercase tracking-[0.2em] mb-2">Ganancia Neta</p>
                                    <p class="text-4xl font-black tracking-tight text-green-400">
                                        <span class="text-green-600 text-2xl mr-1 font-bold">USD</span>{{ formatNumber(stats.financials.total_profit) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 pt-6 border-t border-slate-800 relative z-10">
                            <div class="flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-slate-500 text-[10px] uppercase font-bold tracking-widest">Contenedor ID</span>
                                    <span class="text-indigo-300 font-mono text-xs">{{ container.cod }}</span>
                                </div>
                                <span class="px-3 py-1 bg-indigo-500/10 border border-indigo-500/20 rounded-full text-[10px] font-black text-indigo-400 uppercase tracking-tighter">{{ container.fecha }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- New Charts Section -->
                    <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Doughnut Chart -->
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-6 text-center">Estado del Inventario</h4>
                            <div class="h-64">
                                <Doughnut :data="statusChartData" :options="statusChartOptions" />
                            </div>
                        </div>

                        <!-- Bar Chart -->
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-6 text-center">Distribución por Categoría</h4>
                            <div class="h-64">
                                <Bar :data="categoryChartData" :options="categoryChartOptions" />
                            </div>
                        </div>
                    </div>

                    <!-- Reports & Actions -->
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-center">
                        <h4 class="text-sm font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-6">Reportes del Contenedor</h4>
                        
                        <div class="space-y-4">
                            <button @click="exportReport('pdf')" class="w-full flex items-center justify-between p-4 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 rounded-2xl border border-rose-100 dark:border-rose-900/30 hover:bg-rose-600 hover:text-white transition-all group">
                                <div class="flex items-center">
                                    <div class="p-2 bg-white dark:bg-gray-800 rounded-xl mr-3 shadow-sm group-hover:bg-rose-500 group-hover:text-white transition-colors">
                                        <i class="fa-solid fa-file-pdf text-xl"></i>
                                    </div>
                                    <span class="font-bold uppercase text-xs tracking-wider">Exportar PDF</span>
                                </div>
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </button>

                            <button @click="exportReport('excel')" class="w-full flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-2xl border border-emerald-100 dark:border-emerald-900/30 hover:bg-emerald-600 hover:text-white transition-all group">
                                <div class="flex items-center">
                                    <div class="p-2 bg-white dark:bg-gray-800 rounded-xl mr-3 shadow-sm group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                                        <i class="fa-solid fa-file-excel text-xl"></i>
                                    </div>
                                    <span class="font-bold uppercase text-xs tracking-wider">Exportar Excel</span>
                                </div>
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </button>
                        </div>

                        <p class="mt-8 text-center text-[10px] font-medium text-gray-400 uppercase tracking-[0.25em]">Genere reportes detallados de este expediente</p>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.tooltip:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    padding: 4px 8px;
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    font-size: 12px;
    border-radius: 4px;
    white-space: nowrap;
    z-index: 10;
}
</style>
