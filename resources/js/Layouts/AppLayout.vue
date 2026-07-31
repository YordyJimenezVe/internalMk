<script setup>
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    title: String,
});

const page = usePage();
const userRoles = computed(() => {
    const roles = page.props.auth.user?.roles || [];
    return roles.map(r => typeof r === 'string' ? r : r.name || '');
});
const userPermissions = computed(() => {
    const permissions = page.props.auth.user?.permissions || [];
    return permissions.map(p => typeof p === 'string' ? p : p.name || '');
});

const hasSuperusuario = computed(() => {
    const directRol = page.props.auth.user?.rol || '';
    return directRol === 'Superusuario' || userRoles.value.includes('Superusuario');
});
const hasFacturacion = computed(() => {
    const directRol = page.props.auth.user?.rol || '';
    return directRol === 'Facturacion' || directRol === 'FACTURACION' || 
           userRoles.value.includes('Facturacion') || userRoles.value.includes('FACTURACION');
});
const hasManageRoles = computed(() => userPermissions.value.includes('manage roles'));
const hasViewPartida = computed(() => userPermissions.value.includes('view partida'));
const hasViewMaintenance = computed(() => userPermissions.value.includes('view maintenance'));
const hasManageBilling = computed(() => userPermissions.value.includes('manage billing'));
const hasViewBilling = computed(() => userPermissions.value.includes('view billing'));
const hasManageUsers = computed(() => userPermissions.value.includes('manage users'));
const hasViewBitacora = computed(() => userPermissions.value.includes('view bitacora'));
const hasViewReports = computed(() => userPermissions.value.includes('view reports'));
const hasAccessScan = computed(() => userPermissions.value.includes('access scan'));
const isAdministrador = computed(() => page.props.auth.user?.rol === 'Administrador');

const displayTitle = computed(() => {
    const prefix = unreadCount.value > 0 ? `(${unreadCount.value}) ` : '';
    return `${prefix}${props.title || 'InternalMk'}`;
});

const showingNavigationDropdown = ref(false);
const isSidebarCollapsed = ref(false);
const isDarkMode = ref(false);

const unreadCount = ref(0);
const notifications = ref([]);

const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('notifications.unread'));
        unreadCount.value = response.data.unread_count;
        notifications.value = response.data.notifications;
    } catch (error) {
        console.error('Error fetching notifications:', error);
    }
};

const markAllNotificationsRead = () => {
    router.post(route('notifications.read_all'), {}, {
        preserveScroll: true,
        onSuccess: () => {
            unreadCount.value = 0;
            notifications.value = [];
        }
    });
};

const handleNotifClick = (notif) => {
    router.post(route('notifications.read', notif.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            fetchNotifications();
            if (notif.action_url) {
                router.visit(notif.action_url);
            }
        }
    });
};

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
    localStorage.setItem('sidebarCollapsed', isSidebarCollapsed.value);
};

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('darkMode', 'true');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('darkMode', 'false');
    }
};

