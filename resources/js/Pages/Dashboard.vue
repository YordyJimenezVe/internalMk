<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import { Link, useForm } from '@inertiajs/vue3';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement
} from 'chart.js';
import { Bar, Pie } from 'vue-chartjs';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, ArcElement);

// Define Props from Controller
const props = defineProps({
    stats: Object,
    recentActivity: Array,
    charts: Object, // { revenue: { labels, data }, inventory: { labels, data } }
    utilityPercentage: Number,
});

// Chart Options
const barOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        title: { display: true, text: 'Ingresos últimos 6 meses' }
    }
};

const pieOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'right' }
    }
};

// Data Formatting
const barData = props.charts ? {
    labels: props.charts.revenue.labels,
    datasets: [{
        label: 'Ingresos ($)',
        backgroundColor: '#6366f1',
        data: props.charts.revenue.data
    }]
} : null;

const pieData = props.charts ? {
    labels: props.charts.inventory.labels,
    datasets: [{
        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6b7280'],
        data: props.charts.inventory.data
    }]
} : null;

import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);
const isFacturacion = computed(() => {
    const rol = (user.value?.rol || '').toLowerCase();
    return rol.includes('fact') || (user.value?.roles || []).some(r => (r?.name || '').toLowerCase().includes('fact'));
});
const isMechanic = computed(() => {
    const rol = (user.value?.rol || '').toLowerCase();
    return rol.includes('mecan') || rol.includes('tecn') || rol.includes('tall') || (user.value?.roles || []).some(r => {
        const name = (r?.name || '').toLowerCase();
        return name.includes('mecan') || name.includes('tecn') || name.includes('tall');
    });
});
const isInventory = computed(() => {
    const rol = (user.value?.rol || '').toLowerCase();
    return rol.includes('inv') || (user.value?.roles || []).some(r => (r?.name || '').toLowerCase().includes('inv'));
});
const isAdminOrSuper = computed(() => {
    const rol = (user.value?.rol || '').toLowerCase();
    return rol.includes('admin') || rol.includes('super') || (user.value?.roles || []).some(r => ['superusuario', 'administrador'].includes((r?.name || '').toLowerCase()));
});

// Using inline SVGs to avoid dependency issues with @heroicons/vue
const icons = {
    users: 'fa-solid fa-users',
    money: 'fa-solid fa-hand-holding-dollar',
    cart: 'fa-solid fa-boxes-stacked',
    box: 'fa-solid fa-screwdriver-wrench',
    invoice: 'fa-solid fa-file-invoice-dollar',
    balance: 'fa-solid fa-scale-balanced'
};

const utilityForm = useForm({
    utility_percentage: props.utilityPercentage || 30,
});

const submitUtility = () => {
    utilityForm.post(route('settings.update_utility'), {
        preserveScroll: true
    });
};

</script>

<style scoped>
@keyframes camaro-drive {
    0% { transform: translateX(0); opacity: 0; }
    5% { opacity: 1; }
    90% { opacity: 1; }
    100% { transform: translateX(calc(100% + 100px)); opacity: 0; }
}

@keyframes gasoline-smoke {
    0% { opacity: 0; transform: translate(0, 0) scale(0.8); filter: blur(2px); }
    10% { opacity: 0.9; transform: translate(-20px, -5px) scale(1.1); filter: blur(0px); }
    50% { opacity: 0.5; transform: translate(-80px, -15px) scale(1.5); filter: blur(4px); }
    100% { opacity: 0; transform: translate(-150px, -30px) scale(2); filter: blur(15px); }
}

@keyframes car-vibration {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-0.5px); }
}

@keyframes rotate-wheel {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes flicker-light {
    0%, 100% { opacity: 0.8; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.1); }
    70% { opacity: 0.9; transform: scale(0.95); }
}

.animate-camaro-drive {
    animation: camaro-drive 14s linear infinite;
}

.animate-wheel-spin {
    animation: rotate-wheel 0.4s linear infinite;
}

.animate-flicker {
    animation: flicker-light 0.2s ease-in-out infinite;
}

/* Light overlays */
.car-light {
    position: absolute;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    filter: blur(2px);
    z-index: 20;
}

.headlight {
    left: 94%;
    top: 51%;
    background: #e0f2fe;
    box-shadow: 0 0 15px 5px rgba(186, 230, 253, 0.9), 10px 0 30px 10px rgba(125, 211, 252, 0.4);
}