onMounted(() => {
    // Restore sidebar preference
    const collapsed = localStorage.getItem('sidebarCollapsed');
    if (collapsed === 'true') {
        isSidebarCollapsed.value = true;
    }

    // Restore dark mode preference
    const dark = localStorage.getItem('darkMode');
    if (dark === 'true' || (!dark && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDarkMode.value = true;
        document.documentElement.classList.add('dark');
    }

    // --- GLOBAL SCANNER LISTENER ---
    let scannerBuffer = '';
    let scannerTimeout = null;

    window.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            return;
        }

        if (e.key === 'Enter') {
            if (scannerBuffer.length > 3) {
                e.preventDefault(); // Stop any default form submission
                let scannedData = scannerBuffer;
                scannerBuffer = '';

                if (scannedData.includes("'")) {
                    scannedData = scannedData.replace(/'/g, '-');
                }
                
                router.post(route('scan.process'), { code: scannedData });
            }
            return;
        }

        if (e.key.length === 1) {
            scannerBuffer += e.key;
            if (scannerTimeout) clearTimeout(scannerTimeout);
            scannerTimeout = setTimeout(() => {
                scannerBuffer = '';
            }, 200);
        }
    });

    fetchNotifications();
    setInterval(fetchNotifications, 45000);
});

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'), {}, {
        onFinish: () => {
            window.location.href = '/login';
        }
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 font-sans text-gray-900 antialiased">
        <Head :title="displayTitle" />

        <Banner />

        <!-- Mobile Sidebar Backdrop -->
        <div v-if="showingNavigationDropdown" class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 md:hidden" @click="showingNavigationDropdown = false"></div>

        <!-- Sidebar -->
        <aside 
            :class="[
                'fixed top-0 left-0 z-50 h-screen transition-all duration-300 transform bg-white dark:bg-gray-800 shadow-xl border-r border-gray-200 dark:border-gray-700',
                showingNavigationDropdown ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
                isSidebarCollapsed ? 'md:w-20' : 'md:w-64',
                'w-64'
            ]"
        >
            <!-- Logo area -->
            <div class="flex items-center h-16 px-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800" :class="isSidebarCollapsed ? 'justify-center' : 'justify-between'">
                <Link :href="route('dashboard')" class="flex items-center space-x-2" v-if="!isSidebarCollapsed">
                    <ApplicationMark class="block h-9 w-auto text-indigo-600" />
                    <span class="text-lg font-bold text-gray-800 dark:text-white tracking-wide truncate">InternalMk</span>
                </Link>
                <Link :href="route('dashboard')" v-else>
                     <ApplicationMark class="block h-9 w-auto text-indigo-600" />
                </Link>

                <!-- Mobile close button -->
                <button @click="showingNavigationDropdown = false" class="md:hidden text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Toggle Button (Desktop only) -->
             <div class="hidden md:flex justify-end p-2 border-b border-gray-100 dark:border-gray-700">
                <button @click="toggleSidebar" class="p-1 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition">
                    <svg v-if="!isSidebarCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                </button>
            </div>


            <!-- Navigation Links -->
            <div class="px-3 py-6 overflow-y-auto h-[calc(100vh-8rem)]">
                <nav class="space-y-1 flex flex-col">
                    
                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('dashboard') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Panel' : ''">
                        <font-awesome-icon icon="fa-solid fa-house" class="w-5 h-5 shrink-0" :class="[route().current('dashboard') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Panel de Control</span>
                    </NavLink>

                    <div class="pt-4 pb-2" v-if="!isSidebarCollapsed && (hasManageRoles || hasViewPartida || hasViewMaintenance || hasSuperusuario)">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Gestión</p>
                    </div>
                     <div class="pt-4 pb-2 flex justify-center" v-else-if="isSidebarCollapsed && (hasManageRoles || hasViewPartida || hasViewMaintenance || hasSuperusuario)">
                        <div class="w-4 h-1 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>

                    <NavLink v-if="hasManageRoles || hasSuperusuario" :href="route('container')" :active="route().current('container')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('container') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Contenedor' : ''">
                        <font-awesome-icon icon="fa-solid fa-cube" class="w-5 h-5 shrink-0" :class="[route().current('container') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Contenedor</span>
                    </NavLink>

                    <NavLink v-if="hasViewPartida || hasSuperusuario" :href="route('inventario')" :active="route().current('inventario')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('inventario') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Inventario' : ''">
                        <font-awesome-icon icon="fa-solid fa-box" class="w-5 h-5 shrink-0" :class="[route().current('inventario') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Inventario</span>
                    </NavLink>

                    <NavLink v-if="hasManageBilling || hasSuperusuario" :href="route('inventario.precio_pendiente')" :active="route().current('inventario.precio_pendiente')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('inventario.precio_pendiente') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Precio Pendiente' : ''">
                        <font-awesome-icon icon="fa-solid fa-hourglass-half" class="w-5 h-5 shrink-0" :class="[route().current('inventario.precio_pendiente') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Precio Pendiente</span>
                    </NavLink>


                     <NavLink v-if="hasViewMaintenance || hasSuperusuario" :href="route('maintenance')" :active="route().current('maintenance')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('maintenance') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Mantenimiento' : ''">
                        <font-awesome-icon icon="fa-solid fa-wrench" class="w-5 h-5 shrink-0" :class="[route().current('maintenance') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Mantenimiento</span>
                    </NavLink>

                    <a v-if="hasViewPartida || hasAccessScan || hasManageBilling || hasSuperusuario" href="/generar-qr-etiquetas" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white" :class="[isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Etiquetas QR' : ''">
                        <font-awesome-icon icon="fa-solid fa-qrcode" class="w-5 h-5 shrink-0 text-gray-400 group-hover:text-gray-500 dark:text-gray-400" :class="[isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Etiquetas QR</span>
                    </a>
                    
                    <div class="pt-4 pb-2" v-if="!isSidebarCollapsed && (hasManageUsers || hasManageRoles || hasViewBitacora || hasViewBilling || hasViewReports || hasAccessScan || hasSuperusuario)">
                         <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administración</p>
                    </div>
                     <div class="pt-4 pb-2 flex justify-center" v-else-if="isSidebarCollapsed && (hasManageUsers || hasManageRoles || hasViewBitacora || hasViewBilling || hasViewReports || hasAccessScan || hasSuperusuario)">
                        <div class="w-4 h-1 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>

                    <NavLink v-if="hasManageUsers || hasSuperusuario" :href="route('users.index')" :active="route().current('users.index')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('users.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Usuarios' : ''">
                        <font-awesome-icon icon="fa-solid fa-users" class="w-5 h-5 shrink-0" :class="[route().current('users.index') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Usuarios</span>
                    </NavLink>

                    <NavLink v-if="hasManageRoles || hasSuperusuario" :href="route('roles.index')" :active="route().current('roles.index')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('roles.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Roles' : ''">
                        <font-awesome-icon icon="fa-solid fa-shield-halved" class="w-5 h-5 shrink-0" :class="[route().current('roles.index') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Roles y Permisos</span>
                    </NavLink>

                    <NavLink v-if="hasViewBitacora || hasSuperusuario" :href="route('bitacora.index')" :active="route().current('bitacora.index')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('bitacora.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Bitácora' : ''">
                        <font-awesome-icon icon="fa-solid fa-book" class="w-5 h-5 shrink-0" :class="[route().current('bitacora.index') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Bitácora</span>
                    </NavLink>

                    <NavLink v-if="hasViewBilling || hasSuperusuario || hasFacturacion" :href="route('billing.requests.index')" :active="route().current('billing.requests.index')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('billing.requests.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Solicitudes' : ''">
                        <font-awesome-icon icon="fa-solid fa-file-circle-exclamation" class="w-5 h-5 shrink-0" :class="[route().current('billing.requests.index') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Solicitudes</span>
                    </NavLink>

                    <NavLink v-if="hasManageBilling || hasSuperusuario" :href="route('maintenance.conciliacion')" :active="route().current('maintenance.conciliacion')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('maintenance.conciliacion') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Conciliación Taller' : ''">
                        <font-awesome-icon icon="fa-solid fa-scale-balanced" class="w-5 h-5 shrink-0" :class="[route().current('maintenance.conciliacion') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Conciliación Taller</span>
                    </NavLink>

                    <NavLink v-if="hasViewBilling || hasSuperusuario || hasFacturacion" :href="route('billing')" :active="route().current('billing')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('billing') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Historial' : ''">
                        <font-awesome-icon icon="fa-solid fa-file-invoice-dollar" class="w-5 h-5 shrink-0" :class="[route().current('billing') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Historial de Ventas</span>
                    </NavLink>

                    <NavLink v-if="hasViewReports || hasSuperusuario" :href="route('reports')" :active="route().current('reports')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('reports') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Reportes' : ''">
                        <svg class="w-6 h-6 shrink-0" :class="[route().current('reports') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                        <span v-if="!isSidebarCollapsed">Reportes</span>
                    </NavLink>

                    <!-- Notificaciones Admin Menu -->
                    <NavLink v-if="isAdministrador || hasSuperusuario" :href="route('admin.notifications.index')" :active="route().current('admin.notifications.index')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('admin.notifications.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Alertas y Avisos' : ''">
                        <font-awesome-icon icon="fa-solid fa-bell" class="w-5 h-5 shrink-0" :class="[route().current('admin.notifications.index') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Alertas y Avisos</span>
                    </NavLink>

                    <NavLink v-if="hasAccessScan || hasSuperusuario" :href="route('scan.index')" :active="route().current('scan.index')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('scan.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Escaneo' : ''">
                        <svg class="w-6 h-6 shrink-0" :class="[route().current('scan.index') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 17h.01M4.5 20h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 20z" />
                        </svg>
                        <span v-if="!isSidebarCollapsed">Escaneo</span>
                    </NavLink>
                </nav>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex flex-col flex-1 min-h-screen transition-all duration-300" :class="isSidebarCollapsed ? 'md:pl-20' : 'md:pl-64'">
            
            <!-- Navbar Header -->
            <header class="flex items-center justify-between h-16 px-6 py-4 bg-white border-b border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="flex items-center">
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="text-gray-500 hover:text-gray-700 focus:outline-none md:hidden">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <!-- Header Title Slot -->
                    <h2 v-if="$slots.header" class="ml-4 font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                        <slot name="header" />
                    </h2>
                </div>
                
                <div class="flex items-center space-x-4">
                     <!-- Dark Mode Toggle -->
                     <button @click="toggleDarkMode" class="p-2 rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none transition-colors duration-200" :title="isDarkMode ? 'Cambiar a modo luz' : 'Cambiar a modo oscuro'">
                        <font-awesome-icon :icon="isDarkMode ? 'fa-solid fa-sun' : 'fa-solid fa-moon'" class="w-5 h-5" />
                     </button>

                     <!-- Teams Dropdown -->
                     <div class="relative ms-3" v-if="$page.props.jetstream.hasTeamFeatures">
                        <Dropdown align="right" width="60">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white dark:bg-gray-800 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                        {{ $page.props.auth.user.current_team.name }}
                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </template>
                             <template #content>
                                <div class="w-60">
                                    <div class="block px-4 py-2 text-xs text-gray-400">Gestionar Equipo</div>
                                    <DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">Configuración del Equipo</DropdownLink>
                                    <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">Crear Nuevo Equipo</DropdownLink>
                                    <div class="border-t border-gray-200 dark:border-gray-700" />
                                    <div class="block px-4 py-2 text-xs text-gray-400">Cambiar de Equipo</div>
                                    <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                        <form @submit.prevent="switchToTeam(team)">
                                            <DropdownLink as="button">
                                                <div class="flex items-center">
                                                    <svg v-if="team.id == $page.props.auth.user.current_team_id" class="me-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    <div>{{ team.name }}</div>
                                                </div>
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </div>
                            </template>
                        </Dropdown>
                     </div>

                      <!-- Notification Dropdown -->
                      <div class="relative ms-3 flex items-center">
                           <Dropdown align="right" width="80" :content-classes="['py-0', 'bg-transparent', 'shadow-none', 'ring-0']">
                               <template #trigger>
                                   <button type="button" class="relative p-2 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 rounded-full hover:bg-gray-100 dark:hover:bg-slate-800 transition-all duration-200 focus:outline-none" :title="'Notificaciones'">
                                       <font-awesome-icon icon="fa-solid fa-bell" class="w-5 h-5" :class="{'animate-bounce': unreadCount > 0}" />
                                       <span v-if="unreadCount > 0" class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white ring-2 ring-white dark:ring-slate-900 animate-pulse">
                                           {{ unreadCount }}
                                       </span>
                                   </button>
                               </template>

                               <template #content>
                                   <div class="w-80 max-w-sm bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl shadow-2xl p-4 text-left">
                                       <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-slate-800 mb-3">
                                           <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-wider">Alertas Recientes</span>
                                           <button v-if="unreadCount > 0" @click="markAllNotificationsRead" class="text-[10px] font-extrabold text-indigo-600 dark:text-indigo-400 hover:underline">
                                               Marcar todo leido
                                           </button>
                                       </div>

                                       <div class="space-y-3 max-h-[250px] overflow-y-auto">
                                           <div v-if="notifications.length === 0" class="py-6 text-center text-xs text-gray-400 italic">
                                               <i class="fa-solid fa-bell-slash text-lg mb-2 block text-gray-300"></i>Sin notificaciones pendientes
                                           </div>
                                           <div v-for="notif in notifications" :key="notif.id" @click="handleNotifClick(notif)" class="flex gap-3 p-2.5 hover:bg-gray-50 dark:hover:bg-slate-850 rounded-2xl cursor-pointer transition-all duration-150 border-l-4" :class="[
                                               notif.color === 'rose' ? 'border-rose-500 bg-rose-500/5' :
                                               notif.color === 'emerald' ? 'border-emerald-500 bg-emerald-500/5' :
                                               notif.color === 'amber' ? 'border-amber-500 bg-amber-500/5' :
                                               'border-indigo-500 bg-indigo-500/5'
                                           ]">
                                               <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full" :class="[
                                                   notif.color === 'rose' ? 'bg-rose-100 text-rose-600 dark:bg-rose-950/30 dark:text-rose-400' :
                                                   notif.color === 'emerald' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400' :
                                                   notif.color === 'amber' ? 'bg-amber-100 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400' :
                                                   'bg-indigo-100 text-indigo-600 dark:bg-indigo-950/30 dark:text-indigo-400'
                                               ]">
                                                   <i :class="['fa-solid', notif.icon || 'fa-bell', 'text-xs']"></i>
                                               </div>
                                               <div class="flex-1 min-w-0">
                                                   <div class="text-xs font-bold text-gray-900 dark:text-white truncate uppercase">{{ notif.title }}</div>
                                                   <div class="text-[10px] text-gray-500 dark:text-slate-400 line-clamp-2 mt-0.5 leading-snug">{{ notif.message }}</div>
                                                   <div class="text-[8px] text-gray-400 dark:text-slate-500 mt-1 italic">{{ notif.created_at }}</div>
                                               </div>
                                           </div>
                                       </div>

                                       <div v-if="$page.props.auth.user.rol === 'Administrador'" class="pt-3 border-t border-gray-100 dark:border-slate-800 mt-3 text-center">
                                           <Link :href="route('admin.notifications.index')" class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 uppercase tracking-widest">
                                               Administrar Notificaciones →
                                           </Link>
                                       </div>
                                   </div>
                               </template>
                           </Dropdown>
                      </div>

                     <!-- Settings Dropdown -->
                     <div class="relative ms-3">
                          <Dropdown align="right" width="48">
                            <template #trigger>
                                <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                                </button>
                                <span v-else class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-white focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                        {{ $page.props.auth.user.name }}
                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <div class="block px-4 py-2 text-xs text-gray-400">Gestionar Cuenta</div>
                                <DropdownLink :href="route('profile.show')">
                                    <div class="flex items-center">
                                        <font-awesome-icon icon="fa-solid fa-user" class="mr-2 w-4 h-4" />
                                        <span>Perfil</span>
                                    </div>
                                </DropdownLink>
                                <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">Tokens API</DropdownLink>
                                <div class="border-t border-gray-200 dark:border-gray-700" />
                                <form @submit.prevent="logout">
                                    <DropdownLink as="button">
                                        <div class="flex items-center text-red-600 dark:text-red-400">
                                            <font-awesome-icon icon="fa-solid fa-right-from-bracket" class="mr-2 w-4 h-4" />
                                            <span>Cerrar Sesión</span>
                                        </div>
                                    </DropdownLink>
                                </form>
                            </template>
                          </Dropdown>
                     </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