.taillight {
    left: 0%;
    top: 36%;
    background: #ef4444;
    box-shadow: 0 0 12px 4px rgba(239, 68, 68, 0.8), -5px 0 20px 5px rgba(220, 38, 38, 0.3);
}

/* Wheel overlays - Precise alignment for the generated Camaro image */
.car-wheel {
    position: absolute;
    bottom: 0%; /* Aligned with the bottom of the image container */
    width: 25%;
    height: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.wheel-front { left: 68.5%; bottom: 0%; }
.wheel-rear { left: 8.5%; bottom: 0%; }

/* Smoke trail follows the car */
.smoke-particle {
    position: absolute;
    right: 90%; /* Position behind the rear of the car */
    top: 65%;
    white-space: nowrap;
    pointer-events: none;
}

.animate-gasoline-smoke {
    display: inline-block;
    animation: gasoline-smoke 3s ease-out infinite;
}

.animate-vibration {
    animation: car-vibration 0.1s linear infinite;
}
</style>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    Resumen General
                </h2>
                <span class="text-sm text-gray-500 bg-white dark:bg-gray-800 px-3 py-1 rounded-full shadow-sm border border-gray-100 dark:border-gray-700">
                    Última actualización: Hoy
                </span>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Welcome Section -->
                <div class="mb-8 p-10 bg-[#0f172a] rounded-[2rem] shadow-2xl text-white relative overflow-hidden group border border-white/5">
                    <!-- Mesh Gradient - Decorative spotlights -->
                    <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[140%] bg-indigo-600/20 rounded-full blur-[120px] animate-pulse pointer-events-none"></div>
                    <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[140%] bg-purple-600/20 rounded-full blur-[120px] pointer-events-none"></div>
                    <div class="absolute top-[20%] right-[10%] w-[30%] h-[60%] bg-blue-500/10 rounded-full blur-[100px] pointer-events-none"></div>
                    
                    <!-- Noise/Glass texture overlay -->
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(255,255,255,0.03),transparent)] pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-8">
                            <div class="relative">
                                <h1 class="text-4xl font-black tracking-tight sm:text-5xl mb-1 drop-shadow-2xl whitespace-nowrap">
                                    ¡Hola, {{ $page.props.auth.user.name }}!
                                </h1>
                            </div>
                            
                            <!-- Camaro Animation Stage -->
                            <div class="relative flex-grow h-24 flex items-center overflow-visible">
                                <div class="animate-camaro-drive w-full relative">
                                    <div class="relative inline-block animate-vibration">
                                        <!-- Real Yellow Camaro Image (No BG) with Animated Wheels -->
                                        <div class="relative inline-block">
                                            <img src="/storage/images/camaro_no_bg.png" 
                                                 alt="Yellow Camaro" 
                                                 class="h-16 w-auto drop-shadow-[0_0_15px_rgba(251,191,36,0.6)]" />
                                            
                                            <!-- Animated Wheels Overlays -->
                                            <div class="car-wheel wheel-front">
                                                <svg class="w-full h-full text-[#111111]/90 animate-wheel-spin" viewBox="0 0 100 100">
                                                    <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" stroke-dasharray="10 5" />
                                                    <circle cx="50" cy="50" r="10" fill="currentColor" />
                                                    <path d="M50 5 L50 95 M5 50 L95 50 M18 18 L82 82 M18 82 L82 18" stroke="currentColor" stroke-width="4" />
                                                </svg>
                                            </div>
                                            <div class="car-wheel wheel-rear">
                                                <svg class="w-full h-full text-[#111111]/90 animate-wheel-spin" viewBox="0 0 100 100">
                                                    <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" stroke-dasharray="10 5" />
                                                    <circle cx="50" cy="50" r="10" fill="currentColor" />
                                                    <path d="M50 5 L50 95 M5 50 L95 50 M18 18 L82 82 M18 82 L82 18" stroke="currentColor" stroke-width="4" />
                                                </svg>
                                            </div>

                                            <!-- Dynamic Lights -->
                                            <div class="car-light headlight animate-flicker"></div>
                                            <div class="car-light taillight animate-flicker" style="animation-delay: 0.1s"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-400 text-lg font-medium max-w-2xl leading-relaxed mt-2 opacity-80 uppercase tracking-[0.2em] text-xs">
                            Monitoreo de Sistema v2.0
                        </p>
                    </div>
                </div>

                <!-- Global Utility Setting Widget for Contador and Admin roles -->
                <div v-if="isFacturacion || isAdminOrSuper" class="mb-8 p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center text-2xl shadow-inner">
                            <i class="fa-solid fa-percent"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Porcentaje de Utilidad Global</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Usado para calcular dinámicamente el precio/costo del motor.
                            </p>
                        </div>
                    </div>

                    <!-- If Facturacion (Contador/Raiza), they can edit -->
                    <div v-if="isFacturacion" class="flex items-center gap-2">
                        <form @submit.prevent="submitUtility" class="flex items-center gap-3">
                            <div class="relative rounded-xl shadow-sm">
                                <input 
                                    v-model="utilityForm.utility_percentage" 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    max="100" 
                                    class="block w-28 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl py-2 px-3 text-right font-bold text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 text-lg"
                                >
                                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400 font-bold">%</span>
                                </div>
                            </div>
                            <button 
                                type="submit" 
                                :disabled="utilityForm.processing"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition-all active:scale-95 text-sm whitespace-nowrap"
                            >
                                <i class="fa-solid fa-floppy-disk mr-1"></i> Actualizar
                            </button>
                        </form>
                    </div>

                    <!-- If Admin / Super, they only see read-only -->
                    <div v-else class="flex items-center gap-2 bg-indigo-50/50 dark:bg-indigo-950/20 px-4 py-2 rounded-xl border border-indigo-100/50 dark:border-indigo-900/30">
                        <span class="text-sm text-indigo-700 dark:text-indigo-300 font-semibold">Valor Actual:</span>
                        <span class="text-lg font-black text-indigo-600 dark:text-indigo-400 font-mono">{{ utilityPercentage }}%</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500 ml-2">(Solo Lectura)</span>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" v-if="stats">
                    <!-- Facturación / Invoicing View -->
                    <template v-if="isFacturacion && !isAdminOrSuper">
                        <StatCard 
                            title="Ingresos (Mes)" 
                            :value="'$' + stats.monthlyRevenue" 
                            :icon="icons.money" 
                            color="bg-green-500" 
                            trend="Total Facturado"
                            :trendUp="true"
                            :link="route('billing')"
                        />
                        <StatCard 
                            title="Facturas Emitidas" 
                            :value="stats.monthlyBillingsCount" 
                            :icon="icons.invoice" 
                            color="bg-blue-500" 
                            trend="Mes Actual"
                            :trendUp="true"
                            :link="route('billing')"
                        />
                        <StatCard 
                            title="Conciliaciones Pendientes" 
                            :value="stats.pendingConciliationsCount" 
                            :icon="icons.balance" 
                            color="bg-indigo-500" 
                            trend="Por Conciliar"
                            :trendUp="false"
                            :link="route('maintenance.conciliacion')"
                        />
                        <StatCard 
                            title="Ingresos (Hoy)" 
                            :value="'$' + stats.todayRevenue" 
                            :icon="icons.money" 
                            color="bg-teal-500" 
                            :trend="stats.todayBillingsCount + ' transacciones hoy'"
                            :trendUp="true"
                            :link="route('billing')"
                        />
                    </template>

                    <!-- Mechanics / Workshop View -->
                    <template v-else-if="isMechanic && !isAdminOrSuper">
                        <StatCard 
                            title="Mantenimientos Activos" 
                            :value="stats.activeMaintenances" 
                            :icon="icons.box" 
                            color="bg-red-500" 
                            trend="Actualmente en taller"
                            :trendUp="false"
                            :link="route('maintenance')"
                        />
                        <StatCard 
                            title="Mantenimientos Completados" 
                            :value="stats.completedMaintenancesCount" 
                            :icon="icons.box" 
                            color="bg-green-500" 
                            trend="Listos para despacho"
                            :trendUp="true"
                            :link="route('maintenance')"
                        />
                        <StatCard 
                            title="Partidas Hoy" 
                            :value="stats.newPartidas" 
                            :icon="icons.cart" 
                            color="bg-purple-500" 
                            trend="Nuevos ingresos"
                            :trendUp="true"
                            :link="route('inventario')"
                        />
                        <StatCard 
                            title="Total Artículos" 
                            :value="stats.totalInventoryCount" 
                            :icon="icons.cart" 
                            color="bg-blue-500" 
                            trend="Registrados en sistema"
                            :trendUp="true"
                            :link="route('inventario')"
                        />
                    </template>

                    <!-- Inventory Gestor View -->
                    <template v-else-if="isInventory && !isAdminOrSuper">
                        <StatCard 
                            title="Artículos Disponibles" 
                            :value="stats.availableInventoryCount" 
                            :icon="icons.cart" 
                            color="bg-green-500" 
                            trend="Stock disponible"
                            :trendUp="true"
                            :link="route('inventario')"
                        />
                        <StatCard 
                            title="Artículos en Taller" 
                            :value="stats.inMaintenanceInventoryCount" 
                            :icon="icons.box" 
                            color="bg-red-500" 
                            trend="En mantenimiento / taller"
                            :trendUp="false"
                            :link="route('maintenance')"
                        />
                        <StatCard 
                            title="Total Artículos" 
                            :value="stats.totalInventoryCount" 
                            :icon="icons.cart" 
                            color="bg-blue-500" 
                            trend="Historial de stock"
                            :trendUp="true"
                            :link="route('inventario')"
                        />
                        <StatCard 
                            title="Ingresos Hoy" 
                            :value="stats.newPartidas" 
                            :icon="icons.cart" 
                            color="bg-purple-500" 
                            trend="Registrados hoy"
                            :trendUp="true"
                            :link="route('inventario')"
                        />
                    </template>

                    <!-- Standard/Admin View -->
                    <template v-else>
                        <StatCard 
                            v-if="isAdminOrSuper"
                            title="Total Usuarios" 
                            :value="stats.totalUsers" 
                            :icon="icons.users" 
                            color="bg-blue-500" 
                            trend="Registrados"
                            :trendUp="true"
                            :link="route('users.index')"
                        />
                        <StatCard 
                            title="Ingresos (Mes)" 
                            :value="'$' + stats.monthlyRevenue" 
                            :icon="icons.money" 
                            color="bg-green-500"
                            trend="Facturado"
                            :trendUp="true"
                            :link="route('billing')"
                        />
                        <StatCard 
                            title="Partidas Hoy" 
                            :value="stats.newPartidas" 
                            :icon="icons.cart" 
                            color="bg-purple-500" 
                            trend="Movimientos"
                            :trendUp="true"
                            :link="route('inventario')"
                        />
                        <StatCard 
                            title="Mantenimientos" 
                            :value="stats.activeMaintenances" 
                            :icon="icons.box" 
                            color="bg-red-500" 
                            trend="Activos"
                            :trendUp="false"
                            :link="route('maintenance')"
                        />
                    </template>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column (Charts if Admin) -->
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 p-6">
                        <div v-if="charts">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Análisis de Rendimiento</h3>
                            <div class="grid grid-cols-1 gap-8">
                                <!-- Revenue Chart -->
                                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg h-72">
                                     <Bar :data="barData" :options="barOptions" />
                                </div>
                                
                                <!-- Inventory / Currency Chart -->
                                <div>
                                     <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ charts.inventory.title || 'Distribución de Inventario' }}</h4>
                                    <div class="h-64 flex justify-center">
                                         <Pie :data="pieData" :options="pieOptions" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex flex-col items-center justify-center h-80 text-gray-400">
                             <svg class="h-16 w-16 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span class="text-sm font-medium">Contenido exclusivo para Administradores y Superusuarios</span>
                        </div>
                    </div>

                    <!-- Right Column (Activity) -->
                    <div v-if="$page.props.auth.user.roles.some(r => ['Superusuario', 'Administrador', 'Facturacion'].includes(r.name))"
                         class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700">
                         <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                             <h3 class="text-lg font-bold text-gray-900 dark:text-white">Facturación Reciente</h3>
                        </div>
                        <div class="p-6">
                            <ul class="space-y-6" v-if="recentActivity && recentActivity.length > 0">
                                <li v-for="activity in recentActivity" :key="activity.id" class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 h-2 w-2 mt-2 rounded-full bg-green-500"></div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ activity.description }}</span>
                                        <span class="text-xs text-gray-500">{{ activity.time }}</span>
                                    </div>
                                </li>
                            </ul>
                            <div v-else class="text-center text-gray-500 py-4">
                                No hay actividad reciente.
                            </div>
                            
                            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <Link :href="route('billing')" class="block w-full text-center text-sm font-semibold text-indigo-600 hover:text-indigo-800">Ver todas las facturas &rarr;</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
